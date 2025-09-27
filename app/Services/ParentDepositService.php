<?php

namespace App\Services;

use App\Models\ParentDeposit\ParentDeposit;
use App\Models\ParentDeposit\ParentBalance;
use App\Models\ParentDeposit\ParentDepositTransaction;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\StudentInfo\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class ParentDepositService
{
    /**
     * Create a new deposit for a parent
     */
    public function createDeposit(ParentGuardian $parent, array $data): ParentDeposit
    {
        return DB::transaction(function () use ($parent, $data) {
            // Create the deposit record
            $deposit = ParentDeposit::create([
                'parent_guardian_id' => $parent->id,
                'student_id' => $data['student_id'] ?? null,
                'amount' => $data['amount'],
                'deposit_date' => $data['deposit_date'] ?? now(),
                'payment_method' => $data['payment_method'],
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'deposit_reason' => $data['deposit_reason'] ?? null,
                'status' => 'completed',
                'collected_by' => $data['collected_by'] ?? auth()->id(),
                'branch_id' => $data['branch_id'] ?? activeBranch(),
                'academic_year_id' => $data['academic_year_id'] ?? activeAcademicYear(),
                'journal_id' => $data['journal_id'] ?? null,
            ]);

            // Update or create balance record
            $this->updateBalance($parent, $data['student_id'] ?? null, $data['amount'], 'deposit');

            // Create transaction record
            $this->createTransaction($deposit, [
                'transaction_type' => 'deposit',
                'amount' => $data['amount'],
                'description' => 'Deposit: ' . ($data['deposit_reason'] ?? 'Parent deposit'),
            ]);

            // Clear cache
            $this->clearBalanceCache($parent->id, $data['student_id'] ?? null);

            Log::info('Parent deposit created successfully', [
                'deposit_id' => $deposit->id,
                'parent_id' => $parent->id,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
            ]);

            return $deposit;
        });
    }

    /**
     * Process payment for a deposit (if needed for future payment gateway integration)
     */
    public function processPayment(ParentDeposit $deposit, string $paymentMethod, array $paymentData): bool
    {
        try {
            // For now, we handle local payment methods (Cash, Zaad, Edahab)
            // This method is prepared for future payment gateway integration

            switch ($paymentMethod) {
                case 'cash':
                case '1':
                    return $this->processCashPayment($deposit, $paymentData);
                case 'zaad':
                case '3':
                    return $this->processZaadPayment($deposit, $paymentData);
                case 'edahab':
                case '4':
                    return $this->processEdahabPayment($deposit, $paymentData);
                default:
                    throw new Exception('Unsupported payment method: ' . $paymentMethod);
            }
        } catch (Exception $e) {
            Log::error('Payment processing failed', [
                'deposit_id' => $deposit->id,
                'payment_method' => $paymentMethod,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Allocate deposit to a specific student or fee
     */
    public function allocateDepositToStudent(ParentDeposit $deposit, Student $student, float $amount): ParentDepositTransaction
    {
        return DB::transaction(function () use ($deposit, $student, $amount) {
            // Check if parent has sufficient balance
            $availableBalance = $deposit->parentGuardian->getAvailableBalance($student);
            if ($availableBalance < $amount) {
                throw new Exception('Insufficient balance for allocation');
            }

            // Update balance
            $this->updateBalance($deposit->parentGuardian, $student->id, -$amount, 'allocation');

            // Create transaction record
            $transaction = $this->createTransaction($deposit, [
                'transaction_type' => 'allocation',
                'amount' => $amount,
                'student_id' => $student->id,
                'description' => "Allocated to {$student->full_name}",
            ]);

            // Clear cache
            $this->clearBalanceCache($deposit->parent_guardian_id, $student->id);

            return $transaction;
        });
    }

    /**
     * Get available balance for a parent (optionally for specific student)
     */
    public function getAvailableBalance(ParentGuardian $parent, ?Student $student = null): float
    {
        $cacheKey = "parent_balance_{$parent->id}_" . ($student?->id ?? 'general');

        return Cache::remember($cacheKey, 3600, function () use ($parent, $student) {
            return $parent->getAvailableBalance($student);
        });
    }

    /**
     * Reserve balance for pending operations
     */
    public function reserveBalance(ParentGuardian $parent, float $amount, string $reason, ?Student $student = null): bool
    {
        return DB::transaction(function () use ($parent, $amount, $reason, $student) {
            $balance = $this->getOrCreateBalance($parent, $student);

            if (!$balance->canReserve($amount)) {
                return false;
            }

            $success = $balance->reserveAmount($amount);

            if ($success) {
                // Create transaction record
                $this->createTransactionRecord([
                    'parent_guardian_id' => $parent->id,
                    'student_id' => $student?->id,
                    'transaction_type' => 'allocation',
                    'amount' => $amount,
                    'balance_before' => $balance->available_balance + $amount,
                    'balance_after' => $balance->available_balance,
                    'transaction_date' => now(),
                    'description' => "Reserved: {$reason}",
                ]);

                $this->clearBalanceCache($parent->id, $student?->id);
            }

            return $success;
        });
    }

    /**
     * Release reserved balance
     */
    public function releaseReservedBalance(ParentGuardian $parent, float $amount, ?Student $student = null): bool
    {
        return DB::transaction(function () use ($parent, $amount, $student) {
            $balance = $this->getOrCreateBalance($parent, $student);

            $success = $balance->releaseReserved($amount);

            if ($success) {
                $this->clearBalanceCache($parent->id, $student?->id);
            }

            return $success;
        });
    }

    /**
     * Get or create balance record
     */
    protected function getOrCreateBalance(ParentGuardian $parent, ?Student $student = null): ParentBalance
    {
        return ParentBalance::firstOrCreate([
            'parent_guardian_id' => $parent->id,
            'student_id' => $student?->id,
            'academic_year_id' => activeAcademicYear(),
            'branch_id' => activeBranch(),
        ], [
            'available_balance' => 0,
            'reserved_balance' => 0,
            'total_deposits' => 0,
            'total_withdrawals' => 0,
        ]);
    }

    /**
     * Update balance record
     */
    protected function updateBalance(ParentGuardian $parent, ?int $studentId, float $amount, string $type): void
    {
        $balance = $this->getOrCreateBalance($parent, $studentId ? Student::find($studentId) : null);

        switch ($type) {
            case 'deposit':
                $balance->addDeposit($amount);
                break;
            case 'withdrawal':
            case 'allocation':
                $balance->deductWithdrawal($amount);
                break;
        }
    }

    /**
     * Create transaction record
     */
    protected function createTransaction(ParentDeposit $deposit, array $data): ParentDepositTransaction
    {
        $balance = $this->getOrCreateBalance($deposit->parentGuardian, isset($data['student_id']) ? Student::find($data['student_id']) : null);

        return $this->createTransactionRecord([
            'parent_deposit_id' => $deposit->id,
            'parent_guardian_id' => $deposit->parent_guardian_id,
            'student_id' => $data['student_id'] ?? null,
            'transaction_type' => $data['transaction_type'],
            'amount' => $data['amount'],
            'balance_before' => $balance->available_balance,
            'balance_after' => $balance->available_balance,
            'transaction_date' => now(),
            'description' => $data['description'],
            'created_by' => auth()->id(),
            'branch_id' => activeBranch(),
        ]);
    }

    /**
     * Create transaction record directly
     */
    protected function createTransactionRecord(array $data): ParentDepositTransaction
    {
        return ParentDepositTransaction::create($data);
    }

    /**
     * Process cash payment
     */
    protected function processCashPayment(ParentDeposit $deposit, array $paymentData): bool
    {
        // Cash payments are considered immediately successful
        return true;
    }

    /**
     * Process Zaad payment
     */
    protected function processZaadPayment(ParentDeposit $deposit, array $paymentData): bool
    {
        // For now, assume successful
        // In the future, integrate with Zaad API
        return true;
    }

    /**
     * Process Edahab payment
     */
    protected function processEdahabPayment(ParentDeposit $deposit, array $paymentData): bool
    {
        // For now, assume successful
        // In the future, integrate with Edahab API
        return true;
    }

    /**
     * Clear balance cache
     */
    protected function clearBalanceCache(int $parentId, ?int $studentId = null): void
    {
        $cacheKey = "parent_balance_{$parentId}_" . ($studentId ?? 'general');
        Cache::forget($cacheKey);
    }

    /**
     * Get balance summary for a parent
     */
    public function getBalanceSummary(ParentGuardian $parent): array
    {
        $balances = $parent->balances()
            ->where('academic_year_id', activeAcademicYear())
            ->get();

        $summary = [
            'total_available' => 0,
            'total_reserved' => 0,
            'total_deposits' => 0,
            'total_withdrawals' => 0,
            'accounts' => [],
        ];

        foreach ($balances as $balance) {
            $summary['total_available'] += $balance->available_balance;
            $summary['total_reserved'] += $balance->reserved_balance;
            $summary['total_deposits'] += $balance->total_deposits;
            $summary['total_withdrawals'] += $balance->total_withdrawals;

            $summary['accounts'][] = [
                'student_name' => $balance->student?->full_name ?? 'General Account',
                'available_balance' => $balance->available_balance,
                'reserved_balance' => $balance->reserved_balance,
                'total_balance' => $balance->getTotalBalance(),
                'formatted_available' => $balance->getFormattedAvailableBalance(),
                'formatted_total' => $balance->getFormattedTotalBalance(),
            ];
        }

        return $summary;
    }

    /**
     * Transfer balance between accounts
     */
    public function transferBalance(ParentGuardian $parent, ?Student $fromStudent, ?Student $toStudent, float $amount, string $reason): bool
    {
        return DB::transaction(function () use ($parent, $fromStudent, $toStudent, $amount, $reason) {
            // Check sufficient balance in source account
            $availableBalance = $parent->getAvailableBalance($fromStudent);
            if ($availableBalance < $amount) {
                throw new Exception('Insufficient balance for transfer');
            }

            // Deduct from source account
            $this->updateBalance($parent, $fromStudent?->id, -$amount, 'withdrawal');

            // Add to destination account
            $this->updateBalance($parent, $toStudent?->id, $amount, 'deposit');

            // Create transaction records
            $this->createTransactionRecord([
                'parent_guardian_id' => $parent->id,
                'student_id' => $fromStudent?->id,
                'transaction_type' => 'withdrawal',
                'amount' => $amount,
                'balance_before' => $availableBalance,
                'balance_after' => $availableBalance - $amount,
                'transaction_date' => now(),
                'description' => "Transfer out: {$reason}",
                'created_by' => auth()->id(),
                'branch_id' => activeBranch(),
            ]);

            $this->createTransactionRecord([
                'parent_guardian_id' => $parent->id,
                'student_id' => $toStudent?->id,
                'transaction_type' => 'deposit',
                'amount' => $amount,
                'balance_before' => $parent->getAvailableBalance($toStudent),
                'balance_after' => $parent->getAvailableBalance($toStudent) + $amount,
                'transaction_date' => now(),
                'description' => "Transfer in: {$reason}",
                'created_by' => auth()->id(),
                'branch_id' => activeBranch(),
            ]);

            // Clear caches
            $this->clearBalanceCache($parent->id, $fromStudent?->id);
            $this->clearBalanceCache($parent->id, $toStudent?->id);

            return true;
        });
    }
}