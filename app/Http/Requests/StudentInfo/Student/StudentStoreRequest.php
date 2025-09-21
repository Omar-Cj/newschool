<?php

namespace App\Http\Requests\StudentInfo\Student;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Academic\Classes;

class StudentStoreRequest extends FormRequest
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
     * array:21 [▼ // app\Http\Controllers\StudentInfo\StudentController.php:79
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $mobile = '';
        if (Request()->mobile != '') {
            $mobile = 'max:255|unique:users,phone';
        }

        $email = '';
        if (Request()->email != '') {
            $email = 'max:255|unique:users,email';
        }

        return [
            'mobile' => $mobile,
            'email' => $email,
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'grade' => 'required|in:KG-1,KG-2,Grade1,Grade2,Grade3,Grade4,Grade5,Grade6,Grade7,Grade8,Form1,Form2,Form3,Form4',
            'class' => ['required', 'exists:classes,id'],
            'section' => 'required|max:255',
            'date_of_birth' => 'required|max:255',
            'admission_date' => 'required|max:255',
            'parent' => 'required|max:255',
            'status' => 'required|max:255',
            'siblings_discount' => 'nullable',
            'username' => 'unique:users,username',
            'password' => 'min:6',
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
