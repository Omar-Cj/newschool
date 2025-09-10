<?php

namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Services\StudentServiceManager;
use App\Models\StudentInfo\Student;
use App\Models\Fees\FeesType;
use App\Models\StudentService;
use App\Models\Academic\Classes;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponses;

class StudentServiceController extends Controller
{
    use ApiResponses;

    private StudentServiceManager $serviceManager;

    public function __construct(StudentServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * Show service management dashboard
     */
    public function dashboard()
    {
        $data['title'] = ___('fees.service_management_dashboard');
        return view('backend.fees.service-management.index', compact('data'));
    }

    /**
     * Get dashboard statistics
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            $academicYearId = session('academic_year_id');
            
            $stats = [
                'total_services' => \App\Models\Fees\FeesType::active()->count(),
                'students_with_services' => StudentService::where('academic_year_id', $academicYearId)
                    ->where('is_active', true)
                    ->distinct('student_id')
                    ->count(),
                'services_due_soon' => StudentService::where('academic_year_id', $academicYearId)
                    ->where('is_active', true)
                    ->where('due_date', '<=', now()->addDays(7))
                    ->where('due_date', '>=', now())
                    ->count(),
                'projected_revenue' => StudentService::where('academic_year_id', $academicYearId)
                    ->where('is_active', true)
                    ->sum('final_amount')
            ];

            return $this->success($stats, 'Dashboard statistics retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to get dashboard stats', [
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to retrieve dashboard statistics', 500);
        }
    }

    /**
     * Get services overview for dashboard
     */
    public function getServicesOverview(): JsonResponse
    {
        try {
            $academicYearId = session('academic_year_id');
            
            $services = \App\Models\Fees\FeesType::active()
                ->with(['studentServices' => function ($query) use ($academicYearId) {
                    $query->where('academic_year_id', $academicYearId)
                          ->where('is_active', true);
                }])
                ->get()
                ->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'code' => $service->code,
                        'category' => $service->category,
                        'is_mandatory' => $service->is_mandatory_for_level,
                        'active_subscriptions' => $service->studentServices->count(),
                        'total_revenue' => $service->studentServices->sum('final_amount')
                    ];
                });

            return $this->success($services, 'Services overview retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to get services overview', [
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to retrieve services overview', 500);
        }
    }

