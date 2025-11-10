<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PermissionFeature;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * PermissionFeatureRepository
 *
 * Handles data operations for permission features including
 * CRUD operations, filtering, and bulk operations.
 */
class PermissionFeatureRepository
{
    /**
     * Get all permission features.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): Collection
    {
        return PermissionFeature::with(['permission', 'featureGroup'])
            ->ordered()
            ->get();
    }

    /**
     * Get all permission features (alias for consistency).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): Collection
    {
        return $this->all();
    }

    /**
     * Get all permission features grouped by feature group.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllGrouped(): \Illuminate\Support\Collection
    {
        return PermissionFeature::with(['permission', 'featureGroup'])
            ->ordered()
            ->get()
            ->groupBy('feature_group_id')
            ->map(function ($features, $groupId) {
                $group = $features->first()->featureGroup;
                return [
                    'group' => $group,
                    'features' => $features
                ];
            })
            ->values();
    }

    /**
     * Get features by feature group.
     *
     * @param int $groupId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByGroup(int $groupId): Collection
    {
        return PermissionFeature::byGroup($groupId)
            ->with('permission')
            ->ordered()
            ->get();
    }

    /**
     * Get active features by feature group.
     *
     * @param int $groupId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveByGroup(int $groupId): Collection
    {
        return PermissionFeature::active()
            ->byGroup($groupId)
            ->with('permission')
            ->ordered()
            ->get();
    }

    /**
     * Get features by permission.
     *
     * @param int $permissionId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByPermission(int $permissionId): Collection
    {
        return PermissionFeature::byPermission($permissionId)
            ->with('featureGroup')
            ->ordered()
            ->get();
    }

    /**
     * Get only premium features.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPremiumFeatures(): Collection
    {
        return PermissionFeature::premium()
            ->active()
            ->with(['permission', 'featureGroup'])
            ->ordered()
            ->get();
    }

    /**
     * Get active features.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveFeatures(): Collection
    {
        return PermissionFeature::active()
            ->with(['permission', 'featureGroup'])
            ->ordered()
            ->get();
    }

    /**
     * Create a new permission feature.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\PermissionFeature
     * @throws \Exception
     */
    public function createFeature(array $data): PermissionFeature
    {
        DB::beginTransaction();
        try {
            // Auto-set position if not provided
            if (!isset($data['position'])) {
                $maxPosition = PermissionFeature::where('feature_group_id', $data['feature_group_id'])
                    ->max('position') ?? 0;
                $data['position'] = $maxPosition + 1;
            }

            // Set default values
            if (!isset($data['status'])) {
                $data['status'] = 1;
            }
            if (!isset($data['is_premium'])) {
                $data['is_premium'] = false;
            }

            $feature = PermissionFeature::create($data);

            DB::commit();
            return $feature->load(['permission', 'featureGroup']);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to create permission feature: " . $e->getMessage());
        }
    }

    /**
     * Update an existing permission feature.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return \App\Models\PermissionFeature
     * @throws \Exception
     */
    public function updateFeature(int $id, array $data): PermissionFeature
    {
        DB::beginTransaction();
        try {
            $feature = PermissionFeature::findOrFail($id);
            $feature->update($data);

            DB::commit();
            return $feature->fresh(['permission', 'featureGroup']);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to update permission feature: " . $e->getMessage());
        }
    }

    /**
     * Delete a permission feature.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteFeature(int $id): bool
    {
        DB::beginTransaction();
        try {
            $feature = PermissionFeature::findOrFail($id);

            // Detach from all packages
            $feature->packages()->detach();

            $deleted = $feature->delete();

            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to delete permission feature: " . $e->getMessage());
        }
    }

    /**
     * Bulk assign features to a feature group.
     *
     * @param array<int> $featureIds
     * @param int $groupId
     * @return int Number of features updated
     * @throws \Exception
     */
    public function bulkAssignToGroup(array $featureIds, int $groupId): int
    {
        DB::beginTransaction();
        try {
            $updated = PermissionFeature::whereIn('id', $featureIds)
                ->update(['feature_group_id' => $groupId]);

            DB::commit();
            return $updated;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to bulk assign features to group: " . $e->getMessage());
        }
    }

    /**
     * Find a permission feature by ID.
     *
     * @param int $id
     * @return \App\Models\PermissionFeature|null
     */
    public function find(int $id): ?PermissionFeature
    {
        return PermissionFeature::with(['permission', 'featureGroup'])->find($id);
    }

    /**
     * Get features for a specific package.
     *
     * @param int $packageId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByPackage(int $packageId): Collection
    {
        return PermissionFeature::whereHas('packages', function ($query) use ($packageId) {
            $query->where('package_id', $packageId);
        })
            ->with(['permission', 'featureGroup'])
            ->ordered()
            ->get();
    }

    /**
     * Reorder features within a group.
     *
     * @param array<int, int> $positions Array of feature_id => position
     * @return bool
     * @throws \Exception
     */
    public function reorder(array $positions): bool
    {
        DB::beginTransaction();
        try {
            foreach ($positions as $featureId => $position) {
                PermissionFeature::where('id', $featureId)
                    ->update(['position' => $position]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to reorder features: " . $e->getMessage());
        }
    }
}
