<?php

namespace App\Models\ParentDeposit;

use App\Models\BaseModel;
use App\Models\User;
use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\ParentGuardian;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ParentDeposit extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'parent_guardian_id',
        'student_id',
        'amount',
        'deposit_date',
        'payment_method',
        'transaction_reference',
        'deposit_reason',
        'status',
        'collected_by',
        'branch_id',
        'academic_year_id',
        'journal_id',
        'deposit_number',
    ];

    protected $casts = [
        'deposit_date' => 'datetime',
        'amount' => 'decimal:2',
        'payment_method' => 'integer',
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

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Session::class, 'academic_year_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(\Modules\Journals\Entities\Journal::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ParentDepositTransaction::class);
    }

    public function depositTransaction(): HasOne
    {
        return $this->hasOne(ParentDepositTransaction::class)
                    ->where('transaction_type', 'deposit');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByParent($query, $parentGuardianId)
    {
        return $query->where('parent_guardian_id', $parentGuardianId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('deposit_date', [$startDate, $endDate]);
    }

    public function scopeCurrentAcademicYear($query)
    {
        return $query->where('academic_year_id', activeAcademicYear());
    }

    public function scopeCurrentBranch($query)
    {
        return $query->where('branch_id', activeBranch());
    }

    // Helper methods
    public function getPaymentMethodName(): string
    {
        return match($this->payment_method) {
            1 => 'Cash',
            3 => 'Zaad',
            4 => 'Edahab',
            default => 'Unknown'
        };
    }

    public function getFormattedAmount(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getCollectorName(): string
    {
        return $this->collector?->name ?? 'Unknown';
    }

    public function getBranchName(): string
    {
        return $this->branch?->name ?? 'Unknown Branch';
    }

    public function getParentName(): string
    {
        return $this->parentGuardian?->user?->name ?? 'Unknown Parent';
    }

    public function getStudentName(): string
    {
        return $this->student?->full_name ?? 'General Deposit';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isGeneralDeposit(): bool
    {
        return $this->student_id === null;
    }

    public function isStudentSpecific(): bool
    {
        return $this->student_id !== null;
    }

    // Generate unique deposit number
    public static function generateDepositNumber(): string
    {
        $prefix = 'DEP-' . date('Y') . '-';
        $lastDeposit = static::where('deposit_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastDeposit) {
            $lastNumber = (int) substr($lastDeposit->deposit_number, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    // Boot method to auto-generate deposit number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($deposit) {
            if (empty($deposit->deposit_number)) {
                $deposit->deposit_number = static::generateDepositNumber();
            }
        });
    }

    // Status badge for display
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'completed' => 'Completed',
            'pending' => 'Pending',
            'failed' => 'Failed',
            default => 'Unknown'
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            default => 'secondary'
        };
    }

    // Audit information
    public function getAuditInformation(): array
    {
        return [
            'id' => $this->id,
            'deposit_number' => $this->deposit_number,
            'parent_name' => $this->getParentName(),
            'student_name' => $this->getStudentName(),
            'amount' => $this->amount,
            'payment_method' => $this->getPaymentMethodName(),
            'deposit_date' => $this->deposit_date->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'collected_by' => $this->getCollectorName(),
            'branch' => $this->getBranchName(),
            'reason' => $this->deposit_reason,
            'reference' => $this->transaction_reference,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}