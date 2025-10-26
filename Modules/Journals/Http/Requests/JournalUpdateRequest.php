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
            'branch' => ['nullable', 'string', 'max:255'], // Made nullable for branch_id transition
            'branch_id' => [
                'required',
                'exists:branches,id',
                function ($attribute, $value, $fail) {
                    // Super admins can update journals for any branch
                    if (isSuperAdmin()) {
                        return;
                    }

                    // Regular users can only update journals for their own branch
                    $userBranchId = \Illuminate\Support\Facades\Auth::user()->branch_id;
                    if (!$userBranchId || (int)$value !== (int)$userBranchId) {
                        $fail(___('journals.unauthorized_branch_access') ?? 'You are not authorized to update journals for this branch.');
                    }
                }
            ],
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
            'branch.max' => ___('journals.branch_max_length'),
            'branch_id.exists' => ___('journals.invalid_branch'),
            'status.in' => ___('journals.invalid_status')
        ];
    }
}