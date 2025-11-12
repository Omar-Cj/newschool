<?php

declare(strict_types=1);

namespace Modules\MainApp\Entities;

use App\Enums\Status;
use App\Models\PermissionFeature;
use Illuminate\Database\Eloquent\Model;
use Modules\MainApp\Entities\PackageChild;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [];

    /**
     * Get package children.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function packageChilds(): HasMany
    {
        return $this->hasMany(PackageChild::class, 'package_id', 'id');
    }

    /**
     * Get permission features assigned to this package.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissionFeatures(): BelongsToMany
    {
        return $this->belongsToMany(
            PermissionFeature::class,
            'package_permission_features',
            'package_id',
            'permission_feature_id'
        )->withTimestamps();
    }

    /**
     * Scope a query to only include active packages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', Status::ACTIVE);
    }

    /**
     * Get all allowed permission keywords for this package.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllowedPermissions(): Collection
    {
        $cacheKey = "package_allowed_permissions_{$this->id}";

        // LOG POINT F: Cache lookup
        $cacheHit = Cache::has($cacheKey);
        try {
            Log::info('[PACKAGE] getAllowedPermissions() - Cache lookup', [
                'package_id' => $this->id,
                'package_name' => $this->name,
                'cache_key' => $cacheKey,
                'cache_hit' => $cacheHit,
            ]);
        } catch (\Exception $e) {}

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($cacheKey) {
            // LOG POINT G: Cache miss - executing query
            try {
                Log::info('[PACKAGE] getAllowedPermissions() - Cache miss, executing query', [
                    'package_id' => $this->id,
                    'package_name' => $this->name,
                ]);
            } catch (\Exception $e) {}

            // Step 1: Get permission features with active filter
            $permissionFeatures = $this->permissionFeatures()
                ->active()
                ->with('permission')
                ->get();

            // LOG POINT H: Permission features loaded
            try {
                Log::info('[PACKAGE] getAllowedPermissions() - Permission features loaded', [
                    'package_id' => $this->id,
                    'package_name' => $this->name,
                    'total_features' => $permissionFeatures->count(),
                    'sample_features' => $permissionFeatures->take(5)->map(function($pf) {
                        return [
                            'id' => $pf->id,
                            'name' => $pf->name,
                            'status' => $pf->status,
                            'permission_id' => $pf->permission_id,
                            'has_permission' => $pf->permission !== null,
                            'permission_attribute' => $pf->permission?->attribute,
                        ];
                    })->toArray(),
                ]);
            } catch (\Exception $e) {}

            // Step 2: Pluck permission attributes
            $attributes = $permissionFeatures->pluck('permission.attribute');

            // LOG POINT I: Attributes plucked (may contain nulls)
            try {
                Log::info('[PACKAGE] getAllowedPermissions() - Attributes plucked', [
                    'package_id' => $this->id,
                    'package_name' => $this->name,
                    'total_attributes' => $attributes->count(),
                    'null_count' => $attributes->filter(fn($a) => $a === null)->count(),
                    'sample_attributes' => $attributes->take(10)->toArray(),
                ]);
            } catch (\Exception $e) {}

            // Step 3: Filter, unique, values
            $finalPermissions = $attributes
                ->filter()
                ->unique()
                ->values();

            // LOG POINT J: Final results after filter/unique
            try {
                Log::info('[PACKAGE] getAllowedPermissions() - Final permissions', [
                    'package_id' => $this->id,
                    'package_name' => $this->name,
                    'final_count' => $finalPermissions->count(),
                    'permissions' => $finalPermissions->toArray(),
                ]);
            } catch (\Exception $e) {}

            return $finalPermissions;
        });
    }

    /**
     * Check if package has a specific feature.
     *
     * @param string $permissionAttribute
     * @return bool
     */
    public function hasFeature(string $permissionAttribute): bool
    {
        $allowedPermissions = $this->getAllowedPermissions();
        return $allowedPermissions->contains($permissionAttribute);
    }

    /**
     * Sync features with cache clearing.
     *
     * @param array<int> $featureIds
     * @return void
     */
    public function syncFeatures(array $featureIds): void
    {
        $this->permissionFeatures()->sync($featureIds);

        // Clear caches
        Cache::forget("package_allowed_permissions_{$this->id}");
        Cache::forget("package_features_organized_{$this->id}");
        Cache::forget("package_features_{$this->id}");

        // Clear school caches for schools using this package
        $schools = School::where('package_id', $this->id)->get();
        foreach ($schools as $school) {
            if (method_exists($school, 'clearFeatureCache')) {
                $school->clearFeatureCache();
            }
        }
    }
}
