<?php

namespace App\Services;

use App\Models\Fees\FeesCollect;
use App\Models\Fees\PaymentTransaction;
use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\ParentDeposit\ParentDepositTransaction;
use App\Services\ParentDepositService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class EnhancedFeeCollectionService
{
    protected ParentDepositService $depositService;

    public function __construct(ParentDepositService $depositService)
    {
        $this->depositService = $depositService;
    }

    /**
     * Collect fee with automatic deposit utilization
     */
    public function collectFeeWithDeposit(FeesCollect $feeCollect, array $paymentData): array
    {
        return DB::transaction(function () use ($feeCollect, $paymentData) {
            $student = $feeCollect->student;
            $parent = $student->parent;

            if (!$parent) {
                throw new Exception('Parent/Guardian not found for student');
            }

            $totalAmount = (float) $paymentData['amount'];
            $availableDeposit = $this->checkAvailableDeposit($student);

            $depositUsed = 0;
            $cashPayment = $totalAmount;

            // Use deposits first if available
            if ($availableDeposit > 0) {
                $depositUsed = min($availableDeposit, $totalAmount);
                $cashPayment = $totalAmount - $depositUsed;

                // Allocate deposit
                if ($depositUsed > 0) {
                    $this->allocateDepositToFee($parent, $student, $depositUsed, $feeCollect);
                }
            }

            $transactions = [];

            // Create deposit allocation transaction if deposit was used
            if ($depositUsed > 0) {
                $depositTransaction = PaymentTransaction::create([
                    'fees_collect_id' => $feeCollect->id,
                    'student_id' => $student->id,
                    'payment_date' => now(),
                    'amount' => $depositUsed,
                    'payment_method' => 6, // Deposit allocation
                    'payment_gateway' => 'deposit',
                    'transaction_reference' => 'DEPOSIT_ALLOC_' . time(),
                    'payment_notes' => 'Allocated from parent deposit balance',
                    'collected_by' => auth()->id(),
                    'branch_id' => activeBranch(),
                ]);
                $transactions[] = $depositTransaction;
            }

            // Create cash payment transaction if additional payment needed
            if ($cashPayment > 0) {
                $cashTransaction = PaymentTransaction::create([
                    'fees_collect_id' => $feeCollect->id,
                    'student_id' => $student->id,
                    'payment_date' => $paymentData['payment_date'] ?? now(),
                    'amount' => $cashPayment,
                    'payment_method' => $paymentData['payment_method'],
                    'payment_gateway' => $this->getPaymentGateway($paymentData['payment_method']),
                    'transaction_reference' => $paymentData['transaction_reference'] ?? null,
                    'payment_notes' => $paymentData['payment_notes'] ?? null,
                    'collected_by' => auth()->id(),
                    'branch_id' => activeBranch(),
                ]);
                $transactions[] = $cashTransaction;
            }

            // Update fee collection record
            $feeCollect->update([
                'payment_method' => $depositUsed > 0 ? 6 : $paymentData['payment_method'], // 6 for mixed payment
                'payment_status' => 'paid',
                'total_paid' => $feeCollect->total_paid + $totalAmount,
            ]);

            // Recalculate payment status
            $feeCollect->updatePaymentStatus();

            Log::info('Enhanced fee collection completed', [
                'fee_collect_id' => $feeCollect->id,
                'total_amount' => $totalAmount,
                'deposit_used' => $depositUsed,
                'cash_payment' => $cashPayment,
                'student_id' => $student->id,
            ]);

            return [
                'success' => true,
                'total_amount' => $totalAmount,
                'deposit_used' => $depositUsed,
                'cash_payment' => $cashPayment,
                'transactions' => $transactions,
                'remaining_deposit' => $this->checkAvailableDeposit($student),
            ];
        });
    }

    /**
     * Check available deposit for a student
     */
    public function checkAvailableDeposit(Student $student): float
    {
        $parent = $student->parent;
        if (!$parent) {
            return 0;
        }

        // Check both general and student-specific deposits
        $generalBalance = $this->depositService->getAvailableBalance($parent);
        $studentBalance = $this->depositService->getAvailableBalance($parent, $student);

        return $generalBalance + $studentBalance;
    }

    /**
     * Auto-allocate deposits for fee payment
     */
    public function autoAllocateDeposit(FeesCollect $feeCollect): float
    {
        $student = $feeCollect->student;
        $parent = $student->parent;

        if (!$parent) {
            return 0;
        }

        $feeAmount = $feeCollect->getBalanceAmount();
        $availableDeposit = $this->checkAvailableDeposit($student);

        $allocationAmount = min($feeAmount, $availableDeposit);

        if ($allocationAmount > 0) {
            $this->allocateDepositToFee($parent, $student, $allocationAmount, $feeCollect);
        }

        return $allocationAmount;
    }

    /**
     * Process partial payment with deposit optimization
     */
    public function processPartialPaymentWithDeposit(FeesCollect $feeCollect, array $paymentData): array
    {
        return DB::transaction(function () use ($feeCollect, $paymentData) {
            $student = $feeCollect->student;
            $parent = $student->parent;

            $paymentAmount = (float) $paymentData['amount'];
            $feeBalance = $feeCollect->getBalanceAmount();

            // Validate payment amount
            if ($paymentAmount > $feeBalance) {
                throw new Exception('Payment amount cannot exceed fee balance');
            }

            $availableDeposit = $this->checkAvailableDeposit($student);
            $depositUsed = 0;
            $cashPayment = $paymentAmount;

            // Optimize payment by using deposits first
            if ($availableDeposit > 0 && $parent) {
                $depositUsed = min($availableDeposit, $paymentAmount);
                $cashPayment = $paymentAmount - $depositUsed;

                if ($depositUsed > 0) {
                    $this->allocateDepositToFee($parent, $student, $depositUsed, $feeCollect);
                }
            }

            $transactions = [];

            // Create transactions for both deposit and cash portions
            if ($depositUsed > 0) {
                $transactions[] = PaymentTransaction::create([
                    'fees_collect_id' => $feeCollect->id,
                    'student_id' => $student->id,
                    'payment_date' => now(),
                    'amount' => $depositUsed,
                    'payment_method' => 6, // Deposit allocation
                    'payment_gateway' => 'deposit',
                    'transaction_reference' => 'PARTIAL_DEPOSIT_' . time(),
                    'payment_notes' => 'Partial payment from deposit',
                    'collected_by' => auth()->id(),
                    'branch_id' => activeBranch(),
                ]);
            }

            if ($cashPayment > 0) {
                $transactions[] = PaymentTransaction::create([
                    'fees_collect_id' => $feeCollect->id,
                    'student_id' => $student->id,
                    'payment_date' => $paymentData['payment_date'] ?? now(),
                    'amount' => $cashPayment,
                    'payment_method' => $paymentData['payment_method'],
                    'payment_gateway' => $this->getPaymentGateway($paymentData['payment_method']),
                    'transaction_reference' => $paymentData['transaction_reference'] ?? null,
                    'payment_notes' => $paymentData['payment_notes'] ?? null,
                    'collected_by' => auth()->id(),
                    'branch_id' => activeBranch(),
                ]);
            }

            // Update fee record
            $feeCollect->increment('total_paid', $paymentAmount);
            $feeCollect->updatePaymentStatus();

            return [
                'success' => true,
                'payment_amount' => $paymentAmount,
                'deposit_used' => $depositUsed,
                'cash_payment' => $cashPayment,
                'transactions' => $transactions,
                'remaining_balance' => $feeCollect->fresh()->getBalanceAmount(),
                'remaining_deposit' => $this->checkAvailableDeposit($student),
            ];
        });
    }

    /**
     * Allocate deposit to fee payment
     */
    protected function allocateDepositToFee(ParentGuardian $parent, Student $student, float $amount, FeesCollect $feeCollect): void
    {
        // First try to use student-specific deposit
        $studentBalance = $this->depositService->getAvailableBalance($parent, $student);
        $studentAllocation = min($amount, $studentBalance);

        if ($studentAllocation > 0) {
            $this->createDepositAllocation($parent, $student, $studentAllocation, $feeCollect, 'student_specific');
            $amount -= $studentAllocation;
        }

        // Then use general deposit if needed
        if ($amount > 0) {
            $generalBalance = $this->depositService->getAvailableBalance($parent);
            $generalAllocation = min($amount, $generalBalance);

            if ($generalAllocation > 0) {
                $this->createDepositAllocation($parent, null, $generalAllocation, $feeCollect, 'general');
            }
        }
    }

    /**
     * Create deposit allocation transaction
     */
    protected function createDepositAllocation(ParentGuardian $parent, ?Student $student, float $amount, FeesCollect $feeCollect, string $type): void
    {
        // Update parent balance
        $balance = $parent->balances()
            ->where('academic_year_id', activeAcademicYear())
            ->when($student, function($query) use ($student) {
                return $query->where('student_id', $student->id);
            }, function($query) {
                return $query->whereNull('student_id');
            })
            ->first();

        if ($balance && $balance->deductWithdrawal($amount)) {
            // Create transaction record
            ParentDepositTransaction::create([
                'parent_guardian_id' => $parent->id,
                'student_id' => $student?->id,
                'transaction_type' => 'allocation',
                'amount' => $amount,
                'balance_before' => $balance->available_balance + $amount,
                'balance_after' => $balance->available_balance,
                'transaction_date' => now(),
                'description' => "Fee payment allocation: {$feeCollect->getFeeName()}",
                'fees_collect_id' => $feeCollect->id,
                'created_by' => auth()->id(),
                'branch_id' => activeBranch(),
            ]);
        }
    }

    /**
     * Get payment gateway name from payment method
     */
    protected function getPaymentGateway(int $paymentMethod): string
    {
        return match($paymentMethod) {
            1 => 'cash',
            2 => 'stripe',
            3 => 'zaad',
            4 => 'edahab',
            5 => 'paypal',
            6 => 'deposit',
            default => 'unknown'
        };
    }

    /**
     * Get available payment options for a student
     */
    public function getPaymentOptions(Student $student): array
    {
        $parent = $student->parent;
        $availableDeposit = $parent ? $this->checkAvailableDeposit($student) : 0;

        return [
            'available_deposit' => $availableDeposit,
            'formatted_deposit' => '$' . number_format($availableDeposit, 2),
            'can_use_deposit' => $availableDeposit > 0,
            'payment_methods' => [
                1 => 'Cash',
                3 => 'Zaad',
                4 => 'Edahab',
                6 => 'Deposit Only'
            ],
            'deposit_breakdown' => $parent ? $parent->getAllBalances() : [],
        ];
    }

    /**
     * Calculate optimal payment allocation
     */
    public function calculateOptimalPayment(FeesCollect $feeCollect, float $paymentAmount): array
    {
        $student = $feeCollect->student;
        $availableDeposit = $this->checkAvailableDeposit($student);

        $depositUsage = min($availableDeposit, $paymentAmount);
        $cashRequired = $paymentAmount - $depositUsage;

        return [
            'total_amount' => $paymentAmount,
            'deposit_usage' => $depositUsage,
            'cash_required' => $cashRequired,
            'available_deposit' => $availableDeposit,
            'remaining_deposit' => $availableDeposit - $depositUsage,
            'can_pay_fully_with_deposit' => $availableDeposit >= $paymentAmount,
            'optimization_savings' => $depositUsage > 0 ? $depositUsage : 0,
        ];
    }

    /**
     * Bulk fee collection with deposit optimization
     */
    public function bulkCollectFeesWithDeposits(array $feeCollectIds, array $paymentData): array
    {
        return DB::transaction(function () use ($feeCollectIds, $paymentData) {
            $results = [];
            $totalDepositUsed = 0;
            $totalCashPayment = 0;

            foreach ($feeCollectIds as $feeId) {
                $feeCollect = FeesCollect::findOrFail($feeId);

                try {
                    $result = $this->collectFeeWithDeposit($feeCollect, $paymentData);
                    $results[] = [
                        'fee_id' => $feeId,
                        'success' => true,
                        'result' => $result
                    ];

                    $totalDepositUsed += $result['deposit_used'];
                    $totalCashPayment += $result['cash_payment'];

                } catch (Exception $e) {
                    $results[] = [
                        'fee_id' => $feeId,
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return [
                'results' => $results,
                'summary' => [
                    'total_fees_processed' => count($feeCollectIds),
                    'successful_payments' => count(array_filter($results, fn($r) => $r['success'])),
                    'failed_payments' => count(array_filter($results, fn($r) => !$r['success'])),
                    'total_deposit_used' => $totalDepositUsed,
                    'total_cash_payment' => $totalCashPayment,
                    'total_amount' => $totalDepositUsed + $totalCashPayment,
                ]
            ];
        });
    }
}