<?php

namespace Modules\MainApp\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
            'sub_domain_key'     => 'nullable|max:255|unique:schools,sub_domain_key',
            'name'               => 'required|max:255|unique:schools,name',
            'package'            => 'required',
            'address'            => 'required',
            'phone'              => 'required',
            'email'              => 'required',
            'number_of_branches' => 'required|integer|min:1|max:10',
            'admin_name'         => 'required|string|max:255',
            'admin_email'        => 'required|email|unique:users,email',
            'admin_password'     => 'required|string|min:6|confirmed'
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
            'number_of_branches.required' => 'The number of branches field is required.',
            'number_of_branches.integer'  => 'The number of branches must be a valid integer.',
            'number_of_branches.min'      => 'At least 1 branch is required.',
            'number_of_branches.max'      => 'Maximum 10 branches allowed per school.',
        ];
    }
}
