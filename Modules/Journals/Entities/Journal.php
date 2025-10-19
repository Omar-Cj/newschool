<?php

namespace Modules\Journals\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
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

    protected static function newFactory()
    {
        return \Modules\Journals\Database\factories\JournalFactory::new();
    }
}