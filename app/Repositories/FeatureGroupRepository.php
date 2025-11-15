<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\FeatureGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

/**
 * FeatureGroupRepository
 *
 * Handles all data operations for feature groups including
 * CRUD operations, bulk updates, and retrieval with relationships.
 */
class FeatureGroupRepository
{
    /**
     * Get all feature groups with their permission features.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithFeatures(): Collection
    {
        return FeatureGroup::with(['permissionFeatures' => function ($query) {
            $query->where('status', 1)
                ->orderBy('position')
                ->with('permission');
        }])
            ->orderBy('position')
            ->get();
    }

    /**
     * Get only active feature groups.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveGroups(): Collection
    {
        return FeatureGroup::active()
            ->ordered()
            ->get();
    }

    /**
     * Get active groups with active features.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveGroupsWithFeatures(): Collection
    {
        return FeatureGroup::activeWithFeatures()->get();
    }

    /**
     * Create a new feature group.
     *
     * @param array<string, mixed> $data
     * @return \App\Models\FeatureGroup
     * @throws \Exception
     */
    public function createGroup(array $data): FeatureGroup
    {
        DB::beginTransaction();
        try {
            // Auto-generate slug if not provided
            if (empty($data['slug']) && !empty($data['name'])) {
                $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
            }

            // Auto-set position if not provided
            if (!isset($data['position'])) {
                $maxPosition = FeatureGroup::max('position') ?? 0;
                $data['position'] = $maxPosition + 1;
            }

            // Set default status if not provided
            if (!isset($data['status'])) {
                $data['status'] = 1;
            }

            $featureGroup = FeatureGroup::create($data);

            DB::commit();
            return $featureGroup;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to create feature group: " . $e->getMessage());
        }
    }

    /**
     * Update an existing feature group.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @return \App\Models\FeatureGroup
     * @throws \Exception
     */
    public function updateGroup(int $id, array $data): FeatureGroup
    {
        DB::beginTransaction();
        try {
            $featureGroup = FeatureGroup::findOrFail($id);

            // Update slug if name changed
            if (isset($data['name']) && $data['name'] !== $featureGroup->name) {
                if (empty($data['slug'])) {
                    $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
                }
            }

            $featureGroup->update($data);

            DB::commit();
            return $featureGroup->fresh();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to update feature group: " . $e->getMessage());
        }
    }

    /**
     * Delete a feature group.
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function deleteGroup(int $id): bool
    {
        DB::beginTransaction();
        try {
            $featureGroup = FeatureGroup::findOrFail($id);

            // Check if group has features
            if ($featureGroup->permissionFeatures()->count() > 0) {
                throw new Exception("Cannot delete feature group with associated features. Please reassign or delete features first.");
            }

            $deleted = $featureGroup->delete();

            DB::commit();
            return $deleted;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to delete feature group: " . $e->getMessage());
        }
    }

    /**
     * Reorder feature groups by updating positions.
     *
     * @param array<int, int> $positions Array of group_id => position
     * @return bool
     * @throws \Exception
     */
    public function reorder(array $positions): bool
    {
        DB::beginTransaction();
        try {
            foreach ($positions as $groupId => $position) {
                FeatureGroup::where('id', $groupId)
                    ->update(['position' => $position]);
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Failed to reorder feature groups: " . $e->getMessage());
        }
    }

    /**
     * Find a feature group by ID.
     *
     * @param int $id
     * @return \App\Models\FeatureGroup|null
     */
    public function find(int $id): ?FeatureGroup
    {
        return FeatureGroup::find($id);
    }

    /**
     * Find a feature group by ID with features.
     *
     * @param int $id
     * @return \App\Models\FeatureGroup|null
     */
    public function findWithFeatures(int $id): ?FeatureGroup
    {
        return FeatureGroup::with(['permissionFeatures' => function ($query) {
            $query->orderBy('position')->with('permission');
        }])->find($id);
    }

    /**
     * Find a feature group by slug.
     *
     * @param string $slug
     * @return \App\Models\FeatureGroup|null
     */
    public function findBySlug(string $slug): ?FeatureGroup
    {
        return FeatureGroup::where('slug', $slug)->first();
    }

    /**
     * Get all feature groups.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): Collection
    {
        return FeatureGroup::ordered()->get();
    }

    /**
     * Get all feature groups (alias for consistency).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(): Collection
    {
        return $this->all();
    }

    /**
     * Get all feature groups with permission feature count.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllWithFeatureCount(): Collection
    {
        return FeatureGroup::withCount('permissionFeatures')
            ->orderBy('position')
            ->get();
    }
}
