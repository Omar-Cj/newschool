<?php

namespace App\Http\Requests\Accounts\ExpenseCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseCategoryUpdateRequest extends FormRequest
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
        $branchId = auth()->user()->branch_id ?? 1;
        $categoryId = $this->route('id');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('expense_categories')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                })->ignore($categoryId),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
            ],
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|integer|in:1,2',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name' => ___('account.category_name'),
            'code' => ___('account.category_code'),
            'description' => ___('account.description'),
            'status' => ___('common.status'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => ___('validation.required'),
            'name.unique' => ___('validation.unique'),
            'name.max' => ___('validation.max.string'),
        ];
    }
}
