<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Accounts\Expense;
use App\Models\Accounts\AccountHead;
use App\Models\Accounts\ExpenseCategory;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration converts existing expense_head references to expense_category_id.
     * It creates a new category for each unique account head used in expenses.
     *
     * @return void
     */
    public function up()
    {
        // Get all expenses with their account heads
        $expenses = Expense::with('head')->whereNotNull('expense_head')->get();

        if ($expenses->isEmpty()) {
            return;
        }

        // Group expenses by account head and branch
        $groupedExpenses = $expenses->groupBy(function ($expense) {
            return $expense->expense_head . '_' . ($expense->branch_id ?? 1);
        });

        foreach ($groupedExpenses as $key => $expenseGroup) {
            $firstExpense = $expenseGroup->first();
            $accountHead = $firstExpense->head;

            if (!$accountHead) {
                continue;
            }

            // Check if category already exists for this head and branch
            $category = ExpenseCategory::where('name', $accountHead->name)
                ->where('branch_id', $firstExpense->branch_id ?? 1)
                ->first();

            // Create category if it doesn't exist
            if (!$category) {
                $category = ExpenseCategory::create([
                    'name' => $accountHead->name,
                    'code' => 'MIGRATED-' . $accountHead->id,
                    'description' => 'Migrated from account head: ' . $accountHead->name,
                    'status' => $accountHead->status ?? \App\Enums\Status::ACTIVE,
                    'branch_id' => $firstExpense->branch_id ?? 1,
                ]);
            }

            // Update all expenses in this group
            foreach ($expenseGroup as $expense) {
                $expense->expense_category_id = $category->id;
                $expense->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * This will restore expense_head values from the categories.
     * Note: This may not perfectly restore the original state if multiple expenses
     * were consolidated under one category.
     *
     * @return void
     */
    public function down()
    {
        // Get all expenses with categories
        $expenses = Expense::with('category')->whereNotNull('expense_category_id')->get();

        foreach ($expenses as $expense) {
            if ($expense->category && strpos($expense->category->code, 'MIGRATED-') === 0) {
                // Extract original account head ID from the code
                $headId = str_replace('MIGRATED-', '', $expense->category->code);
                $expense->expense_head = $headId;
                $expense->expense_category_id = null;
                $expense->save();
            }
        }

        // Optionally delete migrated categories
        ExpenseCategory::where('code', 'like', 'MIGRATED-%')->delete();
    }
};
