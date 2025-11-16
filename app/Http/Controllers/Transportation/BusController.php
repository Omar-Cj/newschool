<?php

namespace App\Http\Controllers\Transportation;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transportation\Bus\BusStoreRequest;
use App\Http\Requests\Transportation\Bus\BusUpdateRequest;
use App\Repositories\Transportation\BusRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class BusController extends Controller
{
    private $busRepo;

    public function __construct(BusRepository $busRepo)
    {
        if (!Schema::hasTable('settings') && !Schema::hasTable('users')) {
            abort(400);
        }
        $this->busRepo = $busRepo;
    }

    /**
     * Display a listing of buses.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data['buses'] = $this->busRepo->getAll();
        $data['title'] = ___('transportation.bus_management');
        return view('backend.transportation.bus.index', compact('data'));
    }

    /**
     * Get AJAX data for DataTables server-side processing.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxBusData(Request $request)
    {
        try {
            $result = $this->busRepo->getAjaxData($request);
            return response()->json($result);
        } catch (\Throwable $th) {
            \Log::error('Error in ajaxBusData: ' . $th->getMessage(), [
                'request' => $request->all(),
                'error' => $th->getTraceAsString()
            ]);

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while loading data.'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new bus.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $data['title'] = ___('transportation.create_bus');
        return view('backend.transportation.bus.create', compact('data'));
    }

    /**
     * Store a newly created bus.
     *
     * @param BusStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BusStoreRequest $request)
    {
        $result = $this->busRepo->store($request);
        if ($result['status']) {
            return redirect()->route('bus.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    /**
     * Show the form for editing a bus.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $data['bus'] = $this->busRepo->show($id);
        $data['title'] = ___('transportation.edit_bus');
        return view('backend.transportation.bus.edit', compact('data'));
    }

    /**
     * Update the specified bus.
     *
     * @param BusUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(BusUpdateRequest $request, $id)
    {
        $result = $this->busRepo->update($request, $id);
        if ($result['status']) {
            return redirect()->route('bus.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    /**
     * Remove the specified bus.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $result = $this->busRepo->destroy($id);
        if ($result['status']) {
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
}
