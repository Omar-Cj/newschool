<?php

namespace App\Services;

use App\Models\Fees\FeesCollect;
use App\Models\Fees\PaymentTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ReceiptService
{
    /**
     * Get transaction-centric receipt listing following proper accounting principles.
     * Each receipt represents one actual payment transaction.
     */
    public function getReceiptListing(Request $request): LengthAwarePaginator
    {
        // Get individual payment transactions (primary data source)
        $paymentTransactions = $this->getPaymentTransactionReceipts($request);

        // Get legacy payment records for backward compatibility
        $legacyPayments = $this->getLegacyPaymentReceipts($request);

        // Combine and standardize receipt data
        $allReceipts = $paymentTransactions->merge($legacyPayments);

        // Sort by payment date (most recent first)
        $sortedReceipts = $allReceipts->sortByDesc(function($receipt) {
            return $receipt->payment_date ?? $receipt->date;
        })->values();

        // Paginate results
        return $this->paginateReceipts($sortedReceipts, $request);
    }

    /**
     * Get PaymentTransaction records formatted as individual receipts.
     */
    private function getPaymentTransactionReceipts(Request $request): Collection
    {
        $query = PaymentTransaction::with([
            'student.sessionStudentDetails.class',
            'student.sessionStudentDetails.section',
            'collector',
            'feesCollect.feeType'
        ]);

        // Apply filters
        $this->applyPaymentTransactionFilters($query, $request);

        $transactions = $query->orderBy('payment_date', 'desc')->get();

        return $transactions->map(function($transaction) {
            return $this->formatPaymentTransactionReceipt($transaction);
        });
    }

    /**
     * Get legacy FeesCollect records that represent actual payments.
     */
    private function getLegacyPaymentReceipts(Request $request): Collection
    {
        $query = FeesCollect::with([
            'student.sessionStudentDetails.class',
            'student.sessionStudentDetails.section',
            'collectBy',
            'feeType'
        ])
        // Only include records that represent actual payments
        ->whereNotNull('payment_method')
        ->whereNotNull('fees_collect_by');

        // Apply filters
        $this->applyFeesCollectFilters($query, $request);

        $payments = $query->orderBy('date', 'desc')->get();

        return $payments->map(function($payment) {
            return $this->formatLegacyPaymentReceipt($payment);
        });
    }

    /**
     * Format PaymentTransaction as standardized receipt object.
     */
    private function formatPaymentTransactionReceipt(PaymentTransaction $transaction): object
    {
        $feeAllocation = $this->getPaymentAllocation($transaction);

        return (object) [
            'id' => $transaction->id,
            'type' => 'payment_transaction',
            'receipt_number' => $this->generateReceiptNumber($transaction->id, 'PT'),
            'student' => $transaction->student,
            'payment_date' => $transaction->payment_date,
            'amount_paid' => $transaction->amount,
            'payment_method' => $transaction->getPaymentMethodName(),
            'collected_by' => $transaction->collector,
            'transaction_reference' => $transaction->transaction_reference,
            'fees_affected' => $feeAllocation['fees_affected'],
            'allocation_summary' => $feeAllocation['summary'],
            'has_partial_payments' => $feeAllocation['has_partial'],
            'payment_status' => $feeAllocation['has_partial'] ? 'partial' : 'full',
            'created_at' => $transaction->created_at,
        ];
    }

    /**
     * Format legacy FeesCollect as standardized receipt object.
     */
    private function formatLegacyPaymentReceipt(FeesCollect $payment): object
    {
        // Calculate payment status based on student's total outstanding balance
        // instead of individual fee balance for consistent financial status
        $studentTotalOutstanding = $payment->student->getOutstandingAmount();
        $hasPartial = $studentTotalOutstanding > 0;

        return (object) [
            'id' => $payment->id,
            'type' => 'legacy_payment',
            'receipt_number' => $this->generateReceiptNumber($payment->id, 'LP'),
            'student' => $payment->student,
            'payment_date' => $payment->date,
            'amount_paid' => $payment->amount + ($payment->fine_amount ?? 0),
            'payment_method' => config('site.payment_methods')[$payment->payment_method] ?? 'Unknown',
            'collected_by' => $payment->collectBy,
            'transaction_reference' => $payment->transaction_reference,
            'fees_affected' => [['name' => $payment->getFeeName(), 'amount' => $payment->amount]],
            'allocation_summary' => "Payment for {$payment->getFeeName()}",
            'has_partial_payments' => $hasPartial,
            'payment_status' => $hasPartial ? 'partial' : 'full',
            'created_at' => $payment->created_at,
        ];
    }

    /**
     * Get payment allocation details for a PaymentTransaction.
     */
    private function getPaymentAllocation(PaymentTransaction $transaction): array
    {
        $feeCollect = $transaction->feesCollect;
        $feesAffected = [];

        if ($feeCollect) {
            $remainingBalance = $feeCollect->getBalanceAmount();

            $feesAffected[] = [
                'name' => $feeCollect->getFeeName(),
                'amount' => $transaction->amount,
                'remaining_balance' => $remainingBalance,
                'is_fully_paid' => $remainingBalance <= 0
            ];
        }

        // Calculate payment status based on student's total outstanding balance
        // instead of individual fee balance for more accurate financial status
        $studentTotalOutstanding = $transaction->student->getOutstandingAmount();
        $hasPartial = $studentTotalOutstanding > 0;

        $summary = count($feesAffected) === 1
            ? "Payment for {$feesAffected[0]['name']}"
            : "Payment allocated to " . count($feesAffected) . " fees";

        return [
            'fees_affected' => $feesAffected,
            'summary' => $summary,
            'has_partial' => $hasPartial
        ];
    }

    /**
     * Generate unique receipt number based on type and ID.
     */
    private function generateReceiptNumber(int $id, string $type): string
    {
        $year = date('Y');
        $paddedId = str_pad($id, 6, '0', STR_PAD_LEFT);

        return "RCT-{$type}-{$year}-{$paddedId}";
    }

    /**
     * Apply filters to PaymentTransaction query.
     */
    private function applyPaymentTransactionFilters($query, Request $request): void
    {
        // Search filter
        if ($request->filled('q')) {
            $search = trim($request->get('q'));
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('id', $search)
                    ->orWhere('transaction_reference', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('admission_no', 'like', "%{$search}%");
                    });
            });
        }

        // Date filters
        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->get('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->get('to_date'));
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        // Collector filter
        if ($request->filled('collector_id')) {
            $query->where('collected_by', $request->get('collector_id'));
        }
    }

    /**
     * Apply filters to FeesCollect query.
     */
    private function applyFeesCollectFilters($query, Request $request): void
    {
        // Search filter
        if ($request->filled('q')) {
            $search = trim($request->get('q'));
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('id', $search)
                    ->orWhere('transaction_reference', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('admission_no', 'like', "%{$search}%");
                    });
            });
        }

        // Date filters
        if ($request->filled('from_date')) {
            $query->whereDate('date', '>=', $request->get('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('date', '<=', $request->get('to_date'));
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->get('payment_method'));
        }

        // Collector filter
        if ($request->filled('collector_id')) {
            $query->where('fees_collect_by', $request->get('collector_id'));
        }
    }

    /**
     * Paginate receipt collection.
     */
    private function paginateReceipts(Collection $receipts, Request $request): LengthAwarePaginator
    {
        $currentPage = Paginator::resolveCurrentPage();
        $perPage = 20;
        $total = $receipts->count();

        $paginatedItems = $receipts->forPage($currentPage, $perPage);

        return new LengthAwarePaginator(
            $paginatedItems,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query()
            ]
        );
    }

    /**
     * Get receipt data for individual receipt generation.
     */
    public function getReceiptData($paymentId, string $type = null): ?object
    {
        // Try PaymentTransaction first
        if (!$type || $type === 'payment_transaction') {
            $transaction = PaymentTransaction::with([
                'student.sessionStudentDetails.class',
                'student.sessionStudentDetails.section',
                'collector',
                'feesCollect.feeType'
            ])->find($paymentId);

            if ($transaction) {
                return $this->formatPaymentTransactionReceipt($transaction);
            }
        }

        // Try FeesCollect as fallback
        if (!$type || $type === 'legacy_payment') {
            $payment = FeesCollect::with([
                'student.sessionStudentDetails.class',
                'student.sessionStudentDetails.section',
                'collectBy',
                'feeType'
            ])->find($paymentId);

            if ($payment && $payment->payment_method) {
                return $this->formatLegacyPaymentReceipt($payment);
            }
        }

        return null;
    }

    /**
     * Get all collectors for filter dropdown.
     */
    public function getCollectorsForFilter(): Collection
    {
        // Get collectors from PaymentTransactions
        $ptCollectors = PaymentTransaction::distinct()->pluck('collected_by')->filter();

        // Get collectors from FeesCollect (legacy)
        $fcCollectors = FeesCollect::whereNotNull('payment_method')
            ->distinct()
            ->pluck('fees_collect_by')
            ->filter();

        $allCollectorIds = $ptCollectors->merge($fcCollectors)->unique();

        return $allCollectorIds->isEmpty()
            ? collect()
            : User::whereIn('id', $allCollectorIds)->orderBy('name')->get();
    }

    /**
     * Get school information for receipt headers.
     */
    public function getSchoolInfo(): array
    {
        return [
            'name' => setting('application_name'),
            'address' => setting('address'),
            'phone' => setting('phone'),
            'email' => setting('email'),
            'logo' => setting('logo'),
            'currency' => setting('currency_symbol')
        ];
    }
}