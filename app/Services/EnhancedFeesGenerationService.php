<?php

namespace App\Services;

use App\Models\StudentService;
use App\Models\Fees\FeesCollect;
use App\Models\Fees\FeesGeneration;
use App\Models\Fees\FeesGenerationLog;
use App\Models\StudentInfo\Student;
use App\Services\StudentServiceManager;
use App\Services\BatchIdService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnhancedFeesGenerationService
{
    private StudentServiceManager $serviceManager;
    private BatchIdService $batchIdService;

    public function __construct(StudentServiceManager $serviceManager, BatchIdService $batchIdService)
    {
        $this->serviceManager = $serviceManager;
        $this->batchIdService = $batchIdService;
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

            // Determine billing period based on generation type and due date
            $billingPeriod = null;
            $billingYear = null;
            $billingMonth = null;

            if (isset($criteria['generation_month']) && $criteria['generation_month'] instanceof Carbon) {
                // Monthly generation - use the specified month
                $billingPeriod = $criteria['generation_month']->format('Y-m');
                $billingYear = $criteria['generation_month']->year;
                $billingMonth = $criteria['generation_month']->month;
            } elseif ($this->isMonthlyFeeType($service->feeType)) {
                // Monthly fee type - infer billing period from due date
                $billingPeriod = FeesCollect::inferBillingPeriodFromDueDate($dueDate);
                $billingDate = Carbon::createFromFormat('Y-m', $billingPeriod);
                $billingYear = $billingDate->year;
                $billingMonth = $billingDate->month;
            }
            // For one-time fees, leave billing period as null

            // Create fees collect record with new structure
            $feesCollect = FeesCollect::create([
                'student_id' => $student->id,
                'fee_type_id' => $service->fee_type_id,
                'academic_year_id' => $academicYearId,
                'amount' => $service->final_amount,
                'due_date' => $dueDate,
                'date' => now()->toDateString(),
                'payment_method' => null,
                'fees_collect_by' => null, // Only set when payment is actually collected
                'session_id' => $academicYearId, // Maintain compatibility
                'generation_batch_id' => $batchId,
                'generation_method' => 'service_based',
                'discount_applied' => $service->amount - $service->final_amount,
                'discount_notes' => $service->notes,
                'fine_amount' => 0,
                'late_fee_applied' => 0,
                // Add billing period fields (null for one-time fees)
                'billing_period' => $billingPeriod,
                'billing_year' => $billingYear,
                'billing_month' => $billingMonth,
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

        // Generate classes breakdown for frontend compatibility
        $classesBreakdown = $this->generateClassesBreakdown($filteredPreview);

        return [
            'total_students' => $students->count(),
            'students_with_services' => $filteredPreview->filter(function($item) {
                return $item['services']->isNotEmpty();
            })->count(),
            'total_amount' => $filteredPreview->sum('total_amount'),
            'estimated_amount' => $filteredPreview->sum('total_amount'), // Frontend expects this key
            'total_discount' => $filteredPreview->sum('total_discount'),
            'service_summary' => $this->generateServiceSummary($filteredPreview),
            'category_breakdown' => $this->generateCategoryBreakdown($filteredPreview),
            'classes_breakdown' => $classesBreakdown, // Frontend expects this key
            'duplicate_warning' => $duplicateInfo,
            'students_preview' => $filteredPreview->take(10)->map(function($item) {
                return [
                    'id' => $item['student']->id,
                    'name' => $item['student']->full_name,
                    'class' => $this->getStudentClassName($item['student']),
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
            ->with(['session_class_student']);

        // Apply filters based on criteria
        if (isset($criteria['class_ids']) && !empty($criteria['class_ids'])) {
            $query->whereHas('session_class_student', function ($q) use ($criteria) {
                $q->whereIn('classes_id', $criteria['class_ids']); // Note: column is classes_id, not class_id
                if (isset($criteria['academic_year_id'])) {
                    $q->where('session_id', $criteria['academic_year_id']);
                }
            });
        }

        if (isset($criteria['section_ids']) && !empty($criteria['section_ids'])) {
            $query->whereHas('session_class_student', function ($q) use ($criteria) {
                $q->whereIn('section_id', $criteria['section_ids']);
                if (isset($criteria['academic_year_id'])) {
                    $q->where('session_id', $criteria['academic_year_id']);
                }
            });
        }

        if (isset($criteria['category_ids'])) {
            $query->whereIn('category_id', $criteria['category_ids']);
        }

        if (isset($criteria['gender_ids'])) {
            $query->whereIn('gender_id', $criteria['gender_ids']);
        }

        // Only include students with active service subscriptions
        $query->whereHas('studentServices', function ($q) use ($criteria) {
            if (isset($criteria['academic_year_id']) && $criteria['academic_year_id'] !== null) {
                $q->where('academic_year_id', $criteria['academic_year_id']);
            }
            $q->where('is_active', true);
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

    /**
     * Generate monthly fees for all students with active service subscriptions
     */
    public function generateMonthlyFees(Carbon $month, array $filters = []): array
    {
        $academicYearId = $filters['academic_year_id'] ?? session('academic_year_id');

        $criteria = array_merge($filters, [
            'batch_id' => $this->batchIdService->generateBatchId(),
            'generation_month' => $month,
            'academic_year_id' => $academicYearId,
            'generation_type' => 'monthly_auto',
            'notes' => 'Automated monthly fee generation for ' . $month->format('F Y'),
            'school_id' => $filters['school_id'] ?? null
        ]);

        Log::info('Starting monthly fee generation', [
            'month' => $month->format('Y-m'),
            'academic_year_id' => $academicYearId,
            'filters' => $filters
        ]);

        return $this->generateServiceBasedFees($criteria);
    }

    /**
     * Generate fees for specific month with pro-rated calculations
     */
    public function generateProRatedMonthlyFees(Carbon $month, array $filters = []): array
    {
        $academicYearId = $filters['academic_year_id'] ?? session('academic_year_id');

        // Get students with service subscriptions
        $students = $this->getEligibleStudents(array_merge($filters, [
            'academic_year_id' => $academicYearId
        ]));

        if ($students->isEmpty()) {
            throw new \Exception('No eligible students found for monthly fee generation.');
        }

        // Create generation batch record
        $generation = FeesGeneration::create([
            'batch_id' => $this->batchIdService->generateBatchId(),
            'status' => 'processing',
            'total_students' => $students->count(),
            'filters' => $this->sanitizeMonthlyFilters($month, $filters),
            'notes' => 'Pro-rated monthly fee generation for ' . $month->format('F Y'),
            'created_by' => auth()->id() ?? 1,
            'school_id' => $filters['school_id'] ?? null,
            'started_at' => now()
        ]);

        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        $totalAmount = 0;

        foreach ($students as $student) {
            try {
                $result = $this->generateProRatedFeesForStudent(
                    $student,
                    $month,
                    $academicYearId,
                    $generation->batch_id,
                    $filters
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

        Log::info("Pro-rated monthly fee generation completed", [
            'generation_id' => $generation->id,
            'month' => $month->format('Y-m'),
            'total_students' => $students->count(),
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'total_amount' => $totalAmount
        ]);

        return [
            'generation_id' => $generation->id,
            'month' => $month->format('Y-m'),
            'total_students' => $students->count(),
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors,
            'total_amount' => $totalAmount
        ];
    }

    /**
     * Generate fees for individual student with pro-rating for monthly fees
     */
    private function generateProRatedFeesForStudent(Student $student, Carbon $month, int $academicYearId, string $batchId, array $filters): array
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
            if (!empty($filters['service_categories']) &&
                !in_array($service->feeType->category, $filters['service_categories'])) {
                continue;
            }

            if (!empty($filters['fee_type_ids']) &&
                !in_array($service->fee_type_id, $filters['fee_type_ids'])) {
                continue;
            }

            // Check if this is a monthly fee type
            $isMonthlyFee = $this->isMonthlyFeeType($service->feeType);

            // Skip non-monthly fees unless specifically requested
            if (!$isMonthlyFee && empty($filters['include_one_time_fees'])) {
                continue;
            }

            // Check for existing fee record to prevent duplicates for this billing period
            $billingPeriod = $month->format('Y-m');
            $existing = FeesCollect::where('student_id', $student->id)
                ->where('fee_type_id', $service->fee_type_id)
                ->where('academic_year_id', $academicYearId)
                ->where(function ($query) use ($billingPeriod, $month) {
                    // First try to find by billing_period (for new records)
                    $query->where('billing_period', $billingPeriod)
                        // Fallback to date-based check for legacy records
                        ->orWhere(function ($subQuery) use ($month) {
                            $subQuery->whereYear('date', $month->year)
                                     ->whereMonth('date', $month->month)
                                     ->whereNull('billing_period');
                        });
                })
                ->first();

            if ($existing) {
                continue; // Skip if already generated for this month
            }

            // Calculate amount (with pro-rating if applicable)
            $amount = $this->calculateMonthlyFeeAmount($service, $month);

            // Determine due date for this month
            $dueDate = $this->calculateMonthlyDueDate($service, $month);

            // Create fees collect record with new structure
            $feesCollect = FeesCollect::create([
                'student_id' => $student->id,
                'fee_type_id' => $service->fee_type_id,
                'academic_year_id' => $academicYearId,
                'amount' => $amount,
                'due_date' => $dueDate,
                'date' => $month->startOfMonth()->toDateString(),
                'payment_method' => null,
                'fees_collect_by' => null, // Only set when payment is actually collected
                'session_id' => $academicYearId, // Maintain compatibility
                'generation_batch_id' => $batchId,
                'generation_method' => 'service_based_monthly',
                'discount_applied' => $service->amount - $amount, // Track pro-rating as discount
                'discount_notes' => $this->getProRatingNotes($service, $month),
                'fine_amount' => 0,
                'late_fee_applied' => 0,
                // Add billing period fields for proper monthly tracking
                'billing_period' => $month->format('Y-m'),
                'billing_year' => $month->year,
                'billing_month' => $month->month,
            ]);

            $feesGenerated++;
            $totalAmount += $amount;

            $details[] = [
                'service_id' => $service->id,
                'fee_type_id' => $service->fee_type_id,
                'fee_name' => $service->feeType->name,
                'category' => $service->feeType->category,
                'original_amount' => $service->final_amount,
                'monthly_amount' => $amount,
                'is_prorated' => $amount !== $service->final_amount,
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
     * Calculate monthly fee amount with pro-rating if needed
     */
    private function calculateMonthlyFeeAmount(StudentService $service, Carbon $month): float
    {
        // For now, return the final_amount as-is
        // This can be enhanced with pro-rating logic based on subscription_date
        $baseAmount = $service->final_amount;

        // Check if service was subscribed mid-month and should be pro-rated
        if ($service->subscription_date && $service->subscription_date->month === $month->month) {
            $daysInMonth = $month->daysInMonth;
            $daysFromSubscription = $service->subscription_date->diffInDays($month->endOfMonth()) + 1;

            // Pro-rate only if subscription started after the 1st
            if ($service->subscription_date->day > 1) {
                $proRatedAmount = ($baseAmount / $daysInMonth) * $daysFromSubscription;

                Log::debug('Pro-rating calculation', [
                    'student_id' => $service->student_id,
                    'service' => $service->feeType->name,
                    'base_amount' => $baseAmount,
                    'days_in_month' => $daysInMonth,
                    'days_from_subscription' => $daysFromSubscription,
                    'prorated_amount' => $proRatedAmount
                ]);

                return round($proRatedAmount, 2);
            }
        }

        return $baseAmount;
    }

    /**
     * Calculate due date for monthly fee
     */
    private function calculateMonthlyDueDate(StudentService $service, Carbon $month): Carbon
    {
        // Use service due_date if available, otherwise use fee type offset
        if ($service->due_date) {
            return $service->due_date;
        }

        // Default to end of month plus offset from fee type
        $offset = $service->feeType->due_date_offset ?? 30;
        return $month->endOfMonth()->addDays($offset);
    }

    /**
     * Check if fee type is monthly recurring
     */
    private function isMonthlyFeeType($feeType): bool
    {
        // Check if fee type has frequency field (enhanced model)
        if (isset($feeType->fee_frequency)) {
            return $feeType->fee_frequency === 'monthly';
        }

        // Fallback: assume academic fees are monthly
        return in_array($feeType->category, ['academic', 'transport', 'meal']);
    }

    /**
     * Get pro-rating notes for fee record
     */
    private function getProRatingNotes(StudentService $service, Carbon $month): ?string
    {
        if ($service->subscription_date &&
            $service->subscription_date->month === $month->month &&
            $service->subscription_date->day > 1) {
            return "Pro-rated from " . $service->subscription_date->format('M j, Y');
        }

        return $service->notes;
    }

    /**
     * Sanitize monthly filters for storage
     */
    private function sanitizeMonthlyFilters(Carbon $month, array $filters): array
    {
        return [
            'generation_month' => $month->format('Y-m'),
            'class_ids' => $filters['class_ids'] ?? [],
            'section_ids' => $filters['section_ids'] ?? [],
            'academic_year_id' => $filters['academic_year_id'] ?? session('academic_year_id'),
            'fee_type_ids' => $filters['fee_type_ids'] ?? [],
            'service_categories' => $filters['service_categories'] ?? [],
            'include_one_time_fees' => $filters['include_one_time_fees'] ?? false,
            'generation_type' => 'monthly_auto'
        ];
    }

    /**
     * Preview monthly fees that would be generated
     */
    public function previewMonthlyFees(Carbon $month, array $filters = []): array
    {
        $academicYearId = $filters['academic_year_id'] ?? session('academic_year_id');

        $students = $this->getEligibleStudents(array_merge($filters, [
            'academic_year_id' => $academicYearId
        ]));

        $preview = collect();
        $totalAmount = 0;
        $totalStudents = 0;

        foreach ($students as $student) {
            $services = StudentService::where('student_id', $student->id)
                ->where('academic_year_id', $academicYearId)
                ->where('is_active', true)
                ->with('feeType')
                ->get();

            $studentTotal = 0;
            $studentServices = [];

            foreach ($services as $service) {
                // Apply service filters
                if (!empty($filters['service_categories']) &&
                    !in_array($service->feeType->category, $filters['service_categories'])) {
                    continue;
                }

                if (!empty($filters['fee_type_ids']) &&
                    !in_array($service->fee_type_id, $filters['fee_type_ids'])) {
                    continue;
                }

                $isMonthlyFee = $this->isMonthlyFeeType($service->feeType);

                if (!$isMonthlyFee && empty($filters['include_one_time_fees'])) {
                    continue;
                }

                // Check if fee already exists for this month
                $existing = FeesCollect::where('student_id', $student->id)
                    ->where('fee_type_id', $service->fee_type_id)
                    ->where('academic_year_id', $academicYearId)
                    ->whereYear('date', $month->year)
                    ->whereMonth('date', $month->month)
                    ->exists();

                if ($existing) {
                    continue;
                }

                $monthlyAmount = $this->calculateMonthlyFeeAmount($service, $month);
                $studentTotal += $monthlyAmount;

                $studentServices[] = [
                    'fee_name' => $service->feeType->name,
                    'category' => $service->feeType->category,
                    'original_amount' => $service->final_amount,
                    'monthly_amount' => $monthlyAmount,
                    'is_prorated' => $monthlyAmount !== $service->final_amount
                ];
            }

            if (!empty($studentServices)) {
                $totalStudents++;
                $totalAmount += $studentTotal;

                $preview->push([
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'class' => $student->classes->name ?? 'Unknown',
                    'total_amount' => $studentTotal,
                    'services' => $studentServices
                ]);
            }
        }

        return [
            'month' => $month->format('F Y'),
            'total_students' => $totalStudents,
            'total_amount' => $totalAmount,
            'students_preview' => $preview->take(10)->toArray(),
            'service_summary' => $this->generateMonthlyServiceSummary($preview),
            'duplicate_info' => $this->checkMonthlyDuplicates($students, $month, $filters)
        ];
    }

    /**
     * Generate summary by service for monthly preview
     */
    private function generateMonthlyServiceSummary(Collection $preview): array
    {
        $summary = [];

        foreach ($preview as $studentData) {
            foreach ($studentData['services'] as $service) {
                $serviceKey = $service['fee_name'];

                if (!isset($summary[$serviceKey])) {
                    $summary[$serviceKey] = [
                        'service_name' => $service['fee_name'],
                        'category' => $service['category'],
                        'student_count' => 0,
                        'total_amount' => 0
                    ];
                }

                $summary[$serviceKey]['student_count']++;
                $summary[$serviceKey]['total_amount'] += $service['monthly_amount'];
            }
        }

        return array_values($summary);
    }

    /**
     * Check for existing monthly fees that would be duplicates
     */
    private function checkMonthlyDuplicates(Collection $students, Carbon $month, array $filters): array
    {
        $duplicateCount = FeesCollect::whereIn('student_id', $students->pluck('id'))
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->when(!empty($filters['fee_type_ids']), function($query) use ($filters) {
                $query->whereIn('fee_type_id', $filters['fee_type_ids']);
            })
            ->when(!empty($filters['service_categories']), function($query) use ($filters) {
                $query->whereHas('feeType', function($subQuery) use ($filters) {
                    $subQuery->whereIn('category', $filters['service_categories']);
                });
            })
            ->count();

        return [
            'has_duplicates' => $duplicateCount > 0,
            'count' => $duplicateCount,
            'message' => $duplicateCount > 0
                ? "Warning: {$duplicateCount} fee records already exist for " . $month->format('F Y')
                : null
        ];
    }

    /**
     * Generate fees (wrapper method for compatibility with FeesServiceManager)
     * This method provides compatibility with the FeesServiceManager interface
     */
    public function generateFees(array $data): FeesGeneration
    {
        // Convert legacy data format to service-based criteria
        $criteria = $this->convertLegacyGenerationData($data);
        
        // Generate service-based fees
        $result = $this->generateServiceBasedFees($criteria);
        
        // Return or find the FeesGeneration record
        return FeesGeneration::where('batch_id', $criteria['batch_id'])->first();
    }

    /**
     * Generate service-based preview (wrapper method for compatibility with FeesServiceManager)
     * This method provides compatibility with the FeesServiceManager interface
     */
    public function generateServiceBasedPreview(array $filters): array
    {
        // Convert legacy filters to service-based criteria format
        $criteria = $this->convertLegacyFilters($filters);
        
        // Use existing previewServiceBasedFees method
        return $this->previewServiceBasedFees($criteria);
    }

    /**
     * Convert legacy fee generation filters to service-based criteria
     */
    private function convertLegacyFilters(array $filters): array
    {
        // Get academic year ID, fallback to session ID 1 if not available
        $academicYearId = $filters['academic_year_id'] ?? session('academic_year_id') ?? 1;
        
        $criteria = [
            'academic_year_id' => $academicYearId,
            'include_one_time_fees' => true
        ];

        // Convert class filters
        if (!empty($filters['classes'])) {
            $criteria['class_ids'] = $filters['classes'];
        }

        // Convert section filters
        if (!empty($filters['sections'])) {
            $criteria['section_ids'] = $filters['sections'];
        }

        // Convert fee group filters to fee type IDs
        if (!empty($filters['fees_groups'])) {
            // Get fee types that belong to these fee groups
            $feeTypeIds = \DB::table('fees_types')
                ->whereIn('fees_group_id', $filters['fees_groups'])
                ->pluck('id')
                ->toArray();
            
            if (!empty($feeTypeIds)) {
                $criteria['fee_type_ids'] = $feeTypeIds;
            }
        }

        // Handle month/year filters for monthly generation
        if (isset($filters['month']) && isset($filters['year'])) {
            $criteria['generation_month'] = Carbon::createFromDate($filters['year'], $filters['month'], 1);
        }

        return $criteria;
    }

    /**
     * Convert legacy generation data to service-based criteria
     */
    private function convertLegacyGenerationData(array $data): array
    {
        // Get academic year ID, fallback to session ID 1 if not available
        $academicYearId = $data['academic_year_id'] ?? session('academic_year_id') ?? 1;
        
        $criteria = [
            'batch_id' => $data['batch_id'] ?? $this->batchIdService->generateBatchId(),
            'academic_year_id' => $academicYearId,
            'notes' => $data['notes'] ?? 'Legacy fee generation converted to service-based',
            'school_id' => $data['school_id'] ?? null,
            'include_one_time_fees' => true
        ];

        // Convert class filters
        if (!empty($data['classes'])) {
            $criteria['class_ids'] = $data['classes'];
        }

        // Convert section filters
        if (!empty($data['sections'])) {
            $criteria['section_ids'] = $data['sections'];
        }

        // Convert student filters
        if (!empty($data['students'])) {
            $criteria['student_ids'] = $data['students'];
        }

        // Convert fee group filters to fee type IDs
        if (!empty($data['fees_groups'])) {
            $feeTypeIds = \DB::table('fees_types')
                ->whereIn('fees_group_id', $data['fees_groups'])
                ->pluck('id')
                ->toArray();
            
            if (!empty($feeTypeIds)) {
                $criteria['fee_type_ids'] = $feeTypeIds;
            }
        }

        // Handle due date
        if (!empty($data['due_date'])) {
            $criteria['due_date'] = $data['due_date'];
        }

        // Handle month/year for monthly generation
        if (isset($data['month']) && isset($data['year'])) {
            $criteria['generation_month'] = Carbon::createFromDate($data['year'], $data['month'], 1);
        }

        return $criteria;
    }

    /**
     * Generate classes breakdown for frontend compatibility
     */
    private function generateClassesBreakdown(Collection $preview): array
    {
        $breakdown = [];
        
        foreach ($preview as $studentData) {
            $className = $this->getStudentClassName($studentData['student']);
            
            if (!isset($breakdown[$className])) {
                $breakdown[$className] = [
                    'students' => 0,
                    'amount' => 0
                ];
            }
            
            $breakdown[$className]['students'] += 1;
            $breakdown[$className]['amount'] += $studentData['total_amount'];
        }
        
        return $breakdown;
    }

    /**
     * Get student class name from session_class_student relationship
     */
    private function getStudentClassName($student): string
    {
        try {
            if ($student->session_class_student && $student->session_class_student->classes_id) {
                // Get class name from classes table
                $class = \DB::table('classes')
                    ->where('id', $student->session_class_student->classes_id)
                    ->first();
                
                return $class ? $class->name : 'Unknown Class';
            }
            
            return 'No Class Assigned';
        } catch (\Exception $e) {
            return 'Unknown Class';
        }
    }
}