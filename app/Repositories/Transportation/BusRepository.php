<?php

namespace App\Repositories\Transportation;

use App\Enums\Settings;
use App\Enums\Status;
use App\Models\Transportation\Bus;
use App\Interfaces\Transportation\BusInterface;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;

/**
 * Bus Repository
 *
 * Handles all database operations for the Bus model.
 * Implements the BusInterface contract.
 */
class BusRepository implements BusInterface
{
    use ReturnFormatTrait;

    /**
     * Bus model instance
     *
     * @var Bus
     */
    private $model;

    /**
     * BusRepository constructor.
     *
     * @param Bus $model Bus model instance
     */
    public function __construct(Bus $model)
    {
        $this->model = $model;
    }

    /**
     * Get all active buses.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->active()->get();
    }

    /**
     * Get paginated buses.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll()
    {
        return $this->model->paginate(Settings::PAGINATE);
    }

    /**
     * Store a new bus.
     *
     * @param mixed $request Request object containing bus data
     * @return array Response array with status, message, and data
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $bus = new $this->model;
            $bus->area_name = $request->area_name;
            $bus->bus_number = $request->bus_number;
            $bus->capacity = $request->capacity;
            $bus->driver_name = $request->driver_name;
            $bus->driver_phone = $request->driver_phone;
            $bus->license_plate = $request->license_plate;
            $bus->status = $request->status ?? Status::ACTIVE;
            $bus->branch_id = $request->branch_id ?? auth()->user()->branch_id;
            $bus->save();

            DB::commit();

            return $this->responseWithSuccess(___('alert.created_successfully'), $bus);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    /**
     * Show a specific bus.
     *
     * @param int $id Bus ID
     * @return \App\Models\Transportation\Bus|null
     */
    public function show($id)
    {
        return $this->model->find($id);
    }

    /**
     * Update an existing bus.
     *
     * @param mixed $request Request object containing updated bus data
     * @param int $id Bus ID
     * @return array Response array with status, message, and data
     */
    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $bus = $this->model->findOrFail($id);
            $bus->area_name = $request->area_name;
            $bus->bus_number = $request->bus_number;
            $bus->capacity = $request->capacity;
            $bus->driver_name = $request->driver_name;
            $bus->driver_phone = $request->driver_phone;
            $bus->license_plate = $request->license_plate;
            $bus->status = $request->status ?? $bus->status;
            $bus->branch_id = $request->branch_id ?? $bus->branch_id;
            $bus->save();

            DB::commit();

