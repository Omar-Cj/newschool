<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\Fees\FeesType;
use App\Models\StudentInfo\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentService extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fee_type_id',
        'academic_year_id',
        'amount',
        'due_date',
        'discount_type',
        'discount_value',
        'final_amount',
        'subscription_date',
        'is_active',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'due_date' => 'date',
        'subscription_date' => 'datetime',
        'is_active' => 'boolean'
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeForAcademicYear($query, $yearId)
    {
        return $query->where('academic_year_id', $yearId);
    }

    public function scopeByFeeType($query, $feeTypeId)
    {
        return $query->where('fee_type_id', $feeTypeId);
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeDueWithin($query, int $days)
    {
        return $query->where('due_date', '<=', now()->addDays($days));
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
    }

    public function scopeWithDiscount($query)
    {
        return $query->where('discount_type', '!=', 'none');
    }

    public function scopeWithoutDiscount($query)
    {
        return $query->where('discount_type', 'none');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->whereHas('feeType', function($q) use ($category) {
            $q->where('category', $category);
        });
    }

    // Discount calculation methods
    public function calculateFinalAmount(): float
    {
        return match($this->discount_type) {
            'percentage' => $this->amount * (1 - ($this->discount_value / 100)),
            'fixed' => max(0, $this->amount - $this->discount_value),
            'override' => $this->discount_value,
            default => $this->amount
        };
    }

    public function applyDiscount(string $type, float $value, string $notes = null): void
    {
        $finalAmount = match($type) {
            'percentage' => $this->amount * (1 - ($value / 100)),
            'fixed' => max(0, $this->amount - $value),
            'override' => $value,
            default => $this->amount
        };

        $this->update([
            'discount_type' => $type,
            'discount_value' => $value,
            'final_amount' => $finalAmount,
            'notes' => $notes ?? $this->notes,
            'updated_by' => auth()->id()
        ]);
    }

    public function removeDiscount(): void
    {
        $this->update([
            'discount_type' => 'none',
            'discount_value' => 0,
            'final_amount' => $this->amount,
            'notes' => $this->notes ? $this->notes . ' | Discount removed on ' . now()->format('Y-m-d') : 'Discount removed',
            'updated_by' => auth()->id()
        ]);
    }

    // Status check methods
    public function hasDiscount(): bool
    {
        return $this->discount_type !== 'none' && $this->discount_value > 0;
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast();
    }

    public function isDueSoon(int $days = 7): bool
    {
        return $this->due_date && $this->due_date->between(now(), now()->addDays($days));
    }

    // Helper methods for discount information
    public function getDiscountSummary(): string
    {
        return match($this->discount_type) {
            'percentage' => "{$this->discount_value}% discount",
            'fixed' => "$" . number_format($this->discount_value, 2) . " discount",
            'override' => "Amount override to $" . number_format($this->discount_value, 2),
            default => "No discount"
        };
    }

    public function getDiscountAmount(): float
    {
        return match($this->discount_type) {
            'percentage' => $this->amount * ($this->discount_value / 100),
            'fixed' => min($this->discount_value, $this->amount),
            'override' => max(0, $this->amount - $this->discount_value),
            default => 0
        };
    }

    public function getSavingsPercentage(): float
    {
        if ($this->amount <= 0) {
            return 0;
        }

        return ($this->getDiscountAmount() / $this->amount) * 100;
    }

    // Audit and history methods
    public function getStatusHistory(): array
    {
        return [
            'created' => [
                'date' => $this->created_at,
                'by' => $this->creator?->name,
                'action' => 'Service subscription created'
            ],
            'last_updated' => [
                'date' => $this->updated_at,
                'by' => $this->updater?->name,
                'action' => 'Service subscription updated'
            ],
            'subscription' => [
                'date' => $this->subscription_date,
                'action' => 'Service activated for student'
            ]
        ];
    }

    public function generateAuditLog(): array
    {
        return [
            'student_id' => $this->student_id,
            'student_name' => $this->student->full_name ?? 'Unknown',
            'service_name' => $this->feeType->name ?? 'Unknown Service',
            'academic_year' => $this->academicYear->name ?? 'Unknown Year',
            'original_amount' => $this->amount,
            'discount_applied' => $this->getDiscountSummary(),
            'final_amount' => $this->final_amount,
            'savings' => $this->getDiscountAmount(),
            'status' => $this->is_active ? 'Active' : 'Inactive',
            'due_date' => $this->due_date?->format('Y-m-d'),
            'overdue' => $this->isOverdue(),
            'notes' => $this->notes
        ];
    }

    // Bulk operations helpers
    public function activate(): bool
    {
        return $this->update([
            'is_active' => true,
            'updated_by' => auth()->id()
        ]);
    }

    public function deactivate(string $reason = null): bool
    {
        return $this->update([
            'is_active' => false,
            'notes' => $reason ? ($this->notes ? $this->notes . ' | ' . $reason : $reason) : $this->notes,
            'updated_by' => auth()->id()
        ]);
    }

    public function updateDueDate(Carbon $newDueDate, string $reason = null): bool
    {
        return $this->update([
            'due_date' => $newDueDate,
            'notes' => $reason ? ($this->notes ? $this->notes . ' | Due date changed: ' . $reason : 'Due date changed: ' . $reason) : $this->notes,
            'updated_by' => auth()->id()
        ]);
    }

    // Accessors for better formatting
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getFormattedFinalAmountAttribute(): string
    {
        return '$' . number_format($this->final_amount, 2);
    }

    public function getFormattedDiscountAmountAttribute(): string
    {
        return '$' . number_format($this->getDiscountAmount(), 2);
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        return $this->due_date ? now()->diffInDays($this->due_date, false) : null;
    }
}