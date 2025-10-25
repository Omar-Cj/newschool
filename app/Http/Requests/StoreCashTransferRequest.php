<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\CashTransfer;

class StoreCashTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('create', CashTransfer::class);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'journal_id' => ['required', 'integer', 'exists:journals,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'journal_id.required' => 'Please select a journal.',
            'journal_id.exists' => 'The selected journal does not exist.',
            'amount.required' => 'Please enter a transfer amount.',
            'amount.min' => 'Transfer amount must be greater than zero.',
            'amount.max' => 'Transfer amount is too large.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'journal_id' => 'journal',
            'amount' => 'transfer amount',
            'notes' => 'notes',
        ];
    }
}
