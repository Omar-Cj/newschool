<?php

namespace App\Models\Fees;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Receipt Allocation model for transparent payment distribution
 * Tracks exactly how each payment is allocated across fees
 */
class ReceiptAllocation extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'receipt_id',
        'fees_collect_id',
        'fee_name',
        'fee_type',
        'allocated_amount',
        'allocation_percentage',
        'fee_total_amount',
        'fee_balance_before',
        'fee_balance_after',
        'allocation_order',
        'allocation_method',
        'notes',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
        'allocation_percentage' => 'decimal:2',
        'fee_total_amount' => 'decimal:2',
        'fee_balance_before' => 'decimal:2',
        'fee_balance_after' => 'decimal:2',
        'allocation_order' => 'integer',
    ];

    // Allocation methods
    public const ALLOCATION_METHODS = [
        'proportional' => 'Proportional to Outstanding Balance',
        'priority' => 'Based on Fee Priority',
        'chronological' => 'Oldest Fees First',
        'manual' => 'Manual Allocation',
        'full_payment' => 'Full Payment to Single Fee',
    ];

    // Relationships
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    public function feesCollect(): BelongsTo
    {
        return $this->belongsTo(FeesCollect::class);
    }

    // Helper Methods
    public function getFormattedAllocatedAmount(): string
    {
        return setting('currency_symbol') . number_format($this->allocated_amount, 2);
    }

    public function getFormattedBalanceChange(): string
    {
        $reduction = $this->fee_balance_before - $this->fee_balance_after;
        return setting('currency_symbol') . number_format($reduction, 2);
    }

    public function isFullPayment(): bool
    {
        return $this->fee_balance_after <= 0;
    }

    public function getAllocationMethodName(): string
    {
        return self::ALLOCATION_METHODS[$this->allocation_method] ?? 'Unknown';
    }

    /**
     * Get allocation efficiency score (how much of the fee was paid)
     */
    public function getEfficiencyScore(): float
    {
        if ($this->fee_total_amount <= 0) {
            return 0;
        }

        return ($this->allocated_amount / $this->fee_total_amount) * 100;
    }

    /**
     * Get allocation priority based on due date and fee type
     */
    public function calculateAllocationPriority(): int
    {
        $priority = 50; // Base priority

        $feesCollect = $this->feesCollect;
        if ($feesCollect) {
            // Increase priority for overdue fees
            if ($feesCollect->due_date && $feesCollect->due_date->isPast()) {
                $daysOverdue = $feesCollect->due_date->diffInDays(now());
                $priority += min($daysOverdue, 30); // Max +30 for overdue
            }

            // Adjust based on fee type importance
            $feeType = $feesCollect->feeType;
            if ($feeType) {
                switch ($feeType->type_name) {
                    case 'Tuition':
                        $priority += 20;
                        break;
                    case 'Examination':
                        $priority += 15;
                        break;
                    case 'Library':
                        $priority += 5;
                        break;
                    // Add other fee types as needed
                }
            }
        }

        return min($priority, 100); // Cap at 100
    }
}