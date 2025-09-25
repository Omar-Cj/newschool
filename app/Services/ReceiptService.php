<?php

namespace App\Services;

use App\Models\Fees\FeesCollect;
use App\Models\Fees\PaymentTransaction;
use App\Models\User;
use App\Services\ReceiptNumberingService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ReceiptService
{
    protected $receiptNumberingService;

    public function __construct(ReceiptNumberingService $receiptNumberingService)
    {
        $this->receiptNumberingService = $receiptNumberingService;
    }
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
     * Format PaymentTransaction as standardized receipt object with enhanced allocation details.
     */
    private function formatPaymentTransactionReceipt(PaymentTransaction $transaction): object
    {
        // Use unified receipt numbering if receipt_number exists, otherwise generate
        $receiptNumber = $transaction->receipt_number ?? $this->receiptNumberingService->generateReceiptNumber($transaction->payment_date);

        // Save generated receipt number back to transaction if not exists
        if (!$transaction->receipt_number) {
            try {
                $transaction->update(['receipt_number' => $receiptNumber]);
                $this->receiptNumberingService->confirmReceiptNumber($receiptNumber);
            } catch (\Exception $e) {
                Log::warning('Failed to update receipt number for PaymentTransaction', [
                    'transaction_id' => $transaction->id,
                    'receipt_number' => $receiptNumber,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $feeAllocation = $this->getEnhancedPaymentAllocation($transaction);
        $paymentSequence = $this->getPaymentSequenceInfo($transaction);

        return (object) [
            'id' => $transaction->id,
            'type' => 'payment_transaction',
            'receipt_number' => $receiptNumber,
            'student' => $transaction->student,
            'payment_date' => $transaction->payment_date,
            'amount_paid' => $transaction->amount,
            'payment_method' => $transaction->getPaymentMethodName(),
            'collected_by' => $transaction->collector,
            'transaction_reference' => $transaction->transaction_reference,
            'payment_notes' => $transaction->payment_notes,
            'fees_affected' => $feeAllocation['fees_affected'],
            'allocation_summary' => $feeAllocation['summary'],
            'allocation_methodology' => $feeAllocation['methodology'],
            'has_partial_payments' => $feeAllocation['has_partial'],
            'payment_status' => $feeAllocation['has_partial'] ? 'partial' : 'full',
            'payment_sequence' => $paymentSequence,
            'total_fees_count' => $feeAllocation['total_fees_count'],
            'fully_paid_count' => $feeAllocation['fully_paid_count'],
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at,
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
     * Get enhanced payment allocation details for a PaymentTransaction.
     */
    private function getEnhancedPaymentAllocation(PaymentTransaction $transaction): array
    {
        $feeCollect = $transaction->feesCollect;
        $feesAffected = [];

        if ($feeCollect) {
            $remainingBalance = $feeCollect->getBalanceAmount();
            $totalFeeAmount = $feeCollect->getNetAmount();
            $previouslyPaid = $totalFeeAmount - $remainingBalance - $transaction->amount;

            $feesAffected[] = [
                'id' => $feeCollect->id,
                'name' => $feeCollect->getFeeName(),
                'category' => $feeCollect->feeType->name ?? 'General Fee',
                'total_amount' => $totalFeeAmount,
                'previously_paid' => max(0, $previouslyPaid),
                'amount' => $transaction->amount,
                'remaining_balance' => $remainingBalance,
                'is_fully_paid' => $remainingBalance <= 0,
                'payment_progress' => $totalFeeAmount > 0 ? (($totalFeeAmount - $remainingBalance) / $totalFeeAmount) * 100 : 100,
                'is_final_payment' => $remainingBalance <= 0,
            ];
        }

        // Get all student payments to determine sequence
        $studentPayments = PaymentTransaction::where('student_id', $transaction->student_id)
            ->where('payment_date', '<=', $transaction->payment_date)
            ->orderBy('payment_date')
            ->orderBy('id')
            ->count();

        // Calculate totals
        $totalFeesCount = count($feesAffected);
        $fullyPaidCount = count(array_filter($feesAffected, fn($fee) => $fee['is_fully_paid']));

        // Calculate payment status based on student's total outstanding balance
        $studentTotalOutstanding = method_exists($transaction->student, 'getOutstandingAmount')
            ? $transaction->student->getOutstandingAmount()
            : 0;
        $hasPartial = $studentTotalOutstanding > 0;

        // Generate allocation summary
        $summary = $this->generateAllocationSummary($feesAffected, $transaction->amount, $hasPartial);
        $methodology = $this->getPaymentMethodology($transaction);

        return [
            'fees_affected' => $feesAffected,
            'summary' => $summary,
            'methodology' => $methodology,
            'has_partial' => $hasPartial,
            'total_fees_count' => $totalFeesCount,
            'fully_paid_count' => $fullyPaidCount,
            'payment_sequence_number' => $studentPayments,
            'total_outstanding' => $studentTotalOutstanding,
        ];
    }

    /**
     * Generate user-friendly allocation summary
     */
    private function generateAllocationSummary(array $feesAffected, float $amount, bool $hasPartial): string
    {
        if (empty($feesAffected)) {
            return "Payment of $" . number_format($amount, 2) . " processed";
        }

        if (count($feesAffected) === 1) {
            $fee = $feesAffected[0];
            if ($fee['is_fully_paid']) {
                return "✅ {$fee['name']} fully paid with this payment";
            } else {
                return "💰 Partial payment applied to {$fee['name']} (\$" . number_format($fee['remaining_balance'], 2) . " remaining)";
            }
        }

        $fullyPaidCount = count(array_filter($feesAffected, fn($fee) => $fee['is_fully_paid']));
        $totalCount = count($feesAffected);

        if ($fullyPaidCount === $totalCount) {
            return "✅ All {$totalCount} fees fully paid with this payment";
        } elseif ($fullyPaidCount > 0) {
            return "💰 {$fullyPaidCount} of {$totalCount} fees fully paid, others partially paid";
        } else {
            return "💰 Partial payment applied to {$totalCount} fees";
        }
    }

    /**
     * Get payment methodology explanation
     */
    private function getPaymentMethodology(PaymentTransaction $transaction): string
    {
        // This could be enhanced based on business rules for payment allocation
        // For now, we'll provide a simple explanation
        return "Payment allocated based on chronological priority and fee due dates";
    }

    /**
     * Get payment sequence information for transparency
     */
    private function getPaymentSequenceInfo(PaymentTransaction $transaction): array
    {
        // Get all payments for this student up to this payment
        $allStudentPayments = PaymentTransaction::where('student_id', $transaction->student_id)
            ->where('payment_date', '<=', $transaction->payment_date)
            ->orderBy('payment_date')
            ->orderBy('id')
            ->get();

        $currentIndex = $allStudentPayments->search(function ($payment) use ($transaction) {
            return $payment->id === $transaction->id;
        });

        $totalPayments = $allStudentPayments->count();
        $sequenceNumber = $currentIndex + 1;

        // Get total amounts
        $totalAmountPaid = $allStudentPayments->sum('amount');
        $thisPaymentAmount = $transaction->amount;

        return [
            'sequence_number' => $sequenceNumber,
            'total_payments' => $totalPayments,
            'cumulative_amount' => $totalAmountPaid,
            'this_payment_amount' => $thisPaymentAmount,
            'sequence_description' => "Payment {$sequenceNumber} of {$totalPayments} payments",
            'is_first_payment' => $sequenceNumber === 1,
            'is_latest_payment' => $sequenceNumber === $totalPayments,
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
