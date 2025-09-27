<?php

namespace App\Services\Enhanced;

use App\Models\Fees\Receipt;
use App\Models\Fees\ReceiptAllocation;
use App\Models\Fees\PaymentTransaction;
use App\Models\Fees\FeesCollect;
use App\Services\ReceiptNumberingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Enhanced Receipt Management Service
 * Provides unified receipt management with proper separation of concerns
 */
class ReceiptManagementService
{
    public function __construct(
        private ReceiptNumberingService $numberingService,
        private PaymentAllocationService $allocationService
    ) {}

    /**
     * Create receipt from PaymentTransaction
     */
    public function createReceiptFromPaymentTransaction(PaymentTransaction $transaction): Receipt
    {
        return DB::transaction(function () use ($transaction) {
            $receiptNumber = $this->numberingService->generateReceiptNumber($transaction->payment_date);

            $receipt = Receipt::create([
                'receipt_number' => $receiptNumber,
                'student_id' => $transaction->student_id,
                'payment_date' => $transaction->payment_date,
                'total_amount' => $transaction->amount,
                'payment_method' => $transaction->payment_method,
                'payment_method_details' => [
                    'gateway' => $transaction->payment_gateway,
                    'reference' => $transaction->transaction_reference,
                ],
                'transaction_reference' => $transaction->transaction_reference,
                'collected_by' => $transaction->collected_by,
                'receipt_type' => $this->determineReceiptType($transaction),
                'payment_status' => $this->determinePaymentStatus($transaction),
                'notes' => $transaction->payment_notes,
                'source_type' => PaymentTransaction::class,
                'source_id' => $transaction->id,
                'branch_id' => $transaction->branch_id,
                'academic_year_id' => $this->getAcademicYearId($transaction),
                'session_id' => $this->getSessionId($transaction),
                'receipt_data' => $this->buildReceiptDataFromTransaction($transaction),
            ]);

            // Create allocation records for transparency
            $this->createAllocationRecords($receipt, $transaction);

            return $receipt;
        });
    }

    /**
     * Create receipt from FeesCollect (legacy compatibility)
     */
    public function createReceiptFromFeesCollect(FeesCollect $feesCollect): Receipt
    {
        return DB::transaction(function () use ($feesCollect) {
            $receiptNumber = $this->numberingService->generateReceiptNumber($feesCollect->date);

            $receipt = Receipt::create([
                'receipt_number' => $receiptNumber,
                'student_id' => $feesCollect->student_id,
                'payment_date' => $feesCollect->date,
                'total_amount' => $feesCollect->amount + ($feesCollect->fine_amount ?? 0),
                'payment_method' => $feesCollect->payment_method,
                'payment_method_details' => [
                    'reference' => $feesCollect->transaction_reference,
                ],
                'transaction_reference' => $feesCollect->transaction_reference,
                'collected_by' => $feesCollect->fees_collect_by,
                'receipt_type' => 'payment',
                'payment_status' => $this->determinePaymentStatusFromFeesCollect($feesCollect),
                'source_type' => FeesCollect::class,
                'source_id' => $feesCollect->id,
                'branch_id' => $feesCollect->branch_id,
                'academic_year_id' => $feesCollect->academic_year_id,
                'session_id' => $feesCollect->session_id,
                'receipt_data' => $this->buildReceiptDataFromFeesCollect($feesCollect),
            ]);

            // Create allocation record
            $this->createAllocationFromFeesCollect($receipt, $feesCollect);

            return $receipt;
        });
    }

    /**
     * Get receipt with comprehensive data for display/printing
     */
    public function getReceiptForDisplay(string $receiptNumber): ?Receipt
    {
        return Receipt::with([
            'student.sessionStudentDetails.class',
            'student.sessionStudentDetails.section',
            'collector',
            'allocations.feesCollect.feeType',
            'source'
        ])->where('receipt_number', $receiptNumber)
          ->active()
          ->first();
    }

    /**
     * Get receipts with advanced filtering and pagination
     */
    public function getReceiptsWithFilters(array $filters, int $perPage = 20)
    {
        $query = Receipt::with([
            'student',
            'collector',
            'allocations'
        ])->active();

        // Apply filters
        if (isset($filters['student_id'])) {
            $query->byStudent($filters['student_id']);
        }

        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->byDateRange($filters['date_from'], $filters['date_to']);
        }

        if (isset($filters['payment_method'])) {
            $query->byPaymentMethod($filters['payment_method']);
        }

        if (isset($filters['collector_id'])) {
            $query->byCollector($filters['collector_id']);
        }

