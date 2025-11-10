<?php

declare(strict_types=1);

namespace Modules\MainApp\Entities;

use App\Models\Tenant;
use App\Traits\HasFeatureAccess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class School extends Model
{
    use HasFactory, HasFeatureAccess;

    protected $fillable = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cached_features' => 'array',
        'features_cache_expires_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active schools.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', \App\Enums\Status::ACTIVE);
    }

    /**
     * Get the package associated with this school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class, 'package_id', 'id');
    }

    /**
     * Get the tenant associated with this school.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'sub_domain_key', 'id');
    }

    /**
     * Get all allowed features for this school with 24-hour caching.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllowedFeatures(): Collection
    {
        $cacheKey = "school_features_{$this->id}";

        return Cache::remember($cacheKey, now()->addHours(24), function () {
            if (!$this->package) {
                return collect([]);
            }

            return $this->package->getAllowedPermissions();
        });
    }

    /**
     * Check if school has access to a specific feature.
     *
     * @param string $permissionAttribute
     * @return bool
     */
    public function hasFeature(string $permissionAttribute): bool
    {
        return $this->hasFeatureAccess($permissionAttribute);
    }

    /**
     * Clear feature cache for this school.
     *
     * @return void
     */
    public function clearFeatureCache(): void
    {
        Cache::forget("school_features_{$this->id}");
        Cache::forget("school_features_by_group_{$this->id}");
        Cache::forget($this->getFeatureCacheKey());
        Cache::forget($this->getFeatureGroupsCacheKey());
    }

    /**
     * Get features organized by feature groups.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFeaturesByGroup(): Collection
    {
        $cacheKey = "school_features_by_group_{$this->id}";

        return Cache::remember($cacheKey, now()->addHours(24), function () {
            return $this->getFeatureGroups();
        });
    }

    /**
     * Refresh features cache.
     *
     * @return void
     */
    public function refreshFeatureCache(): void
    {
        $this->clearFeatureCache();
        $this->getAllowedFeatures();
        $this->getFeaturesByGroup();
    }
}

