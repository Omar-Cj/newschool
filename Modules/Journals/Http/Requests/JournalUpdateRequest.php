<?php

namespace Modules\Journals\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JournalUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'branch' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive']
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => ___('journals.name_required'),
            'name.max' => ___('journals.name_max_length'),
            'branch.required' => ___('journals.branch_required'),
            'branch.max' => ___('journals.branch_max_length'),
            'status.in' => ___('journals.invalid_status')
        ];
    }
}