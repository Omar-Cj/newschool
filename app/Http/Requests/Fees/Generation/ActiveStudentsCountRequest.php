<?php

namespace App\Http\Requests\Fees\Generation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\MultiBranch\Entities\Branch;

class ActiveStudentsCountRequest extends FormRequest
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
            'filters.category_id' => [
                'sometimes',
                'integer',
                'exists:student_categories,id',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'branch_id.required' => 'Please select a branch.',
            'branch_id.exists' => 'The selected branch is not active or does not exist.',
            'filters.class_id.exists' => 'The selected class does not exist.',
            'filters.section_id.exists' => 'The selected section does not exist.',
            'filters.gender_id.exists' => 'The selected gender does not exist.',
            'filters.grade.in' => 'The selected grade is not valid.',
            'filters.academic_level.in' => 'The selected academic level is not valid.',
            'filters.category_id.exists' => 'The selected student category does not exist.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'branch_id' => 'branch',
            'filters.class_id' => 'class',
            'filters.section_id' => 'section',
            'filters.gender_id' => 'gender',
            'filters.grade' => 'grade',
            'filters.academic_level' => 'academic level',
            'filters.category_id' => 'student category',
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

            // Validate academic level matches grade if both are provided
            if ($this->has('filters.grade') && $this->has('filters.academic_level')) {
                $grade = $this->input('filters.grade');
                $academicLevel = $this->input('filters.academic_level');

                $gradeToLevelMap = [
                    'KG-1' => 'kg',
                    'KG-2' => 'kg',
                    'Grade1' => 'primary',
                    'Grade2' => 'primary',
                    'Grade3' => 'primary',
                    'Grade4' => 'primary',
                    'Grade5' => 'primary',
                    'Grade6' => 'primary',
                    'Grade7' => 'primary',
                    'Grade8' => 'primary',
                    'Form1' => 'secondary',
                    'Form2' => 'secondary',
                    'Form3' => 'secondary',
                    'Form4' => 'secondary',
                ];

                if (isset($gradeToLevelMap[$grade]) && $gradeToLevelMap[$grade] !== $academicLevel) {
                    $validator->errors()->add('filters.academic_level',
                        'The selected academic level does not match the selected grade.'
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

        return $data;
    }

    /**
     * Get formatted filters for service layer
     */
    public function getFilters(): array
    {
        return $this->getValidatedData()['filters'];
    }

    /**
     * Get branch ID
     */
    public function getBranchId(): int
    {
        return $this->getValidatedData()['branch_id'];
    }

    /**
     * Check if any filters are applied
     */
    public function hasFilters(): bool
    {
        $filters = $this->getFilters();
        return !empty($filters);
    }

    /**
     * Get human-readable filter summary
     */
    public function getFilterSummary(): string
    {
        $filters = $this->getFilters();
        $summary = [];

        if (isset($filters['class_id'])) {
            $class = \App\Models\Academic\Classes::find($filters['class_id']);
            $summary[] = "Class: " . ($class?->name ?? 'Unknown');
        }

        if (isset($filters['section_id'])) {
            $section = \App\Models\Academic\Section::find($filters['section_id']);
            $summary[] = "Section: " . ($section?->name ?? 'Unknown');
        }

        if (isset($filters['gender_id'])) {
            $gender = \App\Models\Gender::find($filters['gender_id']);
            $summary[] = "Gender: " . ($gender?->name ?? 'Unknown');
        }

        if (isset($filters['grade'])) {
            $summary[] = "Grade: " . $filters['grade'];
        }

        if (isset($filters['academic_level'])) {
            $summary[] = "Academic Level: " . ucfirst($filters['academic_level']);
        }

        if (isset($filters['category_id'])) {
            $category = \App\Models\StudentInfo\StudentCategory::find($filters['category_id']);
            $summary[] = "Category: " . ($category?->name ?? 'Unknown');
        }

        return empty($summary) ? 'No filters applied' : implode(', ', $summary);
    }
}