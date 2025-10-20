<?php

namespace App\Services;

use App\Models\Fees\FeesCollect;
use App\Models\Fees\PaymentTransaction;
use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\ParentDeposit\ParentDepositTransaction;
use App\Services\ParentDepositService;
use App\Services\EnhancedFeeCollectionService;
use App\Services\ReceiptGenerationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Exception;

class SiblingFeeCollectionService extends EnhancedFeeCollectionService
{
    protected $receiptGenerationService;

    public function __construct(
        ParentDepositService $depositService,           // Required by parent class
        ReceiptGenerationService $receiptGenerationService  // Required by this class
    ) {
        parent::__construct($depositService);  // Pass dependency to parent
        $this->receiptGenerationService = $receiptGenerationService;
    }
    /**
     * Get sibling fee data for family payment
     */
    public function getSiblingFeeData(Student $student): array
    {
        $parent = $student->parent;

        if (!$parent) {
            throw new Exception('Parent/Guardian not found for student');
        }

        // Get all siblings including the original student
        $siblings = $parent->children()
            ->with(['feesCollects' => function($query) {
                $query->where('academic_year_id', activeAcademicYear())
                      ->where('payment_status', '!=', 'paid')
                      ->with('feeType');
            }])
            ->active()
            ->get();

        $siblingData = [];
        $totalFamilyOutstanding = 0;

        foreach ($siblings as $sibling) {
            $outstandingFees = [];
            $totalSiblingOutstanding = 0;

            foreach ($sibling->feesCollects as $feeCollect) {
                $balanceAmount = $feeCollect->getBalanceAmount();
                if ($balanceAmount > 0) {
                    $outstandingFees[] = [
                        'id' => $feeCollect->id,
                        'fee_name' => $feeCollect->feeType->name ?? 'Fee',
                        'amount' => (float) $balanceAmount,
                        'due_date' => $feeCollect->due_date?->format('Y-m-d'),
                        'is_overdue' => $feeCollect->due_date && $feeCollect->due_date->isPast(),
                    ];
                    $totalSiblingOutstanding += $balanceAmount;
                }
            }

            if ($totalSiblingOutstanding > 0) {
                $siblingData[] = [
                    'id' => $sibling->id,
                    'name' => $sibling->full_name,
                    'admission_no' => $sibling->admission_no ?? 'N/A',
                    'class_section' => ($sibling->session_class_student?->class?->name ?? 'N/A') . 
                                     ' - ' . ($sibling->session_class_student?->section?->name ?? 'N/A'),
                    'outstanding_fees' => $outstandingFees,
                    'total_outstanding' => (float) $totalSiblingOutstanding,
                    'suggested_payment' => 0, // Will be calculated by frontend
                ];
                $totalFamilyOutstanding += $totalSiblingOutstanding;
            }
        }

        $availableDeposit = $this->checkAvailableDeposit($student);

        return [
            'parent_info' => [
                'id' => $parent->id,
                'name' => $parent->guardian_name ?? $parent->father_name,
                'email' => $parent->guardian_email,
                'mobile' => $parent->guardian_mobile ?? $parent->father_mobile,
            ],
            'available_deposit' => (float) $availableDeposit,
            'formatted_deposit' => Setting('currency_symbol') . number_format($availableDeposit, 2),
            'siblings' => $siblingData,
            'total_family_outstanding' => (float) $totalFamilyOutstanding,
            'formatted_total_outstanding' => Setting('currency_symbol') . number_format($totalFamilyOutstanding, 2),
            'can_pay_fully_with_deposit' => $availableDeposit >= $totalFamilyOutstanding,
        ];
    }

