<?php

namespace App\Http\Requests\Staff\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentStoreRequest extends FormRequest
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
        $schoolId = auth()->user()->school_id;
        $branchId = auth()->user()->branch_id;

        return [
            'name' => [
                'required',
                Rule::unique('departments', 'name')
                    ->where('school_id', $schoolId)
                    ->where('branch_id', $branchId),
            ],
            'status' => 'required'
        ];
    }
}
