<?php

namespace Modules\MultiBranch\Entities;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\MultiBranch\Database\factories\BranchFactory;
use App\Enums\Status;

/**
 * Branch Model - School-Isolated Branch Management
 *
 * IMPORTANT: Extends BaseModel to enable automatic school_id filtering via SchoolScope.
 * Each branch belongs to a single school and all branch data is automatically isolated.
 *
 * School Context Behavior:
 * - School Users (school_id NOT NULL): See ONLY their school's branches
 * - System Admin (school_id NULL): See branches from ALL schools
 *
 * After migration 2025_11_09_000001, the branches table will have school_id column
 * with foreign key constraint to schools table for proper multi-tenant isolation.
 */
class Branch extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'school_id',  // Added for multi-tenant isolation
        'name',
        'phone',
        'email',
        'address',
        'lat',
        'long',
        'status',
        'country_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'status' => 'string',
    ];
    
    /**
     * Get all journals for this branch
     */
    public function journals(): HasMany
    {
        return $this->hasMany(\Modules\Journals\Entities\Journal::class);
    }

    /**
     * Get all terms for this branch
     */
    public function terms(): HasMany
    {
        return $this->hasMany(\App\Models\Examination\Term::class);
    }

    /**
     * Get active terms for this branch
     */
    public function activeTerms(): HasMany
    {
        return $this->hasMany(\App\Models\Examination\Term::class)
            ->where('status', 'active');
    }

    /**
     * Get the school this branch belongs to
     */
    public function school()
    {
        return $this->belongsTo(\Modules\MainApp\Entities\School::class);
    }

    /**
     * Scope to get only active branches
     */
    public function scopeActive($query)
    {
        return $query->where('status', Status::ACTIVE);
    }

    /**
     * Check if branch is active
     */
    public function isActive(): bool
    {
        return $this->status === Status::ACTIVE;
    }

    protected static function newFactory(): BranchFactory
    {
        //return BranchFactory::new();
    }
}