    /**
     * Calculate optimal payment distribution across siblings
     */
    public function calculatePaymentDistribution(array $siblingIds, float $totalAmount, string $method = 'equal'): array
    {
        $siblings = Student::with(['feesCollects' => function($query) {
            $query->where('academic_year_id', activeAcademicYear())
                  ->where('payment_status', '!=', 'paid');
        }])->whereIn('id', $siblingIds)->get();

        $distribution = [];
        $totalOutstanding = 0;

        // Calculate total outstanding for each sibling
        foreach ($siblings as $sibling) {
            $siblingOutstanding = $sibling->feesCollects->sum(function($fee) {
                return $fee->getBalanceAmount();
            });

            $distribution[$sibling->id] = [
                'student_id' => $sibling->id,
                'name' => $sibling->full_name,
                'outstanding_amount' => (float) $siblingOutstanding,
                'suggested_payment' => 0,
            ];

            $totalOutstanding += $siblingOutstanding;
        }

        // Apply distribution method
        switch ($method) {
            case 'equal':
                $equalAmount = $totalAmount / count($siblings);
                foreach ($distribution as $studentId => &$data) {
                    $data['suggested_payment'] = min($equalAmount, $data['outstanding_amount']);
                }
                break;

            case 'proportional':
                if ($totalOutstanding > 0) {
                    foreach ($distribution as $studentId => &$data) {
                        $proportion = $data['outstanding_amount'] / $totalOutstanding;
                        $proportionalAmount = $totalAmount * $proportion;
                        $data['suggested_payment'] = min($proportionalAmount, $data['outstanding_amount']);
                    }
                }
                break;

            case 'priority':
                // Pay overdue fees first, then distribute remaining
                $sortedSiblings = collect($distribution)->sortByDesc('outstanding_amount');
                $remainingAmount = $totalAmount;

                foreach ($sortedSiblings as $studentId => &$data) {
                    if ($remainingAmount <= 0) break;

                    $allocation = min($remainingAmount, $data['outstanding_amount']);
                    $data['suggested_payment'] = $allocation;
                    $remainingAmount -= $allocation;
                }
                break;
        }

        return [
            'distribution' => array_values($distribution),
            'total_amount' => (float) $totalAmount,
            'total_outstanding' => (float) $totalOutstanding,
            'distribution_method' => $method,
            'total_allocated' => (float) array_sum(array_column($distribution, 'suggested_payment')),
        ];
    }

    /**
     * Convert string payment method to integer ID
     */
    protected function convertPaymentMethodToId(string $paymentMethod): int
    {
        return match($paymentMethod) {
            'cash' => 1,
            'zaad' => 3,
            'edahab' => 4,
            'deposit' => 6,
            default => 1 // Default to cash
        };
    }

