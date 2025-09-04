<?php

namespace App\Services;

use App\Models\Fees\FeesGeneration;
use App\Models\Fees\FeesGenerationLog;
use App\Models\Fees\FeesCollect;
use App\Models\Fees\FeesAssign;
use App\Repositories\Fees\FeesGenerationRepository;
use App\Repositories\StudentInfo\StudentRepository;
use App\Interfaces\Fees\FeesAssignInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class FeesGenerationService
{
    private $repo;
    private $studentRepo;
    private $feesAssignRepo;

    public function __construct(
        FeesGenerationRepository $repo,
        StudentRepository $studentRepo,
        FeesAssignInterface $feesAssignRepo
    ) {
        $this->repo = $repo;
        $this->studentRepo = $studentRepo;
        $this->feesAssignRepo = $feesAssignRepo;
    }

    public function generatePreview(array $filters): array
    {
        $students = $this->getEligibleStudents($filters);
        $feesData = $this->calculateFeesForStudents($students, $filters);
        
        return [
            'total_students' => $students->count(),
            'estimated_amount' => $feesData['total_amount'],
            'classes_breakdown' => $feesData['classes_breakdown'],
            'fees_breakdown' => $feesData['fees_breakdown'],
            'duplicate_warning' => $this->checkForDuplicates($students, $filters),
            'students_preview' => $students->take(10)->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'class' => $student->class->name ?? '',
                    'section' => $student->section->name ?? '',
                ];
            })
        ];
    }

    public function generateFees(array $data): FeesGeneration
    {
        return DB::transaction(function () use ($data) {
            // Get eligible students
            $students = $this->getEligibleStudents($data);
            
            if ($students->isEmpty()) {
                throw new \Exception(___('fees.no_eligible_students'));
            }

            // Filter by selected students if provided
            if (!empty($data['selected_students'])) {
                $students = $students->whereIn('id', $data['selected_students']);
            }

            // Create generation record
            $generation = $this->repo->create([
                'batch_id' => $data['batch_id'],
                'status' => 'pending',
                'total_students' => $students->count(),
                'filters' => $this->sanitizeFilters($data),
                'notes' => $data['notes'] ?? null,
                'created_by' => $data['created_by'],
                'school_id' => $data['school_id'],
            ]);

            // Create logs for each student
            $students->each(function ($student) use ($generation) {
                $this->repo->createLog([
                    'fees_generation_id' => $generation->id,
                    'student_id' => $student->id,
                    'status' => 'pending',
                ]);
            });

            // Start processing (for now synchronously, later will be queued)
            $this->processGeneration($generation, $data);

            return $generation;
        });
    }

    public function processGeneration(FeesGeneration $generation, array $data): void
    {
        try {
            $this->repo->update($generation, [
                'status' => 'processing',
                'started_at' => now()
            ]);

            $logs = $generation->logs()->with('student')->get();
            $successCount = 0;
            $failedCount = 0;
            $totalAmount = 0;

            foreach ($logs as $log) {
                try {
                    $result = $this->generateFeesForStudent($log->student, $data, $generation->batch_id);
                    
                    $this->repo->updateLog($log, [
                        'status' => 'success',
                        'amount' => $result['amount'],
                        'fees_collect_id' => $result['fees_collect_id'],
                        'fee_details' => $result['details']
                    ]);

                    $successCount++;
                    $totalAmount += $result['amount'];
                    
                } catch (\Exception $e) {
                    $this->repo->updateLog($log, [
                        'status' => 'failed',
                        'error_message' => $e->getMessage()
                    ]);
                    
                    $failedCount++;
                }

                // Update progress
                $processed = $successCount + $failedCount;
                $this->repo->update($generation, [
                    'processed_students' => $processed,
                    'successful_students' => $successCount,
                    'failed_students' => $failedCount,
                    'total_amount' => $totalAmount
                ]);
            }

            // Mark as completed
            $this->repo->update($generation, [
                'status' => $failedCount > 0 && $successCount === 0 ? 'failed' : 'completed',
                'completed_at' => now()
            ]);

        } catch (\Exception $e) {
            $this->repo->update($generation, [
                'status' => 'failed',
                'completed_at' => now()
            ]);
            
            throw $e;
        }
    }

    private function generateFeesForStudent($student, array $data, string $batchId): array
    {
        // Get student's fee assignments
        $assignments = $this->getStudentFeeAssignments($student, $data);
        
        if ($assignments->isEmpty()) {
            throw new \Exception("No fee assignments found for student {$student->name}");
        }

        $totalAmount = 0;
        $details = [];
        $feesCollectIds = [];

        foreach ($assignments as $assignment) {
            // Check for duplicates
            $existingFee = FeesCollect::where('student_id', $student->id)
                ->where('fees_master_id', $assignment->id)
                ->whereMonth('created_at', $data['month'])
                ->whereYear('created_at', $data['year'])
                ->first();

            if ($existingFee) {
                throw new \Exception("Fee already exists for this month");
            }

            // Calculate amount with discounts
            $amount = $this->calculateFeeAmount($student, $assignment, $data);
            
            // Create fees collect record
            $feesCollect = FeesCollect::create([
                'student_id' => $student->id,
                'fees_master_id' => $assignment->id,
                'amount' => $amount['net_amount'],
                'generation_batch_id' => $batchId,
                'generation_method' => 'bulk',
                'due_date' => $data['due_date'] ?? Carbon::parse($data['year'] . '-' . $data['month'] . '-01')->endOfMonth(),
                'discount_applied' => $amount['discount'],
                'late_fee_applied' => 0,
                'status' => 'pending', // Assuming there's a status field
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $feesCollectIds[] = $feesCollect->id;
            $totalAmount += $amount['net_amount'];
            
            $details[] = [
                'fees_master_id' => $assignment->id,
                'fees_name' => $assignment->name ?? 'Fee',
                'original_amount' => $amount['original_amount'],
                'discount' => $amount['discount'],
                'net_amount' => $amount['net_amount']
            ];
        }

        return [
            'amount' => $totalAmount,
            'fees_collect_id' => $feesCollectIds[0] ?? null, // Return first ID for reference
            'details' => $details
        ];
    }

    private function calculateFeeAmount($student, $assignment, array $data): array
    {
        $originalAmount = $assignment->amount ?? 0;
        $discount = 0;

        // Apply sibling discount if applicable
        $siblingDiscount = $this->calculateSiblingDiscount($student);
        
        // Apply early payment discount if applicable
        $earlyPaymentDiscount = $this->calculateEarlyPaymentDiscount($originalAmount, $data);
        
        $totalDiscount = $siblingDiscount + $earlyPaymentDiscount;
        $netAmount = max(0, $originalAmount - $totalDiscount);

        return [
            'original_amount' => $originalAmount,
            'discount' => $totalDiscount,
            'net_amount' => $netAmount
        ];
    }

    private function calculateSiblingDiscount($student): float
    {
        // Implement sibling discount logic based on your business rules
        // This is a placeholder implementation
        return 0;
    }

    private function calculateEarlyPaymentDiscount(float $amount, array $data): float
    {
        // Implement early payment discount logic
        // This is a placeholder implementation
        return 0;
    }

    private function getEligibleStudents(array $filters): Collection
    {
        $query = $this->studentRepo->getBaseQuery()
            ->with(['class', 'section'])
            ->where('status', 'active');

        if (!empty($filters['classes'])) {
            $query->whereIn('class_id', $filters['classes']);
        }

        if (!empty($filters['sections'])) {
            $query->whereIn('section_id', $filters['sections']);
        }

        return $query->get();
    }

    private function getStudentFeeAssignments($student, array $filters): Collection
    {
        $query = FeesAssign::with(['feesAssignChilds.feesMaster'])
            ->where('classes_id', $student->class_id)
            ->where('section_id', $student->section_id);

        if (!empty($filters['fees_groups'])) {
            $query->whereIn('fees_group_id', $filters['fees_groups']);
        }

        return $query->get()->pluck('feesAssignChilds')->flatten();
    }

    private function calculateFeesForStudents(Collection $students, array $filters): array
    {
        $totalAmount = 0;
        $classesBreakdown = [];
        $feesBreakdown = [];

        $students->each(function ($student) use (&$totalAmount, &$classesBreakdown, &$feesBreakdown, $filters) {
            $assignments = $this->getStudentFeeAssignments($student, $filters);
            $studentAmount = 0;

            $assignments->each(function ($assignment) use (&$studentAmount, &$feesBreakdown) {
                $amount = $assignment->feesMaster->amount ?? 0;
                $studentAmount += $amount;
                
                $feeName = $assignment->feesMaster->name ?? 'Unknown Fee';
                $feesBreakdown[$feeName] = ($feesBreakdown[$feeName] ?? 0) + $amount;
            });

            $totalAmount += $studentAmount;
            
            $className = $student->class->name ?? 'Unknown Class';
            $classesBreakdown[$className] = [
                'students' => ($classesBreakdown[$className]['students'] ?? 0) + 1,
                'amount' => ($classesBreakdown[$className]['amount'] ?? 0) + $studentAmount
            ];
        });

        return [
            'total_amount' => $totalAmount,
            'classes_breakdown' => $classesBreakdown,
            'fees_breakdown' => $feesBreakdown
        ];
    }

    private function checkForDuplicates(Collection $students, array $filters): array
    {
        $duplicateCount = FeesCollect::whereIn('student_id', $students->pluck('id'))
            ->whereMonth('created_at', $filters['month'])
            ->whereYear('created_at', $filters['year'])
            ->count();

        return [
            'has_duplicates' => $duplicateCount > 0,
            'count' => $duplicateCount,
            'message' => $duplicateCount > 0 
                ? "Warning: {$duplicateCount} students already have fees for this month"
                : null
        ];
    }

    private function sanitizeFilters(array $data): array
    {
        return [
            'classes' => $data['classes'] ?? [],
            'sections' => $data['sections'] ?? [],
            'month' => $data['month'],
            'year' => $data['year'],
            'fees_groups' => $data['fees_groups'] ?? [],
            'selected_students' => !empty($data['selected_students']) ? count($data['selected_students']) . ' selected' : 'all'
        ];
    }

    public function getGenerationStatus(FeesGeneration $generation): array
    {
        return [
            'id' => $generation->id,
            'batch_id' => $generation->batch_id,
            'status' => $generation->status,
            'progress_percentage' => $generation->progress_percentage,
            'total_students' => $generation->total_students,
            'processed_students' => $generation->processed_students,
            'successful_students' => $generation->successful_students,
            'failed_students' => $generation->failed_students,
            'total_amount' => $generation->total_amount,
            'started_at' => $generation->started_at?->toISOString(),
            'completed_at' => $generation->completed_at?->toISOString(),
            'is_completed' => $generation->isCompleted(),
            'can_be_cancelled' => $generation->canBeCancelled()
        ];
    }

    public function cancelGeneration(FeesGeneration $generation): void
    {
        if (!$generation->canBeCancelled()) {
            throw new \Exception(___('fees.cannot_cancel_generation'));
        }

        DB::transaction(function () use ($generation) {
            // Update generation status
            $this->repo->update($generation, [
                'status' => 'cancelled',
                'completed_at' => now()
            ]);

            // Update pending logs
            FeesGenerationLog::where('fees_generation_id', $generation->id)
                ->where('status', 'pending')
                ->update(['status' => 'skipped']);

            // Remove any generated fees_collect records for this batch
            FeesCollect::where('generation_batch_id', $generation->batch_id)->delete();
        });
    }

    public function getStudentCount(array $filters): int
    {
        return $this->getEligibleStudents($filters)->count();
    }
}