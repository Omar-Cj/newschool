<?php

namespace App\Models\Fees;

use App\Models\BaseModel;
use App\Models\StudentInfo\Student;
use App\Models\StudentService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        return $this->payment_method !== null;
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