    /**
     * Process sibling payment transaction with consolidated receipt generation
     */
    public function processSiblingPayment(array $paymentData): array
    {
        return DB::transaction(function () use ($paymentData) {
            // Generate unique payment session ID for grouping related transactions
            $paymentSessionId = 'FAM_' . time() . '_' . uniqid();

            $paymentMode = $paymentData['payment_mode'] ?? 'direct'; // 'deposit' | 'direct'
            $siblingPayments = $paymentData['sibling_payments'] ?? [];
            $paymentMethod = $paymentData['payment_method'] ?? 1; // Default to cash

            // Convert string payment method to integer if needed
            if (is_string($paymentMethod)) {
                $paymentMethod = $this->convertPaymentMethodToId($paymentMethod);
            }

            $paymentDate = $paymentData['payment_date'] ?? now();
            $notes = $paymentData['notes'] ?? '';
            $journalId = $paymentData['journal_id'] ?? null;

            // Extract discount information
            $discountType = $paymentData['discount_type'] ?? null;
            $discountValue = (float) ($paymentData['discount_amount'] ?? 0);

            // Calculate total outstanding amount for proportional discount distribution
            $totalOutstanding = array_sum(array_column($siblingPayments, 'amount'));

            // Calculate total discount amount
            $totalDiscountAmount = 0;
            if ($discountType && $discountValue > 0 && $totalOutstanding > 0) {
                if ($discountType === 'percentage') {
                    $totalDiscountAmount = ($totalOutstanding * $discountValue) / 100;
                    // Cap percentage discount at 100%
                    $totalDiscountAmount = min($totalDiscountAmount, $totalOutstanding);
                } else {
                    // Fixed amount discount
                    $totalDiscountAmount = min($discountValue, $totalOutstanding);
                }
            }

            $results = [];
            $allTransactionIds = []; // Track all transaction IDs for receipt generation
            $totalProcessed = 0;
            $totalDepositUsed = 0;
            $totalCashPayment = 0;
            $totalDiscountApplied = 0;

            foreach ($siblingPayments as $siblingPayment) {
                $student = Student::findOrFail($siblingPayment['student_id']);
                $paymentAmount = (float) $siblingPayment['amount'];
                $feeIds = $siblingPayment['fee_ids'] ?? [];

                if ($paymentAmount <= 0) {
                    continue;
                }

                // Get fees to pay for this sibling
                $feesToPay = FeesCollect::whereIn('id', $feeIds)
                    ->where('student_id', $student->id)
                    ->get();

                // Calculate proportional discount for this sibling
                $siblingDiscount = 0;
                if ($totalDiscountAmount > 0 && $totalOutstanding > 0) {
                    $proportion = $paymentAmount / $totalOutstanding;
                    $siblingDiscount = $totalDiscountAmount * $proportion;
                }

                // Calculate net payment amount after discount
                // User pays: original amount - discount
                $netPaymentAmount = $paymentAmount - $siblingDiscount;

                $siblingResult = $this->processSiblingIndividualPayment(
                    $student,
                    $feesToPay,
                    $netPaymentAmount,  // Use net amount (after discount)
                    $paymentMode,
                    is_string($paymentMethod) ? $this->convertPaymentMethodToId($paymentMethod) : $paymentMethod,
                    $paymentDate,
                    $notes,
                    $journalId,
                    $siblingDiscount,
                    $discountType,
                    $paymentSessionId // Pass session ID to link transactions
                );

                // Collect transaction IDs for receipt generation
                if ($siblingResult['success'] && !empty($siblingResult['transactions'])) {
                    foreach ($siblingResult['transactions'] as $transaction) {
                        $allTransactionIds[] = $transaction->id;
                    }
                }

                $results[] = [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'success' => $siblingResult['success'],
                    'gross_amount' => $paymentAmount,           // Original amount before discount
                    'discount_amount' => $siblingDiscount,       // Discount applied
                    'net_payment' => $netPaymentAmount,          // Actual amount paid (gross - discount)
                    'deposit_used' => $siblingResult['deposit_used'] ?? 0,
                    'cash_payment' => $siblingResult['cash_payment'] ?? 0,
                    'transactions' => $siblingResult['transactions'] ?? [],
                    'error' => $siblingResult['error'] ?? null,
                ];

                if ($siblingResult['success']) {
                    $totalProcessed += $netPaymentAmount;       // Track net payment, not gross
                    $totalDepositUsed += $siblingResult['deposit_used'] ?? 0;
                    $totalCashPayment += $siblingResult['cash_payment'] ?? 0;
                    $totalDiscountApplied += $siblingDiscount;
                }
            }

            // Generate individual receipts for each student in family payment
            $receipts = [];
            if (!empty($allTransactionIds)) {
                try {
                    $receipts = $this->receiptGenerationService->generateFamilyReceipts(
                        $paymentSessionId,
                        $allTransactionIds
                    );

                    Log::info('Individual receipts generated for family payment', [
                        'receipt_numbers' => collect($receipts)->pluck('receipt_number')->toArray(),
                        'payment_session_id' => $paymentSessionId,
                        'receipt_count' => count($receipts),
                        'transaction_count' => count($allTransactionIds),
                    ]);
                } catch (Exception $e) {
                    Log::error('Failed to generate family receipts', [
                        'payment_session_id' => $paymentSessionId,
                        'error' => $e->getMessage(),
                    ]);
                    // Don't fail the entire payment if receipt generation fails
                    // The transactions are already created successfully
                }
            }

            Log::info('Sibling payment processing completed', [
                'payment_mode' => $paymentMode,
                'total_net_payment' => $totalProcessed,         // Net amount actually paid
                'total_deposit_used' => $totalDepositUsed,
                'total_cash_payment' => $totalCashPayment,
                'total_discount_applied' => $totalDiscountApplied,
                'discount_type' => $discountType,
                'total_coverage' => $totalProcessed + $totalDiscountApplied,  // Payment + Discount
                'results_count' => count($results),
            ]);

            return [
                'success' => true,
                'receipts' => $receipts, // Individual receipts for each student
                'receipt_numbers' => collect($receipts)->pluck('receipt_number')->toArray(),
                'payment_session_id' => $paymentSessionId,
                'results' => $results,
                'summary' => [
                    'total_net_payment' => (float) $totalProcessed,           // Net payment (after discount)
                    'total_discount_applied' => (float) $totalDiscountApplied, // Total discount
                    'total_coverage' => (float) ($totalProcessed + $totalDiscountApplied),  // Total covered
                    'total_deposit_used' => (float) $totalDepositUsed,
                    'total_cash_payment' => (float) $totalCashPayment,
                    'discount_type' => $discountType,
                    'successful_payments' => count(array_filter($results, fn($r) => $r['success'])),
                    'failed_payments' => count(array_filter($results, fn($r) => !$r['success'])),
                ],
            ];
        });
    }

