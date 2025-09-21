<?php

namespace App\Services;

use App\Models\StudentService;
use App\Models\Fees\FeesType;
use App\Models\StudentInfo\Student;
use App\Models\AcademicLevelConfig;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentServiceManager
{
    /**
     * Subscribe student to a service with optional customizations
     */
    public function subscribeToService(
        Student $student, 
        FeesType $feeType, 
        array $options = []
    ): StudentService {
        
        // Validate service applicability
        $academicLevel = $this->determineAcademicLevel($student);
        if (!$feeType->isApplicableFor($academicLevel)) {
            throw new \InvalidArgumentException(
                "Service '{$feeType->name}' is not applicable for {$academicLevel} level students."
            );
        }

        $academicYearId = $options['academic_year_id'] ?? session('academic_year_id');
        
        // Check for existing subscription to prevent duplicates
        $existing = StudentService::where('student_id', $student->id)
            ->where('fee_type_id', $feeType->id)
            ->where('academic_year_id', $academicYearId)
            ->first();

        // ✅ ENHANCED: Check for conflicting academic level fees (prevent both primary + secondary)
        // Skip complex conflict detection during initial registration to prevent transaction issues
        $skipConflictCheck = $options['skip_conflict_check'] ?? false;
        
        if (!$skipConflictCheck) {
            try {
                $conflictingFees = StudentService::where('student_id', $student->id)
                    ->where('academic_year_id', $academicYearId)
                    ->where('is_active', true)
                    ->whereHas('feeType', function($query) use ($feeType) {
                        $query->where('category', $feeType->category)
                              ->where('is_mandatory_for_level', true)
                              ->where('academic_level', '!=', $feeType->academic_level)
                              ->where('academic_level', '!=', 'all');
                    })
                    ->with('feeType')
                    ->get();

                if ($conflictingFees->count() > 0) {
                    $conflictingFeeNames = $conflictingFees->pluck('feeType.name')->join(', ');
                    Log::warning('Preventing conflicting fee assignment', [
                        'student_id' => $student->id,
                        'student_name' => $student->full_name,
                        'new_fee' => $feeType->name . ' (' . $feeType->academic_level . ')',
                        'conflicting_fees' => $conflictingFeeNames,
                        'academic_level' => $academicLevel,
                        'action' => 'Removing conflicting fees to prevent dual fee assignment'
                    ]);

                    // Remove conflicting fees before adding the correct one
                    foreach ($conflictingFees as $conflictingFee) {
                        $conflictingFee->update([
                            'is_active' => false,
                            'notes' => ($conflictingFee->notes ? $conflictingFee->notes . ' | ' : '') . 
                                      "Deactivated due to conflicting academic level. Student academic level: {$academicLevel}",
                            'updated_by' => auth()->id()
                        ]);
                    }
                }
            } catch (\Exception $e) {
                // Don't let conflict detection break the service subscription
                Log::warning('Conflict detection failed, proceeding with service subscription', [
                    'student_id' => $student->id,
                    'fee_type' => $feeType->name,
                    'error' => $e->getMessage()
                ]);
            }
        } else {
            Log::debug('Skipping conflict detection during registration', [
                'student_id' => $student->id,
                'fee_type' => $feeType->name
            ]);
        }

        if ($existing) {
            if ($existing->is_active) {
                throw new \InvalidArgumentException(
                    "Student is already subscribed to this service for the current academic year."
                );
            }
            
            // Reactivate existing subscription
            $existing->activate();
            return $existing;
        }

        $termStart = $options['term_start'] ?? now();
        
        $service = new StudentService([
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'academic_year_id' => $academicYearId,
            'amount' => $options['amount'] ?? $feeType->amount,
            'final_amount' => $options['amount'] ?? $feeType->amount,
            'subscription_date' => now(),
            'is_active' => $options['is_active'] ?? true,
            'notes' => $options['notes'] ?? null,
            'created_by' => auth()->id()
        ]);

        $service->save();

        // Apply discount if provided
        if (isset($options['discount'])) {
            $this->applyDiscount(
                $service, 
                $options['discount']['type'], 
                $options['discount']['value'],
                $options['discount']['notes'] ?? null
            );
        }

        Log::info("Student service subscription created", [
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'academic_year_id' => $academicYearId,
            'amount' => $service->final_amount
        ]);

        return $service;
    }

    /**
     * Get all services available for a student's academic level
     */
    public function getAvailableServices(Student $student): Collection
    {
        $academicLevel = $this->determineAcademicLevel($student);
        
        return FeesType::active()
            ->forAcademicLevel($academicLevel)
            ->orderBy('is_mandatory_for_level', 'desc')
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
    }

    /**
     * Get mandatory services for a student's academic level
     */
    public function getMandatoryServices(Student $student): Collection
    {
        $academicLevel = $this->determineAcademicLevel($student);
        
        return FeesType::active()
            ->mandatoryForLevel($academicLevel)
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    /**
     * Auto-subscribe student to mandatory services for their academic level
     */
    public function autoSubscribeMandatoryServices(Student $student, $academicYearId = null, array $options = []): Collection
    {
        $academicLevel = $this->determineAcademicLevel($student);
        $academicYearId = $academicYearId ?? session('academic_year_id');
        
        $mandatoryServices = FeesType::active()
            ->mandatoryForLevel($academicLevel)
            ->get();

        $subscriptions = collect();

        DB::transaction(function () use ($student, $mandatoryServices, $academicYearId, &$subscriptions) {
            foreach ($mandatoryServices as $service) {
                // Check if already subscribed
                $existing = StudentService::where('student_id', $student->id)
                    ->where('fee_type_id', $service->id)
                    ->where('academic_year_id', $academicYearId)
                    ->first();

                if (!$existing) {
                    try {
                        $subscription = $this->subscribeToService($student, $service, [
                            'academic_year_id' => $academicYearId,
                            'notes' => 'Automatically assigned mandatory service',
                            'skip_conflict_check' => $options['skip_conflict_check'] ?? false
                        ]);
                        $subscriptions->push($subscription);
                    } catch (\Exception $e) {
                        Log::error("Failed to auto-subscribe mandatory service", [
                            'student_id' => $student->id,
                            'fee_type_id' => $service->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        });

        return $subscriptions;
    }

    /**
     * Apply discount to a service subscription
     */
    public function applyDiscount(
        StudentService $service, 
        string $type, 
        float $value, 
        string $notes = null
    ): void {
        // Validate discount type
        $validTypes = ['percentage', 'fixed', 'override'];
        if (!in_array($type, $validTypes)) {
            throw new \InvalidArgumentException("Invalid discount type. Must be one of: " . implode(', ', $validTypes));
        }

        // Validate discount value
        if ($value < 0) {
            throw new \InvalidArgumentException("Discount value cannot be negative");
        }

        if ($type === 'percentage' && $value > 100) {
            throw new \InvalidArgumentException("Percentage discount cannot exceed 100%");
        }

        if ($type === 'fixed' && $value > $service->amount) {
            throw new \InvalidArgumentException("Fixed discount cannot exceed the service amount");
        }

        $originalAmount = $service->amount;
        
        $finalAmount = match($type) {
            'percentage' => $originalAmount * (1 - ($value / 100)),
            'fixed' => max(0, $originalAmount - $value),
            'override' => $value,
            default => $originalAmount
        };

        $service->update([
            'discount_type' => $type,
            'discount_value' => $value,
            'final_amount' => $finalAmount,
            'notes' => $notes ?? $service->notes,
            'updated_by' => auth()->id()
        ]);

        Log::info("Discount applied to student service", [
            'student_service_id' => $service->id,
            'discount_type' => $type,
            'discount_value' => $value,
            'original_amount' => $originalAmount,
            'final_amount' => $finalAmount
        ]);
    }

    /**
     * Remove discount from a service subscription
     */
    public function removeDiscount(StudentService $service, string $reason = null): void
    {
        $service->removeDiscount();
        
        if ($reason) {
            $service->update([
                'notes' => ($service->notes ? $service->notes . ' | ' : '') . "Discount removed: {$reason}",
                'updated_by' => auth()->id()
            ]);
        }

        Log::info("Discount removed from student service", [
            'student_service_id' => $service->id,
            'reason' => $reason
        ]);
    }

    /**
     * Bulk subscribe multiple students to services
     */
    public function bulkSubscribeStudents(
        Collection $students, 
        array $feeTypeIds, 
        array $options = []
    ): array {
        
        $results = [
            'success' => [],
            'errors' => [],
            'skipped' => []
        ];
        
        DB::transaction(function () use ($students, $feeTypeIds, $options, &$results) {
            foreach ($students as $student) {
                foreach ($feeTypeIds as $feeTypeId) {
                    try {
                        $feeType = FeesType::findOrFail($feeTypeId);
                        
                        // Validate service is applicable for student's level
                        if ($feeType->isApplicableFor($this->determineAcademicLevel($student))) {
                            $subscription = $this->subscribeToService($student, $feeType, $options);
                            $results['success'][] = [
                                'student_id' => $student->id,
                                'student_name' => $student->full_name,
                                'service_name' => $feeType->name,
                                'subscription_id' => $subscription->id
                            ];
                        } else {
                            $results['skipped'][] = [
                                'student_id' => $student->id,
                                'student_name' => $student->full_name,
                                'service_name' => $feeType->name,
                                'reason' => 'Service not applicable for student academic level'
                            ];
                        }
                    } catch (\Exception $e) {
                        $results['errors'][] = [
                            'student_id' => $student->id,
                            'student_name' => $student->full_name ?? 'Unknown',
                            'fee_type_id' => $feeTypeId,
                            'error' => $e->getMessage()
                        ];
                    }
                }
            }
        });

        return $results;
    }

    /**
     * Bulk apply discount to multiple student services
     */
    public function bulkApplyDiscount(
        Collection $studentServices,
        string $discountType,
        float $discountValue,
        string $notes = null
    ): array {
        
        $results = [
            'success' => 0,
            'errors' => []
        ];

        DB::transaction(function () use ($studentServices, $discountType, $discountValue, $notes, &$results) {
            foreach ($studentServices as $service) {
                try {
                    $this->applyDiscount($service, $discountType, $discountValue, $notes);
                    $results['success']++;
                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'service_id' => $service->id,
                        'student_name' => $service->student->full_name ?? 'Unknown',
                        'service_name' => $service->feeType->name ?? 'Unknown Service',
                        'error' => $e->getMessage()
                    ];
                }
            }
        });

        return $results;
    }

    /**
     * Determine academic level based on student's class
     * 
     * NEW SCALABLE APPROACH: Uses explicit academic_level field from classes table
     * instead of fragile name-based detection
     */
    public function determineAcademicLevel(Student $student): string
    {
        try {
            // ENHANCED: Use Student model's getAcademicLevel() method with robust error handling
            $academicLevel = $student->getAcademicLevel();
            
            if (!$academicLevel) {
                Log::warning('getAcademicLevel() returned empty value, using fallback', [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'has_session_details' => !is_null($student->sessionStudentDetails),
                    'class_info' => $student->sessionStudentDetails?->class?->name ?? 'No class'
                ]);
                
                // Emergency fallback: return primary as safe default
                return 'primary';
            }
            
            Log::debug('Successfully determined academic level', [
                'student_id' => $student->id,
                'academic_level' => $academicLevel,
                'method' => 'Student.getAcademicLevel()'
            ]);
            
            return $academicLevel;
            
        } catch (\Exception $e) {
            // Critical error handling: never let fee assignment fail completely
            Log::critical('Failed to determine academic level, using emergency fallback', [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'emergency_fallback' => 'primary',
                'action_required' => 'Check student class assignment and academic level configuration'
            ]);
            
            // Return safe default to prevent registration failure
            return 'primary';
        }
    }

    /**
     * Generate preview of fees for students based on their service subscriptions
     */
    public function generateFeePreview(Collection $students, $academicYearId = null): Collection
    {
        $academicYearId = $academicYearId ?? session('academic_year_id');
        
        return $students->map(function ($student) use ($academicYearId) {
            $services = StudentService::where('student_id', $student->id)
                ->where('academic_year_id', $academicYearId)
                ->where('is_active', true)
                ->with('feeType')
                ->get();

            return [
                'student' => $student,
                'services' => $services,
                'total_amount' => $services->sum('final_amount'),
                'total_discount' => $services->sum(function($service) {
                    return $service->amount - $service->final_amount;
                }),
                'fee_breakdown' => $services->groupBy('feeType.category'),
                'mandatory_count' => $services->filter(function($service) {
                    return $service->feeType->is_mandatory_for_level;
                })->count(),
                'optional_count' => $services->filter(function($service) {
                    return !$service->feeType->is_mandatory_for_level;
                })->count()
            ];
        });
    }

    /**
     * Get student's current service subscriptions for an academic year
     */
    public function getStudentServices(Student $student, $academicYearId = null): Collection
    {
        $academicYearId = $academicYearId ?? session('academic_year_id');
        
        return StudentService::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->where('is_active', true)
            ->with(['feeType', 'academicYear'])
            ->orderBy('subscription_date')
            ->get();
    }

    /**
     * Unsubscribe student from a service
     */
    public function unsubscribeFromService(StudentService $service, string $reason = null): void
    {
        $service->deactivate($reason);

        Log::info("Student unsubscribed from service", [
            'student_service_id' => $service->id,
            'student_id' => $service->student_id,
            'fee_type_id' => $service->fee_type_id,
            'reason' => $reason
        ]);
    }

    /**
     * Transfer service subscription to another academic year
     */
    public function transferServiceSubscription(
        StudentService $service, 
        int $newAcademicYearId,
        array $adjustments = []
    ): StudentService {
        
        $newService = $this->subscribeToService(
            $service->student,
            $service->feeType,
            array_merge([
                'academic_year_id' => $newAcademicYearId,
                'amount' => $adjustments['amount'] ?? $service->amount,
                'discount' => isset($adjustments['discount']) ? $adjustments['discount'] : [
                    'type' => $service->discount_type,
                    'value' => $service->discount_value
                ],
                'notes' => "Transferred from previous academic year"
            ], $adjustments)
        );

        Log::info("Service subscription transferred", [
            'original_service_id' => $service->id,
            'new_service_id' => $newService->id,
            'from_academic_year' => $service->academic_year_id,
            'to_academic_year' => $newAcademicYearId
        ]);

        return $newService;
    }

    /**
     * Get services available for a specific grade
     */
    public function getServicesForGrade(string $grade): Collection
    {
        $student = new Student(['grade' => $grade]);
        $academicLevel = $student->getAcademicLevelFromGrade();

        return FeesType::active()
            ->forAcademicLevel($academicLevel)
            ->orderBy('is_mandatory_for_level', 'desc')
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get mandatory services for a specific grade
     */
    public function getMandatoryServicesForGrade(string $grade): Collection
    {
        $student = new Student(['grade' => $grade]);
        $academicLevel = $student->getAcademicLevelFromGrade();

        return FeesType::active()
            ->mandatoryForLevel($academicLevel)
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    /**
     * Bulk subscribe students by grade to mandatory services
     */
    public function bulkSubscribeByGrade(array $grades, $academicYearId = null, array $options = []): array
    {
        $academicYearId = $academicYearId ?? session('academic_year_id');
        $results = ['success' => [], 'errors' => []];

        foreach ($grades as $grade) {
            $students = Student::byGrade($grade)->active()->get();
            $mandatoryServices = $this->getMandatoryServicesForGrade($grade);

            foreach ($students as $student) {
                foreach ($mandatoryServices as $service) {
                    try {
                        $subscription = $this->subscribeToService($student, $service, array_merge($options, [
                            'academic_year_id' => $academicYearId,
                            'skip_conflict_check' => true // Skip during bulk operations
                        ]));

                        $results['success'][] = [
                            'student_id' => $student->id,
                            'student_name' => $student->full_name,
                            'grade' => $grade,
                            'service_name' => $service->name,
                            'subscription_id' => $subscription->id
                        ];
                    } catch (\Exception $e) {
                        $results['errors'][] = [
                            'student_id' => $student->id,
                            'student_name' => $student->full_name,
                            'grade' => $grade,
                            'service_name' => $service->name,
                            'error' => $e->getMessage()
                        ];

                        Log::warning('Failed to subscribe student to service during bulk operation', [
                            'student_id' => $student->id,
                            'grade' => $grade,
                            'service_id' => $service->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        return $results;
    }

    /**
     * Get service statistics by grade
     */
    public function getServiceStatsByGrade(): array
    {
        $grades = Student::getAllGrades();
        $stats = [];

        foreach ($grades as $grade) {
            $studentCount = Student::byGrade($grade)->active()->count();
            $mandatoryServices = $this->getMandatoryServicesForGrade($grade);
            $allServices = $this->getServicesForGrade($grade);

            $stats[$grade] = [
                'student_count' => $studentCount,
                'mandatory_services_count' => $mandatoryServices->count(),
                'total_services_count' => $allServices->count(),
                'mandatory_services' => $mandatoryServices->pluck('name')->toArray(),
                'estimated_mandatory_fees' => $mandatoryServices->sum('amount') * $studentCount
            ];
        }

        return $stats;
    }
}