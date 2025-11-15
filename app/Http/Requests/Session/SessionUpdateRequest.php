<?php

namespace App\Http\Requests\Session;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SessionUpdateRequest extends FormRequest
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
                Rule::unique('sessions')
                    ->where(function ($query) use ($schoolId) {
                        return $query->where('school_id', $schoolId);
                    })
                    ->ignore(Request()->id),
            ],
            'start_date'   => 'required|max:255',
            'end_date'     => 'required|max:255',
            'status'       => 'required'
        ];
    }
}
