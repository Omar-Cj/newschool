<?php

namespace App\Services;

use App\Models\Fees\FeesCollect;
use App\Models\Fees\PaymentTransaction;
use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\ParentDeposit\ParentDepositTransaction;
use App\Services\ParentDepositService;
use App\Services\EnhancedFeeCollectionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Exception;

class SiblingFeeCollectionService extends EnhancedFeeCollectionService
{
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
     * Process sibling payment transaction
     */
    public function processSiblingPayment(array $paymentData): array
    {
        return DB::transaction(function () use ($paymentData) {
            $paymentMode = $paymentData['payment_mode'] ?? 'direct'; // 'deposit' | 'direct'
            $siblingPayments = $paymentData['sibling_payments'] ?? [];
            $paymentMethod = $paymentData['payment_method'] ?? 1; // Default to cash

            // Convert string payment method to integer if needed
            if (is_string($paymentMethod)) {
                $paymentMethod = $this->convertPaymentMethodToId($paymentMethod);
            }

            $paymentDate = $paymentData['payment_date'] ?? now();
            $notes = $paymentData['notes'] ?? '';

            $results = [];
            $totalProcessed = 0;
            $totalDepositUsed = 0;
            $totalCashPayment = 0;

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

                $siblingResult = $this->processSiblingIndividualPayment(
                    $student,
                    $feesToPay,
                    $paymentAmount,
                    $paymentMode,
                    is_string($paymentMethod) ? $this->convertPaymentMethodToId($paymentMethod) : $paymentMethod,
                    $paymentDate,
                    $notes
                );

                $results[] = [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'success' => $siblingResult['success'],
                    'payment_amount' => $paymentAmount,
                    'deposit_used' => $siblingResult['deposit_used'] ?? 0,
                    'cash_payment' => $siblingResult['cash_payment'] ?? 0,
                    'transactions' => $siblingResult['transactions'] ?? [],
                    'error' => $siblingResult['error'] ?? null,
                ];

                if ($siblingResult['success']) {
                    $totalProcessed += $paymentAmount;
                    $totalDepositUsed += $siblingResult['deposit_used'] ?? 0;
                    $totalCashPayment += $siblingResult['cash_payment'] ?? 0;
                }
            }

            Log::info('Sibling payment processing completed', [
                'payment_mode' => $paymentMode,
                'total_processed' => $totalProcessed,
                'total_deposit_used' => $totalDepositUsed,
                'total_cash_payment' => $totalCashPayment,
                'results_count' => count($results),
            ]);

            return [
                'success' => true,
                'results' => $results,
                'summary' => [
                    'total_processed' => (float) $totalProcessed,
                    'total_deposit_used' => (float) $totalDepositUsed,
                    'total_cash_payment' => (float) $totalCashPayment,
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
        string $notes
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

            // Distribute payment across fees
            $remainingAmount = $paymentAmount;
            foreach ($feesToPay as $feeCollect) {
                if ($remainingAmount <= 0) break;

                $feeBalance = $feeCollect->getBalanceAmount();
                $feePayment = min($remainingAmount, $feeBalance);

                if ($feePayment > 0) {
                    // Create payment transaction
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
                        'payment_notes' => $notes . ' (Sibling Payment)',
                        'collected_by' => auth()->id(),
                        'branch_id' => activeBranch(),
                    ]);

                    // Update fee collection
                    $feeCollect->increment('total_paid', $feePayment);
                    $feeCollect->updatePaymentStatus();

                    $transactions[] = $transaction;
                    $remainingAmount -= $feePayment;
                }
            }

            // Handle overpayment by depositing to parent account
            if ($remainingAmount > 0 && $parent && $paymentMode === 'direct') {
                $this->depositService->addDeposit($parent, $remainingAmount, $student, 'Fee overpayment deposit');
            }

            return [
                'success' => true,
                'deposit_used' => (float) $depositUsed,
                'cash_payment' => (float) $cashPayment,
                'transactions' => $transactions,
                'overpayment_deposited' => (float) max(0, $remainingAmount),
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