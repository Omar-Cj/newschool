<?php

namespace App\Http\Requests\Academic\Shift;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShiftUpdateRequest extends FormRequest
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

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shifts')
                    ->where(function ($query) use ($schoolId) {
                        return $query->where('school_id', $schoolId);
                    })
                    ->ignore(Request()->id),
            ],
            'status' => 'required'
        ];
    }
}
