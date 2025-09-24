<?php

namespace App\Services;

use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\StudentCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FeeEligibilityService
{
    /**
     * Check if a student is eligible for fee generation and collection
     */
    public function isStudentEligibleForFees(Student $student): bool
    {
        try {
            // Check if student is active
            if ($student->status !== \App\Enums\Status::ACTIVE) {
                Log::debug('Student not eligible for fees - inactive status', [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'status' => $student->status
                ]);
                return false;
            }

            // Check if student has a category
            if (!$student->student_category_id) {
                Log::warning('Student has no category assigned', [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name
                ]);
                // Default to eligible if no category (backward compatibility)
                return true;
            }

            // Check if student's category is fee-exempt
            $isExempt = $this->isStudentCategoryFeeExempt($student->student_category_id);

            if ($isExempt) {
                Log::info('Student not eligible for fees - fee-exempt category', [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'category_id' => $student->student_category_id,
                    'category_name' => $student->studentCategory?->name
                ]);
            }

            return !$isExempt;

        } catch (\Exception $e) {
            Log::error('Error checking student fee eligibility', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            // Default to eligible in case of errors (fail-safe approach)
            return true;
        }
    }

    /**
     * Check if a student category is fee-exempt
     */
    public function isStudentCategoryFeeExempt(int $categoryId): bool
    {
        return Cache::remember("student_category_fee_exempt_{$categoryId}", 3600, function () use ($categoryId) {
            $category = StudentCategory::find($categoryId);
            return $category ? (bool) $category->is_fee_exempt : false;
        });
    }

    /**
     * Filter a collection of students to only include fee-eligible students
     */
    public function filterEligibleStudents(Collection $students): Collection
    {
        return $students->filter(function ($student) {
            return $this->isStudentEligibleForFees($student);
        });
    }

    /**
     * Get all fee-exempt student categories
     */
    public function getFeeExemptCategories(): Collection
    {
        return Cache::remember('fee_exempt_categories', 3600, function () {
            return StudentCategory::where('is_fee_exempt', true)
                ->where('status', \App\Enums\Status::ACTIVE)
                ->get();
        });
    }

    /**
     * Get statistics about fee eligibility
     */
    public function getFeeEligibilityStatistics(): array
    {
        $totalStudents = Student::where('status', \App\Enums\Status::ACTIVE)->count();
        $exemptCategories = $this->getFeeExemptCategories();

        $exemptStudents = Student::where('status', \App\Enums\Status::ACTIVE)
            ->whereIn('student_category_id', $exemptCategories->pluck('id'))
            ->count();

        $eligibleStudents = $totalStudents - $exemptStudents;

        return [
            'total_active_students' => $totalStudents,
            'fee_eligible_students' => $eligibleStudents,
            'fee_exempt_students' => $exemptStudents,
            'exempt_categories_count' => $exemptCategories->count(),
            'exempt_categories' => $exemptCategories->pluck('name')->toArray()
        ];
    }

    /**
     * Validate if fee generation should be allowed for given criteria
     */
    public function validateFeeGenerationCriteria(array $criteria): array
    {
        $validation = [
            'is_valid' => true,
            'warnings' => [],
            'errors' => [],
            'statistics' => []
        ];

        try {
            // Get basic eligibility stats
            $stats = $this->getFeeEligibilityStatistics();
            $validation['statistics'] = $stats;

            // Check if there are any eligible students at all
            if ($stats['fee_eligible_students'] === 0) {
                $validation['errors'][] = 'No fee-eligible students found in the system. All active students appear to be in fee-exempt categories.';
                $validation['is_valid'] = false;
            }

            // Warning about exempt students if they exist
            if ($stats['fee_exempt_students'] > 0) {
                $validation['warnings'][] = sprintf(
                    'Note: %d students in fee-exempt categories (%s) will be automatically excluded from fee generation.',
                    $stats['fee_exempt_students'],
                    implode(', ', $stats['exempt_categories'])
                );
            }

            Log::debug('Fee generation criteria validation completed', [
                'criteria' => $criteria,
                'validation_result' => $validation
            ]);

        } catch (\Exception $e) {
            Log::error('Error validating fee generation criteria', [
                'criteria' => $criteria,
                'error' => $e->getMessage()
            ]);

            $validation['errors'][] = 'Unable to validate fee generation criteria: ' . $e->getMessage();
            $validation['is_valid'] = false;
        }

        return $validation;
    }

    /**
     * Clear fee eligibility cache
     */
    public function clearEligibilityCache(): void
    {
        // Clear category-specific caches
        $categories = StudentCategory::pluck('id');
        foreach ($categories as $categoryId) {
            Cache::forget("student_category_fee_exempt_{$categoryId}");
        }

        // Clear general caches
        Cache::forget('fee_exempt_categories');

        Log::info('Fee eligibility cache cleared');
    }

    /**
     * Bulk update fee exemption status for categories
     */
    public function bulkUpdateCategoryExemptions(array $exemptions): array
    {
        $results = [
            'updated' => 0,
            'errors' => []
        ];

        try {
            DB::transaction(function () use ($exemptions, &$results) {
                foreach ($exemptions as $categoryId => $isExempt) {
                    $category = StudentCategory::find($categoryId);
                    if ($category) {
                        $category->update(['is_fee_exempt' => (bool) $isExempt]);
                        $results['updated']++;
                    } else {
                        $results['errors'][] = "Category with ID {$categoryId} not found";
                    }
                }
            });

            // Clear cache after updates
            $this->clearEligibilityCache();

            Log::info('Bulk category exemption update completed', $results);

        } catch (\Exception $e) {
            Log::error('Error during bulk category exemption update', [
                'exemptions' => $exemptions,
                'error' => $e->getMessage()
            ]);

            $results['errors'][] = 'Database transaction failed: ' . $e->getMessage();
        }

        return $results;
    }
}