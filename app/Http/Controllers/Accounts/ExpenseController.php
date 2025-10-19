<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Http\Requests\Accounts\Expense\ExpenseStoreRequest;
use App\Http\Requests\Accounts\Expense\ExpenseUpdateRequest;
use App\Repositories\Accounts\AccountHeadRepository;
use App\Repositories\Accounts\ExpenseRepository;
use Modules\Journals\Repositories\JournalRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ExpenseController extends Controller
{
    private $expenseRepo, $expenseCategoryRepo, $journalRepo;

    function __construct(
        ExpenseRepository $expenseRepo,
        \App\Repositories\Accounts\ExpenseCategoryRepository $expenseCategoryRepo,
        JournalRepository $journalRepo
    ) {
        if (!Schema::hasTable('settings') && !Schema::hasTable('users')) {
            abort(400);
        }
        $this->expenseRepo = $expenseRepo;
        $this->expenseCategoryRepo = $expenseCategoryRepo;
        $this->journalRepo = $journalRepo;
    }

    public function index()
    {
        $data['expense'] = $this->expenseRepo->getAll();
        $data['categories'] = $this->expenseCategoryRepo->getActiveCategories();
        $data['title'] = ___('account.expense');
        return view('backend.accounts.expense.index', compact('data'));
    }

    /**
     * Handle AJAX request for DataTables server-side processing.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxExpenseData(Request $request)
    {
        try {
            $result = $this->expenseRepo->getAjaxData($request);
            return response()->json($result);
        } catch (\Throwable $th) {
            \Log::error('Expense AJAX data fetch failed: ' . $th->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
                'exception' => $th->getTraceAsString()
            ]);

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => ___('alert.something_went_wrong_please_try_again')
            ], 500);
        }
    }

    public function create()
    {
        $data['title'] = ___('account.create_expense');
        $data['categories'] = $this->expenseCategoryRepo->getActiveCategories();
        $data['journals'] = $this->journalRepo->getJournalsForDropdown();
        return view('backend.accounts.expense.create', compact('data'));
    }

    public function store(ExpenseStoreRequest $request)
    {
        $result = $this->expenseRepo->store($request);
        if($result['status']){
            return redirect()->route('expense.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function edit($id)
    {
        $data['categories'] = $this->expenseCategoryRepo->getActiveCategories();
        $data['expense'] = $this->expenseRepo->show($id);
        $data['title'] = ___('account.edit_expense');
        $data['journals'] = $this->journalRepo->getJournalsForDropdown();
        return view('backend.accounts.expense.edit', compact('data'));
    }

    public function update(ExpenseUpdateRequest $request, $id)
    {
        $result = $this->expenseRepo->update($request, $id);
        if($result['status']){
            return redirect()->route('expense.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function delete($id)
    {
        $result = $this->expenseRepo->destroy($id);
        if($result['status']):
            $success[0] = $result['message'];
            $success[1] = 'success';
            $success[2] = ___('alert.deleted');
            $success[3] = ___('alert.OK');
            return response()->json($success);
        else:
            $success[0] = $result['message'];
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
            return response()->json($success);
        endif;     
    }
}