            return $this->responseWithSuccess(___('alert.updated_successfully'), $bus);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    /**
     * Delete a bus.
     *
     * Prevents deletion if students are assigned to the bus.
     *
     * @param int $id Bus ID
     * @return array Response array with status, message, and data
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $bus = $this->model->findOrFail($id);

            // Check if bus has students assigned
            if ($bus->students()->count() > 0) {
                return $this->responseWithError(___('alert.cannot_delete_bus_with_students'), []);
            }

            $bus->delete();

            DB::commit();

            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    /**
     * Get active buses only.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveBuses()
    {
        return $this->model->active()->orderBy('area_name')->get();
    }

    /**
     * Get data for DataTables AJAX requests.
     *
     * Handles server-side DataTables processing with:
     * - Pagination (start, length)
     * - Ordering by columns
     * - Global search across multiple fields
     * - Custom filters (status, has_students, keyword)
     *
     * @param mixed $request Request object containing DataTables parameters
     * @return array DataTables formatted response array
     */
    public function getAjaxData($request)
    {
        try {
            // Base query with student count
            $query = $this->model->withCount('students');

            // Global search across multiple columns
            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = $request->search['value'];
                $query->where(function ($q) use ($searchValue) {
                    $q->where('area_name', 'like', "%{$searchValue}%")
                        ->orWhere('bus_number', 'like', "%{$searchValue}%")
                        ->orWhere('driver_name', 'like', "%{$searchValue}%")
                        ->orWhere('license_plate', 'like', "%{$searchValue}%");
                });
            }

            // Custom filter: status
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            // Custom filter: has_students (buses with/without students)
            if ($request->has('has_students') && $request->has_students !== '') {
                if ($request->has_students == 'yes') {
                    $query->has('students');
                } elseif ($request->has_students == 'no') {
                    $query->doesntHave('students');
                }
            }

            // Custom filter: keyword search
            if ($request->has('keyword') && !empty($request->keyword)) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('area_name', 'like', "%{$keyword}%")
                        ->orWhere('bus_number', 'like', "%{$keyword}%")
                        ->orWhere('driver_name', 'like', "%{$keyword}%")
                        ->orWhere('license_plate', 'like', "%{$keyword}%");
                });
            }

            // Total records before filtering
            $totalRecords = $this->model->count();

            // Total records after filtering
            $filteredRecords = $query->count();

            // Column ordering
            if ($request->has('order') && count($request->order) > 0) {
                $orderColumnIndex = $request->order[0]['column'];
                $orderDirection = $request->order[0]['dir'];

                // Map column index to database column name
                $columns = [
                    0 => 'id',
                    1 => 'area_name',
                    2 => 'bus_number',
                    3 => 'capacity',
                    4 => 'driver_name',
                    5 => 'license_plate',
                    6 => 'status',
                    7 => 'students_count',
                ];

                if (isset($columns[$orderColumnIndex])) {
                    $orderColumn = $columns[$orderColumnIndex];
                    $query->orderBy($orderColumn, $orderDirection);
                } else {
                    $query->orderBy('area_name', 'asc');
                }
            } else {
                $query->orderBy('area_name', 'asc');
            }

            // Pagination
            $start = $request->start ?? 0;
            $length = $request->length ?? 10;
            $buses = $query->skip($start)->take($length)->get();

            // Format data for DataTables (NUMERIC ARRAY format matching Expense pattern)
            $data = [];
            $counter = $start + 1; // Serial number starts from current page

            foreach ($buses as $bus) {
                $row = [];

                // Index 0: Serial number
                $row[] = $counter++;

                // Index 1: Area name (required field)
                $row[] = e($bus->area_name);

                // Index 2: Bus number (optional)
                $row[] = $bus->bus_number ? e($bus->bus_number) : '—';

                // Index 3: Capacity (optional)
                $row[] = $bus->capacity ?? '—';

                // Index 4: Driver name (optional)
                $row[] = $bus->driver_name ? e($bus->driver_name) : '—';

                // Index 5: License plate (optional)
                $row[] = $bus->license_plate ? e($bus->license_plate) : '—';

                // Index 6: Status badge HTML
                $statusBadge = $bus->status == Status::ACTIVE
                    ? '<span class="badge badge-success">' . ___('common.active') . '</span>'
                    : '<span class="badge badge-danger">' . ___('common.inactive') . '</span>';
                $row[] = $statusBadge;

                // Index 7: Students count with capacity indicator
                $studentsCount = $bus->students_count;
                $capacity = $bus->capacity;

                if ($capacity) {
                    $percentage = ($studentsCount / $capacity) * 100;
                    $badgeClass = $percentage >= 100 ? 'badge-danger' : ($percentage >= 80 ? 'badge-warning' : 'badge-info');
                    $studentsInfo = '<span class="badge badge-sm ' . $badgeClass . '">' . $studentsCount . ' / ' . $capacity . '</span>';
                } else {
                    $studentsInfo = '<span class="badge badge-sm badge-secondary">' . $studentsCount . '</span>';
                }
                $row[] = $studentsInfo;

                // Index 8: Actions HTML (conditional based on permissions)
                if (hasPermission('bus_update') || hasPermission('bus_delete')) {
                    $actions = '<div class="dropdown dropdown-action">';
                    $actions .= '<button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">';
                    $actions .= '<i class="fa-solid fa-ellipsis"></i>';
                    $actions .= '</button>';
                    $actions .= '<ul class="dropdown-menu dropdown-menu-end">';

                    if (hasPermission('bus_update')) {
                        $actions .= '<li>';
                        $actions .= '<a class="dropdown-item" href="' . route('bus.edit', $bus->id) . '">';
                        $actions .= '<i class="far fa-edit text-info"></i> ' . ___('common.edit');
                        $actions .= '</a>';
                        $actions .= '</li>';
                    }

                    if (hasPermission('bus_delete')) {
                        $actions .= '<li>';
                        $actions .= '<a class="dropdown-item" href="javascript:void(0);" onclick="delete_row(\'bus/delete\', ' . $bus->id . ')">';
                        $actions .= '<i class="far fa-trash-alt text-danger"></i> ' . ___('common.delete');
                        $actions .= '</a>';
                        $actions .= '</li>';
                    }

                    $actions .= '</ul>';
                    $actions .= '</div>';
                    $row[] = $actions;
                }

                $data[] = $row;
            }

            // DataTables response format
            return [
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data,
            ];
        } catch (\Throwable $th) {
            return [
                'draw' => intval($request->draw ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $th->getMessage(),
            ];
        }
    }
}
