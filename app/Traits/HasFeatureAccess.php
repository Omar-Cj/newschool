<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * HasFeatureAccess Trait
 *
 * Provides feature access checking functionality to models
 * (typically School or User models).
 *
 * Requires the model to have a `package` relationship.
 */
trait HasFeatureAccess
{
    /**
     * Check if the model has access to a specific feature.
     *
     * @param string $featureAttribute Permission attribute to check
     * @return bool
     */
    public function hasFeatureAccess(string $featureAttribute): bool
    {
        // LOG POINT 9: Trait method entry
        Log::channel('feature_access')->debug('HasFeatureAccess::hasFeatureAccess() called', [
            'feature_attribute' => $featureAttribute,
            'model_class' => get_class($this),
            'model_id' => $this->id,
            'package_id' => $this->package_id ?? null,
        ]);

        $allowedFeatures = $this->getAllowedFeatures();

        // LOG POINT 10: Allowed features retrieved
        Log::channel('feature_access')->debug('HasFeatureAccess::getAllowedFeatures() result', [
            'feature_attribute' => $featureAttribute,
            'model_class' => get_class($this),
            'model_id' => $this->id,
            'package_id' => $this->package_id ?? null,
            'allowed_features_count' => $allowedFeatures->count(),
            'allowed_features_list' => $allowedFeatures->toArray(),
            'cache_key' => $this->getFeatureCacheKey(),
        ]);

        $hasAccess = $allowedFeatures->contains($featureAttribute);

        // LOG POINT 11: Contains check result
        Log::channel('feature_access')->info('HasFeatureAccess::hasFeatureAccess() result', [
            'feature_attribute' => $featureAttribute,
            'model_class' => get_class($this),
            'model_id' => $this->id,
            'package_id' => $this->package_id ?? null,
            'has_access' => $hasAccess,
            'feature_in_list' => $hasAccess,
        ]);

        return $hasAccess;
    }

    /**
     * Get all allowed permission attributes for this model.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllowedFeatures(): Collection
    {
        $cacheKey = $this->getFeatureCacheKey();

        // LOG POINT 12: Cache lookup
        $isCached = Cache::has($cacheKey);
        Log::channel('feature_access')->debug('getAllowedFeatures() - Cache lookup', [
            'model_class' => get_class($this),
            'model_id' => $this->id,
            'cache_key' => $cacheKey,
            'is_cached' => $isCached,
        ]);

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($cacheKey) {
            // LOG POINT 13: Package check
            Log::channel('feature_access')->debug('getAllowedFeatures() - Cache miss, building', [
                'model_class' => get_class($this),
                'model_id' => $this->id,
                'cache_key' => $cacheKey,
                'has_package' => $this->package !== null,
                'package_id' => $this->package_id ?? null,
            ]);

            if (!$this->package) {
                Log::channel('feature_access')->warning('getAllowedFeatures() - No package', [
                    'model_class' => get_class($this),
                    'model_id' => $this->id,
                    'package_id' => $this->package_id ?? null,
                    'returning_empty_collection' => true,
                ]);
                return collect([]);
            }

            $features = $this->package
                ->permissionFeatures()
                ->active()
                ->with('permission')
                ->get()
                ->pluck('permission.attribute')
                ->filter()
                ->unique()
                ->values();

            // LOG POINT 14: Features loaded from database
            Log::channel('feature_access')->info('getAllowedFeatures() - Features loaded', [
                'model_class' => get_class($this),
                'model_id' => $this->id,
                'package_id' => $this->package_id ?? null,
                'package_name' => $this->package->name ?? null,
                'features_count' => $features->count(),
                'features_list' => $features->toArray(),
                'cache_key' => $cacheKey,
                'cached_until' => now()->addHours(24)->toDateTimeString(),
            ]);

            return $features;
        });
    }

    /**
     * Get features organized by feature groups.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getFeatureGroups(): Collection
    {
        $cacheKey = $this->getFeatureGroupsCacheKey();

        return Cache::remember($cacheKey, now()->addHours(24), function () {
            if (!$this->package) {
                return collect([]);
            }

            return $this->package
                ->permissionFeatures()
                ->active()
                ->with(['featureGroup', 'permission'])
                ->get()
                ->groupBy('feature_group_id')
                ->map(function ($features, $groupId) {
                    $group = $features->first()->featureGroup;
                    return [
                        'id' => $group->id ?? $groupId,
                        'name' => $group->name ?? 'Uncategorized',
                        'slug' => $group->slug ?? 'uncategorized',
                        'description' => $group->description ?? null,
                        'icon' => $group->icon ?? null,
                        'features' => $features->map(function ($feature) {
                            return [
                                'id' => $feature->id,
                                'name' => $feature->name,
                                'description' => $feature->description,
                                'is_premium' => $feature->is_premium,
                                'permission_attribute' => $feature->permission->attribute ?? null,
                            ];
                        })->values(),
                    ];
                })
                ->values();
        });
    }

    /**
     * Check if the model can access a feature with a specific action.
     *
     * @param string $featureAttribute Permission attribute
     * @param string $action Action to check (read, create, update, delete)
     * @return bool
     */
    public function canAccessFeature(string $featureAttribute, string $action = 'read'): bool
    {
        // First check if base feature access exists
        if (!$this->hasFeatureAccess($featureAttribute)) {
            return false;
        }

        // If action is 'read', base access is sufficient
        if ($action === 'read') {
            return true;
        }

        // For other actions, check specific permission if needed
        // This can be extended based on your permission structure
        $actionAttribute = "{$featureAttribute}.{$action}";
        return $this->hasFeatureAccess($actionAttribute) || $this->hasFeatureAccess($featureAttribute);
    }

