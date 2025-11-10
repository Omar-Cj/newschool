<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\PermissionFeatureRepository;
use App\Repositories\FeatureGroupRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Permission;

class PermissionFeatureController extends Controller
{
    private $repository;
    private $featureGroupRepository;

    public function __construct(
        PermissionFeatureRepository $repository,
        FeatureGroupRepository $featureGroupRepository
    ) {
        $this->repository = $repository;
        $this->featureGroupRepository = $featureGroupRepository;
    }

    /**
     * Display a listing of permission features
     */
    public function index(Request $request)
    {
        $groupId = $request->get('group_id');

        if ($groupId) {
            $data['permission_features'] = $this->repository->getByGroup($groupId);
        } else {
            $data['permission_features'] = $this->repository->getAllGrouped();
        }

        $data['feature_groups'] = $this->featureGroupRepository->getAll();
        $data['selected_group'] = $groupId;
        $data['title'] = ___('common.Permission Features');

        return view('backend.features.permissions.index', compact('data'));
    }

    /**
     * Show the form for creating a new permission feature
     */
    public function create()
    {
        $data['feature_groups'] = $this->featureGroupRepository->getActive();
        $data['permissions'] = Permission::orderBy('name')->get();
        $data['title'] = ___('common.Create Permission Feature');

        return view('backend.features.permissions.form', compact('data'));
    }

    /**
     * Store a newly created permission feature
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,id|unique:permission_features,permission_id',
            'feature_group_id' => 'required|exists:feature_groups,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_premium' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
        ]);

        // Set defaults
        $validated['is_premium'] = $validated['is_premium'] ?? false;

        if (!isset($validated['position'])) {
            $validated['position'] = $this->repository->getMaxPositionInGroup($validated['feature_group_id']) + 1;
        }

        // Auto-generate name from permission if not provided
        if (empty($validated['name'])) {
            $permission = Permission::find($validated['permission_id']);
            $validated['name'] = ucwords(str_replace('_', ' ', $permission->name));
        }

        $result = $this->repository->create($validated);

        if ($result) {
            // Clear cache
            Cache::forget('feature_groups_with_features');

            return redirect()->route('permission-features.index')
                ->with('success', ___('common.Permission feature created successfully'));
        }

        return back()->withInput()
            ->with('danger', ___('common.Failed to create permission feature'));
    }

    /**
     * Show the form for editing the specified permission feature
     */
    public function edit($id)
    {
        $data['permission_feature'] = $this->repository->findById($id);

        if (!$data['permission_feature']) {
            return redirect()->route('permission-features.index')
                ->with('danger', ___('common.Permission feature not found'));
        }

        $data['feature_groups'] = $this->featureGroupRepository->getActive();
        $data['permissions'] = Permission::orderBy('name')->get();
        $data['title'] = ___('common.Edit Permission Feature');

        return view('backend.features.permissions.form', compact('data'));
    }

    /**
     * Update the specified permission feature
     */
    public function update(Request $request, $id)
    {
        $permissionFeature = $this->repository->findById($id);

        if (!$permissionFeature) {
            return redirect()->route('permission-features.index')
                ->with('danger', ___('common.Permission feature not found'));
        }

        $validated = $request->validate([
            'permission_id' => 'required|exists:permissions,id|unique:permission_features,permission_id,' . $id,
            'feature_group_id' => 'required|exists:feature_groups,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_premium' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
        ]);

        // Set defaults
        $validated['is_premium'] = $validated['is_premium'] ?? false;

        // Auto-generate name from permission if not provided
        if (empty($validated['name'])) {
            $permission = Permission::find($validated['permission_id']);
            $validated['name'] = ucwords(str_replace('_', ' ', $permission->name));
        }

        $result = $this->repository->update($id, $validated);

        if ($result) {
            // Clear cache
            Cache::forget('feature_groups_with_features');

            return redirect()->route('permission-features.index')
                ->with('success', ___('common.Permission feature updated successfully'));
        }

        return back()->withInput()
            ->with('danger', ___('common.Failed to update permission feature'));
    }

    /**
     * Remove the specified permission feature
     */
    public function destroy($id)
    {
        $result = $this->repository->delete($id);

        if ($result['status']) {
            // Clear cache
            Cache::forget('feature_groups_with_features');

            return response()->json([
                $result['message'],
                'success',
                ___('alert.deleted'),
                ___('alert.OK')
            ]);
        }

        return response()->json([
            $result['message'],
            'error',
            ___('alert.oops'),
        ]);
    }

    /**
     * Bulk assign permission features
     */
    public function bulkAssign(Request $request)
    {
        $validated = $request->validate([
            'feature_group_id' => 'required|exists:feature_groups,id',
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'required|exists:permissions,id',
            'is_premium' => 'nullable|boolean',
        ]);

        $created = 0;
        $skipped = 0;

        foreach ($validated['permission_ids'] as $index => $permissionId) {
            // Check if already exists
            if ($this->repository->existsByPermission($permissionId)) {
                $skipped++;
                continue;
            }

            $permission = Permission::find($permissionId);

            $data = [
                'permission_id' => $permissionId,
                'feature_group_id' => $validated['feature_group_id'],
                'name' => ucwords(str_replace('_', ' ', $permission->name)),
                'is_premium' => $validated['is_premium'] ?? false,
                'position' => $index,
                'status' => true,
            ];

            if ($this->repository->create($data)) {
                $created++;
            }
        }

        // Clear cache
        Cache::forget('feature_groups_with_features');

        $message = sprintf(
            ___('common.Bulk assignment completed: %d created, %d skipped'),
            $created,
            $skipped
        );

        return redirect()->route('permission-features.index')
            ->with('success', $message);
    }
}
