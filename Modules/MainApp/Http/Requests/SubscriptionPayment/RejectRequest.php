<?php

declare(strict_types=1);

namespace Modules\MainApp\Http\Requests\SubscriptionPayment;

use Illuminate\Foundation\Http\FormRequest;

/**
 * RejectRequest - Validation for rejecting subscription payment
 *
 * Validates rejection reason which is required for audit trail.
 */
class RejectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only super admins can reject payments
        // Adjust based on your permission system
        return true; // TODO: Add proper authorization check
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rejection_reason' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'rejection_reason' => 'rejection reason',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'rejection_reason.required' => 'Please provide a reason for rejecting this payment.',
            'rejection_reason.min' => 'Rejection reason must be at least 10 characters.',
            'rejection_reason.max' => 'Rejection reason cannot exceed 1000 characters.',
        ];
    }

    /**
     * Prepare data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from rejection reason
        if ($this->has('rejection_reason')) {
            $this->merge(['rejection_reason' => trim($this->input('rejection_reason'))]);
        }
    }
}
