<?php

namespace App\Http\Requests\StudentInfo\Student;

use Illuminate\Foundation\Http\FormRequest;

class StudentImportRequest extends FormRequest
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
            'grade'   => 'required|in:KG-1,KG-2,Grade1,Grade2,Grade3,Grade4,Grade5,Grade6,Grade7,Grade8,Form1,Form2,Form3,Form4',
            'file'    => 'required|mimes:xlsx,csv',
            'class'   => 'required|exists:classes,id',
            'section' => 'required|exists:sections,id'
        ];
    }

    /**
     * Custom validation messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'grade.required' => 'Grade is required for bulk student import.',
            'grade.in' => 'Invalid grade selected. Please select a valid grade.',
            'class.required' => 'Class is required for bulk student import.',
            'class.exists' => 'Selected class does not exist.',
            'section.required' => 'Section is required for bulk student import.',
            'section.exists' => 'Selected section does not exist.',
            'file.required' => 'Excel file is required for import.',
            'file.mimes' => 'File must be an Excel file (.xlsx or .csv).',
        ];
    }
}
