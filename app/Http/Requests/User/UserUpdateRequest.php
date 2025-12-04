<?php

namespace App\Http\Requests\User;

use Illuminate\Support\Facades\Request;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
        // dd(Request()->user_id);
        return [
            'role'         => 'required',
            'designation'  => 'required',
            'department'   => 'required',
            'first_name'   => 'required|max:25',
            'email'        => 'required|unique:users,email,'.Request()->user_id,
            'gender'       => 'required',
            'dob'          => 'nullable|date',
            'phone'        => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|max:11',
            'status'       => 'required',
            'image'        => 'max:2048',
            // Password fields - optional
            'password'     => 'nullable|min:8|confirmed',
        ];
    }

    /**
     * Get custom validation messages
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
