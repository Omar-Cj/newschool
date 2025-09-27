<?php

namespace App\Services\Enhanced;

use App\Models\Fees\FeesCollect;
use App\Models\Fees\Receipt;
use App\Models\Fees\ReceiptAllocation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Payment Allocation Service
 * Handles transparent allocation of payments across multiple fees
 */
class PaymentAllocationService
{
    /**
     * Allocate payment across multiple fees using specified strategy
     */
    public function allocatePayment(
        float $paymentAmount,
        Collection $outstandingFees,
        string $allocationMethod = 'priority'
    ): array {
        $allocations = [];
        $remainingAmount = $paymentAmount;

        // Sort fees based on allocation method
        $sortedFees = $this->sortFeesByAllocationMethod($outstandingFees, $allocationMethod);

        foreach ($sortedFees as $fee) {
            if ($remainingAmount <= 0) {
                break;
            }

            $feeBalance = $fee->getBalanceAmount();

            if ($feeBalance <= 0) {
                continue; // Skip fully paid fees
            }

            $allocationAmount = min($remainingAmount, $feeBalance);

            $allocations[] = [
                'fees_collect_id' => $fee->id,
                'fee_name' => $fee->getFeeName(),
                'fee_type' => $fee->feeType?->name ?? 'Unknown',
                'allocated_amount' => $allocationAmount,
                'allocation_percentage' => ($allocationAmount / $paymentAmount) * 100,
                'fee_total_amount' => $fee->getNetAmount(),
                'fee_balance_before' => $feeBalance,
                'fee_balance_after' => $feeBalance - $allocationAmount,
                'allocation_method' => $allocationMethod,
                'priority_score' => $this->calculatePriorityScore($fee),
            ];

            $remainingAmount -= $allocationAmount;
        }

        // Log allocation details for transparency
        $this->logAllocationDetails($paymentAmount, $allocations, $allocationMethod);

        return [
            'allocations' => $allocations,
            'total_allocated' => $paymentAmount - $remainingAmount,
            'remaining_amount' => $remainingAmount,
            'allocation_summary' => $this->generateAllocationSummary($allocations),
        ];
    }

    /**
     * Create receipt allocation records from allocation results
     */
    public function createReceiptAllocations(Receipt $receipt, array $allocationResults): void
    {
        foreach ($allocationResults['allocations'] as $index => $allocation) {
            ReceiptAllocation::create([
                'receipt_id' => $receipt->id,
                'fees_collect_id' => $allocation['fees_collect_id'],
                'fee_name' => $allocation['fee_name'],
                'fee_type' => $allocation['fee_type'],
                'allocated_amount' => $allocation['allocated_amount'],
                'allocation_percentage' => $allocation['allocation_percentage'],
                'fee_total_amount' => $allocation['fee_total_amount'],
                'fee_balance_before' => $allocation['fee_balance_before'],
                'fee_balance_after' => $allocation['fee_balance_after'],
                'allocation_order' => $index + 1,
                'allocation_method' => $allocation['allocation_method'],
                'notes' => $this->generateAllocationNotes($allocation),
            ]);
        }
    }

    /**
     * Get detailed allocation explanation for user display
     */
    public function getAllocationExplanation(array $allocations): array
    {
        $explanation = [
            'summary' => $this->generateAllocationSummary($allocations),
            'details' => [],
            'methodology' => $this->getMethodologyExplanation($allocations[0]['allocation_method'] ?? 'priority'),
        ];

        foreach ($allocations as $allocation) {
            $explanation['details'][] = [
                'fee_name' => $allocation['fee_name'],
                'amount_allocated' => $allocation['allocated_amount'],
                'percentage_of_payment' => round($allocation['allocation_percentage'], 1) . '%',
                'fee_status_before' => $this->formatFeeStatus($allocation['fee_balance_before'], $allocation['fee_total_amount']),
                'fee_status_after' => $this->formatFeeStatus($allocation['fee_balance_after'], $allocation['fee_total_amount']),
                'is_fully_paid' => $allocation['fee_balance_after'] <= 0,
            ];
        }

        return $explanation;
    }

    /**
     * Sort fees based on allocation method
     */
    private function sortFeesByAllocationMethod(Collection $fees, string $method): Collection
    {
        return match ($method) {
            'priority' => $fees->sortByDesc(fn($fee) => $this->calculatePriorityScore($fee)),
            'chronological' => $fees->sortBy('due_date'),
            'proportional' => $fees->sortByDesc('getBalanceAmount'),
            'smallest_first' => $fees->sortBy('getBalanceAmount'),
            'largest_first' => $fees->sortByDesc('getBalanceAmount'),
            default => $fees,
        };
    }