        if (isset($filters['receipt_type'])) {
            $query->where('receipt_type', $filters['receipt_type']);
        }

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        // Search functionality
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('receipt_number', 'like', "%{$search}%")
                  ->orWhere('transaction_reference', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('admission_no', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('payment_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->paginate($perPage);
    }

    /**
     * Void receipt with proper audit trail
     */
    public function voidReceipt(string $receiptNumber, string $reason): bool
    {
        return DB::transaction(function () use ($receiptNumber, $reason) {
            $receipt = Receipt::where('receipt_number', $receiptNumber)->first();

            if (!$receipt || $receipt->isVoided()) {
                return false;
            }

            // Void the receipt
            $receipt->voidReceipt($reason, Auth::id());

            // Reverse related financial transactions
            $this->reverseFinancialImpact($receipt);

            return true;
        });
    }

    /**
     * Get receipt statistics for dashboard/reporting
     */
    public function getReceiptStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $receipts = Receipt::active()
            ->byDateRange($startDate, $endDate)
            ->get();

        return [
            'total_receipts' => $receipts->count(),
            'total_amount' => $receipts->sum('total_amount'),
            'by_payment_method' => $receipts->groupBy('payment_method')
                ->map->sum('total_amount'),
            'by_receipt_type' => $receipts->groupBy('receipt_type')
                ->map->count(),
            'by_payment_status' => $receipts->groupBy('payment_status')
                ->map->count(),
            'average_receipt_amount' => $receipts->avg('total_amount'),
            'largest_receipt' => $receipts->max('total_amount'),
            'daily_totals' => $receipts->groupBy(function ($receipt) {
                return $receipt->payment_date->format('Y-m-d');
            })->map->sum('total_amount'),
        ];
    }

    // Private helper methods

    private function determineReceiptType(PaymentTransaction $transaction): string
    {
        $feesCollect = $transaction->feesCollect;

        if (!$feesCollect) {
            return 'payment';
        }

        $balanceAfterPayment = $feesCollect->getBalanceAmount();

        return $balanceAfterPayment > 0 ? 'partial_payment' : 'payment';
    }

    private function determinePaymentStatus(PaymentTransaction $transaction): string
    {
        $feesCollect = $transaction->feesCollect;

        if (!$feesCollect) {
            return 'completed';
        }

        return $feesCollect->getBalanceAmount() > 0 ? 'partial' : 'completed';
    }

    private function determinePaymentStatusFromFeesCollect(FeesCollect $feesCollect): string
    {
        return $feesCollect->getBalanceAmount() > 0 ? 'partial' : 'completed';
    }

    private function buildReceiptDataFromTransaction(PaymentTransaction $transaction): array
    {
        return [
            'transaction_number' => $transaction->transaction_number,
            'journal_id' => $transaction->journal_id,
            'fee_details' => $transaction->feesCollect ? [
                'fee_name' => $transaction->feesCollect->getFeeName(),
                'fee_type' => $transaction->feesCollect->feeType?->name,
                'total_fee_amount' => $transaction->feesCollect->getNetAmount(),
                'balance_before' => $transaction->feesCollect->getBalanceAmount() + $transaction->amount,
                'balance_after' => $transaction->feesCollect->getBalanceAmount(),
            ] : null,
        ];
    }

    private function buildReceiptDataFromFeesCollect(FeesCollect $feesCollect): array
    {
        return [
            'fee_details' => [
                'fee_name' => $feesCollect->getFeeName(),
                'fee_type' => $feesCollect->feeType?->name,
                'total_fee_amount' => $feesCollect->getNetAmount(),
                'fine_amount' => $feesCollect->fine_amount ?? 0,
            ],
            'legacy_payment' => true,
        ];
    }

    private function createAllocationRecords(Receipt $receipt, PaymentTransaction $transaction): void
    {
        if ($transaction->feesCollect) {
            $feesCollect = $transaction->feesCollect;
            $balanceBefore = $feesCollect->getBalanceAmount() + $transaction->amount;

            ReceiptAllocation::create([
                'receipt_id' => $receipt->id,
                'fees_collect_id' => $feesCollect->id,
                'fee_name' => $feesCollect->getFeeName(),
                'fee_type' => $feesCollect->feeType?->name ?? 'Unknown',
                'allocated_amount' => $transaction->amount,
                'allocation_percentage' => 100.0,
                'fee_total_amount' => $feesCollect->getNetAmount(),
                'fee_balance_before' => $balanceBefore,
                'fee_balance_after' => $feesCollect->getBalanceAmount(),
                'allocation_order' => 1,
                'allocation_method' => 'full_payment',
            ]);
        }
    }

    private function createAllocationFromFeesCollect(Receipt $receipt, FeesCollect $feesCollect): void
    {
        $allocatedAmount = $feesCollect->amount + ($feesCollect->fine_amount ?? 0);

        ReceiptAllocation::create([
            'receipt_id' => $receipt->id,
            'fees_collect_id' => $feesCollect->id,
            'fee_name' => $feesCollect->getFeeName(),
            'fee_type' => $feesCollect->feeType?->name ?? 'Unknown',
            'allocated_amount' => $allocatedAmount,
            'allocation_percentage' => 100.0,
            'fee_total_amount' => $feesCollect->getNetAmount(),
            'fee_balance_before' => $allocatedAmount,
            'fee_balance_after' => $feesCollect->getBalanceAmount(),
            'allocation_order' => 1,
            'allocation_method' => 'full_payment',
        ]);
    }

    private function getAcademicYearId(PaymentTransaction $transaction): ?int
    {
        return $transaction->feesCollect?->academic_year_id;
    }

    private function getSessionId(PaymentTransaction $transaction): ?int
    {
        return $transaction->feesCollect?->session_id;
    }

    private function reverseFinancialImpact(Receipt $receipt): void
    {
        // Reverse income records, payment transactions, etc.
        // This would need to be implemented based on your accounting system
        // For now, we'll just log the reversal
        \Log::info('Receipt voided', [
            'receipt_number' => $receipt->receipt_number,
            'amount' => $receipt->total_amount,
            'voided_by' => Auth::id(),
        ]);
    }
}