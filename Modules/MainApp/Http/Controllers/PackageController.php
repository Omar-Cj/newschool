<?php

namespace Modules\MainApp\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Modules\MainApp\Http\Requests\Package\StoreRequest;
use Modules\MainApp\Http\Requests\Package\UpdateRequest;
use Modules\MainApp\Http\Repositories\PackageRepository;
use App\Services\FeatureManagementService;
use App\Repositories\FeatureGroupRepository;

class PackageController extends Controller
{
    private $repo;
    private $featureManagementService;
    private $featureGroupRepo;

    function __construct(
        PackageRepository $repo,
        FeatureManagementService $featureManagementService,
        FeatureGroupRepository $featureGroupRepo
    )
    {

        if (!Schema::hasTable('settings') && !Schema::hasTable('users')  ) {
            abort(400);
        }
        $this->repo = $repo;
        $this->featureManagementService = $featureManagementService;
        $this->featureGroupRepo = $featureGroupRepo;
    }

    public function index()
    {
        $data['packages'] = $this->repo->getAll();
        $data['title']    = ___('settings.Packages');
        return view('mainapp::package.index', compact('data'));
    }

    public function create()
    {
        $data['feature_groups'] = $this->featureGroupRepo->getActiveGroupsWithFeatures();
        $data['title'] = ___('settings.Create package');
        return view('mainapp::package.create', compact('data'));
    }

    public function store(StoreRequest $request)
    {
        $result = $this->repo->store($request);

        if ($result['status']) {
            // Sync permission features if provided
            if ($request->has('permission_features')) {
                $this->featureManagementService->syncPackageFeatures(
                    $result['data'],
                    $request->input('permission_features', [])
                );
            }

            // Clear school caches
            $this->clearSchoolCaches();

            return redirect()->route('package.index')->with('success', $result['message']);
        }

        return back()->with('danger', $result['message']);
    }

    public function edit($id)
    {
        $data['feature_groups'] = $this->featureGroupRepo->getActiveGroupsWithFeatures();
        $data['package'] = $this->repo->show($id);
        $data['package_features'] = $this->featureManagementService->getPackageFeatureIds($id);
        $data['title'] = ___('settings.Edit package');
        return view('mainapp::package.edit', compact('data'));
    }

    public function update(UpdateRequest $request, $id)
    {
        $result = $this->repo->update($request, $id);

        if ($result['status']) {
            // Sync permission features if provided
            if ($request->has('permission_features')) {
                $this->featureManagementService->syncPackageFeatures(
                    $result['data'],
                    $request->input('permission_features', [])
                );
            }

            // Clear school caches
            $this->clearSchoolCaches();

            return redirect()->route('package.index')->with('success', $result['message']);
        }

        return back()->with('danger', $result['message']);
    }

    public function delete($id)
    {
        $result = $this->repo->destroy($id);

        if ($result['status']) {
            // Clear school caches
            $this->clearSchoolCaches();

            $success[0] = $result['message'];
            $success[1] = 'success';
            $success[2] = ___('alert.deleted');
            $success[3] = ___('alert.OK');
            return response()->json($success);
        } else {
            $success[0] = $result['message'];
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
            return response()->json($success);
        }
    }

    /**
     * Clear all school permission caches
     */
    private function clearSchoolCaches()
    {
        Cache::tags(['school_permissions'])->flush();
        Cache::forget('feature_groups_with_features');
    }
}
