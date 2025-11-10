<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\FeatureGroupRepository;
use App\Repositories\PermissionFeatureRepository;
use Modules\MainApp\Entities\Package;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * FeatureManagementService
 *
 * Orchestrates feature management operations including organization,
 * package synchronization, and feature statistics.
 */
class FeatureManagementService
{
    /**
     * Create a new service instance.
     *
     * @param \App\Repositories\FeatureGroupRepository $featureGroupRepo
     * @param \App\Repositories\PermissionFeatureRepository $permissionFeatureRepo
     */
    public function __construct(
        protected FeatureGroupRepository $featureGroupRepo,
        protected PermissionFeatureRepository $permissionFeatureRepo
    ) {}

    /**
     * Get organized features grouped by feature groups.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getOrganizedFeatures(): Collection
    {
        $cacheKey = 'organized_features';

        return Cache::remember($cacheKey, now()->addHours(24), function () {
            return $this->featureGroupRepo->getActiveGroupsWithFeatures();
        });
    }

    /**
     * Sync package features with cache clearing.
     *
     * @param \Modules\MainApp\Entities\Package $package
     * @param array<int> $featureIds
     * @return bool
     * @throws \Exception
     */
    public function syncPackageFeatures(Package $package, array $featureIds): bool
    {
        DB::beginTransaction();
        try {
            // Validate feature IDs
            $this->validateFeatureAssignment($package->id, $featureIds);

            // Sync features
            $package->permissionFeatures()->sync($featureIds);

            // Clear caches
            $this->clearPackageFeatureCaches($package->id);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to sync package features: " . $e->getMessage());
        }
    }

    /**
     * Extract permission keywords from a collection of features.
     *
     * @param \Illuminate\Support\Collection $features
     * @return \Illuminate\Support\Collection
     */
    public function getPermissionsFromFeatures(Collection $features): Collection
    {
        return $features
            ->pluck('permission.attribute')
            ->filter()
            ->unique()
            ->values();
    }

    /**
     * Get feature usage statistics.
     *
     * @return array<string, mixed>
     */
    public function getFeatureStats(): array
    {
        $cacheKey = 'feature_statistics';

        return Cache::remember($cacheKey, now()->addHours(1), function () {
            $totalGroups = $this->featureGroupRepo->all()->count();
            $activeGroups = $this->featureGroupRepo->getActiveGroups()->count();

            $allFeatures = $this->permissionFeatureRepo->all();
            $totalFeatures = $allFeatures->count();
            $activeFeatures = $allFeatures->where('status', 1)->count();
            $premiumFeatures = $allFeatures->where('is_premium', true)->count();

            // Package statistics
            $packages = Package::with('permissionFeatures')->get();
            $packagesWithFeatures = $packages->filter(function ($package) {
                return $package->permissionFeatures->count() > 0;
            })->count();

            $avgFeaturesPerPackage = $packages->count() > 0
                ? round($packages->sum(fn($p) => $p->permissionFeatures->count()) / $packages->count(), 2)
                : 0;

            return [
                'groups' => [
                    'total' => $totalGroups,
                    'active' => $activeGroups,
                    'inactive' => $totalGroups - $activeGroups,
                ],
                'features' => [
                    'total' => $totalFeatures,
                    'active' => $activeFeatures,
                    'inactive' => $totalFeatures - $activeFeatures,
                    'premium' => $premiumFeatures,
                    'standard' => $totalFeatures - $premiumFeatures,
                ],
                'packages' => [
                    'total' => $packages->count(),
                    'with_features' => $packagesWithFeatures,
                    'avg_features_per_package' => $avgFeaturesPerPackage,
                ],
            ];
        });
    }

    /**
     * Validate feature assignment to a package.
     *
     * @param int $packageId
     * @param array<int> $featureIds
     * @return bool
     * @throws \Exception
     */
    public function validateFeatureAssignment(int $packageId, array $featureIds): bool
    {
        if (empty($featureIds)) {
            return true;
        }

        // Check if all feature IDs exist and are active
        $validFeatures = $this->permissionFeatureRepo->getActiveFeatures()
            ->pluck('id')
            ->toArray();

        $invalidFeatures = array_diff($featureIds, $validFeatures);

        if (!empty($invalidFeatures)) {
            throw new Exception(
                "Invalid or inactive feature IDs: " . implode(', ', $invalidFeatures)
            );
        }

        return true;
    }

    /**
     * Get permission feature IDs for a specific package.
     *
     * @param int $packageId
     * @return array<int>
     */
    public function getPackageFeatureIds(int $packageId): array
    {
        $cacheKey = "package_feature_ids_{$packageId}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($packageId) {
            $package = Package::with('permissionFeatures')->find($packageId);

            if (!$package) {
                return [];
            }

            return $package->permissionFeatures->pluck('id')->toArray();
        });
    }

    /**
     * Get features organized by group for a specific package.
     *
     * @param int $packageId
     * @return \Illuminate\Support\Collection
     */
    public function getPackageFeaturesOrganized(int $packageId): Collection
    {
        $cacheKey = "package_features_organized_{$packageId}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($packageId) {
            $package = Package::with([
                'permissionFeatures.featureGroup',
                'permissionFeatures.permission'
            ])->findOrFail($packageId);

            return $package->permissionFeatures
                ->groupBy('feature_group_id')
                ->map(function ($features, $groupId) {
                    $group = $features->first()->featureGroup;
                    return [
                        'group' => [
                            'id' => $group->id,
                            'name' => $group->name,
                            'slug' => $group->slug,
                            'description' => $group->description,
                            'icon' => $group->icon,
                        ],
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
     * Clear all feature-related caches for a package.
     *
     * @param int $packageId
     * @return void
     */
    protected function clearPackageFeatureCaches(int $packageId): void
    {
        Cache::forget("package_feature_ids_{$packageId}");
        Cache::forget("package_features_organized_{$packageId}");
        Cache::forget("package_allowed_permissions_{$packageId}");
        Cache::forget('organized_features');
        Cache::forget('feature_statistics');

        // Clear school feature caches for schools using this package
        $schools = \Modules\MainApp\Entities\School::where('package_id', $packageId)->get();
        foreach ($schools as $school) {
            Cache::forget("school_features_{$school->id}");
            Cache::forget("school_features_by_group_{$school->id}");
        }
    }

    /**
     * Bulk update feature statuses.
     *
     * @param array<int> $featureIds
     * @param int $status
     * @return int Number of features updated
     * @throws \Exception
     */
    public function bulkUpdateStatus(array $featureIds, int $status): int
    {
        DB::beginTransaction();
        try {
            $updated = \App\Models\PermissionFeature::whereIn('id', $featureIds)
                ->update(['status' => $status]);

            // Clear caches
            Cache::forget('organized_features');
            Cache::forget('feature_statistics');

            DB::commit();
            return $updated;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to bulk update feature statuses: " . $e->getMessage());
        }
    }

    /**
     * Clone features from one package to another.
     *
     * @param int $sourcePackageId
     * @param int $targetPackageId
     * @return bool
     * @throws \Exception
     */
    public function clonePackageFeatures(int $sourcePackageId, int $targetPackageId): bool
    {
        DB::beginTransaction();
        try {
            $sourcePackage = Package::with('permissionFeatures')->findOrFail($sourcePackageId);
            $targetPackage = Package::findOrFail($targetPackageId);

            $featureIds = $sourcePackage->permissionFeatures->pluck('id')->toArray();

            $targetPackage->permissionFeatures()->sync($featureIds);

            $this->clearPackageFeatureCaches($targetPackageId);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to clone package features: " . $e->getMessage());
        }
    }
}
