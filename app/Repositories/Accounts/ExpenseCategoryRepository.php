<?php

namespace App\Repositories\Accounts;

use App\Enums\Settings;
use App\Models\Accounts\ExpenseCategory;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Accounts\ExpenseCategoryInterface;

class ExpenseCategoryRepository implements ExpenseCategoryInterface
{
    use ReturnFormatTrait;

    private $expenseCategory;

    public function __construct(ExpenseCategory $expenseCategory)
    {
        $this->expenseCategory = $expenseCategory;
    }

    /**
     * Get all expense categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->expenseCategory->active()->get();
    }

    /**
     * Get paginated expense categories.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll()
    {
        return $this->expenseCategory->latest()->paginate(Settings::PAGINATE);
    }

    /**
     * Store a new expense category.
     *
     * @param mixed $request
     * @return array
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            $category = new $this->expenseCategory;
            $category->name = $request->name;
            $category->code = $request->code;
            $category->description = $request->description;
            $category->status = $request->status ?? \App\Enums\Status::ACTIVE;
            $category->save();

            DB::commit();
            return $this->responseWithSuccess(___('alert.created_successfully'), $category);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    /**
     * Show a specific expense category.
     *
     * @param int $id
     * @return \App\Models\Accounts\ExpenseCategory|null
     */
    public function show($id)
    {
        return $this->expenseCategory->find($id);
    }

    /**
     * Update an expense category.
     *
     * @param mixed $request
     * @param int $id
     * @return array
     */
    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $category = $this->expenseCategory->findOrFail($id);
            $category->name = $request->name;
            $category->code = $request->code;
            $category->description = $request->description;
            $category->status = $request->status ?? $category->status;
            $category->save();

            DB::commit();
            return $this->responseWithSuccess(___('alert.updated_successfully'), $category);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    /**
     * Delete an expense category.
     *
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $category = $this->expenseCategory->find($id);

            // Check if category has expenses
            if ($category->expenses()->count() > 0) {
                return $this->responseWithError(___('alert.cannot_delete_category_with_expenses'), []);
            }

            $category->delete();

            DB::commit();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    /**
     * Get active expense categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveCategories()
    {
        return $this->expenseCategory->active()->orderBy('name', 'asc')->get();
    }

    /**
     * Get AJAX data for DataTables server-side processing.
     *
     * @param mixed $request
     * @return array
     */
    public function getAjaxData($request)
    {
        // DataTables parameters
        $draw = intval($request->input('draw'));
        $start = intval($request->input('start'));
        $length = intval($request->input('length'));
        $searchValue = $request->input('search.value');
        $orderColumn = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');

        // Custom filter parameters
        $statusFilter = $request->input('status');
        $hasExpensesFilter = $request->input('has_expenses');
        $keywordFilter = $request->input('keyword');

        // Base query with expense count
        $query = $this->expenseCategory->withCount('expenses');

        // Apply status filter
        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('status', intval($statusFilter));
        }

        // Apply has_expenses filter
        if ($hasExpensesFilter === 'yes') {
            $query->whereHas('expenses');
        } elseif ($hasExpensesFilter === 'no') {
            $query->whereDoesntHave('expenses');
        }
        // If 'all' or empty, don't apply expense filter

        // Apply keyword filter (searches in name and code)
        if (!empty($keywordFilter)) {
            $query->where(function($q) use ($keywordFilter) {
                $q->where('name', 'LIKE', "%{$keywordFilter}%")
                  ->orWhere('code', 'LIKE', "%{$keywordFilter}%");
            });
        }

        // Apply DataTables global search
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'LIKE', "%{$searchValue}%")
                  ->orWhere('code', 'LIKE', "%{$searchValue}%")
                  ->orWhere('description', 'LIKE', "%{$searchValue}%");
            });
        }

        // Count total and filtered records
        $totalRecords = $this->expenseCategory->count();
        $filteredRecords = $query->count();

        // Apply ordering
        $columns = ['id', 'name', 'code', 'description', 'status', 'expenses_count'];
        if (isset($columns[$orderColumn])) {
            $orderField = $columns[$orderColumn];
            $query->orderBy($orderField, $orderDir);
        } else {
            $query->latest();
        }

        // Apply pagination
        $categories = $query->offset($start)->limit($length)->get();

        // Format data for DataTables
        $data = [];
        $key = $start + 1;

        foreach ($categories as $row) {
            // Generate status badge HTML
            $statusHtml = '';
            if ($row->status == \App\Enums\Status::ACTIVE) {
                $statusHtml = '<span class="badge badge-success">' . ___('common.active') . '</span>';
            } else {
                $statusHtml = '<span class="badge badge-danger">' . ___('common.inactive') . '</span>';
            }

            // Generate actions dropdown HTML
            $actionsHtml = '';
            if (hasPermission('expense_category_update') || hasPermission('expense_category_delete')) {
                $actionsHtml = '<div class="dropdown dropdown-action">
                    <button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-ellipsis"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">';

                if (hasPermission('expense_category_update')) {
                    $actionsHtml .= '<li><a class="dropdown-item" href="' . route('expense-category.edit', $row->id) . '">
                        <span class="icon mr-8"><i class="fa-solid fa-pen-to-square"></i></span>' . ___('common.edit') . '</a></li>';
                }

                if (hasPermission('expense_category_delete')) {
                    $actionsHtml .= '<li><a class="dropdown-item" href="javascript:void(0);" onclick="delete_row(\'expense-category/delete\', ' . $row->id . ')">
                        <span class="icon mr-8"><i class="fa-solid fa-trash-can"></i></span><span>' . ___('common.delete') . '</span></a></li>';
                }

                $actionsHtml .= '</ul></div>';
            }

            // Build row data array
            $rowData = [
                $key++,                                           // Serial number
                htmlspecialchars($row->name ?? ''),              // Name
                htmlspecialchars($row->code ?? ''),              // Code
                \Illuminate\Support\Str::limit($row->description ?? '', 50), // Description (truncated)
                $statusHtml,                                      // Status badge
                $row->expenses_count,                             // Expenses count
            ];

            // Add actions column if user has permissions
            if (hasPermission('expense_category_update') || hasPermission('expense_category_delete')) {
                $rowData[] = $actionsHtml;
            }

            $data[] = $rowData;
        }

        // Return DataTables format
        return [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }
}
