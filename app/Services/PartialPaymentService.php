<?php

namespace App\Services;

use App\Models\Fees\FeesCollect;
use App\Models\Fees\PaymentTransaction;
use App\Models\Accounts\Income;
use App\Models\Accounts\AccountHead;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PartialPaymentService
{
    public function processPayment(array $paymentData, int $feeCollectId): array
    {
        DB::beginTransaction();

        try {
            $feeCollect = FeesCollect::findOrFail($feeCollectId);

            // Validate payment
            $validation = $this->validatePayment($paymentData, $feeCollect);
            if (!$validation['valid']) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => $validation['message']
                ];
            }

            // Create payment transaction
            $paymentTransaction = $this->createPaymentTransaction($paymentData, $feeCollect);

            // Update fee collect record
            $this->updateFeeCollectRecord($feeCollect, $paymentData['amount']);

            // Create income record
            $this->createIncomeRecord($paymentTransaction, $feeCollect);

            // Handle tax if applicable
            $this->handleTax($paymentTransaction);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'payment_id' => $paymentTransaction->id,
                    'transaction_number' => $paymentTransaction->transaction_number,
                    'amount_paid' => $paymentTransaction->amount,
                    'remaining_balance' => $feeCollect->fresh()->getBalanceAmount(),
                    'payment_status' => $feeCollect->fresh()->payment_status,
                    'total_paid' => $feeCollect->fresh()->total_paid
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Partial payment processing failed', [
                'error' => $e->getMessage(),
                'fee_collect_id' => $feeCollectId,
                'payment_data' => $paymentData
            ]);

            return [
                'success' => false,
                'message' => 'Payment processing failed. Please try again.'
            ];
        }
    }

    private function validatePayment(array $paymentData, FeesCollect $feeCollect): array
    {
        $paymentAmount = (float) $paymentData['amount'];
        $remainingBalance = $feeCollect->getBalanceAmount();

        // Check if fee is already fully paid
        if ($feeCollect->isPaid()) {
            return [
                'valid' => false,
                'message' => 'This fee has already been fully paid.'
            ];
        }

        // Check payment amount
        if ($paymentAmount <= 0) {
            return [
                'valid' => false,
                'message' => 'Payment amount must be greater than zero.'
            ];
        }

        // Check if payment exceeds remaining balance
        if ($paymentAmount > $remainingBalance) {
            return [
                'valid' => false,
                'message' => sprintf('Payment amount ($%.2f) cannot exceed remaining balance ($%.2f).',
                    $paymentAmount, $remainingBalance)
            ];
        }

        // Validate payment method specific requirements
        if (in_array($paymentData['payment_method'], ['zaad', 'edahab'])) {
            if (empty($paymentData['transaction_reference'])) {
                return [
                    'valid' => false,
                    'message' => 'Transaction reference is required for ' . $paymentData['payment_method'] . ' payments.'
                ];
            }
        }

        return ['valid' => true];
    }

    private function createPaymentTransaction(array $paymentData, FeesCollect $feeCollect): PaymentTransaction
    {
        // Map payment method string to integer
        $paymentMethodMap = [
            'cash' => 1,
            'stripe' => 2,
            'zaad' => 3,
            'edahab' => 4,
            'paypal' => 5
        ];

        return PaymentTransaction::create([
            'fees_collect_id' => $feeCollect->id,
            'student_id' => $feeCollect->student_id,
            'payment_date' => $paymentData['payment_date'] ?? now()->toDateString(),
            'amount' => $paymentData['amount'],
            'payment_method' => $paymentMethodMap[$paymentData['payment_method']] ?? 1,
            'payment_gateway' => $paymentData['payment_method'],
            'transaction_reference' => $paymentData['transaction_reference'] ?? null,
            'payment_notes' => $paymentData['payment_notes'] ?? null,
            'journal_id' => $paymentData['journal_id'] ?? null,
            'collected_by' => Auth::id(),
            'branch_id' => Auth::user()->branch_id ?? null,
        ]);
    }

    private function updateFeeCollectRecord(FeesCollect $feeCollect, float $paymentAmount): void
    {
        $feeCollect->total_paid += $paymentAmount;
        $feeCollect->updatePaymentStatus();
        $feeCollect->save();
    }

    private function createIncomeRecord(PaymentTransaction $paymentTransaction, FeesCollect $feeCollect): void
    {
        $accountHead = AccountHead::where('type', 1)->where('status', 1)->first();

        if ($accountHead) {
            Income::create([
                'fees_collect_id' => $feeCollect->id,
                'name' => sprintf('Payment for %s (Transaction: %s)',
                    $feeCollect->getFeeName(),
                    $paymentTransaction->transaction_number),
                'session_id' => setting('session'),
                'income_head' => $accountHead->id,
                'date' => $paymentTransaction->payment_date,
                'amount' => $paymentTransaction->amount,
                'invoice_number' => 'payment_' . $paymentTransaction->id,
            ]);
        }
    }

    private function handleTax(PaymentTransaction $paymentTransaction): void
    {
        $tax = calculateTax($paymentTransaction->amount);

        if ($tax > 0) {
            $settings = Setting::whereIn('name', ['tax_income_head'])->pluck('value', 'name');
            $accountHead = AccountHead::where('name', $settings['tax_income_head'] ?? '')->first();

            if ($accountHead) {
                Income::create([
                    'name' => 'Tax on Payment - ' . $paymentTransaction->transaction_number,
                    'session_id' => setting('session'),
                    'income_head' => $accountHead->id,
                    'date' => $paymentTransaction->payment_date,
                    'amount' => $tax,
                    'invoice_number' => 'tax_' . $paymentTransaction->id,
                ]);
            }
        }
    }

    public function getPaymentHistory(int $feeCollectId): array
    {
        $feeCollect = FeesCollect::with('paymentTransactions.collector')->findOrFail($feeCollectId);

        $payments = $feeCollect->paymentTransactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number,
                'date' => $transaction->payment_date->format('Y-m-d'),
                'amount' => $transaction->amount,
                'payment_method' => $transaction->getPaymentMethodName(),
                'transaction_reference' => $transaction->transaction_reference,
                'collected_by' => $transaction->getCollectorName(),
                'payment_notes' => $transaction->payment_notes,
            ];
        });

        return [
            'fee_id' => $feeCollect->id,
            'fee_name' => $feeCollect->getFeeName(),
            'total_amount' => $feeCollect->getNetAmount(),
            'total_paid' => $feeCollect->total_paid,
            'balance_amount' => $feeCollect->getBalanceAmount(),
            'payment_status' => $feeCollect->payment_status,
            'payments' => $payments->toArray(),
        ];
    }

    public function getOutstandingBalance(int $feeCollectId): float
    {
        $feeCollect = FeesCollect::findOrFail($feeCollectId);
        return $feeCollect->getBalanceAmount();
    }

    public function reversePayment(int $paymentTransactionId, string $reason = ''): array
    {
        DB::beginTransaction();

        try {
            $paymentTransaction = PaymentTransaction::findOrFail($paymentTransactionId);
            $feeCollect = $paymentTransaction->feesCollect;

            // Update fee collect totals
            $feeCollect->total_paid -= $paymentTransaction->amount;
            $feeCollect->updatePaymentStatus();
            $feeCollect->save();

            // Remove associated income records
            Income::where('invoice_number', 'payment_' . $paymentTransaction->id)->delete();
            Income::where('invoice_number', 'tax_' . $paymentTransaction->id)->delete();

            // Soft delete or mark as reversed
            $paymentTransaction->delete();

            // Log the reversal
            Log::info('Payment reversed', [
                'transaction_id' => $paymentTransactionId,
                'fee_collect_id' => $feeCollect->id,
                'amount' => $paymentTransaction->amount,
                'reason' => $reason,
                'reversed_by' => Auth::id()
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Payment has been successfully reversed.',
                'data' => [
                    'new_balance' => $feeCollect->fresh()->getBalanceAmount(),
                    'payment_status' => $feeCollect->fresh()->payment_status
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment reversal failed', [
                'error' => $e->getMessage(),
                'payment_transaction_id' => $paymentTransactionId
            ]);

            return [
                'success' => false,
                'message' => 'Payment reversal failed. Please try again.'
            ];
        }
    }

    public function getStudentPaymentSummary(int $studentId): array
    {
        $fees = FeesCollect::where('student_id', $studentId)
            ->with('paymentTransactions')
            ->get();

        $summary = [
            'total_fees' => $fees->sum(fn($fee) => $fee->getNetAmount()),
            'total_paid' => $fees->sum('total_paid'),
            'outstanding_balance' => $fees->sum(fn($fee) => $fee->getBalanceAmount()),
            'fully_paid_count' => $fees->where('payment_status', 'paid')->count(),
            'partially_paid_count' => $fees->where('payment_status', 'partial')->count(),
            'unpaid_count' => $fees->where('payment_status', 'unpaid')->count(),
            'recent_payments' => $fees->flatMap->paymentTransactions
                ->sortByDesc('payment_date')
                ->take(5)
                ->map(fn($payment) => [
                    'date' => $payment->payment_date->format('Y-m-d'),
                    'amount' => $payment->amount,
                    'fee_name' => $payment->feesCollect->getFeeName(),
                    'method' => $payment->getPaymentMethodName()
                ])
                ->values()
                ->toArray()
        ];

        return $summary;
    }
}