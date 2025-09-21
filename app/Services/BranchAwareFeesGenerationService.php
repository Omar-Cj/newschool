<?php

namespace App\Services;

use App\Models\Fees\FeesGeneration;
use App\Models\Fees\FeesGenerationLog;
use App\Models\Fees\FeesCollect;
use App\Models\Fees\FeesType;
use App\Models\StudentInfo\Student;
use App\Models\StudentService;
use Modules\MultiBranch\Entities\Branch;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BranchAwareFeesGenerationService
{
    private StudentFeesService $studentFeesService;

    public function __construct(StudentFeesService $studentFeesService)
    {
        $this->studentFeesService = $studentFeesService;
    }

    /**
     * Generate fees for specific branch with active student filtering
     */
    public function generateFeesForBranch(
        int $branchId,
        int $academicYearId,
        array $feeTypeIds = [],
        array $filters = [],
        string $notes = null
    ): FeesGeneration {
        // Validate branch exists and is active
        $branch = $this->validateBranch($branchId);

        // Create generation record
        $generation = $this->createGenerationRecord($branchId, $academicYearId, $filters, $notes);

        // Get active students for the branch
        $students = $this->getActiveStudentsForBranch($branchId, $filters);

        // Update total students count
        $generation->update(['total_students' => $students->count()]);

        // Generate fees for each student
        $this->processStudentFees($generation, $students, $feeTypeIds, $academicYearId);

        return $generation->fresh();
    }

    /**
     * Generate fees for all active branches
     */
    public function generateFeesForAllBranches(
        int $academicYearId,
        array $feeTypeIds = [],
        array $filters = []
    ): Collection {
        $activeBranches = Branch::active()->get();
        $generations = collect();

        foreach ($activeBranches as $branch) {
            try {
                $generation = $this->generateFeesForBranch(
                    $branch->id,
                    $academicYearId,
                    $feeTypeIds,
                    $filters,
                    "Auto-generated for all branches"
                );
                $generations->push($generation);
            } catch (\Exception $e) {
                Log::error("Failed to generate fees for branch {$branch->id}", [
                    'branch_id' => $branch->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $generations;
    }

    /**
     * Get active students for specific branch with optional filters
     */
    public function getActiveStudentsForBranch(int $branchId, array $filters = []): Collection
    {
        $query = Student::active()
            ->where('branch_id', $branchId);

        // Apply additional filters
        if (isset($filters['class_id'])) {
            $query->whereHas('sessionStudentDetails', function($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        if (isset($filters['section_id'])) {
            $query->whereHas('sessionStudentDetails', function($q) use ($filters) {
                $q->where('section_id', $filters['section_id']);
            });
        }

        if (isset($filters['gender_id'])) {
            $query->where('gender_id', $filters['gender_id']);
        }

        if (isset($filters['grade'])) {
            $query->byGrade($filters['grade']);
        }

        if (isset($filters['academic_level'])) {
            $query->byAcademicLevel($filters['academic_level']);
        }

        return $query->get();
    }

    /**
     * Get branch-specific fee generation statistics
     */
    public function getBranchGenerationStats(int $branchId, int $academicYearId = null): array
    {
        $query = FeesGeneration::byBranch($branchId);

        if ($academicYearId) {
            $query->whereHas('feesCollects', function($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            });
        }

        $generations = $query->get();

        return [
            'total_generations' => $generations->count(),
            'completed_generations' => $generations->where('status', 'completed')->count(),
            'failed_generations' => $generations->where('status', 'failed')->count(),
            'in_progress_generations' => $generations->whereIn('status', ['pending', 'processing'])->count(),
            'total_students_processed' => $generations->sum('processed_students'),
            'total_amount_generated' => $generations->sum('total_amount'),
            'average_success_rate' => $generations->where('processed_students', '>', 0)->avg('success_rate') ?? 0,
        ];
    }

    /**
     * Get fee collection summary by branch
     */
    public function getBranchFeeCollectionSummary(int $branchId, int $academicYearId = null): array
    {
        $query = FeesCollect::byBranch($branchId);

        if ($academicYearId) {
            $query->byAcademicYear($academicYearId);
        }

        $collections = $query->get();

        return [
            'total_fees' => $collections->count(),
            'paid_fees' => $collections->where('payment_method', '!=', null)->count(),
            'unpaid_fees' => $collections->where('payment_method', null)->count(),
            'overdue_fees' => $collections->filter(fn($fee) => $fee->isOverdue())->count(),
            'total_amount' => $collections->sum('amount'),
            'total_collected' => $collections->where('payment_method', '!=', null)->sum('amount'),
            'total_outstanding' => $collections->where('payment_method', null)->sum('amount'),
            'collection_rate' => $collections->count() > 0 ?
                ($collections->where('payment_method', '!=', null)->count() / $collections->count()) * 100 : 0,
        ];
    }

    /**
     * Validate branch exists and is active
     */
    private function validateBranch(int $branchId): Branch
    {
        $branch = Branch::find($branchId);

        if (!$branch) {
            throw new \Exception("Branch with ID {$branchId} not found");
        }

        if (!$branch->isActive()) {
            throw new \Exception("Branch {$branch->name} is not active");
        }

        return $branch;
    }

    /**
     * Create generation record
     */
    private function createGenerationRecord(
        int $branchId,
        int $academicYearId,
        array $filters,
        string $notes = null
    ): FeesGeneration {
        return FeesGeneration::create([
            'batch_id' => 'BATCH_' . Str::upper(Str::random(8)) . '_' . time(),
            'branch_id' => $branchId,
            'status' => 'pending',
            'filters' => array_merge($filters, ['academic_year_id' => $academicYearId]),
            'notes' => $notes,
            'started_at' => now(),
            'created_by' => auth()->id(),
            'school_id' => session('school_id'), // If multi-tenant
        ]);
    }

    /**
     * Process fee generation for students
     */
    private function processStudentFees(
        FeesGeneration $generation,
        Collection $students,
        array $feeTypeIds,
        int $academicYearId
    ): void {
        $generation->update(['status' => 'processing']);

        $successCount = 0;
        $failureCount = 0;
        $totalAmount = 0;

        try {
            DB::transaction(function () use (
                $generation, $students, $feeTypeIds, $academicYearId,
                &$successCount, &$failureCount, &$totalAmount
            ) {
                foreach ($students as $student) {
                    try {
                        $amount = $this->generateFeesForStudent(
                            $student,
                            $feeTypeIds,
                            $academicYearId,
                            $generation->batch_id
                        );

                        $totalAmount += $amount;
                        $successCount++;

                        // Log success
                        $this->logGenerationResult($generation, $student, 'success', $amount);

                    } catch (\Exception $e) {
                        $failureCount++;

                        // Log failure
                        $this->logGenerationResult($generation, $student, 'failed', 0, $e->getMessage());

                        Log::error("Failed to generate fees for student {$student->id}", [
                            'student_id' => $student->id,
                            'generation_id' => $generation->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            });

            // Update generation with final results
            $generation->update([
                'status' => 'completed',
                'processed_students' => $successCount + $failureCount,
                'successful_students' => $successCount,
                'failed_students' => $failureCount,
                'total_amount' => $totalAmount,
                'completed_at' => now(),
            ]);

        } catch (\Exception $e) {
            // Mark generation as failed
            $generation->update([
                'status' => 'failed',
                'notes' => ($generation->notes ?? '') . " | Error: " . $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    /**
     * Generate fees for individual student using StudentFeesService
     */
    private function generateFeesForStudent(
        Student $student,
        array $feeTypeIds,
        int $academicYearId,
        string $batchId
    ): float {
        // Use existing StudentFeesService but ensure branch context
        if (empty($feeTypeIds)) {
            // Get all applicable fee types for student's academic level
            $feeTypeIds = FeesType::active()
                ->forAcademicLevel($student->getAcademicLevel())
                ->pluck('id')
                ->toArray();
        }

        $totalAmount = 0;

        foreach ($feeTypeIds as $feeTypeId) {
            $feeType = FeesType::find($feeTypeId);

            if (!$feeType) continue;

            // Check if fee is applicable for student's academic level
            if (!$feeType->isApplicableFor($student->getAcademicLevel())) {
                continue;
            }

            // Create fee collect record with branch context
            $feeCollect = FeesCollect::create([
                'student_id' => $student->id,
                'fee_type_id' => $feeTypeId,
                'academic_year_id' => $academicYearId,
                'branch_id' => $student->branch_id,
                'amount' => $feeType->amount,
                'final_amount' => $feeType->amount,
                'generation_batch_id' => $batchId,
                'generation_method' => 'bulk',
                'due_date' => $this->calculateDueDate($feeType),
                'billing_period' => now()->format('Y-m'),
                'billing_year' => now()->year,
                'billing_month' => now()->month,
                'date' => now(),
            ]);

            $totalAmount += $feeType->amount;
        }

        return $totalAmount;
    }

    /**
     * Calculate due date based on fee type
     */
    private function calculateDueDate(FeesType $feeType): Carbon
    {
        // Default due date is end of current month
        $dueDate = now()->endOfMonth();

        // Adjust based on fee frequency
        switch ($feeType->fee_frequency) {
            case 'monthly':
                $dueDate = now()->endOfMonth();
                break;
            case 'semester':
                $dueDate = now()->addMonths(6)->endOfMonth();
                break;
            case 'annual':
                $dueDate = now()->addYear()->endOfMonth();
                break;
            case 'one_time':
                $dueDate = now()->addDays(30);
                break;
        }

        return $dueDate;
    }

    /**
     * Log generation result
     */
    private function logGenerationResult(
        FeesGeneration $generation,
        Student $student,
        string $status,
        float $amount = 0,
        string $errorMessage = null
    ): void {
        FeesGenerationLog::create([
            'fees_generation_id' => $generation->id,
            'student_id' => $student->id,
            'status' => $status,
            'amount' => $amount,
            'error_message' => $errorMessage,
            'processed_at' => now(),
        ]);
    }
}