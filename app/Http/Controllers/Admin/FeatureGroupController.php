<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\FeatureGroupRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FeatureGroupController extends Controller
{
    private $repository;

    public function __construct(FeatureGroupRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of feature groups
     */
    public function index()
    {
        $data['feature_groups'] = $this->repository->getAllWithFeatureCount();
        $data['title'] = ___('common.Feature Groups');

        return view('backend.features.groups.index', compact('data'));
    }

    /**
     * Show the form for creating a new feature group
     */
    public function create()
    {
        $data['title'] = ___('common.Create Feature Group');
        $data['icons'] = $this->getAvailableIcons();

        return view('backend.features.groups.form', compact('data'));
    }

    /**
     * Store a newly created feature group
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:feature_groups,slug',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'position' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Set default position if not provided
        if (!isset($validated['position'])) {
            $validated['position'] = $this->repository->getMaxPosition() + 1;
        }

        $result = $this->repository->create($validated);

        if ($result) {
            // Clear cache
            Cache::forget('feature_groups_with_features');

            return redirect()->route('feature-groups.index')
                ->with('success', ___('common.Feature group created successfully'));
        }

        return back()->withInput()
            ->with('danger', ___('common.Failed to create feature group'));
    }

    /**
     * Show the form for editing the specified feature group
     */
    public function edit($id)
    {
        $data['feature_group'] = $this->repository->findById($id);

        if (!$data['feature_group']) {
            return redirect()->route('feature-groups.index')
                ->with('danger', ___('common.Feature group not found'));
        }

        $data['title'] = ___('common.Edit Feature Group');
        $data['icons'] = $this->getAvailableIcons();

        return view('backend.features.groups.form', compact('data'));
    }

    /**
     * Update the specified feature group
     */
    public function update(Request $request, $id)
    {
        $featureGroup = $this->repository->findById($id);

        if (!$featureGroup) {
            return redirect()->route('feature-groups.index')
                ->with('danger', ___('common.Feature group not found'));
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:feature_groups,slug,' . $id,
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'position' => 'nullable|integer|min:0',
            'status' => 'required|boolean',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $result = $this->repository->update($id, $validated);

        if ($result) {
            // Clear cache
            Cache::forget('feature_groups_with_features');

            return redirect()->route('feature-groups.index')
                ->with('success', ___('common.Feature group updated successfully'));
        }

        return back()->withInput()
            ->with('danger', ___('common.Failed to update feature group'));
    }

    /**
     * Remove the specified feature group
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
     * Reorder feature groups
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:feature_groups,id',
        ]);

        $result = $this->repository->reorder($validated['order']);

        if ($result) {
            // Clear cache
            Cache::forget('feature_groups_with_features');

            return response()->json([
                'success' => true,
                'message' => ___('common.Feature groups reordered successfully')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => ___('common.Failed to reorder feature groups')
        ], 422);
    }

    /**
     * Get available Font Awesome icons
     */
    private function getAvailableIcons()
    {
        return [
            'fa-solid fa-graduation-cap' => 'Graduation Cap',
            'fa-solid fa-book' => 'Book',
            'fa-solid fa-users' => 'Users',
            'fa-solid fa-calendar' => 'Calendar',
            'fa-solid fa-chart-line' => 'Chart',
            'fa-solid fa-bell' => 'Bell',
            'fa-solid fa-cog' => 'Settings',
            'fa-solid fa-database' => 'Database',
            'fa-solid fa-shield-alt' => 'Shield',
            'fa-solid fa-mobile-alt' => 'Mobile',
            'fa-solid fa-envelope' => 'Envelope',
            'fa-solid fa-clipboard' => 'Clipboard',
            'fa-solid fa-file-alt' => 'File',
            'fa-solid fa-user-graduate' => 'Student',
            'fa-solid fa-chalkboard-teacher' => 'Teacher',
            'fa-solid fa-building' => 'Building',
            'fa-solid fa-money-bill' => 'Money',
            'fa-solid fa-bus' => 'Bus',
            'fa-solid fa-book-reader' => 'Book Reader',
            'fa-solid fa-trophy' => 'Trophy',
        ];
    }
}
