<?php

declare(strict_types=1);

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;

/**
 * ExportReportRequest
 *
 * Validates report export requests including format and parameters
 */
class ExportReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Authorization is handled in controller
        return true;
    }

    /**
     * Get the validation rules that apply to the request
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'parameters' => 'required|array',
            'parameters.*' => 'nullable',
        ];
    }

    /**
     * Get custom messages for validator errors
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'parameters.required' => 'Report parameters are required',
            'parameters.array' => 'Parameters must be provided as an array',
        ];
    }
}
