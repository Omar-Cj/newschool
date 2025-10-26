<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Journals\Entities\Journal;

class CashTransfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'journal_id',
        'branch_id',
        'amount',
        'notes',
        'transferred_by',
        'approved_by',
        'approved_at',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected $appends = ['payment_method_breakdown'];

    // Relationships
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForJournal($query, int $journalId)
    {
        return $query->where('journal_id', $journalId);
    }

    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Accessors
    public function getPaymentMethodBreakdownAttribute(): array
    {
        if (!$this->journal) {
            return [];
        }

        $breakdown = $this->journal->getPaymentMethodBreakdown();
        $totalJournalAmount = array_sum($breakdown);

        if ($totalJournalAmount == 0) {
            return [];
        }

        // Calculate proportional breakdown for this transfer
        $result = [];
        foreach ($breakdown as $method => $methodAmount) {
            $proportion = $methodAmount / $totalJournalAmount;
            $result[$method] = round($this->amount * $proportion, 2);
        }

        return $result;
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->status === 'approved';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsRejectedAttribute(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmount(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get transfer status label
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => 'Unknown'
        };
    }

    /**
     * Get badge class for status
     */
    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            default => 'badge-secondary'
        };
    }
}
