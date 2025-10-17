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
}
