<?php

declare(strict_types=1);

namespace App\Http\Requests\Report;

use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\Report\ReportRepository;

/**
 * ExecuteReportRequest
 *
 * Validates report execution requests and ensures all required
 * parameters are provided with correct types
 */
class ExecuteReportRequest extends FormRequest
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
            'parameters.*' => 'nullable', // Allow any parameter values
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

    /**
     * Configure the validator instance
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation can be added here
            // For now, detailed parameter validation is done in the service
        });
    }
}
