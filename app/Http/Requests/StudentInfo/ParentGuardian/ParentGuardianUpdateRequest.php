<?php

namespace App\Http\Requests\StudentInfo\ParentGuardian;

use Illuminate\Foundation\Http\FormRequest;

class ParentGuardianUpdateRequest extends FormRequest
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
            'guardian_mobile'        => 'required|max:255|unique:users,phone,'.Request()->user_id,
            'guardian_name'          => 'required|max:255',
            'status'                 => 'required|max:255',
            'guardian_profession'    => 'nullable|max:255',
            'guardian_email'         => 'nullable|max:255',
            'guardian_address'       => 'nullable|max:255',
            'guardian_relation'      => 'nullable|max:255',
            'guardian_place_of_work' => 'nullable|max:255',
            'guardian_position'      => 'nullable|max:255',
            'username'               => 'nullable|unique:users,username,'.Request()->user_id.',id',

        ];
    }
}
