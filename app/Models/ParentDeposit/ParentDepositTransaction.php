<?php

namespace App\Models\ParentDeposit;

use App\Models\BaseModel;
use App\Models\User;
use App\Models\Fees\FeesCollect;
use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\ParentGuardian;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentDepositTransaction extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'parent_deposit_id',
        'parent_guardian_id',
        'student_id',
        'transaction_type',
        'amount',
        'balance_before',
        'balance_after',
        'transaction_date',
        'description',
        'fees_collect_id',
        'reference_number',
        'created_by',
        'branch_id',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    // Relationships
    public function parentDeposit(): BelongsTo
    {
        return $this->belongsTo(ParentDeposit::class);
    }

    public function parentGuardian(): BelongsTo
    {
        return $this->belongsTo(ParentGuardian::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feesCollect(): BelongsTo
    {
        return $this->belongsTo(FeesCollect::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
    }

    // Scopes
    public function scopeDeposits($query)
    {
        return $query->where('transaction_type', 'deposit');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('transaction_type', 'withdrawal');
    }

    public function scopeAllocations($query)
    {
        return $query->where('transaction_type', 'allocation');
    }

    public function scopeRefunds($query)
    {
        return $query->where('transaction_type', 'refund');
    }

    public function scopeByParent($query, $parentGuardianId)
    {
        return $query->where('parent_guardian_id', $parentGuardianId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('transaction_date', '>=', now()->subDays($days));
    }

    // Helper methods
    public function getFormattedAmount(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getFormattedBalanceBefore(): string
    {
        return '$' . number_format($this->balance_before, 2);
    }

    public function getFormattedBalanceAfter(): string
    {
        return '$' . number_format($this->balance_after, 2);
    }

    public function getTransactionTypeLabel(): string
    {
        return match($this->transaction_type) {
            'deposit' => 'Deposit',
            'withdrawal' => 'Withdrawal',
            'allocation' => 'Fee Allocation',
            'refund' => 'Refund',
            default => 'Unknown'
        };
    }

    public function getParentName(): string
    {
        return $this->parentGuardian?->user?->name ?? 'Unknown Parent';
    }

    public function getStudentName(): string
    {
        return $this->student?->full_name ?? 'General';
    }

    public function getCreatorName(): string
    {
        return $this->creator?->name ?? 'System';
    }

    public function getBranchName(): string
    {
        return $this->branch?->name ?? 'Unknown Branch';
    }

    public function isDeposit(): bool
    {
        return $this->transaction_type === 'deposit';
    }

    public function isWithdrawal(): bool
    {
        return $this->transaction_type === 'withdrawal';
    }

    public function isAllocation(): bool
    {
        return $this->transaction_type === 'allocation';
    }

    public function isRefund(): bool
    {
        return $this->transaction_type === 'refund';
    }

    public function isPositive(): bool
    {
        return in_array($this->transaction_type, ['deposit', 'refund']);
    }

    public function isNegative(): bool
    {
        return in_array($this->transaction_type, ['withdrawal', 'allocation']);
    }

    public function getBalanceChange(): float
    {
        return $this->balance_after - $this->balance_before;
    }

    public function getFormattedBalanceChange(): string
    {
        $change = $this->getBalanceChange();
        $prefix = $change >= 0 ? '+' : '';
        return $prefix . '$' . number_format($change, 2);
    }

    // Generate unique reference number
    public static function generateReferenceNumber(): string
    {
        $prefix = 'TXN-' . date('Y') . '-';
        $lastTransaction = static::where('reference_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->reference_number, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 8, '0', STR_PAD_LEFT);
    }

    // Boot method to auto-generate reference number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->reference_number)) {
                $transaction->reference_number = static::generateReferenceNumber();
            }
        });
    }

    // Transaction type color for display
    public function getTypeColorAttribute(): string
    {
        return match($this->transaction_type) {
            'deposit' => 'success',
            'withdrawal' => 'warning',
            'allocation' => 'info',
            'refund' => 'primary',
            default => 'secondary'
        };
    }

    // Transaction type icon for display
    public function getTypeIconAttribute(): string
    {
        return match($this->transaction_type) {
            'deposit' => 'fa-arrow-down',
            'withdrawal' => 'fa-arrow-up',
            'allocation' => 'fa-exchange-alt',
            'refund' => 'fa-undo',
            default => 'fa-question'
        };
    }

    // Audit information
    public function getAuditInformation(): array
    {
        return [
            'id' => $this->id,
            'reference_number' => $this->reference_number,
            'parent_name' => $this->getParentName(),
            'student_name' => $this->getStudentName(),
            'transaction_type' => $this->getTransactionTypeLabel(),
            'amount' => $this->amount,
            'balance_before' => $this->balance_before,
            'balance_after' => $this->balance_after,
            'balance_change' => $this->getBalanceChange(),
            'transaction_date' => $this->transaction_date->format('Y-m-d H:i:s'),
            'description' => $this->description,
            'created_by' => $this->getCreatorName(),
            'branch' => $this->getBranchName(),
            'fees_collect_id' => $this->fees_collect_id,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}