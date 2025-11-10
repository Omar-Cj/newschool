<?php

declare(strict_types=1);

namespace App\Services;

use Modules\MainApp\Entities\Package;
use Modules\MainApp\Entities\School;
use App\Repositories\PermissionFeatureRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * PackageFeatureService
 *
 * Handles package-specific feature operations including assignment,
 * comparison, and package upgrades.
 */
class PackageFeatureService
{
    /**
     * Create a new service instance.
     *
     * @param \App\Repositories\PermissionFeatureRepository $permissionFeatureRepo
     * @param \App\Services\FeatureManagementService $featureManagementService
     */
    public function __construct(
        protected PermissionFeatureRepository $permissionFeatureRepo,
        protected FeatureManagementService $featureManagementService
    ) {}

    /**
     * Assign features to a package.
     *
     * @param int $packageId
     * @param array<int> $featureIds
     * @return bool
     * @throws \Exception
     */
    public function assignFeaturesToPackage(int $packageId, array $featureIds): bool
    {
        $package = Package::findOrFail($packageId);
        return $this->featureManagementService->syncPackageFeatures($package, $featureIds);
    }

    /**
     * Get all features for a specific package.
     *
     * @param int $packageId
     * @return \Illuminate\Support\Collection
     */
    public function getPackageFeatures(int $packageId): Collection
    {
        $cacheKey = "package_features_{$packageId}";

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($packageId) {
            return $this->permissionFeatureRepo->getByPackage($packageId);
        });
    }

    /**
     * Compare features between two packages.
     *
     * @param int $packageId1
     * @param int $packageId2
     * @return array<string, mixed>
     */
    public function comparePackages(int $packageId1, int $packageId2): array
    {
        $package1 = Package::with('permissionFeatures')->findOrFail($packageId1);
        $package2 = Package::with('permissionFeatures')->findOrFail($packageId2);

        $features1 = $package1->permissionFeatures->pluck('id');
        $features2 = $package2->permissionFeatures->pluck('id');

        $onlyInPackage1 = $features1->diff($features2);
        $onlyInPackage2 = $features2->diff($features1);
        $common = $features1->intersect($features2);

        return [
            'package1' => [
                'id' => $package1->id,
                'name' => $package1->name ?? "Package {$package1->id}",
                'total_features' => $features1->count(),
                'unique_features' => $onlyInPackage1->count(),
            ],
            'package2' => [
                'id' => $package2->id,
                'name' => $package2->name ?? "Package {$package2->id}",
                'total_features' => $features2->count(),
                'unique_features' => $onlyInPackage2->count(),
            ],
            'common_features' => $common->count(),
            'only_in_package1' => $this->getFeatureDetails($onlyInPackage1),
            'only_in_package2' => $this->getFeatureDetails($onlyInPackage2),
            'common_features_details' => $this->getFeatureDetails($common),
        ];
    }

    /**
     * Get feature difference between current and target package.
     *
     * @param int $currentPackageId
     * @param int $targetPackageId
     * @return array<string, mixed>
     */
    public function getFeatureDiff(int $currentPackageId, int $targetPackageId): array
    {
        $currentFeatures = Package::with('permissionFeatures')
            ->findOrFail($currentPackageId)
            ->permissionFeatures
            ->pluck('id');

        $targetFeatures = Package::with('permissionFeatures')
            ->findOrFail($targetPackageId)
            ->permissionFeatures
            ->pluck('id');

        $gained = $targetFeatures->diff($currentFeatures);
        $lost = $currentFeatures->diff($targetFeatures);
        $retained = $currentFeatures->intersect($targetFeatures);

        return [
            'upgrade' => $gained->count() > 0 || $lost->count() === 0,
            'downgrade' => $lost->count() > 0,
            'summary' => [
                'features_gained' => $gained->count(),
                'features_lost' => $lost->count(),
                'features_retained' => $retained->count(),
            ],
            'gained_features' => $this->getFeatureDetails($gained),
            'lost_features' => $this->getFeatureDetails($lost),
            'retained_features' => $this->getFeatureDetails($retained),
        ];
    }

    /**
     * Upgrade a school's package.
     *
     * @param int $schoolId
     * @param int $newPackageId
     * @return bool
     * @throws \Exception
     */
    public function upgradePackage(int $schoolId, int $newPackageId): bool
    {
        DB::beginTransaction();
        try {
            $school = School::findOrFail($schoolId);
            $oldPackageId = $school->package_id;

            // Update school package
            $school->update(['package_id' => $newPackageId]);

            // Clear feature cache for school
            if (method_exists($school, 'clearFeatureCache')) {
                $school->clearFeatureCache();
            } else {
                Cache::forget("school_features_{$schoolId}");
                Cache::forget("school_features_by_group_{$schoolId}");
            }

            // Log package change
            activity()
                ->performedOn($school)
                ->withProperties([
                    'old_package_id' => $oldPackageId,
                    'new_package_id' => $newPackageId,
                    'feature_diff' => $this->getFeatureDiff($oldPackageId, $newPackageId),
                ])
                ->log('package_upgraded');

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to upgrade package: " . $e->getMessage());
        }
    }

    /**
     * Get detailed information about features.
     *
     * @param \Illuminate\Support\Collection $featureIds
     * @return \Illuminate\Support\Collection
     */
    protected function getFeatureDetails(Collection $featureIds): Collection
    {
        if ($featureIds->isEmpty()) {
            return collect([]);
        }

        return \App\Models\PermissionFeature::with(['permission', 'featureGroup'])
            ->whereIn('id', $featureIds)
            ->get()
            ->map(function ($feature) {
                return [
                    'id' => $feature->id,
                    'name' => $feature->name,
                    'description' => $feature->description,
                    'is_premium' => $feature->is_premium,
                    'group' => $feature->featureGroup->name ?? null,
                    'permission_attribute' => $feature->permission->attribute ?? null,
                ];
            });
    }

    /**
     * Get package upgrade recommendations.
     *
     * @param int $currentPackageId
     * @return \Illuminate\Support\Collection
     */
    public function getUpgradeRecommendations(int $currentPackageId): Collection
    {
        $currentPackage = Package::with('permissionFeatures')->findOrFail($currentPackageId);
        $currentFeatureCount = $currentPackage->permissionFeatures->count();

        $packages = Package::with('permissionFeatures')
            ->where('id', '!=', $currentPackageId)
            ->active()
            ->get();

        return $packages->map(function ($package) use ($currentPackageId, $currentFeatureCount) {
            $diff = $this->getFeatureDiff($currentPackageId, $package->id);

            return [
                'package_id' => $package->id,
                'package_name' => $package->name ?? "Package {$package->id}",
                'is_upgrade' => $diff['upgrade'],
                'features_gained' => $diff['summary']['features_gained'],
                'features_lost' => $diff['summary']['features_lost'],
                'total_features' => $package->permissionFeatures->count(),
                'feature_increase_percentage' => $currentFeatureCount > 0
                    ? round((($package->permissionFeatures->count() - $currentFeatureCount) / $currentFeatureCount) * 100, 2)
                    : 0,
            ];
        })
        ->filter(fn($pkg) => $pkg['is_upgrade'])
        ->sortByDesc('features_gained')
        ->values();
    }

    /**
     * Check if a package has a specific feature.
     *
     * @param int $packageId
     * @param int $featureId
     * @return bool
     */
    public function packageHasFeature(int $packageId, int $featureId): bool
    {
        $features = $this->getPackageFeatures($packageId);
        return $features->contains('id', $featureId);
    }

    /**
     * Get packages that include a specific feature.
     *
     * @param int $featureId
     * @return \Illuminate\Support\Collection
     */
    public function getPackagesWithFeature(int $featureId): Collection
    {
        $feature = \App\Models\PermissionFeature::with('packages')->findOrFail($featureId);
        return $feature->packages;
    }
}
