<?php

namespace App\Interfaces\Accounts;

interface ExpenseCategoryInterface
{
    /**
     * Get all expense categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all();

    /**
     * Get paginated expense categories.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAll();

    /**
     * Store a new expense category.
     *
     * @param mixed $request
     * @return array
     */
    public function store($request);

    /**
     * Show a specific expense category.
     *
     * @param int $id
     * @return \App\Models\Accounts\ExpenseCategory|null
     */
    public function show($id);

    /**
     * Update an expense category.
     *
     * @param mixed $request
     * @param int $id
     * @return array
     */
    public function update($request, $id);

    /**
     * Delete an expense category.
     *
     * @param int $id
     * @return array
     */
    public function destroy($id);

    /**
     * Get active expense categories.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveCategories();
}
