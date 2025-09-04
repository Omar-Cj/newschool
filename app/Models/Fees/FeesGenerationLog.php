<?php

namespace App\Models\Fees;

use App\Models\BaseModel;
use App\Models\StudentInfo\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeesGenerationLog extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'fees_generation_id',
        'student_id',
        'fees_collect_id',
        'status',
        'amount',
        'error_message',
        'fee_details',
    ];

    protected $casts = [
        'fee_details' => 'array',
        'amount' => 'decimal:2',
    ];

    public function feesGeneration(): BelongsTo
    {
        return $this->belongsTo(FeesGeneration::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function feesCollect(): BelongsTo
    {
        return $this->belongsTo(FeesCollect::class);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeSkipped($query)
    {
        return $query->where('status', 'skipped');
    }

    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isSkipped(): bool
    {
        return $this->status === 'skipped';
    }
}