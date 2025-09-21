<?php

namespace App\Models\Fees;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeesGeneration extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'status',
        'total_students',
        'processed_students',
        'successful_students',
        'failed_students',
        'total_amount',
        'filters',
        'notes',
        'started_at',
        'completed_at',
        'created_by',
        'school_id',
        'branch_id',
    ];

    protected $casts = [
        'filters' => 'array',
        'total_amount' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
    }

    // Note: School relationship can be added if School model exists
    // public function school(): BelongsTo
    // {
    //     return $this->belongsTo(School::class);
    // }

    public function logs(): HasMany
    {
        return $this->hasMany(FeesGenerationLog::class);
    }

    public function feesCollects(): HasMany
    {
        return $this->hasMany(FeesCollect::class, 'generation_batch_id', 'batch_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['pending', 'processing']);
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

    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_students == 0) {
            return 0;
        }
        
        return round(($this->processed_students / $this->total_students) * 100, 2);
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->processed_students == 0) {
            return 0;
        }
        
        return round(($this->successful_students / $this->processed_students) * 100, 2);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isInProgress(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
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

    public function validateBranchAccess(): bool
    {
        return $this->branch_id && $this->isBranchActive();
    }
}