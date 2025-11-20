<?php

declare(strict_types=1);

namespace Modules\MainApp\Http\Requests\SubscriptionPayment;

use Illuminate\Foundation\Http\FormRequest;
use Modules\MainApp\Entities\SubscriptionPayment;

/**
 * StoreRequest - Validation for creating subscription payment records
 *
 * Validates payment data including amount, payment method, dates, and references.
 */
class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Only super admins can create payment records
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
            'subscription_id' => [
                'required',
                'integer',
                'exists:subscriptions,id',
            ],
            'school_id' => [
                'required',
                'integer',
                'exists:schools,id',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ],
            'payment_method' => [
                'required',
                'string',
                'in:' . implode(',', [
                    SubscriptionPayment::METHOD_CASH,
                    SubscriptionPayment::METHOD_BANK_TRANSFER,
                    SubscriptionPayment::METHOD_MOBILE_MONEY,
                    SubscriptionPayment::METHOD_CHEQUE,
                    SubscriptionPayment::METHOD_CREDIT_CARD,
                    SubscriptionPayment::METHOD_PAYPAL,
                ]),
            ],
            'payment_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'reference_number' => [
                'nullable',
                'string',
                'max:255',
            ],
            'transaction_id' => [
                'nullable',
                'string',
                'max:255',
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
            'subscription_id' => 'subscription',
            'school_id' => 'school',
            'payment_method' => 'payment method',
            'payment_date' => 'payment date',
            'reference_number' => 'reference number',
            'transaction_id' => 'transaction ID',
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
            'subscription_id.required' => 'Please select a subscription.',
            'subscription_id.exists' => 'The selected subscription does not exist.',
            'school_id.required' => 'Please select a school.',
            'school_id.exists' => 'The selected school does not exist.',
            'amount.required' => 'Please enter the payment amount.',
            'amount.numeric' => 'Payment amount must be a number.',
            'amount.min' => 'Payment amount must be greater than or equal to zero.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'The selected payment method is invalid.',
            'payment_date.required' => 'Please enter the payment date.',
            'payment_date.date' => 'Payment date must be a valid date.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
            'reference_number.max' => 'Reference number cannot exceed 255 characters.',
            'transaction_id.max' => 'Transaction ID cannot exceed 255 characters.',
        ];
    }

    /**
     * Prepare data for validation.
     * Sanitize and normalize input data.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Remove any currency symbols or commas from amount
        if ($this->has('amount')) {
            $amount = $this->input('amount');
            $amount = preg_replace('/[^0-9.]/', '', (string) $amount);
            $this->merge(['amount' => $amount]);
        }

        // Trim whitespace from text fields
        if ($this->has('reference_number')) {
            $this->merge(['reference_number' => trim($this->input('reference_number'))]);
        }

        if ($this->has('transaction_id')) {
            $this->merge(['transaction_id' => trim($this->input('transaction_id'))]);
        }
    }
}