    /**
     * Get recent activities for dashboard
     */
    public function getRecentActivities(): JsonResponse
    {
        try {
            // Get recent service subscriptions and activities
            $recentSubscriptions = StudentService::with(['student', 'feeType'])
                ->where('created_at', '>=', now()->subDays(7))
                ->latest()
                ->limit(10)
                ->get();

            $activities = $recentSubscriptions->map(function ($subscription) {
                return [
                    'type' => 'subscription',
                    'title' => 'Service Subscription',
                    'description' => "{$subscription->student->full_name} subscribed to {$subscription->feeType->name}",
                    'time' => $subscription->created_at->diffForHumans()
                ];
            });

            return $this->success($activities->toArray(), 'Recent activities retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to get recent activities', [
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to retrieve recent activities', 500);
        }
    }

    /**
     * Get academic level statistics
     */
    public function getAcademicLevelStats(): JsonResponse
    {
        try {
            $academicYearId = session('academic_year_id');
            
            $stats = [];
            $levels = ['kg', 'primary', 'secondary', 'high_school'];
            
            foreach ($levels as $level) {
                // Get students with this academic level
                $studentsQuery = Student::whereHas('studentServices', function ($query) use ($academicYearId, $level) {
                    $query->where('academic_year_id', $academicYearId)
                          ->where('is_active', true)
                          ->whereHas('feeType', function ($subQuery) use ($level) {
                              $subQuery->where('academic_levels', 'like', "%{$level}%");
                          });
                });

                $studentCount = $studentsQuery->count();
                $servicesCount = StudentService::where('academic_year_id', $academicYearId)
                    ->where('is_active', true)
                    ->whereHas('feeType', function ($query) use ($level) {
                        $query->where('academic_levels', 'like', "%{$level}%");
                    })->count();
                $revenue = StudentService::where('academic_year_id', $academicYearId)
                    ->where('is_active', true)
                    ->whereHas('feeType', function ($query) use ($level) {
                        $query->where('academic_levels', 'like', "%{$level}%");
                    })->sum('final_amount');

                $stats[$level] = [
                    'students' => $studentCount,
                    'services' => $servicesCount,
                    'revenue' => number_format($revenue, 2)
                ];
            }

            return $this->success($stats, 'Academic level statistics retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to get academic level stats', [
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to retrieve academic level statistics', 500);
        }
    }

    /**
     * Search student services
     */
    public function searchStudentServices(Request $request): JsonResponse
    {
        try {
            $academicYearId = session('academic_year_id');
            $searchTerm = $request->input('search');
            $classFilter = $request->input('class');
            $serviceFilter = $request->input('service');

            $query = Student::with(['studentServices.feeType', 'classes']);

            if ($searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('first_name', 'like', "%{$searchTerm}%")
                      ->orWhere('last_name', 'like', "%{$searchTerm}%")
                      ->orWhere('admission_no', 'like', "%{$searchTerm}%");
                });
            }

            if ($classFilter) {
                $query->whereHas('sessionClassStudent', function ($q) use ($classFilter) {
                    $q->where('classes_id', $classFilter);
                });
            }

            if ($serviceFilter) {
                $query->whereHas('studentServices', function ($q) use ($serviceFilter, $academicYearId) {
                    $q->where('fee_type_id', $serviceFilter)
                      ->where('academic_year_id', $academicYearId)
                      ->where('is_active', true);
                });
            }

            $students = $query->limit(50)->get()->map(function ($student) use ($academicYearId) {
                $services = $student->studentServices()
                    ->where('academic_year_id', $academicYearId)
                    ->where('is_active', true)
                    ->get();

                return [
                    'id' => $student->id,
                    'name' => $student->full_name,
                    'admission_no' => $student->admission_no,
                    'class' => $student->classes->name ?? 'N/A',
                    'services_count' => $services->count(),
                    'total_amount' => $services->sum('final_amount')
                ];
            });

            return $this->success($students->toArray(), 'Student services search completed');
        } catch (\Exception $e) {
            Log::error('Failed to search student services', [
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to search student services', 500);
        }
    }

    /**
     * Export service report
     */
    public function exportServiceReport(Request $request)
    {
        try {
            // This is a placeholder - implement actual export logic
            return response()->download(storage_path('app/temp/service-report.xlsx'));
        } catch (\Exception $e) {
            Log::error('Failed to export service report', [
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to export service report');
        }
    }

    /**
     * Get available services for student registration (no specific student yet)
     */
    public function getAvailableServicesForRegistration(Request $request): JsonResponse
    {
        try {
            // Simulate student data to determine academic level
            $classId = $request->input('class_id');
            
            if (!$classId) {
                return $this->error('Class ID is required', 400);
            }
            
            // Get class information to determine academic level
            $classInfo = \App\Models\Academic\Classes::find($classId);
            if (!$classInfo) {
                return $this->error('Invalid class ID', 400);
            }
            
            // Create temporary student-like object with academic level info
            $tempStudent = new Student();
            $tempStudent->classes = $classInfo;
            
            // Use service manager to determine academic level and get services
            $academicLevel = $this->serviceManager->determineAcademicLevel($tempStudent);
            
            $availableServices = FeesType::active()
                ->forAcademicLevel($academicLevel)
                ->orderBy('is_mandatory_for_level', 'desc')
                ->orderBy('category')
                ->orderBy('name')
                ->get()
                ->groupBy('category');
                
            // Transform for frontend consumption
            $transformedServices = [];
            foreach ($availableServices as $category => $services) {
                $transformedServices[$category] = $services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'code' => $service->code,
                        'amount' => $service->amount,
                        'is_mandatory_for_level' => $service->is_mandatory_for_level,
                        'due_date_offset' => $service->due_date_offset,
                        'description' => $service->description,
                        'category' => $service->category
                    ];
                })->toArray();
            }
                
            return $this->success($transformedServices, 'Available services retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to retrieve available services for registration', [
                'class_id' => $request->input('class_id'),
                'error' => $e->getMessage()
            ]);
            
            return $this->error('Failed to retrieve available services', 500);
        }
    }

    /**
     * Get available services for a student based on their academic level
     */
    public function getAvailableServices(Student $student): JsonResponse
    {
        try {
            $services = $this->serviceManager->getAvailableServices($student);
            $academicLevel = $student->getAcademicLevel();

            return $this->success([
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'academic_level' => $academicLevel,
                'available_services' => $services->map(function ($categoryServices, $category) {
                    return [
                        'category' => $category,
                        'services' => $categoryServices->map(function ($service) {
                            return [
                                'id' => $service->id,
                                'name' => $service->name,
                                'code' => $service->code,
                                'amount' => $service->amount,
                                'is_mandatory' => $service->is_mandatory_for_level,
                                'due_date_offset' => $service->due_date_offset,
                                'description' => $service->description
                            ];
                        })->values()
                    ];
                })->values()
            ], 'Available services retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to get available services', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to retrieve available services', 500);
        }
    }

    /**
     * Get student's current service subscriptions
     */
    public function getStudentServices(Student $student, Request $request): JsonResponse
    {
        try {
            $academicYearId = $request->input('academic_year_id', session('academic_year_id'));
            $services = $this->serviceManager->getStudentServices($student, $academicYearId);
            $summary = $student->getServicesSummary($academicYearId);

            return $this->success([
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'academic_year_id' => $academicYearId,
                'services' => $services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'service_name' => $service->feeType->name,
                        'category' => $service->feeType->category,
                        'original_amount' => $service->amount,
                        'discount_type' => $service->discount_type,
                        'discount_value' => $service->discount_value,
                        'final_amount' => $service->final_amount,
                        'due_date' => $service->due_date?->format('Y-m-d'),
                        'is_overdue' => $service->isOverdue(),
                        'due_soon' => $service->isDueSoon(),
                        'notes' => $service->notes,
                        'subscription_date' => $service->subscription_date?->format('Y-m-d H:i:s')
                    ];
                }),
                'summary' => $summary
            ], 'Student services retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Failed to get student services', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to retrieve student services', 500);
        }
    }

    /**
     * Subscribe student to a service
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'fee_type_id' => 'required|exists:fees_types,id',
            'academic_year_id' => 'nullable|exists:sessions,id',
            'amount' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'discount.type' => 'nullable|in:none,percentage,fixed,override',
            'discount.value' => 'nullable|numeric|min:0',
            'discount.notes' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $student = Student::findOrFail($request->input('student_id'));
            $feeType = FeesType::findOrFail($request->input('fee_type_id'));

            $options = [
                'academic_year_id' => $request->input('academic_year_id'),
                'amount' => $request->input('amount'),
                'due_date' => $request->input('due_date'),
                'notes' => $request->input('notes')
            ];

            if ($request->has('discount') && $request->input('discount.type') !== 'none') {
                $options['discount'] = [
                    'type' => $request->input('discount.type'),
                    'value' => $request->input('discount.value'),
                    'notes' => $request->input('discount.notes')
                ];
            }

            $subscription = $this->serviceManager->subscribeToService($student, $feeType, $options);

            return $this->success([
                'subscription' => [
                    'id' => $subscription->id,
                    'student_name' => $subscription->student->full_name,
                    'service_name' => $subscription->feeType->name,
                    'original_amount' => $subscription->amount,
                    'final_amount' => $subscription->final_amount,
                    'discount_applied' => $subscription->getDiscountSummary(),
                    'due_date' => $subscription->due_date?->format('Y-m-d'),
                    'subscription_date' => $subscription->subscription_date?->format('Y-m-d H:i:s')
                ]
            ], 'Student successfully subscribed to service', 201);

        } catch (\Exception $e) {
            Log::error('Failed to subscribe student to service', [
                'student_id' => $request->input('student_id'),
                'fee_type_id' => $request->input('fee_type_id'),
                'error' => $e->getMessage()
            ]);

            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Apply discount to a service subscription
     */
    public function applyDiscount(StudentService $service, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'discount_type' => 'required|in:percentage,fixed,override',
            'discount_value' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $originalAmount = $service->final_amount;
            
            $this->serviceManager->applyDiscount(
                $service,
                $request->input('discount_type'),
                $request->input('discount_value'),
                $request->input('notes')
            );

            $service->refresh();

            return $this->success([
                'service_id' => $service->id,
                'student_name' => $service->student->full_name,
                'service_name' => $service->feeType->name,
                'original_amount' => $service->amount,
                'previous_final_amount' => $originalAmount,
                'new_final_amount' => $service->final_amount,
                'discount_summary' => $service->getDiscountSummary(),
                'savings' => $originalAmount - $service->final_amount
            ], 'Discount applied successfully');

        } catch (\Exception $e) {
            Log::error('Failed to apply discount', [
                'service_id' => $service->id,
                'error' => $e->getMessage()
            ]);

            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * Remove discount from a service subscription
     */
    public function removeDiscount(StudentService $service, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            if (!$service->hasDiscount()) {
                return $this->error('Service does not have any discount to remove', 400);
            }

            $originalFinalAmount = $service->final_amount;
            $this->serviceManager->removeDiscount($service, $request->input('reason'));
            $service->refresh();

            return $this->success([
                'service_id' => $service->id,
                'student_name' => $service->student->full_name,
                'service_name' => $service->feeType->name,
                'original_amount' => $service->amount,
                'previous_final_amount' => $originalFinalAmount,
                'current_final_amount' => $service->final_amount,
                'discount_removed' => $service->final_amount - $originalFinalAmount
            ], 'Discount removed successfully');

        } catch (\Exception $e) {
            Log::error('Failed to remove discount', [
                'service_id' => $service->id,
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to remove discount', 500);
        }
    }

    /**
     * Auto-subscribe student to mandatory services
     */
    public function autoSubscribeMandatory(Student $student, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'academic_year_id' => 'nullable|exists:sessions,id'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $academicYearId = $request->input('academic_year_id', session('academic_year_id'));
            $subscriptions = $this->serviceManager->autoSubscribeMandatoryServices($student, $academicYearId);

            return $this->success([
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'academic_level' => $student->getAcademicLevel(),
                'subscriptions_created' => $subscriptions->count(),
                'subscriptions' => $subscriptions->map(function ($subscription) {
                    return [
                        'id' => $subscription->id,
                        'service_name' => $subscription->feeType->name,
                        'amount' => $subscription->final_amount,
                        'due_date' => $subscription->due_date?->format('Y-m-d')
                    ];
                }),
                'total_amount' => $subscriptions->sum('final_amount')
            ], 'Mandatory services auto-subscribed successfully', 201);

        } catch (\Exception $e) {
            Log::error('Failed to auto-subscribe mandatory services', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to auto-subscribe mandatory services', 500);
        }
    }

    /**
     * Bulk subscribe students to services
     */
    public function bulkSubscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'fee_type_ids' => 'required|array|min:1',
            'fee_type_ids.*' => 'exists:fees_types,id',
            'academic_year_id' => 'nullable|exists:sessions,id',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $students = Student::whereIn('id', $request->input('student_ids'))->get();
            $options = [
                'academic_year_id' => $request->input('academic_year_id'),
                'notes' => $request->input('notes', 'Bulk subscription')
            ];

            $results = $this->serviceManager->bulkSubscribeStudents(
                $students,
                $request->input('fee_type_ids'),
                $options
            );

            return $this->success([
                'total_students' => $students->count(),
                'total_services' => count($request->input('fee_type_ids')),
                'successful_subscriptions' => count($results['success']),
                'skipped_subscriptions' => count($results['skipped']),
                'failed_subscriptions' => count($results['errors']),
                'results' => $results
            ], 'Bulk subscription completed');

        } catch (\Exception $e) {
            Log::error('Failed to bulk subscribe students', [
                'student_ids' => $request->input('student_ids'),
                'fee_type_ids' => $request->input('fee_type_ids'),
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to bulk subscribe students', 500);
        }
    }

    /**
     * Bulk apply discount to services
     */
    public function bulkApplyDiscount(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_ids' => 'required|array|min:1',
            'service_ids.*' => 'exists:student_services,id',
            'discount_type' => 'required|in:percentage,fixed,override',
            'discount_value' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $services = StudentService::whereIn('id', $request->input('service_ids'))
                ->with(['student', 'feeType'])
                ->get();

            $results = $this->serviceManager->bulkApplyDiscount(
                $services,
                $request->input('discount_type'),
                $request->input('discount_value'),
                $request->input('notes')
            );

            return $this->success([
                'total_services' => $services->count(),
                'successful_applications' => $results['success'],
                'failed_applications' => count($results['errors']),
                'errors' => $results['errors']
            ], 'Bulk discount application completed');

        } catch (\Exception $e) {
            Log::error('Failed to bulk apply discount', [
                'service_ids' => $request->input('service_ids'),
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to bulk apply discount', 500);
        }
    }

    /**
     * Deactivate/Unsubscribe from service
     */
    public function unsubscribe(StudentService $service, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            if (!$service->is_active) {
                return $this->error('Service is already inactive', 400);
            }

            $this->serviceManager->unsubscribeFromService($service, $request->input('reason'));

            return $this->success([
                'service_id' => $service->id,
                'student_name' => $service->student->full_name,
                'service_name' => $service->feeType->name,
                'status' => 'unsubscribed',
                'reason' => $request->input('reason')
            ], 'Successfully unsubscribed from service');

        } catch (\Exception $e) {
            Log::error('Failed to unsubscribe from service', [
                'service_id' => $service->id,
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to unsubscribe from service', 500);
        }
    }

    /**
     * Get service preview for fee generation
     */
    public function generatePreview(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'academic_year_id' => 'nullable|exists:sessions,id'
        ]);

        if ($validator->fails()) {
            return $this->error('Validation failed', 422, $validator->errors());
        }

        try {
            $students = Student::whereIn('id', $request->input('student_ids'))->get();
            $academicYearId = $request->input('academic_year_id', session('academic_year_id'));

            $preview = $this->serviceManager->generateFeePreview($students, $academicYearId);

            return $this->success([
                'academic_year_id' => $academicYearId,
                'total_students' => $students->count(),
                'total_amount' => $preview->sum('total_amount'),
                'total_discount' => $preview->sum('total_discount'),
                'students' => $preview->map(function ($studentPreview) {
                    return [
                        'student_id' => $studentPreview['student']->id,
                        'student_name' => $studentPreview['student']->full_name,
                        'total_amount' => $studentPreview['total_amount'],
                        'total_discount' => $studentPreview['total_discount'],
                        'service_count' => $studentPreview['services']->count(),
                        'mandatory_count' => $studentPreview['mandatory_count'],
                        'optional_count' => $studentPreview['optional_count'],
                        'services' => $studentPreview['services']->map(function ($service) {
                            return [
                                'name' => $service->feeType->name,
                                'category' => $service->feeType->category,
                                'amount' => $service->final_amount,
                                'due_date' => $service->due_date?->format('Y-m-d')
                            ];
                        })
                    ];
                })
            ], 'Fee preview generated successfully');

        } catch (\Exception $e) {
            Log::error('Failed to generate fee preview', [
                'student_ids' => $request->input('student_ids'),
                'error' => $e->getMessage()
            ]);

            return $this->error('Failed to generate fee preview', 500);
        }
    }

    /**
     * Get services for student registration based on class selection
     */
    public function getServicesForRegistration(Request $request): JsonResponse
    {
        try {
            $classId = $request->get('class_id');
            
            if (!$classId) {
                return $this->error('Class ID is required');
            }
            
            // Get class information to determine academic level
            $class = Classes::find($classId);
            if (!$class) {
                return $this->error('Class not found');
            }
            
            // Determine academic level from class
            $academicLevel = $this->determineAcademicLevel($class);
            $academicYearId = setting('session');
            
            // Get services for this academic level
            $services = FeesType::active()
                ->forAcademicLevel($academicLevel)
                ->get();
            
            // Group by category and separate mandatory from optional
            $groupedServices = [];
            foreach ($services as $service) {
                $category = $service->category;
                $isMandatory = $service->is_mandatory_for_level;
                
                if (!isset($groupedServices[$category])) {
                    $groupedServices[$category] = [
                        'mandatory' => [],
                        'optional' => []
                    ];
                }
                
                $serviceData = [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'amount' => $service->amount,
                    'due_date' => $service->due_date,
                    'category' => $service->category,
                    'is_mandatory_for_level' => $isMandatory
                ];
                
                if ($isMandatory) {
                    $groupedServices[$category]['mandatory'][] = $serviceData;
                } else {
                    $groupedServices[$category]['optional'][] = $serviceData;
                }
            }
            
            return $this->success([
                'services' => $groupedServices,
                'academic_level' => $academicLevel,
                'class_name' => $class->name
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to get services for registration: ' . $e->getMessage());
            return $this->error('Failed to retrieve services');
        }
    }
    
    /**
     * Determine academic level from class
     */
    private function determineAcademicLevel($class): string
    {
        $classNumber = (int) $class->numeric_name ?? 0;
        
        return match(true) {
            $classNumber >= 1 && $classNumber <= 5 => 'primary',
            $classNumber >= 6 && $classNumber <= 10 => 'secondary', 
            $classNumber >= 11 && $classNumber <= 12 => 'high_school',
            $classNumber < 1 => 'kg',
            default => 'primary'
        };
    }
}