    /**
     * Clear feature access cache for this model.
     *
     * @return void
     */
    public function clearFeatureCache(): void
    {
        Cache::forget($this->getFeatureCacheKey());
        Cache::forget($this->getFeatureGroupsCacheKey());
    }

    /**
     * Get cache key for features.
     *
     * @return string
     */
    protected function getFeatureCacheKey(): string
    {
        $modelClass = class_basename($this);
        return "model_features_{$modelClass}_{$this->id}";
    }

    /**
     * Get cache key for feature groups.
     *
     * @return string
     */
    protected function getFeatureGroupsCacheKey(): string
    {
        $modelClass = class_basename($this);
        return "model_feature_groups_{$modelClass}_{$this->id}";
    }

    /**
     * Check if the model has multiple features.
     *
     * @param array<string> $featureAttributes
     * @param bool $requireAll Whether all features must be present (AND) or any (OR)
     * @return bool
     */
    public function hasFeatures(array $featureAttributes, bool $requireAll = true): bool
    {
        $allowedFeatures = $this->getAllowedFeatures();

        if ($requireAll) {
            // All features must be present
            foreach ($featureAttributes as $attribute) {
                if (!$allowedFeatures->contains($attribute)) {
                    return false;
                }
            }
            return true;
        } else {
            // At least one feature must be present
            foreach ($featureAttributes as $attribute) {
                if ($allowedFeatures->contains($attribute)) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * Get premium features for this model.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPremiumFeatures(): Collection
    {
        if (!$this->package) {
            return collect([]);
        }

        return $this->package
            ->permissionFeatures()
            ->active()
            ->premium()
            ->with(['permission', 'featureGroup'])
            ->get()
            ->map(function ($feature) {
                return [
                    'id' => $feature->id,
                    'name' => $feature->name,
                    'description' => $feature->description,
                    'group' => $feature->featureGroup->name ?? null,
                    'permission_attribute' => $feature->permission->attribute ?? null,
                ];
            });
    }

    /**
     * Check if the model has any premium features.
     *
     * @return bool
     */
    public function hasPremiumFeatures(): bool
    {
        return $this->getPremiumFeatures()->isNotEmpty();
    }

    /**
     * Get feature access summary.
     *
     * @return array<string, mixed>
     */
    public function getFeatureAccessSummary(): array
    {
        $allFeatures = $this->getAllowedFeatures();
        $premiumFeatures = $this->getPremiumFeatures();

        return [
            'total_features' => $allFeatures->count(),
            'premium_features' => $premiumFeatures->count(),
            'standard_features' => $allFeatures->count() - $premiumFeatures->count(),
            'has_premium_access' => $this->hasPremiumFeatures(),
            'package_name' => $this->package->name ?? 'No Package',
            'package_id' => $this->package->id ?? null,
        ];
    }
}
