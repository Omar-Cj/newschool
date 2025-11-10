<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * FeatureGroup Model
 *
 * Represents a grouping of related permission features for better organization
 * and management in the feature permission system.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon
 * @property int $position
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class FeatureGroup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'position',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'integer',
        'position' => 'integer',
    ];

    /**
     * Get the permission features that belong to this group.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissionFeatures(): HasMany
    {
        return $this->hasMany(PermissionFeature::class, 'feature_group_id');
    }

    /**
     * Scope a query to only include active feature groups.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to order feature groups by position.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $direction
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrdered(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('position', $direction);
    }

    /**
     * Get active feature groups with their features.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActiveWithFeatures(Builder $query): Builder
    {
        return $query->active()
            ->with(['permissionFeatures' => function ($query) {
                $query->where('status', 1)->orderBy('position');
            }])
            ->ordered();
    }
}
