<?php

namespace Modules\MainApp\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        // Get Super Admin ID for email uniqueness exclusion
        $superAdmin = \App\Models\User::where('school_id', Request()->id)
            ->where('role_id', 1) // SUPERADMIN
            ->first();
        $superAdminId = $superAdmin ? $superAdmin->id : 0;

        return [
            // 'sub_domain_key' => 'required|max:255|unique:schools,sub_domain_key,'.Request()->id,
            'name'           => 'required|max:255|unique:schools,name,'.Request()->id,
            // 'package'        => 'required',
            // 'address'        => 'required',
            // 'phone'          => 'required',
            // 'email'          => 'required',
            'status'         => 'required',
            // Super Admin fields (optional)
            'admin_email'    => 'nullable|email|max:255|unique:users,email,' . $superAdminId,
            'admin_password' => 'nullable|min:8|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'admin_email.unique' => 'This email is already in use by another user.',
            'admin_email.email' => 'Please enter a valid email address.',
            'admin_password.min' => 'Password must be at least 8 characters.',
            'admin_password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
