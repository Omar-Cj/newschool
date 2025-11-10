<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Modules\MainApp\Entities\Package;

/**
 * PermissionFeature Model
 *
 * Represents individual features linked to permissions that can be assigned
 * to packages for granular access control.
 *
 * @property int $id
 * @property int $permission_id
 * @property int $feature_group_id
 * @property string $name
 * @property string|null $description
 * @property bool $is_premium
 * @property int $position
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class PermissionFeature extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'permission_id',
        'feature_group_id',
        'name',
        'description',
        'is_premium',
        'position',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_premium' => 'boolean',
        'status' => 'integer',
        'position' => 'integer',
        'permission_id' => 'integer',
        'feature_group_id' => 'integer',
    ];

    /**
     * Get the permission that owns this feature.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Get the feature group that this feature belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function featureGroup(): BelongsTo
    {
        return $this->belongsTo(FeatureGroup::class);
    }

    /**
     * Get the packages that have this feature.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(
            Package::class,
            'package_permission_features',
            'permission_feature_id',
            'package_id'
        )->withTimestamps();
    }

    /**
     * Scope a query to only include active features.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    /**
     * Scope a query to only include premium features.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePremium(Builder $query): Builder
    {
        return $query->where('is_premium', true);
    }

    /**
     * Scope a query to filter features by group.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $groupId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByGroup(Builder $query, int $groupId): Builder
    {
        return $query->where('feature_group_id', $groupId);
    }

    /**
     * Scope a query to order features by position.
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
     * Scope a query to filter features by permission.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $permissionId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPermission(Builder $query, int $permissionId): Builder
    {
        return $query->where('permission_id', $permissionId);
    }
}
