<?php

namespace App\Http\Requests\Examination\Type;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExamTypeUpdateRequest extends FormRequest
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
        $branchId = $this->user()->branch_id ?? 1;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('exam_types')
                    ->where(function ($query) use ($schoolId, $branchId) {
                        return $query->where('school_id', $schoolId)
                                     ->where('branch_id', $branchId);
                    })
                    ->ignore(Request()->id),
            ],
            'status'    => 'required'
        ];
    }
}
