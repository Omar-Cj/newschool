<?php

namespace App\Http\Requests\Academic\Subject;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubjectStoreRequest extends FormRequest
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
                Rule::unique('subjects', 'name')
                    ->where('school_id', $schoolId)
                    ->where('branch_id', $branchId)
                    ->where('type', $this->type),
            ],
            'type' => 'required',
            'status' => 'required|max:10',
            'code'   => 'required|max:50',
        ];

    }

    public function messages()
    {
        return [
            'name.unique' => 'The combination of name, type, school, and branch must be unique.',
        ];
    }
}
