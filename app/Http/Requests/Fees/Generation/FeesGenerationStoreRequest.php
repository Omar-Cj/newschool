<?php

namespace App\Http\Requests\Fees\Generation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\MultiBranch\Entities\Branch;
use App\Models\Fees\FeesType;

class FeesGenerationStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'branch_id' => [
                'required',
                'integer',
                Rule::exists('branches', 'id')->where(function ($query) {
                    return $query->where('status', \App\Enums\Status::ACTIVE);
                }),
            ],
            'academic_year_id' => [
                'required',
                'integer',
                'exists:sessions,id',
            ],
            'fee_type_ids' => [
                'required',
                'array',
                'min:1',
            ],
            'fee_type_ids.*' => [
                'integer',
                Rule::exists('fees_types', 'id')->where(function ($query) {
                    return $query->where('status', \App\Enums\Status::ACTIVE);
                }),
            ],
            'filters' => [
                'sometimes',
                'array',
            ],
            'filters.class_id' => [
                'sometimes',
                'integer',
                'exists:classes,id',
            ],
            'filters.section_id' => [
                'sometimes',
                'integer',
                'exists:sections,id',
            ],
            'filters.gender_id' => [
                'sometimes',
                'integer',
                'exists:genders,id',
            ],
            'filters.grade' => [
                'sometimes',
                'string',
                Rule::in(['KG-1', 'KG-2', 'Grade1', 'Grade2', 'Grade3', 'Grade4', 'Grade5', 'Grade6', 'Grade7', 'Grade8', 'Form1', 'Form2', 'Form3', 'Form4']),
            ],
            'filters.academic_level' => [
                'sometimes',
                'string',
                Rule::in(['kg', 'primary', 'secondary', 'high_school']),
            ],
            'notes' => [
                'sometimes',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'branch_id.required' => 'Please select a branch for fee generation.',
            'branch_id.exists' => 'The selected branch is not active or does not exist.',
            'academic_year_id.required' => 'Please select an academic year.',
            'academic_year_id.exists' => 'The selected academic year does not exist.',
            'fee_type_ids.required' => 'Please select at least one fee type.',
            'fee_type_ids.min' => 'Please select at least one fee type.',
            'fee_type_ids.*.exists' => 'One or more selected fee types are not active or do not exist.',
            'filters.class_id.exists' => 'The selected class does not exist.',
            'filters.section_id.exists' => 'The selected section does not exist.',
            'filters.gender_id.exists' => 'The selected gender does not exist.',
            'filters.grade.in' => 'The selected grade is not valid.',
            'filters.academic_level.in' => 'The selected academic level is not valid.',
            'notes.max' => 'Notes cannot exceed 500 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'branch_id' => 'branch',
            'academic_year_id' => 'academic year',
            'fee_type_ids' => 'fee types',
            'fee_type_ids.*' => 'fee type',
            'filters.class_id' => 'class',
            'filters.section_id' => 'section',
            'filters.gender_id' => 'gender',
            'filters.grade' => 'grade',
            'filters.academic_level' => 'academic level',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation for branch access
            if ($this->branch_id) {
                $branch = Branch::find($this->branch_id);
                if (!$branch || !$branch->isActive()) {
                    $validator->errors()->add('branch_id', 'The selected branch is not accessible.');
                }
            }

            // Validate fee types are applicable for selected filters
            if ($this->fee_type_ids && $this->has('filters.academic_level')) {
                $academicLevel = $this->input('filters.academic_level');
                $invalidFeeTypes = FeesType::whereIn('id', $this->fee_type_ids)
                    ->where(function($query) use ($academicLevel) {
                        $query->where('academic_level', '!=', 'all')
                              ->where('academic_level', '!=', $academicLevel);
                    })
                    ->pluck('name');

                if ($invalidFeeTypes->isNotEmpty()) {
                    $validator->errors()->add('fee_type_ids',
                        'The following fee types are not applicable for the selected academic level: ' .
                        $invalidFeeTypes->implode(', ')
                    );
                }
            }

            // Validate section belongs to class if both are provided
            if ($this->has('filters.class_id') && $this->has('filters.section_id')) {
                $section = \App\Models\Academic\Section::where('id', $this->input('filters.section_id'))
                    ->where('class_id', $this->input('filters.class_id'))
                    ->first();

                if (!$section) {
                    $validator->errors()->add('filters.section_id',
                        'The selected section does not belong to the selected class.'
                    );
                }
            }
        });
    }

    /**
     * Get the validated data with proper formatting
     */
    public function getValidatedData(): array
    {
        $data = $this->validated();

        // Ensure filters is always an array
        $data['filters'] = $data['filters'] ?? [];

        // Add user context
        $data['created_by'] = auth()->id();

        return $data;
    }

    /**
     * Get formatted data for service layer
     */
    public function getServiceData(): array
    {
        $data = $this->getValidatedData();

        return [
            'branch_id' => $data['branch_id'],
            'academic_year_id' => $data['academic_year_id'],
            'fee_type_ids' => $data['fee_type_ids'],
            'filters' => $data['filters'],
            'notes' => $data['notes'] ?? null,
        ];
    }
}