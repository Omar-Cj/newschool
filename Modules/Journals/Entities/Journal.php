<?php

namespace Modules\Journals\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\CashTransfer;
use App\Models\Fees\PaymentTransaction;
use Modules\MainApp\Entities\School;
use App\Models\Fees\FeesCollect;
use Modules\MultiBranch\Entities\Branch;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'branch',
        'branch_id',
        'description',
        'school_id',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    protected $appends = [
        'total_collected',
        'receipt_cash',
        'deposit_amount',
        'transferred_amount',
        'progress_percentage',
        'remaining_balance',
    ];

    /**
     * Get the school that owns the journal
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the user who created the journal
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the branch that owns the journal
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get all fee collections for this journal
     */
    public function feesCollects(): HasMany
    {
        return $this->hasMany(FeesCollect::class);
    }

    /**
     * Get all audit logs for this journal
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(JournalAuditLog::class)->latest('performed_at');
    }

    /**
     * Get all payment transactions for this journal
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Get all cash transfers for this journal
     */
    public function cashTransfers(): HasMany
    {
        return $this->hasMany(CashTransfer::class);
    }

    /**
     * Get only approved cash transfers
     */
    public function approvedTransfers(): HasMany
    {
        return $this->hasMany(CashTransfer::class)->where('status', 'approved');
    }

    /**
     * Scope to get only active journals
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get journals for a specific school
     */
    public function scopeForSchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Scope to search journals by name or branch
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('branch', 'LIKE', "%{$search}%")
              ->orWhereHas('branch', function ($branchQuery) use ($search) {
                  $branchQuery->where('name', 'LIKE', "%{$search}%");
              });
        });
    }

    /**
     * Scope to filter journals by branch_id
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Get formatted display name with branch
     */
    public function getDisplayNameAttribute(): string
    {
        $branchName = is_object($this->branch) ? $this->branch->name : $this->branch;
        return "{$this->name} ({$branchName})";
    }

    /**
     * Check if journal is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get total collected amount from payment transactions
     */
    public function getTotalCollectedAttribute(): float
    {
        return $this->receipt_cash + $this->deposit_amount;
    }

    /**
     * Get receipt cash amount (all payments except deposits)
     * Payment methods: 1=Cash, 2=Stripe, 3=Zaad, 4=Edahab, 5=PayPal
     */
    public function getReceiptCashAttribute(): float
    {
        return Cache::tags(['journal_totals', "journal_{$this->id}"])
            ->remember("journal_receipt_cash_{$this->id}", 3600, function () {
                return $this->paymentTransactions()
                    ->where('payment_method', '!=', 6)
                    ->sum('amount') ?? 0;
            });
    }

    /**
     * Get deposit amount (payment_method = 6)
     */
    public function getDepositAmountAttribute(): float
    {
        return Cache::tags(['journal_totals', "journal_{$this->id}"])
            ->remember("journal_deposit_{$this->id}", 3600, function () {
                return $this->paymentTransactions()
                    ->where('payment_method', 6)
                    ->sum('amount') ?? 0;
            });
    }

    /**
     * Get total transferred amount (approved transfers only)
     */
    public function getTransferredAmountAttribute(): float
    {
        return Cache::tags(['journal_transfers', "journal_{$this->id}"])
            ->remember("journal_transferred_{$this->id}", 3600, function () {
                return $this->approvedTransfers()->sum('amount') ?? 0;
            });
    }

    /**
     * Get transfer progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        $totalCollected = $this->total_collected;

        if ($totalCollected == 0) {
            return 0;
        }

        $transferred = $this->transferred_amount;
        return min(round(($transferred / $totalCollected) * 100, 2), 100);
    }

    /**
     * Get remaining balance to be transferred
     */
    public function getRemainingBalanceAttribute(): float
    {
        return max($this->total_collected - $this->transferred_amount, 0);
    }

    /**
     * Check if journal is fully transferred
     */
    public function isFullyTransferred(): bool
    {
        return $this->progress_percentage >= 100;
    }

    /**
     * Check if journal can be closed (inactive)
     */
    public function canBeClosed(): bool
    {
        return $this->status === 'active' && $this->isFullyTransferred();
    }

    /**
     * Get payment method breakdown
     */
    public function getPaymentMethodBreakdown(): array
    {
        return Cache::tags(['journal_breakdown', "journal_{$this->id}"])
            ->remember("journal_breakdown_{$this->id}", 3600, function () {
                $breakdown = $this->paymentTransactions()
                    ->select('payment_method', DB::raw('SUM(amount) as total'))
                    ->groupBy('payment_method')
                    ->pluck('total', 'payment_method')
                    ->toArray();

                // Convert payment method integers to names
                $result = [];
                foreach ($breakdown as $method => $amount) {
                    $methodName = match((int)$method) {
                        1 => 'Cash',
                        2 => 'Stripe',
                        3 => 'Zaad',
                        4 => 'Edahab',
                        5 => 'PayPal',
                        6 => 'Deposit',
                        default => 'Unknown'
                    };
                    $result[$methodName] = (float)$amount;
                }

                return $result;
            });
    }

    /**
     * Get receipt cash collected by specific user
     * (All payments except deposits - payment_method != 6)
     *
     * @param int $userId User ID to filter by
     * @return float Total receipt cash collected by user
     */
    public function getUserReceiptCash(int $userId): float
    {
        return $this->paymentTransactions()
            ->where('payment_method', '!=', 6)
            ->where('collected_by', $userId)
            ->sum('amount') ?? 0;
    }

    /**
     * Get deposit amount collected by specific user
     * (Only deposit payments - payment_method = 6)
     *
     * @param int $userId User ID to filter by
     * @return float Total deposit amount collected by user
     */
    public function getUserDepositAmount(int $userId): float
    {
        return $this->paymentTransactions()
            ->where('payment_method', 6)
            ->where('collected_by', $userId)
            ->sum('amount') ?? 0;
    }

    /**
     * Get total collected by specific user (receipt cash + deposits)
     *
     * @param int $userId User ID to filter by
     * @return float Total amount collected by user
     */
    public function getUserTotalCollected(int $userId): float
    {
        return $this->paymentTransactions()
            ->where('collected_by', $userId)
            ->sum('amount') ?? 0;
    }

    /**
     * Get user's remaining balance (their collections minus their approved transfers)
     * This represents how much the user can still transfer from this journal
     *
     * @param int $userId User ID to calculate balance for
     * @return float Remaining balance available for transfer by user
     */
    public function getUserRemainingBalance(int $userId): float
    {
        // Calculate total collected by this user
        $collected = $this->getUserTotalCollected($userId);

        // Sum approved transfers made by this user from this journal
        $transferred = $this->approvedTransfers()
            ->where('transferred_by', $userId)
            ->sum('amount') ?? 0;

        // Remaining = Collected - Already Transferred
        return max($collected - $transferred, 0);
    }

    /**
     * Get user's transfer progress percentage
     * Shows what percentage of user's collections has been transferred
     *
     * @param int $userId User ID to calculate progress for
     * @return float Progress percentage (0-100)
     */
    public function getUserTransferProgress(int $userId): float
    {
        $collected = $this->getUserTotalCollected($userId);

        if ($collected == 0) {
            return 0;
        }

        $transferred = $this->approvedTransfers()
            ->where('transferred_by', $userId)
            ->sum('amount') ?? 0;

        return min(round(($transferred / $collected) * 100, 2), 100);
    }

    /**
     * Clear all cache for this journal
     */
    public function clearCache(): void
    {
        Cache::tags([
            'journal_totals',
            'journal_transfers',
            'journal_breakdown',
            "journal_{$this->id}"
        ])->flush();
    }

    protected static function newFactory()
    {
        return \Modules\Journals\Database\factories\JournalFactory::new();
    }
}