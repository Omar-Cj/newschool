<?php

namespace App\Services;

use App\Models\Fees\FeesGeneration;
use App\Models\Fees\FeesGenerationLog;
use App\Models\Fees\FeesCollect;
use App\Models\Fees\FeesAssign;
use App\Models\StudentInfo\SessionClassStudent;
use App\Repositories\Fees\FeesGenerationRepository;
use App\Repositories\StudentInfo\StudentRepository;
use App\Interfaces\Fees\FeesAssignInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use App\Enums\Status;

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
        
        if ($students->isEmpty()) {
            throw new \Exception('No eligible students found for the selected criteria.');
        }

        // Check if fee groups are selected
        if (empty($filters['fees_groups'])) {
            throw new \Exception('Please select at least one fee group.');
        }

        // Validate that fee masters exist for selected groups
        $feeMastersCount = \App\Models\Fees\FeesMaster::whereIn('fees_group_id', $filters['fees_groups'])
            ->where('session_id', setting('session'))
            ->count();
            
        if ($feeMastersCount === 0) {
            throw new \Exception('No fee masters found for the selected fee groups. Please set up fee masters first.');
        }

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
                    'name' => $student->full_name ?? ($student->first_name . ' ' . $student->last_name),
                    'class' => $student->sessionStudentDetails->class->name ?? '',
                    'section' => $student->sessionStudentDetails->section->name ?? '',
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
        // Get or create fee assignments for this student
        $feeAssignments = $this->getOrCreateStudentFeeAssignments($student, $data);
        
        if ($feeAssignments->isEmpty()) {
            throw new \Exception("No fee assignments found or created for student {$student->full_name}");
        }

        $totalAmount = 0;
        $details = [];
        $feesCollectIds = [];

        foreach ($feeAssignments as $feeAssignment) {
            // Check for duplicates based on fees_assign_children_id
            $existingFee = FeesCollect::where('student_id', $student->id)
                ->where('fees_assign_children_id', $feeAssignment->id)
                ->whereMonth('created_at', $data['month'])
                ->whereYear('created_at', $data['year'])
                ->first();

            if ($existingFee) {
                continue; // Skip this fee as it already exists
            }

            // Get the fee master for amount calculation
            $feeMaster = $feeAssignment->feesMaster;
            if (!$feeMaster) {
                continue; // Skip if fee master not found
            }

            // Calculate amount with discounts
            $amount = $this->calculateFeeAmount($student, $feeMaster, $data);
            
            // Create fees collect record with proper schema
            $feesCollect = FeesCollect::create([
                'date' => now()->toDateString(),
                'payment_method' => null, // Will be set when payment is made
                'fees_assign_children_id' => $feeAssignment->id,
                'fees_collect_by' => auth()->id(),
                'student_id' => $student->id,
                'session_id' => setting('session') ?? $feeAssignment->feesAssign->session_id,
                'amount' => $amount['net_amount'],
                'fine_amount' => 0, // No fine for bulk generation
                'generation_batch_id' => $batchId,
                'generation_method' => 'bulk',
                'due_date' => $data['due_date'] ?? Carbon::parse($data['year'] . '-' . $data['month'] . '-01')->endOfMonth(),
                'late_fee_applied' => 0,
                'discount_applied' => $amount['discount']
            ]);

            $feesCollectIds[] = $feesCollect->id;
            $totalAmount += $amount['net_amount'];
            
            $details[] = [
                'fees_master_id' => $feeMaster->id,
                'fees_assign_children_id' => $feeAssignment->id,
                'fees_name' => $feeMaster->name ?? 'Fee',
                'original_amount' => $amount['original_amount'],
                'discount' => $amount['discount'],
                'net_amount' => $amount['net_amount']
            ];
        }

        if (empty($feesCollectIds)) {
            throw new \Exception("All fees for this month already exist for student {$student->full_name}");
        }

        return [
            'amount' => $totalAmount,
            'fees_collect_id' => $feesCollectIds[0] ?? null, // Return first ID for reference
            'details' => $details
        ];
    }

    private function calculateFeeAmount($student, $feeMaster, array $data): array
    {
        $originalAmount = $feeMaster->amount ?? 0;
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
        $currentSession = setting('session');
        
        if (!$currentSession) {
            throw new \Exception('No active session found. Please configure the current academic session.');
        }
        
        $query = SessionClassStudent::query()
            ->where('session_id', $currentSession)
            ->with([
                'student' => function($q) {
                    $q->where('status', \App\Enums\Status::ACTIVE);
                },
                'class',
                'section'
            ]);

        // Filter by classes using correct column name (classes_id)
        if (!empty($filters['classes'])) {
            $query->whereIn('classes_id', $filters['classes']);
        }

        // Filter by sections using correct column name (section_id)  
        if (!empty($filters['sections'])) {
            $query->whereIn('section_id', $filters['sections']);
        }

        // Get session class students and filter out null students
        $students = $query->get()
            ->filter(function($sessionStudent) {
                return $sessionStudent->student && $sessionStudent->student->status == \App\Enums\Status::ACTIVE;
            })
            ->pluck('student')
            ->filter() // Remove any null students
            ->values(); // Reset array keys

        if ($students->isEmpty()) {
            throw new \Exception('No active students found for the selected criteria. Please check class and section selections.');
        }

        return $students;
    }

    private function getOrCreateStudentFeeAssignments($student, array $data): Collection
    {
        $currentSession = setting('session');
        $studentSessionDetails = $student->sessionStudentDetails;
        
        if (!$studentSessionDetails) {
            throw new \Exception("Student {$student->full_name} has no session class enrollment for the current session");
        }

        if (!$studentSessionDetails->classes_id || !$studentSessionDetails->section_id) {
            throw new \Exception("Student {$student->full_name} has incomplete class/section information");
        }

        // Get existing fee assignments for this student
        $existingAssignments = \App\Models\Fees\FeesAssignChildren::where('student_id', $student->id)
            ->whereHas('feesAssign', function($query) use ($currentSession) {
                $query->where('session_id', $currentSession);
            })
            ->with(['feesMaster', 'feesAssign']);

        // Filter by fee groups if specified
        if (!empty($data['fees_groups'])) {
            $existingAssignments->whereHas('feesMaster', function($query) use ($data) {
                $query->whereIn('fees_group_id', $data['fees_groups']);
            });
        }

        $assignments = $existingAssignments->get();

        // If no assignments exist, create them based on class and section fee setup
        if ($assignments->isEmpty()) {
            $assignments = $this->createFeeAssignmentsForStudent($student, $data, $currentSession);
        }

        if ($assignments->isEmpty()) {
            throw new \Exception("No fee assignments could be found or created for student {$student->full_name}");
        }

        return $assignments;
    }

    private function createFeeAssignmentsForStudent($student, array $data, $sessionId): Collection
    {
        $studentSessionDetails = $student->sessionStudentDetails;
        
        // Find fee assign record for this student's class/section/session
        $feesAssign = \App\Models\Fees\FeesAssign::where('session_id', $sessionId)
            ->where('classes_id', $studentSessionDetails->classes_id)
            ->where('section_id', $studentSessionDetails->section_id)
            ->first();

        if (!$feesAssign) {
            // Create fee assign record if it doesn't exist
            $feesAssign = \App\Models\Fees\FeesAssign::create([
                'session_id' => $sessionId,
                'classes_id' => $studentSessionDetails->classes_id,
                'section_id' => $studentSessionDetails->section_id,
                'category_id' => $student->student_category_id,
                'gender_id' => $student->gender_id,
                'fees_group_id' => !empty($data['fees_groups']) ? $data['fees_groups'][0] : null,
            ]);
        }

        // Get fee masters for the selected fee groups
        $feeMastersQuery = \App\Models\Fees\FeesMaster::where('session_id', $sessionId);
        
        if (!empty($data['fees_groups'])) {
            $feeMastersQuery->whereIn('fees_group_id', $data['fees_groups']);
        }

        $feeMasters = $feeMastersQuery->get();
        $assignments = collect();

        foreach ($feeMasters as $feeMaster) {
            // Check if assignment already exists
            $existingAssignment = \App\Models\Fees\FeesAssignChildren::where('fees_assign_id', $feesAssign->id)
                ->where('fees_master_id', $feeMaster->id)
                ->where('student_id', $student->id)
                ->first();

            if (!$existingAssignment) {
                $assignment = \App\Models\Fees\FeesAssignChildren::create([
                    'fees_assign_id' => $feesAssign->id,
                    'fees_master_id' => $feeMaster->id,
                    'student_id' => $student->id,
                ]);
                
                // Load relationships
                $assignment->load(['feesMaster', 'feesAssign']);
                $assignments->push($assignment);
            } else {
                $existingAssignment->load(['feesMaster', 'feesAssign']);
                $assignments->push($existingAssignment);
            }
        }

        return $assignments;
    }

    private function calculateFeesForStudents(Collection $students, array $filters): array
    {
        $totalAmount = 0;
        $classesBreakdown = [];
        $feesBreakdown = [];

        $students->each(function ($student) use (&$totalAmount, &$classesBreakdown, &$feesBreakdown, $filters) {
            $studentAmount = 0;
            
            try {
                // Get fee assignments for this student
                $feeAssignments = $this->getOrCreateStudentFeeAssignments($student, $filters);
                
                $feeAssignments->each(function ($feeAssignment) use (&$studentAmount, &$feesBreakdown) {
                    $feeMaster = $feeAssignment->feesMaster;
                    if ($feeMaster) {
                        $amount = $feeMaster->amount ?? 0;
                        $studentAmount += $amount;
                        
                        $feeName = $feeMaster->name ?? 'Unknown Fee';
                        $feesBreakdown[$feeName] = ($feesBreakdown[$feeName] ?? 0) + $amount;
                    }
                });
            } catch (\Exception $e) {
                // Skip students with issues during preview
                return;
            }

            $totalAmount += $studentAmount;
            
            // Get class name from student's session details
            $className = $student->sessionStudentDetails->class->name ?? 'Unknown Class';
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
        $currentSession = setting('session');
        
        // Count existing fee collections for the selected month/year and session
        $duplicateCount = FeesCollect::whereIn('student_id', $students->pluck('id'))
            ->where('session_id', $currentSession)
            ->whereMonth('date', $filters['month'])
            ->whereYear('date', $filters['year'])
            ->when(!empty($filters['fees_groups']), function($query) use ($filters) {
                $query->whereHas('feesAssignChildren.feesMaster', function($subQuery) use ($filters) {
                    $subQuery->whereIn('fees_group_id', $filters['fees_groups']);
                });
            })
            ->count();

        return [
            'has_duplicates' => $duplicateCount > 0,
            'count' => $duplicateCount,
            'message' => $duplicateCount > 0 
                ? "Warning: {$duplicateCount} fee records already exist for the selected criteria"
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