    /**
     * Calculate priority score for fee allocation
     */
    private function calculatePriorityScore(FeesCollect $fee): float
    {
        $score = 50; // Base score

        // Due date factor
        if ($fee->due_date) {
            if ($fee->due_date->isPast()) {
                $daysOverdue = $fee->due_date->diffInDays(now());
                $score += min($daysOverdue * 2, 40); // Max +40 for overdue
            } else {
                $daysUntilDue = now()->diffInDays($fee->due_date);
                if ($daysUntilDue <= 7) {
                    $score += (8 - $daysUntilDue) * 2; // Increase priority as due date approaches
                }
            }
        }

        // Fee type importance
        $feeType = $fee->feeType?->name ?? '';
        $score += match (strtolower($feeType)) {
            'tuition', 'school fees' => 30,
            'examination', 'exam fees' => 25,
            'admission', 'registration' => 20,
            'transport', 'bus fees' => 15,
            'library', 'lab fees' => 10,
            'hostel', 'dormitory' => 15,
            'fine', 'penalty' => 5,
            default => 10,
        };

        // Amount factor (larger amounts get slightly higher priority)
        $balance = $fee->getBalanceAmount();
        if ($balance > 1000) {
            $score += 10;
        } elseif ($balance > 500) {
            $score += 5;
        }

        return min($score, 100); // Cap at 100
    }

    /**
     * Generate human-readable allocation summary
     */
    private function generateAllocationSummary(array $allocations): string
    {
        $count = count($allocations);

        if ($count === 0) {
            return 'No fees allocated.';
        }

        if ($count === 1) {
            $allocation = $allocations[0];
            $status = $allocation['fee_balance_after'] <= 0 ? 'fully paid' : 'partially paid';
            return "Payment allocated to {$allocation['fee_name']} ({$status}).";
        }

        $fullyPaidCount = count(array_filter($allocations, fn($a) => $a['fee_balance_after'] <= 0));
        $partiallyPaidCount = $count - $fullyPaidCount;

        $summary = "Payment allocated across {$count} fees";

        if ($fullyPaidCount > 0) {
            $summary .= " ({$fullyPaidCount} fully paid";
            if ($partiallyPaidCount > 0) {
                $summary .= ", {$partiallyPaidCount} partially paid";
            }
            $summary .= ")";
        } else {
            $summary .= " (all partially paid)";
        }

        return $summary . ".";
    }

    /**
     * Generate allocation notes for record keeping
     */
    private function generateAllocationNotes(array $allocation): string
    {
        $notes = [];

        if ($allocation['fee_balance_after'] <= 0) {
            $notes[] = 'Fee fully paid';
        } else {
            $remaining = $allocation['fee_balance_after'];
            $notes[] = "Remaining balance: " . setting('currency_symbol') . number_format($remaining, 2);
        }

        if ($allocation['allocation_percentage'] < 10) {
            $notes[] = 'Small allocation due to payment distribution';
        }

        return implode('; ', $notes);
    }

    /**
     * Get methodology explanation for user understanding
     */
    private function getMethodologyExplanation(string $method): string
    {
        return match ($method) {
            'priority' => 'Fees are paid based on priority: overdue fees first, then by fee type importance (tuition, exams, etc.)',
            'chronological' => 'Fees are paid in order of their due dates, with oldest fees paid first',
            'proportional' => 'Payment is distributed proportionally based on outstanding balances',
            'smallest_first' => 'Smallest outstanding fees are paid first to clear more fees completely',
            'largest_first' => 'Largest outstanding fees are paid first',
            'manual' => 'Payment allocation was manually specified',
            default => 'Standard priority-based allocation method used',
        };
    }

    /**
     * Format fee status for display
     */
    private function formatFeeStatus(float $balance, float $total): string
    {
        if ($balance <= 0) {
            return 'Fully Paid';
        }

        if ($balance >= $total) {
            return 'Unpaid';
        }

        $paidAmount = $total - $balance;
        $percentage = ($paidAmount / $total) * 100;

        return sprintf('%.1f%% Paid (%s/%s)',
            $percentage,
            setting('currency_symbol') . number_format($paidAmount, 2),
            setting('currency_symbol') . number_format($total, 2)
        );
    }

    /**
     * Log allocation details for audit trail
     */
    private function logAllocationDetails(float $paymentAmount, array $allocations, string $method): void
    {
        Log::info('Payment allocation processed', [
            'payment_amount' => $paymentAmount,
            'allocation_method' => $method,
            'fees_affected' => count($allocations),
            'total_allocated' => array_sum(array_column($allocations, 'allocated_amount')),
            'allocations' => array_map(function ($allocation) {
                return [
                    'fee_name' => $allocation['fee_name'],
                    'amount' => $allocation['allocated_amount'],
                    'percentage' => round($allocation['allocation_percentage'], 2),
                ];
            }, $allocations),
        ]);
    }
}