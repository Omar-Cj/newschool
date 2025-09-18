<?php

namespace Modules\MultiBranch\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\MultiBranch\Database\factories\BranchFactory;
use App\Enums\Status;

class Branch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
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
