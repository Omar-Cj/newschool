<?php

namespace App\Http\Requests\Fees\Type;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeesTypeStoreRequest extends FormRequest
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
            'name'                    => [
                'required',
                'max:255',
                Rule::unique('fees_types', 'name')
                    ->where('school_id', $schoolId)
                    ->where('branch_id', $branchId)
            ],
            'code'                    => [
                'nullable',
                'max:50',
                Rule::unique('fees_types', 'code')
                    ->where('school_id', $schoolId)
                    ->where('branch_id', $branchId)
            ],
            'description'             => 'nullable|max:1000',
            'academic_level'          => 'required|in:all,kg,primary,secondary,high_school',
            'category'                => 'required|in:academic,transport,meal,accommodation,activity,other',
            'amount'                  => 'required|numeric|min:0',
            'due_date_offset'         => 'nullable|integer|min:0|max:365',
            'is_mandatory_for_level'  => 'nullable|boolean',
            'status'                  => 'required'
        ];
    }
}
