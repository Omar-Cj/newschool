<?php

namespace App\Models\Fees;

use App\Models\BaseModel;
use App\Models\StudentInfo\Student;
use App\Models\StudentService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeesCollect extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'fine_amount' => 'decimal:2',
        'late_fee_applied' => 'decimal:2',
        'discount_applied' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'billing_year' => 'integer',
        'billing_month' => 'integer',
        'total_paid' => 'decimal:2',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feeType(): BelongsTo
    {
        return $this->belongsTo(FeesType::class, 'fee_type_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Session::class, 'academic_year_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Academic\Session::class, 'session_id');
    }

    public function feesGeneration(): BelongsTo
    {
        return $this->belongsTo(FeesGeneration::class, 'generation_batch_id', 'batch_id');
    }

    public function feesGenerationLog(): HasOne
    {
        return $this->hasOne(FeesGenerationLog::class);
    }

    // Legacy relationship - maintained for backward compatibility
    public function feesAssignChildren(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Fees\FeesAssignChildren::class, 'fees_assign_children_id');
    }

    public function collectBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'fees_collect_by');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(\Modules\Journals\Entities\Journal::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // Get associated student service if exists
    public function studentService(): ?StudentService
    {
        if ($this->fee_type_id && $this->student_id && $this->academic_year_id) {
            return StudentService::where('student_id', $this->student_id)
                ->where('fee_type_id', $this->fee_type_id)
                ->where('academic_year_id', $this->academic_year_id)
                ->first();
        }
        
        return null;
    }

    // Scopes for generation methods
    public function scopeBulkGenerated($query)
    {
        return $query->where('generation_method', 'bulk');
    }

    public function scopeServiceBased($query)
    {
        return $query->where('generation_method', 'service_based');
    }

    public function scopeManuallyCreated($query)
    {
        return $query->where('generation_method', 'manual');
    }

    public function scopeAutomated($query)
    {
        return $query->where('generation_method', 'automated');
    }

    public function scopeLegacy($query)
    {
        return $query->where('generation_method', 'legacy');
    }

    // Scopes for new structure
    public function scopeByFeeType($query, $feeTypeId)
    {
        return $query->where('fee_type_id', $feeTypeId);
    }

    public function scopeByAcademicYear($query, $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->whereHas('feeType', function($q) use ($category) {
            $q->where('category', $category);
        });
    }

    public function scopeWithBatchId($query, string $batchId)
    {
        return $query->where('generation_batch_id', $batchId);
    }

    // Billing period scopes
    public function scopeByBillingPeriod($query, string $billingPeriod)
    {
        return $query->where('billing_period', $billingPeriod);
    }

    public function scopeByBillingYear($query, int $year)
    {
        return $query->where('billing_year', $year);
    }

    public function scopeByBillingMonth($query, int $month)
    {
        return $query->where('billing_month', $month);
    }

    public function scopeCurrentMonth($query)
    {
        $now = now();
        return $query->where('billing_year', $now->year)
                     ->where('billing_month', $now->month);
    }

    public function scopeCurrentYear($query)
    {
        return $query->where('billing_year', now()->year);
    }

    public function scopeBillingPeriodRange($query, string $startPeriod, string $endPeriod)
    {
        return $query->whereBetween('billing_period', [$startPeriod, $endPeriod]);
    }

    public function scopeWithBillingPeriod($query)
    {
        return $query->whereNotNull('billing_period');
    }

    public function scopeWithoutBillingPeriod($query)
    {
        return $query->whereNull('billing_period');
    }

    public function scopePaid($query)
    {
        return $query->whereNotNull('payment_method');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereNull('payment_method');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->whereNull('payment_method');
    }

    public function scopeDueWithin($query, int $days)
    {
        return $query->where('due_date', '<=', now()->addDays($days))
                     ->whereNull('payment_method');
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForActiveBranch($query)
    {
        return $query->whereHas('branch', function($q) {
            $q->where('status', \App\Enums\Status::ACTIVE);
        });
    }

    // Status check methods
    public function isBulkGenerated(): bool
    {
        return in_array($this->generation_method, ['bulk', 'service_based']);
    }

    public function isServiceBased(): bool
    {
        return $this->generation_method === 'service_based';
    }

    public function isLegacyRecord(): bool
    {
        return $this->generation_method === 'legacy' || $this->generation_method === null;
    }

    public function hasNewStructure(): bool
    {
        return $this->fee_type_id !== null;
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->isPaid();
    }

    public function isPaid(): bool
    {
        // Check both old and new payment tracking methods
        return $this->payment_method !== null || $this->payment_status === 'paid' || $this->total_paid >= $this->amount;
    }

    public function isPartiallyPaid(): bool
    {
        return $this->payment_status === 'partial' || ($this->total_paid > 0 && $this->total_paid < $this->amount);
    }

    public function isUnpaid(): bool
    {
        return $this->payment_status === 'unpaid' || ($this->total_paid == 0 && $this->payment_method === null);
    }
    
    public function isGenerated(): bool
    {
        return $this->generation_method !== null;
    }
    
    public function isPending(): bool
    {
        return !$this->isPaid() && $this->isGenerated();
    }

    public function hasDiscount(): bool
    {
        return $this->discount_applied > 0;
    }

    public function hasLateFee(): bool
    {
        return $this->late_fee_applied > 0;
    }

    // Helper methods for fee information
    public function getFeeName(): string
    {
        if ($this->hasNewStructure() && $this->feeType) {
            return $this->feeType->name;
        }

        // Fallback to legacy structure
        if ($this->feesAssignChildren && $this->feesAssignChildren->feesMaster) {
            return $this->feesAssignChildren->feesMaster->name ?? 'Fee';
        }

        return 'Unknown Fee';
    }

    public function getFeeCategory(): string
    {
        if ($this->hasNewStructure() && $this->feeType) {
            return $this->feeType->getFormattedCategory();
        }

        // Default category for legacy records
        return 'Academic';
    }

    public function getTotalAmount(): float
    {
        return $this->amount + $this->fine_amount + $this->late_fee_applied;
    }

    public function getNetAmount(): float
    {
        return $this->getTotalAmount() - $this->discount_applied;
    }

    public function getBalanceAmount(): float
    {
        return max(0, $this->getNetAmount() - $this->total_paid);
    }

    public function getPaidAmount(): float
    {
        return $this->total_paid;
    }

    public function getPaymentPercentage(): float
    {
        $netAmount = $this->getNetAmount();
        if ($netAmount > 0) {
            return ($this->total_paid / $netAmount) * 100;
        }
        return 0;
    }

    public function getDiscountPercentage(): float
    {
        if ($this->amount > 0) {
            return ($this->discount_applied / $this->amount) * 100;
        }
        
        return 0;
    }

    public function getDaysOverdue(): ?int
    {
        if ($this->isOverdue()) {
            return $this->due_date->diffInDays(now());
        }
        
        return null;
    }

    public function getDaysUntilDue(): ?int
    {
        if ($this->due_date && !$this->isPaid()) {
            return now()->diffInDays($this->due_date, false);
        }

        return null;
    }

    // Billing period helper methods
    public function hasBillingPeriod(): bool
    {
        return $this->billing_period !== null;
    }

    public function setBillingPeriodFromDate(\Carbon\Carbon $date): void
    {
        $this->billing_period = $date->format('Y-m');
        $this->billing_year = $date->year;
        $this->billing_month = $date->month;
    }

    public function getBillingPeriodLabel(): string
    {
        if (!$this->hasBillingPeriod()) {
            return 'Unknown Period';
        }

        $date = \Carbon\Carbon::createFromFormat('Y-m', $this->billing_period);
        return $date->format('F Y'); // e.g., "October 2024"
    }

    public function getBillingPeriodShortLabel(): string
    {
        if (!$this->hasBillingPeriod()) {
            return 'Unknown';
        }

        $date = \Carbon\Carbon::createFromFormat('Y-m', $this->billing_period);
        return $date->format('M Y'); // e.g., "Oct 2024"
    }

    public function isCurrentMonth(): bool
    {
        if (!$this->hasBillingPeriod()) {
            return false;
        }

        $now = now();
        return $this->billing_year === $now->year && $this->billing_month === $now->month;
    }

    public function isPreviousMonth(): bool
    {
        if (!$this->hasBillingPeriod()) {
            return false;
        }

        $currentMonth = now()->format('Y-m');
        return $this->billing_period < $currentMonth;
    }

    public function isFutureMonth(): bool
    {
        if (!$this->hasBillingPeriod()) {
            return false;
        }

        $currentMonth = now()->format('Y-m');
        return $this->billing_period > $currentMonth;
    }

    public static function inferBillingPeriodFromDueDate(\Carbon\Carbon $dueDate): string
    {
        // Infer billing period from due date
        // Most fees are due in the month they're for, or the following month
        $billingMonth = $dueDate->copy();

        // If due date is after 15th, assume it's for the same month
        // If due date is before 15th, assume it's for the previous month
        if ($dueDate->day <= 15) {
            $billingMonth->subMonth();
        }

        return $billingMonth->format('Y-m');
    }

    // Accessor methods for better formatting
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getFormattedTotalAmountAttribute(): string
    {
        return '$' . number_format($this->getTotalAmount(), 2);
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return '$' . number_format($this->getNetAmount(), 2);
    }

    public function getFormattedDiscountAttribute(): string
    {
        return '$' . number_format($this->discount_applied, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->isPaid()) {
            return 'Paid';
        }
        
        if ($this->isOverdue()) {
            return 'Overdue';
        }
        
        if ($this->isDueSoon()) {
            return 'Due Soon';
        }
        
        return 'Pending';
    }

    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->status_badge) {
            'Paid' => 'success',
            'Overdue' => 'danger',
            'Due Soon' => 'warning',
            default => 'info'
        };
    }

    private function isDueSoon(int $days = 7): bool
    {
        return $this->due_date &&
               $this->due_date->between(now(), now()->addDays($days)) &&
               !$this->isPaid();
    }

    // Branch-related helper methods
    public function getBranchName(): string
    {
        return $this->branch?->name ?? 'Unknown Branch';
    }

    public function isBranchActive(): bool
    {
        return $this->branch?->isActive() ?? false;
    }

    public function getBranchInfo(): array
    {
        return [
            'id' => $this->branch_id,
            'name' => $this->getBranchName(),
            'is_active' => $this->isBranchActive(),
            'email' => $this->branch?->email,
            'phone' => $this->branch?->phone,
            'address' => $this->branch?->address,
        ];
    }

    // Payment status management
    public function updatePaymentStatus(): void
    {
        $netAmount = $this->getNetAmount();

        if ($this->total_paid <= 0) {
            $this->payment_status = 'unpaid';
        } elseif ($this->total_paid >= $netAmount) {
            $this->payment_status = 'paid';
            // Set legacy payment_method for backward compatibility if not set
            if ($this->payment_method === null) {
                $this->payment_method = 1; // Default to cash
            }
        } else {
            $this->payment_status = 'partial';
        }
    }

    public function recalculateTotalPaid(): void
    {
        $this->total_paid = $this->paymentTransactions()->sum('amount');
        $this->updatePaymentStatus();
        $this->save();
    }

    // Migration helper methods
    public function migrateToNewStructure(): bool
    {
        if ($this->hasNewStructure()) {
            return true; // Already migrated
        }

        // Try to get fee type from legacy structure
        if ($this->feesAssignChildren && $this->feesAssignChildren->feesMaster) {
            $this->update([
                'fee_type_id' => $this->feesAssignChildren->feesMaster->fees_type_id,
                'academic_year_id' => $this->session_id,
                'generation_method' => 'legacy'
            ]);
            
            return true;
        }

        return false;
    }

    public function getAuditInformation(): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'student_name' => $this->student->full_name ?? 'Unknown',
            'branch_id' => $this->branch_id,
            'branch_name' => $this->getBranchName(),
            'fee_name' => $this->getFeeName(),
            'fee_category' => $this->getFeeCategory(),
            'amount' => $this->amount,
            'discount' => $this->discount_applied,
            'net_amount' => $this->getNetAmount(),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'payment_status' => $this->status_badge,
            'generation_method' => $this->generation_method,
            'is_new_structure' => $this->hasNewStructure(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString()
        ];
    }
}
