<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounts\ExpenseCategory\ExpenseCategoryStoreRequest;
use App\Http\Requests\Accounts\ExpenseCategory\ExpenseCategoryUpdateRequest;
use App\Repositories\Accounts\ExpenseCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ExpenseCategoryController extends Controller
{
    private $expenseCategoryRepo;

    public function __construct(ExpenseCategoryRepository $expenseCategoryRepo)
    {
        if (!Schema::hasTable('settings') && !Schema::hasTable('users')) {
            abort(400);
        }
        $this->expenseCategoryRepo = $expenseCategoryRepo;
    }

    /**
     * Display a listing of expense categories.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data['categories'] = $this->expenseCategoryRepo->getAll();
        $data['title'] = ___('account.expense_categories');
        return view('backend.accounts.expense-category.index', compact('data'));
    }

    /**
     * Show the form for creating a new expense category.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $data['title'] = ___('account.create_expense_category');
        return view('backend.accounts.expense-category.create', compact('data'));
    }

    /**
     * Store a newly created expense category.
     *
     * @param ExpenseCategoryStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(ExpenseCategoryStoreRequest $request)
    {
        $result = $this->expenseCategoryRepo->store($request);
        if ($result['status']) {
            return redirect()->route('expense-category.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    /**
     * Show the form for editing an expense category.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $data['category'] = $this->expenseCategoryRepo->show($id);
        $data['title'] = ___('account.edit_expense_category');
        return view('backend.accounts.expense-category.edit', compact('data'));
    }

    /**
     * Update the specified expense category.
     *
     * @param ExpenseCategoryUpdateRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ExpenseCategoryUpdateRequest $request, $id)
    {
        $result = $this->expenseCategoryRepo->update($request, $id);
        if ($result['status']) {
            return redirect()->route('expense-category.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    /**
     * Remove the specified expense category.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        $result = $this->expenseCategoryRepo->destroy($id);
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
