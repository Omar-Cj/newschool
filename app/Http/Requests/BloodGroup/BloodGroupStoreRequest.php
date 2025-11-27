<?php

namespace App\Http\Requests\BloodGroup;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BloodGroupStoreRequest extends FormRequest
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
            'name'   => [
                'required',
                'max:255',
                Rule::unique('blood_groups', 'name')
                    ->where('school_id', $schoolId)
                    ->where('branch_id', $branchId)
            ],
            'status' => 'required'
        ];
    }
}