    /**
     * Process payment for individual sibling
     */
    protected function processSiblingIndividualPayment(
        Student $student,
        Collection $feesToPay,
        float $paymentAmount,
        string $paymentMode,
        int $paymentMethod,
        $paymentDate,
        string $notes,
        ?int $journalId = null,
        float $discountAmount = 0,
        ?string $discountType = null,
        ?string $paymentSessionId = null
    ): array {
        try {
            $parent = $student->parent;
            $depositUsed = 0;
            $cashPayment = $paymentAmount;
            $transactions = [];

            // Handle deposit payment mode
            if ($paymentMode === 'deposit' && $parent) {
                $availableDeposit = $this->checkAvailableDeposit($student);
                $depositUsed = min($availableDeposit, $paymentAmount);
                $cashPayment = $paymentAmount - $depositUsed;

                if ($depositUsed > 0) {
                    $this->allocateDepositToFees($parent, $student, $depositUsed, $feesToPay);
                }
            }

            // Distribute payment and discount across fees proportionally
            // NOTE: $paymentAmount here is already the NET amount (after discount deduction)
            // Example: If user owes $30 and has $10 discount, $paymentAmount = $20
            // The $discountAmount ($10) is stored separately and distributed proportionally
            // Both payment and discount are split proportionally based on each fee's balance
            $totalFeesBalance = $feesToPay->sum(function($fee) {
                return $fee->getBalanceAmount();
            });

            foreach ($feesToPay as $feeCollect) {
                $feeBalance = $feeCollect->getBalanceAmount();

                // Distribute payment proportionally to match discount distribution
                if ($totalFeesBalance > 0) {
                    $feeProportion = $feeBalance / $totalFeesBalance;
                    $feePayment = $paymentAmount * $feeProportion;
                    $feePayment = min($feePayment, $feeBalance); // Cap at fee balance
                    $feePayment = round($feePayment, 2);
                } else {
                    $feePayment = 0;
                }

                if ($feePayment > 0) {
                    // Calculate proportional discount for this specific fee
                    $feeDiscount = 0;
                    if ($discountAmount > 0 && $totalFeesBalance > 0) {
                        $feeProportion = $feeBalance / $totalFeesBalance;
                        $feeDiscount = $discountAmount * $feeProportion;
                        $feeDiscount = round($feeDiscount, 2);
                    }

                    // Create payment transaction with session ID for grouping
                    $transaction = PaymentTransaction::create([
                        'fees_collect_id' => $feeCollect->id,
                        'student_id' => $student->id,
                        'payment_date' => $paymentDate,
                        'amount' => $feePayment,
                        'payment_method' => $paymentMode === 'deposit' ? 6 : $paymentMethod,
                        'payment_gateway' => $paymentMode === 'deposit' ? 'deposit' : $this->getPaymentGateway($paymentMethod),
                        'transaction_reference' => $paymentMode === 'deposit' ?
                            'SIBLING_DEPOSIT_' . time() :
                            ($paymentData['transaction_reference'] ?? null),
                        'payment_session_id' => $paymentSessionId, // Link to payment session
                        'payment_notes' => $notes . ' (Sibling Payment)',
                        'journal_id' => $journalId,
                        'collected_by' => auth()->id(),
                        'branch_id' => activeBranch(),
                    ]);

                    // Update fee collection with payment and discount
                    // Payment is the NET amount (already reduced by discount)
                    $feeCollect->increment('total_paid', $feePayment);

                    // Store discount information in fees_collects table
                    // Discount + Payment = Total Coverage
                    // Example: $10 payment + $5 discount = $15 fee covered
                    if ($feeDiscount > 0) {
                        $feeCollect->update([
                            'journal_id' => $journalId,
                            'discount_amount' => ($feeCollect->discount_amount ?? 0) + $feeDiscount,
                            'discount_type' => $discountType,
                            'discount_applied' => ($feeCollect->discount_applied ?? 0) + $feeDiscount, // Legacy field
                        ]);
                    } elseif ($journalId) {
                        // Update journal_id even when there's no discount
                        $feeCollect->update([
                            'journal_id' => $journalId,
                        ]);
                    }

                    // Update payment status based on new balance
                    // Balance = amount - total_paid - discount_applied
                    $feeCollect->updatePaymentStatus();

                    $transactions[] = $transaction;
                }
            }

            // Calculate actual overpayment (proportional distribution may leave unused funds)
            $totalActualPayment = collect($transactions)->sum('amount');
            $overpayment = max(0, $paymentAmount - $totalActualPayment);

            // Handle overpayment by depositing to parent account
            if ($overpayment > 0 && $parent && $paymentMode === 'direct') {
                $this->depositService->addDeposit($parent, $overpayment, $student, 'Fee overpayment deposit');
            }

            return [
                'success' => true,
                'deposit_used' => (float) $depositUsed,
                'cash_payment' => (float) $cashPayment,
                'transactions' => $transactions,
                'overpayment_deposited' => (float) $overpayment,
            ];

        } catch (Exception $e) {
            Log::error('Sibling individual payment failed', [
                'student_id' => $student->id,
                'payment_amount' => $paymentAmount,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'deposit_used' => 0,
                'cash_payment' => 0,
                'transactions' => [],
            ];
        }
    }

    /**
     * Allocate deposit to multiple fees
     */
    protected function allocateDepositToFees(ParentGuardian $parent, Student $student, float $amount, Collection $fees): void
    {
        $remainingAmount = $amount;

        foreach ($fees as $feeCollect) {
            if ($remainingAmount <= 0) break;

            $feeBalance = $feeCollect->getBalanceAmount();
            $allocation = min($remainingAmount, $feeBalance);

            if ($allocation > 0) {
                $this->allocateDepositToFee($parent, $student, $allocation, $feeCollect);
                $remainingAmount -= $allocation;
            }
        }
    }

    /**
     * Validate sibling payment data
     */
    public function validateSiblingPayment(array $paymentData): array
    {
        $errors = [];
        $siblingPayments = $paymentData['sibling_payments'] ?? [];

        if (empty($siblingPayments)) {
            $errors[] = 'No sibling payments specified';
            return ['valid' => false, 'errors' => $errors];
        }

        // Validate discount if provided
        $discountType = $paymentData['discount_type'] ?? null;
        $discountAmount = (float) ($paymentData['discount_amount'] ?? 0);

        if ($discountType && $discountAmount > 0) {
            // Validate discount type
            if (!in_array($discountType, ['fixed', 'percentage'])) {
                $errors[] = 'Invalid discount type. Must be "fixed" or "percentage".';
            }

            // Validate percentage discount
            if ($discountType === 'percentage' && $discountAmount > 100) {
                $errors[] = 'Percentage discount cannot exceed 100%.';
            }

            // Validate discount doesn't exceed total payment
            $totalPayment = array_sum(array_column($siblingPayments, 'amount'));
            if ($discountType === 'fixed' && $discountAmount > $totalPayment) {
                $errors[] = 'Fixed discount amount cannot exceed total payment amount.';
            }
        }

        $totalPayment = 0;
        foreach ($siblingPayments as $index => $siblingPayment) {
            $studentId = $siblingPayment['student_id'] ?? null;
            $amount = $siblingPayment['amount'] ?? 0;

            if (!$studentId) {
                $errors[] = "Student ID missing for payment #{$index}";
                continue;
            }

            if ($amount <= 0) {
                $errors[] = "Invalid payment amount for student #{$studentId}";
                continue;
            }

            // Validate student exists and has outstanding fees
            $student = Student::find($studentId);
            if (!$student) {
                $errors[] = "Student not found: #{$studentId}";
                continue;
            }

            $totalOutstanding = $student->feesCollects()
                ->where('academic_year_id', activeAcademicYear())
                ->where('payment_status', '!=', 'paid')
                ->sum(DB::raw('amount - total_paid'));

            if ($amount > $totalOutstanding + 100) { // Allow small overpayment tolerance
                $errors[] = "Payment amount exceeds outstanding fees for {$student->full_name}";
            }

            $totalPayment += $amount;
        }

        // Validate deposit availability for deposit payments
        if (($paymentData['payment_mode'] ?? 'direct') === 'deposit') {
            $firstStudent = Student::find($siblingPayments[0]['student_id']);
            if ($firstStudent) {
                $availableDeposit = $this->checkAvailableDeposit($firstStudent);
                if ($totalPayment > $availableDeposit) {
                    $errors[] = 'Insufficient deposit balance for total payment amount';
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'total_payment' => (float) $totalPayment,
        ];
    }
}