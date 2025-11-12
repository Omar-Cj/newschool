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
use Illuminate\Support\Facades\Log;

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

        // LOG POINT A: Cache lookup
        $cacheHit = Cache::has($cacheKey);
        try {
            Log::info('[SCHOOL] getAllowedFeatures() - Cache lookup', [
                'school_id' => $this->id,
                'school_name' => $this->name,
                'package_id' => $this->package_id,
                'cache_key' => $cacheKey,
                'cache_hit' => $cacheHit,
            ]);
        } catch (\Exception $e) {
            // Silently ignore logging errors
        }

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($cacheKey) {
            // LOG POINT B: Cache miss - building features
            try {
                Log::info('[SCHOOL] getAllowedFeatures() - Cache miss, building features', [
                    'school_id' => $this->id,
                    'school_name' => $this->name,
                    'package_id' => $this->package_id,
                    'has_package_relationship' => $this->package !== null,
                ]);
            } catch (\Exception $e) {}

            if (!$this->package) {
                // LOG POINT C: No package - returning empty
                try {
                    Log::warning('[SCHOOL] getAllowedFeatures() - No package found', [
                        'school_id' => $this->id,
                        'school_name' => $this->name,
                        'package_id' => $this->package_id,
                        'result' => 'empty_array',
                    ]);
                } catch (\Exception $e) {}
                return collect([]);
            }

            // LOG POINT D: Package found, calling getAllowedPermissions()
            try {
                Log::info('[SCHOOL] getAllowedFeatures() - Package found, fetching permissions', [
                    'school_id' => $this->id,
                    'school_name' => $this->name,
                    'package_id' => $this->package->id,
                    'package_name' => $this->package->name,
                ]);
            } catch (\Exception $e) {}

            $permissions = $this->package->getAllowedPermissions();

            // LOG POINT E: Results from package
            try {
                Log::info('[SCHOOL] getAllowedFeatures() - Permissions received from package', [
                    'school_id' => $this->id,
                    'school_name' => $this->name,
                    'package_id' => $this->package->id,
                    'package_name' => $this->package->name,
                    'permission_count' => $permissions->count(),
                    'sample_permissions' => $permissions->take(10)->toArray(),
                ]);
            } catch (\Exception $e) {}

            return $permissions;
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

