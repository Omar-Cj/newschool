<?php

namespace App\Repositories\Accounts;

use App\Enums\Settings;
use App\Models\Accounts\Expense;
use App\Traits\CommonHelperTrait;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Accounts\ExpenseInterface;

class ExpenseRepository implements ExpenseInterface
{
    use ReturnFormatTrait;
    use CommonHelperTrait;
    private $expense;

    public function __construct(Expense $expense)
    {
        $this->expense = $expense;
    }

    public function all()
    {
        return $this->expense->active()->where('session_id', setting('session'))->get();
    }

    public function getAll()
    {
        return $this->expense->latest()->where('session_id', setting('session'))->paginate(Settings::PAGINATE);
    }

    /**
     * Get expenses data for DataTables AJAX server-side processing.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getAjaxData($request)
    {
        try {
            // Base query with session filter and eager loading
            $query = $this->expense->with(['category', 'upload', 'journal'])
                ->where('session_id', setting('session'));

            // Apply filters
            // Category filter
            if ($request->filled('category_id')) {
                $query->where('expense_category_id', $request->category_id);
            }

            // Date range filters
            if ($request->filled('start_date')) {
                $query->whereDate('date', '>=', $request->start_date);
            }

            if ($request->filled('end_date')) {
                $query->whereDate('date', '<=', $request->end_date);
            }

            // Amount range filters
            if ($request->filled('min_amount')) {
                $query->where('amount', '>=', $request->min_amount);
            }

            if ($request->filled('max_amount')) {
                $query->where('amount', '<=', $request->max_amount);
            }

            // Keyword search (searches in name and invoice_number)
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where(function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('invoice_number', 'LIKE', "%{$keyword}%");
                });
            }

            // DataTables search functionality (global search)
            if ($request->filled('search.value')) {
                $searchValue = $request->input('search.value');
                $query->where(function ($q) use ($searchValue) {
                    $q->where('name', 'LIKE', "%{$searchValue}%")
                      ->orWhere('invoice_number', 'LIKE', "%{$searchValue}%")
                      ->orWhere('amount', 'LIKE', "%{$searchValue}%")
                      ->orWhereHas('category', function ($categoryQuery) use ($searchValue) {
                          $categoryQuery->where('name', 'LIKE', "%{$searchValue}%");
                      });
                });
            }

            // Get total count before filtering
            $recordsTotal = $this->expense->where('session_id', setting('session'))->count();

            // Get filtered count
            $recordsFiltered = $query->count();

            // Apply ordering
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'desc');

            $columns = ['id', 'name', 'expense_category_id', 'date', 'invoice_number', 'amount', 'upload_id', 'actions'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'id';

            if ($orderColumn === 'expense_category_id') {
                // Order by category name if sorting by category column
                $query->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
                      ->orderBy('expense_categories.name', $orderDirection)
                      ->select('expenses.*');
            } else {
                $query->orderBy($orderColumn, $orderDirection);
            }

            // Apply pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 10);
            $expenses = $query->skip($start)->take($length)->get();

            // Format data for DataTables
            $data = [];
            $counter = $start + 1;

            foreach ($expenses as $expense) {
                $row = [];

                // Serial number
                $row[] = $counter++;

                // Name
                $row[] = e($expense->name);

                // Category
                $categoryName = $expense->category->name ?? ($expense->head->name ?? '-');
                $row[] = e($categoryName);

                // Date
                $row[] = dateFormat($expense->date);

                // Invoice number
                $row[] = e($expense->invoice_number);

                // Amount with currency symbol
                $row[] = Setting('currency_symbol') . ' ' . number_format($expense->amount, 2);

                // Document download link
                if ($expense->upload && $expense->upload->path) {
                    $documentUrl = globalAsset($expense->upload->path);
                    $row[] = '<a href="' . $documentUrl . '" download class="btn btn-sm btn-primary">' . ___('common.download') . '</a>';
                } else {
                    $row[] = '-';
                }

                // Actions dropdown (only if user has permissions)
                $actions = '<div class="dropdown dropdown-action">
                    <button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-ellipsis"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">';

                if (hasPermission('expense_update')) {
                    $editUrl = route('expense.edit', $expense->id);
                    $actions .= '<li>
                        <a class="dropdown-item" href="' . $editUrl . '">
                            <span class="icon mr-8"><i class="fa-solid fa-pen-to-square"></i></span>
                            ' . ___('common.edit') . '
                        </a>
                    </li>';
                }

                if (hasPermission('expense_delete')) {
                    $actions .= '<li>
                        <a class="dropdown-item" href="javascript:void(0);" onclick="delete_row(\'expense/delete\', ' . $expense->id . ')">
                            <span class="icon mr-8"><i class="fa-solid fa-trash-can"></i></span>
                            <span>' . ___('common.delete') . '</span>
                        </a>
                    </li>';
                }

                $actions .= '</ul></div>';

                // Only add actions column if user has update or delete permissions
                if (hasPermission('expense_update') || hasPermission('expense_delete')) {
                    $row[] = $actions;
                }

                $data[] = $row;
            }

            // Return DataTables formatted response
            return [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ];

        } catch (\Throwable $th) {
            \Log::error('Expense AJAX data processing failed: ' . $th->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $th->getTraceAsString()
            ]);

            throw $th;
        }
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $expenseStore                      = new $this->expense;
            $expenseStore->session_id          = setting('session');
            $expenseStore->name                = $request->name;
            $expenseStore->expense_category_id = $request->expense_category_id;
            $expenseStore->date                = $request->date;
            $expenseStore->amount              = $request->amount;
            $expenseStore->invoice_number      = $request->invoice_number;
            $expenseStore->upload_id           = $this->UploadImageCreate($request->document, 'backend/uploads/expenses');
            $expenseStore->description         = $request->description;
            $expenseStore->journal_id          = $request->journal_id;
            $expenseStore->save();

            DB::commit();
            return $this->responseWithSuccess(___('alert.created_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Expense creation failed: ' . $th->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
                'exception' => $th->getTraceAsString()
            ]);
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function show($id)
    {
        return $this->expense->find($id);
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $expenseUpdate                      = $this->expense->findOrfail($id);
            $expenseUpdate->session_id          = setting('session');
            $expenseUpdate->name                = $request->name;
            $expenseUpdate->expense_category_id = $request->expense_category_id;
            $expenseUpdate->date                = $request->date;
            $expenseUpdate->amount              = $request->amount;
            $expenseUpdate->invoice_number      = $request->invoice_number;
            $expenseUpdate->upload_id           = $this->UploadImageUpdate($request->document, 'backend/uploads/expenses', $expenseUpdate->upload_id);
            $expenseUpdate->description         = $request->description;
            $expenseUpdate->journal_id          = $request->journal_id;
            $expenseUpdate->save();

            DB::commit();
            return $this->responseWithSuccess(___('alert.updated_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $expenseDestroy = $this->expense->find($id);
            $this->UploadImageDelete($expenseDestroy->upload_id);
            $expenseDestroy->delete();

            DB::commit();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }
}
