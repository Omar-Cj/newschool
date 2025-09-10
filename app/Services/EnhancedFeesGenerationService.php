<?php

namespace App\Services;

use App\Models\StudentService;
use App\Models\Fees\FeesCollect;
use App\Models\Fees\FeesGeneration;
use App\Models\Fees\FeesGenerationLog;
use App\Models\StudentInfo\Student;
use App\Services\StudentServiceManager;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnhancedFeesGenerationService
{
    private StudentServiceManager $serviceManager;

    public function __construct(StudentServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Generate fees based on student service subscriptions
     */
    public function generateServiceBasedFees(array $criteria): array
    {
        return DB::transaction(function () use ($criteria) {
            
            // Get eligible students based on criteria
            $students = $this->getEligibleStudents($criteria);
            
            if ($students->isEmpty()) {
                throw new \Exception('No eligible students found for the selected criteria.');
            }

            // Create generation batch record
            $generation = FeesGeneration::create([
                'batch_id' => $criteria['batch_id'],
                'status' => 'processing',
                'total_students' => $students->count(),
                'filters' => $this->sanitizeFilters($criteria),
                'notes' => $criteria['notes'] ?? null,
                'created_by' => auth()->id(),
                'school_id' => $criteria['school_id'] ?? null,
                'started_at' => now()
            ]);

            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            $totalAmount = 0;

            foreach ($students as $student) {
                try {
                    $result = $this->generateFeesForStudent(
                        $student, 
                        $criteria['academic_year_id'], 
                        $generation->batch_id,
                        $criteria
                    );
                    
                    if ($result['fees_generated'] > 0) {
                        $successCount++;
                        $totalAmount += $result['total_amount'];

                        // Create generation log
                        FeesGenerationLog::create([
                            'fees_generation_id' => $generation->id,
                            'student_id' => $student->id,
                            'status' => 'success',
                            'amount' => $result['total_amount'],
                            'fee_details' => $result['details']
                        ]);
                    }
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = [
                        'student_id' => $student->id,
                        'student_name' => $student->full_name,
                        'error' => $e->getMessage()
                    ];

                    // Create error log
                    FeesGenerationLog::create([
                        'fees_generation_id' => $generation->id,
                        'student_id' => $student->id,
                        'status' => 'failed',
                        'error_message' => $e->getMessage()
                    ]);
                }
            }

            // Update generation record
            $generation->update([
                'status' => $errorCount > 0 && $successCount === 0 ? 'failed' : 
                           ($errorCount > 0 ? 'completed_with_errors' : 'completed'),
                'processed_students' => $successCount + $errorCount,
                'successful_students' => $successCount,
                'failed_students' => $errorCount,
                'total_amount' => $totalAmount,
                'completed_at' => now()
            ]);

            Log::info("Service-based fee generation completed", [
                'generation_id' => $generation->id,
                'total_students' => $students->count(),
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'total_amount' => $totalAmount
            ]);

            return [
                'generation_id' => $generation->id,
                'total_students' => $students->count(),
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'errors' => $errors,
                'total_amount' => $totalAmount
            ];
        });
    }

    /**
     * Generate fees for individual student based on their service subscriptions
     */
    private function generateFeesForStudent(Student $student, int $academicYearId, string $batchId, array $criteria): array
    {
        $services = StudentService::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->where('is_active', true)
            ->with('feeType')
            ->get();

        if ($services->isEmpty()) {
            throw new \Exception("No active service subscriptions found for student {$student->full_name}");
        }

        $feesGenerated = 0;
        $totalAmount = 0;
        $details = [];

        foreach ($services as $service) {
            // Apply service filter if specified
            if (!empty($criteria['service_categories']) && 
                !in_array($service->feeType->category, $criteria['service_categories'])) {
                continue;
            }

            if (!empty($criteria['fee_type_ids']) && 
                !in_array($service->fee_type_id, $criteria['fee_type_ids'])) {
                continue;
            }

            // Check for existing fee record to prevent duplicates
            $existing = FeesCollect::where('student_id', $student->id)
                ->where('fee_type_id', $service->fee_type_id)
                ->where('academic_year_id', $academicYearId)
                ->where('generation_batch_id', $batchId)
                ->first();

            if ($existing) {
                continue; // Skip if already generated
            }

            // Determine due date
            $dueDate = $criteria['due_date'] ?? $service->due_date ?? now()->addDays(30);
            if (is_string($dueDate)) {
                $dueDate = Carbon::parse($dueDate);
            }

            // Create fees collect record with new structure
            $feesCollect = FeesCollect::create([
                'student_id' => $student->id,
                'fee_type_id' => $service->fee_type_id,
                'academic_year_id' => $academicYearId,
                'amount' => $service->final_amount,
                'due_date' => $dueDate,
                'date' => now()->toDateString(),
                'payment_method' => null,
                'fees_collect_by' => auth()->id(),
                'session_id' => $academicYearId, // Maintain compatibility
                'generation_batch_id' => $batchId,
                'generation_method' => 'service_based',
                'discount_applied' => $service->amount - $service->final_amount,
                'discount_notes' => $service->notes,
                'fine_amount' => 0,
                'late_fee_applied' => 0
            ]);
            
            $feesGenerated++;
            $totalAmount += $service->final_amount;

            $details[] = [
                'service_id' => $service->id,
                'fee_type_id' => $service->fee_type_id,
                'fee_name' => $service->feeType->name,
                'category' => $service->feeType->category,
                'original_amount' => $service->amount,
                'discount_type' => $service->discount_type,
                'discount_value' => $service->discount_value,
                'final_amount' => $service->final_amount,
                'fees_collect_id' => $feesCollect->id
            ];
        }

        return [
            'fees_generated' => $feesGenerated,
            'total_amount' => $totalAmount,
            'details' => $details
        ];
    }

    /**
     * Preview fees that would be generated
     */
    public function previewServiceBasedFees(array $criteria): array
    {
        $students = $this->getEligibleStudents($criteria);
        $preview = $this->serviceManager->generateFeePreview($students, $criteria['academic_year_id']);
        
        // Apply filters to preview
        $filteredPreview = $preview->map(function ($studentData) use ($criteria) {
            $filteredServices = $studentData['services']->filter(function ($service) use ($criteria) {
                if (!empty($criteria['service_categories']) && 
                    !in_array($service->feeType->category, $criteria['service_categories'])) {
                    return false;
                }

                if (!empty($criteria['fee_type_ids']) && 
                    !in_array($service->fee_type_id, $criteria['fee_type_ids'])) {
                    return false;
                }

                return true;
            });

            return [
                'student' => $studentData['student'],
                'services' => $filteredServices,
                'total_amount' => $filteredServices->sum('final_amount'),
                'total_discount' => $filteredServices->sum(function($service) {
                    return $service->amount - $service->final_amount;
                }),
                'fee_breakdown' => $filteredServices->groupBy('feeType.category')
            ];
        });

        // Check for duplicates
        $duplicateInfo = $this->checkForDuplicates($students, $criteria);

        return [
            'total_students' => $students->count(),
            'students_with_services' => $filteredPreview->filter(function($item) {
                return $item['services']->isNotEmpty();
            })->count(),
            'total_amount' => $filteredPreview->sum('total_amount'),
            'total_discount' => $filteredPreview->sum('total_discount'),
            'service_summary' => $this->generateServiceSummary($filteredPreview),
            'category_breakdown' => $this->generateCategoryBreakdown($filteredPreview),
            'duplicate_warning' => $duplicateInfo,
            'students_preview' => $filteredPreview->take(10)->map(function($item) {
                return [
                    'id' => $item['student']->id,
                    'name' => $item['student']->full_name,
                    'class' => $item['student']->classes->name ?? 'Unknown',
                    'services_count' => $item['services']->count(),
                    'total_amount' => $item['total_amount']
                ];
            })
        ];
    }

    /**
     * Get students eligible for fee generation based on criteria
     */
    private function getEligibleStudents(array $criteria): Collection
    {
        $query = Student::query()
            ->where('status', \App\Enums\Status::ACTIVE)
            ->with(['classes', 'section']);

        // Apply filters based on criteria
        if (isset($criteria['class_ids'])) {
            $query->whereIn('class_id', $criteria['class_ids']);
        }

        if (isset($criteria['section_ids'])) {
            $query->whereIn('section_id', $criteria['section_ids']);
        }

        if (isset($criteria['category_ids'])) {
            $query->whereIn('category_id', $criteria['category_ids']);
        }

        if (isset($criteria['gender_ids'])) {
            $query->whereIn('gender_id', $criteria['gender_ids']);
        }

        // Only include students with active service subscriptions
        $query->whereHas('studentServices', function ($q) use ($criteria) {
            $q->where('academic_year_id', $criteria['academic_year_id'])
              ->where('is_active', true);
        });

        return $query->get();
    }

    /**
     * Generate summary by service type
     */
    private function generateServiceSummary(Collection $preview): array
    {
        $summary = [];
        
        foreach ($preview as $studentData) {
            foreach ($studentData['services'] as $service) {
                $serviceKey = $service->feeType->name;
                
                if (!isset($summary[$serviceKey])) {
                    $summary[$serviceKey] = [
                        'service_name' => $service->feeType->name,
                        'category' => $service->feeType->category,
                        'student_count' => 0,
                        'total_amount' => 0,
                        'total_discount' => 0,
                        'average_amount' => 0
                    ];
                }
                
                $summary[$serviceKey]['student_count']++;
                $summary[$serviceKey]['total_amount'] += $service->final_amount;
                $summary[$serviceKey]['total_discount'] += ($service->amount - $service->final_amount);
            }
        }

        // Calculate averages
        foreach ($summary as &$item) {
            $item['average_amount'] = $item['student_count'] > 0 
                ? $item['total_amount'] / $item['student_count'] 
                : 0;
        }
        
        return array_values($summary);
    }

    /**
     * Generate breakdown by service category
     */
    private function generateCategoryBreakdown(Collection $preview): array
    {
        $breakdown = [];
        
        foreach ($preview as $studentData) {
            foreach ($studentData['services'] as $service) {
                $category = $service->feeType->category;
                
                if (!isset($breakdown[$category])) {
                    $breakdown[$category] = [
                        'category' => $category,
                        'service_count' => 0,
                        'student_count' => 0,
                        'total_amount' => 0,
                        'total_discount' => 0
                    ];
                }
                
                $breakdown[$category]['service_count']++;
                $breakdown[$category]['total_amount'] += $service->final_amount;
                $breakdown[$category]['total_discount'] += ($service->amount - $service->final_amount);
            }
        }

        return array_values($breakdown);
    }

    /**
     * Check for existing fee records that would be duplicates
     */
    private function checkForDuplicates(Collection $students, array $criteria): array
    {
        $duplicateCount = FeesCollect::whereIn('student_id', $students->pluck('id'))
            ->where('academic_year_id', $criteria['academic_year_id'])
            ->when(!empty($criteria['fee_type_ids']), function($query) use ($criteria) {
                $query->whereIn('fee_type_id', $criteria['fee_type_ids']);
            })
            ->when(!empty($criteria['service_categories']), function($query) use ($criteria) {
                $query->whereHas('feeType', function($subQuery) use ($criteria) {
                    $subQuery->whereIn('category', $criteria['service_categories']);
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

    /**
     * Sanitize criteria for storage
     */
    private function sanitizeFilters(array $criteria): array
    {
        return [
            'class_ids' => $criteria['class_ids'] ?? [],
            'section_ids' => $criteria['section_ids'] ?? [],
            'academic_year_id' => $criteria['academic_year_id'],
            'fee_type_ids' => $criteria['fee_type_ids'] ?? [],
            'service_categories' => $criteria['service_categories'] ?? [],
            'due_date' => $criteria['due_date'] ?? null,
            'generation_type' => 'service_based'
        ];
    }

    /**
     * Get generation status with enhanced information
     */
    public function getGenerationStatus(FeesGeneration $generation): array
    {
        $logs = $generation->logs()->get();
        
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
            'can_be_cancelled' => $generation->canBeCancelled(),
            'generation_type' => 'service_based',
            'recent_logs' => $logs->sortByDesc('created_at')->take(5)->map(function($log) {
                return [
                    'student_name' => $log->student->full_name ?? 'Unknown',
                    'status' => $log->status,
                    'amount' => $log->amount,
                    'error_message' => $log->error_message,
                    'created_at' => $log->created_at->toISOString()
                ];
            })->values()
        ];
    }

    /**
     * Cancel generation and cleanup
     */
    public function cancelGeneration(FeesGeneration $generation): void
    {
        if (!$generation->canBeCancelled()) {
            throw new \Exception('Generation cannot be cancelled in its current state.');
        }

        DB::transaction(function () use ($generation) {
            // Update generation status
            $generation->update([
                'status' => 'cancelled',
                'completed_at' => now()
            ]);

            // Update pending logs
            FeesGenerationLog::where('fees_generation_id', $generation->id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);

            // Remove any generated fees_collect records for this batch
            $deletedCount = FeesCollect::where('generation_batch_id', $generation->batch_id)
                ->where('payment_method', null) // Only delete unpaid fees
                ->delete();

            Log::info("Service-based fee generation cancelled", [
                'generation_id' => $generation->id,
                'batch_id' => $generation->batch_id,
                'deleted_fees_count' => $deletedCount
            ]);
        });
    }

    /**
     * Get count of students that would be affected
     */
    public function getStudentCount(array $criteria): int
    {
        return $this->getEligibleStudents($criteria)->count();
    }

    /**
     * Auto-subscribe eligible students to mandatory services before fee generation
     */
    public function autoSubscribeMandatoryServices(array $criteria): array
    {
        $students = $this->getEligibleStudents($criteria);
        $results = [
            'total_students' => $students->count(),
            'subscriptions_created' => 0,
            'errors' => []
        ];

        foreach ($students as $student) {
            try {
                $subscriptions = $this->serviceManager->autoSubscribeMandatoryServices(
                    $student, 
                    $criteria['academic_year_id']
                );
                $results['subscriptions_created'] += $subscriptions->count();
            } catch (\Exception $e) {
                $results['errors'][] = [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }
}