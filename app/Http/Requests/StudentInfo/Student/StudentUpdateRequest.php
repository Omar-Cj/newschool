<?php

namespace App\Http\Requests\StudentInfo\Student;

use Illuminate\Foundation\Http\FormRequest;

class StudentUpdateRequest extends FormRequest
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
        // Get the student record to access user_id
        $student = \App\Models\StudentInfo\Student::find($this->id);
        $userId = $student ? $student->user_id : null;

        $mobile = '';
        if (!empty($this->mobile)) {
            $mobile = $userId ? 'max:255|unique:users,phone,' . $userId : 'max:255';
        }

        $email = '';
        if (!empty($this->email)) {
            $email = $userId ? 'max:255|unique:users,email,' . $userId : 'max:255';
        }

        $username = '';
        if (!empty($this->username)) {
            $username = $userId ? 'unique:users,username,' . $userId . ',id' : 'unique:users,username';
        }

        return [
            'mobile'         => $mobile,
            'email'          => $email,
            'first_name'     => 'required|max:255',
            'last_name'      => 'required|max:255',
            'grade' => 'required|in:KG-1,KG-2,Grade1,Grade2,Grade3,Grade4,Grade5,Grade6,Grade7,Grade8,Form1,Form2,Form3,Form4',
            'class'          => 'required|max:255',
            'section'        => 'required|max:255',
            'date_of_birth'  => 'nullable|date',
            'admission_date' => 'required|date',
            'parent'         => 'required|max:255',
            'status'         => 'required|max:255',
            'username'       => $username,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'previous_school_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Service validation rules
            'services' => 'nullable|array',
            'services.*.fee_type_id' => 'required_with:services.*|exists:fees_types,id',
            'services.*.amount' => 'required_with:services.*|numeric|min:0',
            'services.*.discount_type' => 'required_with:services.*|in:none,percentage,fixed',
            'services.*.discount_value' => 'nullable|numeric|min:0',
            'services.*.is_active' => 'required_with:services.*|boolean'
        ];
    }
}
