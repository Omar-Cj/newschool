<?php

namespace App\Http\Requests\Accounts\Expense;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'invoice_number' => 'nullable|string|max:100',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'description' => 'nullable|string|max:1000',
            'journal_id' => ['required', 'exists:journals,id'],
        ];
    }
}
