<?php

namespace App\Http\Requests\Academic\Classes;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClassesStoreRequest extends FormRequest
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
        $schoolId = $this->user()->school_id;
        $branchId = $this->user()->branch_id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes', 'name')
                    ->where('school_id', $schoolId)
                    ->where('branch_id', $branchId),
            ],
            'status' => 'required',
            'academic_level' => 'required|in:kg,primary,secondary,high_school'
        ];
    }

    public function messages()
    {
        return [
            'academic_level.required' => 'Academic level is required for proper fee assignment.',
            'academic_level.in' => 'Please select a valid academic level.'
        ];
    }
}
