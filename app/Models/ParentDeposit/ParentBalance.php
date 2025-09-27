<?php

namespace App\Models\ParentDeposit;

use App\Models\BaseModel;
use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\ParentGuardian;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParentBalance extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'parent_guardian_id',
        'student_id',
        'available_balance',
        'reserved_balance',
        'total_deposits',
        'total_withdrawals',
        'last_transaction_date',
        'academic_year_id',
        'branch_id',
    ];

    protected $casts = [
        'available_balance' => 'decimal:2',
        'reserved_balance' => 'decimal:2',
        'total_deposits' => 'decimal:2',
        'total_withdrawals' => 'decimal:2',
        'last_transaction_date' => 'datetime',
    ];

    // Relationships
    public function parentGuardian(): BelongsTo
    {
        return $this->belongsTo(ParentGuardian::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Session::class, 'academic_year_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ParentDepositTransaction::class, 'parent_guardian_id', 'parent_guardian_id')
                    ->when($this->student_id, function($query) {
                        return $query->where('student_id', $this->student_id);
                    });
    }

    // Scopes
    public function scopeByParent($query, $parentGuardianId)
    {
        return $query->where('parent_guardian_id', $parentGuardianId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeGeneral($query)
    {
        return $query->whereNull('student_id');
    }

    public function scopeStudentSpecific($query)
    {
        return $query->whereNotNull('student_id');
    }

    public function scopeCurrentAcademicYear($query)
    {
        return $query->where('academic_year_id', activeAcademicYear());
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branch_id', activeBranch());
    }

    public function scopeWithPositiveBalance($query)
    {
        return $query->where('available_balance', '>', 0);
    }

    // Helper methods
    public function getTotalBalance(): float
    {
        return $this->available_balance + $this->reserved_balance;
    }

    public function getNetBalance(): float
    {
        return $this->total_deposits - $this->total_withdrawals;
    }

    public function getFormattedAvailableBalance(): string
    {
        return '$' . number_format($this->available_balance, 2);
    }

    public function getFormattedReservedBalance(): string
    {
        return '$' . number_format($this->reserved_balance, 2);
    }

    public function getFormattedTotalBalance(): string
    {
        return '$' . number_format($this->getTotalBalance(), 2);
    }

    public function getFormattedTotalDeposits(): string
    {
        return '$' . number_format($this->total_deposits, 2);
    }

    public function getFormattedTotalWithdrawals(): string
    {
        return '$' . number_format($this->total_withdrawals, 2);
    }

    public function getFormattedNetBalance(): string
    {
        return '$' . number_format($this->getNetBalance(), 2);
    }

    public function getParentName(): string
    {
        return $this->parentGuardian?->user?->name ?? 'Unknown Parent';
    }

    public function getStudentName(): string
    {
        return $this->student?->full_name ?? 'General Account';
    }

    public function getBranchName(): string
    {
        return $this->branch?->name ?? 'Unknown Branch';
    }

    public function hasAvailableBalance(): bool
    {
        return $this->available_balance > 0;
    }

    public function hasReservedBalance(): bool
    {
        return $this->reserved_balance > 0;
    }

    public function canWithdraw(float $amount): bool
    {
        return $this->available_balance >= $amount;
    }

    public function canReserve(float $amount): bool
    {
        return $this->available_balance >= $amount;
    }

    public function isGeneralAccount(): bool
    {
        return $this->student_id === null;
    }

    public function isStudentSpecific(): bool
    {
        return $this->student_id !== null;
    }

    // Balance operations
    public function addDeposit(float $amount): void
    {
        $this->available_balance += $amount;
        $this->total_deposits += $amount;
        $this->last_transaction_date = now();
        $this->save();
    }

    public function deductWithdrawal(float $amount): bool
    {
        if (!$this->canWithdraw($amount)) {
            return false;
        }

        $this->available_balance -= $amount;
        $this->total_withdrawals += $amount;
        $this->last_transaction_date = now();
        $this->save();

        return true;
    }

    public function reserveAmount(float $amount): bool
    {
        if (!$this->canReserve($amount)) {
            return false;
        }

        $this->available_balance -= $amount;
        $this->reserved_balance += $amount;
        $this->save();

        return true;
    }

    public function releaseReserved(float $amount): bool
    {
        if ($this->reserved_balance < $amount) {
            return false;
        }

        $this->reserved_balance -= $amount;
        $this->available_balance += $amount;
        $this->save();

        return true;
    }

    public function allocateReserved(float $amount): bool
    {
        if ($this->reserved_balance < $amount) {
            return false;
        }

        $this->reserved_balance -= $amount;
        $this->total_withdrawals += $amount;
        $this->last_transaction_date = now();
        $this->save();

        return true;
    }

    // Get balance utilization percentage
    public function getUtilizationPercentage(): float
    {
        $totalDeposits = $this->total_deposits;
        if ($totalDeposits == 0) {
            return 0;
        }

        return ($this->total_withdrawals / $totalDeposits) * 100;
    }

    // Get balance health status
    public function getBalanceHealthStatus(): string
    {
        $utilization = $this->getUtilizationPercentage();

        if ($utilization < 25) {
            return 'excellent';
        } elseif ($utilization < 50) {
            return 'good';
        } elseif ($utilization < 75) {
            return 'moderate';
        } else {
            return 'low';
        }
    }

    public function getBalanceHealthColor(): string
    {
        return match($this->getBalanceHealthStatus()) {
            'excellent' => 'success',
            'good' => 'info',
            'moderate' => 'warning',
            'low' => 'danger',
            default => 'secondary'
        };
    }

    // Summary information
    public function getSummary(): array
    {
        return [
            'available_balance' => $this->available_balance,
            'reserved_balance' => $this->reserved_balance,
            'total_balance' => $this->getTotalBalance(),
            'total_deposits' => $this->total_deposits,
            'total_withdrawals' => $this->total_withdrawals,
            'net_balance' => $this->getNetBalance(),
            'utilization_percentage' => $this->getUtilizationPercentage(),
            'health_status' => $this->getBalanceHealthStatus(),
            'last_transaction_date' => $this->last_transaction_date?->format('Y-m-d H:i:s'),
        ];
    }

    // Audit information
    public function getAuditInformation(): array
    {
        return [
            'id' => $this->id,
            'parent_name' => $this->getParentName(),
            'student_name' => $this->getStudentName(),
            'available_balance' => $this->available_balance,
            'reserved_balance' => $this->reserved_balance,
            'total_balance' => $this->getTotalBalance(),
            'total_deposits' => $this->total_deposits,
            'total_withdrawals' => $this->total_withdrawals,
            'net_balance' => $this->getNetBalance(),
            'utilization_percentage' => round($this->getUtilizationPercentage(), 2),
            'health_status' => $this->getBalanceHealthStatus(),
            'last_transaction_date' => $this->last_transaction_date?->format('Y-m-d H:i:s'),
            'branch' => $this->getBranchName(),
            'is_student_specific' => $this->isStudentSpecific(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}