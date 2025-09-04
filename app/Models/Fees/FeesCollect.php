<?php

namespace App\Models\Fees;

use App\Models\BaseModel;
use App\Models\StudentInfo\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FeesCollect extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'due_date' => 'date',
        'late_fee_applied' => 'decimal:2',
        'discount_applied' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feesGeneration(): BelongsTo
    {
        return $this->belongsTo(FeesGeneration::class, 'generation_batch_id', 'batch_id');
    }

    public function feesGenerationLog(): HasOne
    {
        return $this->hasOne(FeesGenerationLog::class);
    }

    public function scopeBulkGenerated($query)
    {
        return $query->where('generation_method', 'bulk');
    }

    public function scopeManuallyCreated($query)
    {
        return $query->where('generation_method', 'manual');
    }

    public function scopeAutomated($query)
    {
        return $query->where('generation_method', 'automated');
    }

    public function scopeWithBatchId($query, string $batchId)
    {
        return $query->where('generation_batch_id', $batchId);
    }

    public function isBulkGenerated(): bool
    {
        return $this->generation_method === 'bulk';
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !$this->isPaid();
    }

    public function isPaid(): bool
    {
        // This would need to be implemented based on your payment tracking logic
        return false; // Placeholder
    }
}
