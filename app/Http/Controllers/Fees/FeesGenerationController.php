<?php

namespace App\Http\Controllers\Fees;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Fees\FeesGenerationRepository;
use App\Services\FeesGenerationService;
use App\Services\EnhancedFeesGenerationService;
use App\Services\FeesServiceManager;
use App\Services\BatchIdService;
use App\Repositories\Academic\ClassesRepository;
use App\Repositories\Academic\SectionRepository;
use App\Repositories\Academic\ClassSetupRepository;
use App\Repositories\StudentInfo\StudentRepository;
use App\Repositories\Fees\FeesMasterRepository;
use App\Models\StudentInfo\Student;
use App\Models\Academic\Classes as AcademicClass;
use App\Models\Fees\FeesType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class FeesGenerationController extends Controller
{
    private $repo;
    private $service;
    private $enhancedService;
    private $serviceManager;
    private $batchIdService;
    private $classRepo;
    private $sectionRepo;
    private $classSetupRepo;
    private $studentRepo;
    private $feesMasterRepo;

    public function __construct(
        FeesGenerationRepository $repo,
        FeesGenerationService $service,
        EnhancedFeesGenerationService $enhancedService,
        FeesServiceManager $serviceManager,
        BatchIdService $batchIdService,
        ClassesRepository $classRepo,
        SectionRepository $sectionRepo,
        ClassSetupRepository $classSetupRepo,
        StudentRepository $studentRepo,
        FeesMasterRepository $feesMasterRepo
    ) {
        $this->repo = $repo;
        $this->service = $service;
        $this->enhancedService = $enhancedService;
        $this->serviceManager = $serviceManager;
        $this->batchIdService = $batchIdService;
        $this->classRepo = $classRepo;
        $this->sectionRepo = $sectionRepo;
        $this->classSetupRepo = $classSetupRepo;
        $this->studentRepo = $studentRepo;
        $this->feesMasterRepo = $feesMasterRepo;
    }

    public function index()
    {
        $data['title'] = ___('fees.fee_generation');
        $branchId = auth()->user()->branch_id ?? null;

        // Get enhanced service statistics
        $stats = $this->serviceManager->getUsageStatistics($branchId);
        $data['enhanced_stats'] = $stats;

        // Get total available services
        $data['total_available_services'] = FeesType::active()
            ->when($branchId && Schema::hasColumn('fees_types', 'branch_id'), function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->count();

        // Total active classes for dashboard summary
        $data['total_classes'] = AcademicClass::active()
            ->when($branchId && Schema::hasColumn('classes', 'branch_id'), function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->count();

        // Get service-based generation history
        $data['generations'] = $this->repo->getPaginateAll();

        return view('backend.fees.generation.index', compact('data'));
    }

    public function create()
    {
        $data['title'] = ___('fees.fee_generation');
        $data['classes'] = $this->classRepo->assignedAll();
        $data['sections'] = [];
        $data['fees_groups'] = $this->feesMasterRepo->allGroups();
        
        return view('backend.fees.generation.create', compact('data'));
    }

    public function preview(Request $request): JsonResponse
    {
        try {
            $selectionMethod = $request->input('selection_method', 'class_section');

            $rules = [
                'selection_method' => ['required', Rule::in(['class_section', 'grade'])],
                'classes' => ['nullable', 'array'],
                'classes.*' => ['exists:classes,id'],
                'sections' => ['nullable', 'array'],
                'sections.*' => ['exists:sections,id'],
                'grades' => ['nullable', 'array'],
                'grades.*' => [Rule::in(Student::getAllGrades())],
                'month' => 'required|integer|between:1,12',
                'year' => 'required|integer|in:' . date('Y'),
                'fees_groups' => ['nullable', 'array'],
                'fees_groups.*' => ['exists:fees_groups,id']
            ];

            if ($selectionMethod === 'class_section') {
                $rules['classes'][] = 'required';
            }

            if ($selectionMethod === 'grade') {
                $rules['grades'][] = 'required';
            }

            $filters = $request->validate($rules);
            $filters['selection_method'] = $selectionMethod;
            $filters['classes'] = $filters['classes'] ?? [];
            $filters['sections'] = $filters['sections'] ?? [];
            $filters['grades'] = $filters['grades'] ?? [];
            $filters['fees_groups'] = $filters['fees_groups'] ?? [];

            $preview = $this->service->generatePreview($filters);

            return response()->json([
                'success' => true,
                'data' => $preview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function generate(Request $request): JsonResponse
    {
        try {
            $selectionMethod = $request->input('selection_method', 'class_section');

            $rules = [
                'selection_method' => ['required', Rule::in(['class_section', 'grade'])],
                'classes' => ['nullable', 'array'],
                'classes.*' => ['exists:classes,id'],
                'sections' => ['nullable', 'array'],
                'sections.*' => ['exists:sections,id'],
                'grades' => ['nullable', 'array'],
                'grades.*' => [Rule::in(Student::getAllGrades())],
                'month' => 'required|integer|between:1,12',
                'year' => 'required|integer|in:' . date('Y'),
                'fees_groups' => ['nullable', 'array'],
                'fees_groups.*' => ['exists:fees_groups,id'],
                'selected_students' => ['nullable', 'array'],
                'selected_students.*' => ['exists:students,id'],
                'notes' => 'nullable|string|max:500'
            ];

            if ($selectionMethod === 'class_section') {
                $rules['classes'][] = 'required';
            }

            if ($selectionMethod === 'grade') {
                $rules['grades'][] = 'required';
            }

            $data = $request->validate($rules);
            $data['selection_method'] = $selectionMethod;
            $data['classes'] = $data['classes'] ?? [];
            $data['sections'] = $data['sections'] ?? [];
            $data['grades'] = $data['grades'] ?? [];
            $data['fees_groups'] = $data['fees_groups'] ?? [];
            $data['selected_students'] = $data['selected_students'] ?? [];

            // Generate batch ID using BatchIdService
            $batchId = $this->batchIdService->generateBatchId();
            
            // Add batch ID and creator info
            $data['batch_id'] = $batchId;
            $data['created_by'] = auth()->id();
            $data['school_id'] = auth()->user()->school_id ?? null;

            $generation = $this->service->generateFees($data);

            return response()->json([
                'success' => true,
                'message' => ___('fees.generation_started'),
                'data' => [
                    'batch_id' => $generation->batch_id,
                    'id' => $generation->id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function status(Request $request, $batchId): JsonResponse
    {
        try {
            $generation = $this->repo->findByBatchId($batchId);
            
            if (!$generation) {
                return response()->json([
                    'success' => false,
                    'message' => ___('fees.generation_not_found')
                ], 404);
            }

            $status = $this->service->getGenerationStatus($generation);

            return response()->json([
                'success' => true,
                'data' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function history()
    {
        $data['title'] = ___('fees.generation_history');
        $data['generations'] = $this->repo->getHistoryPaginated();
        
        return view('backend.fees.generation.history', compact('data'));
    }

    public function show($id)
    {
        $data['title'] = ___('fees.generation_details');
        $data['generation'] = $this->repo->findWithLogs($id);
        
        if (!$data['generation']) {
            return redirect()->route('fees-generation.index')
                ->with('error', ___('fees.generation_not_found'));
        }
        
        return view('backend.fees.generation.show', compact('data'));
    }

    public function cancel(Request $request, $id): JsonResponse
    {
        try {
            $generation = $this->repo->show($id);
            
            if (!$generation) {
                return response()->json([
                    'success' => false,
                    'message' => ___('fees.generation_not_found')
                ], 404);
            }

            if (!$generation->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => ___('fees.cannot_cancel_generation')
                ], 422);
            }

            $this->service->cancelGeneration($generation);

            return response()->json([
                'success' => true,
                'message' => ___('fees.generation_cancelled')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getSections(Request $request): JsonResponse
    {
        try {
            $classIds = $request->input('class_ids', []);
            
            if (empty($classIds)) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }

            $sections = $this->classSetupRepo->getSectionsByClasses($classIds);

            return response()->json([
                'success' => true,
                'data' => $sections
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getStudentCount(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['classes', 'sections', 'month', 'year']);
            $count = $this->service->getStudentCount($filters);

            return response()->json([
                'success' => true,
                'data' => ['count' => $count]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Generate preview using the active service
     */
    public function generatePreviewWithManager(Request $request): JsonResponse
    {
        try {
            // Get all form data and process array parameters
            $filters = [
                'selection_method' => $request->input('selection_method', 'class_section'),
                'classes' => $request->input('classes', []),
                'sections' => $request->input('sections', []),
                'grades' => $request->input('grades', []),
                'month' => $request->input('month'),
                'year' => $request->input('year'),
                'fees_groups' => $request->input('fees_groups', []),
                'academic_year_id' => $request->input('academic_year_id', session('academic_year_id', 1))
            ];
            
            // Use service manager to get preview from active service
            $preview = $this->serviceManager->generatePreview($filters);
            
            $preview['active_system'] = $this->serviceManager->isEnhancedSystemEnabled() ? 'enhanced' : 'legacy';
            
            return response()->json([
                'success' => true,
                'data' => $preview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate fees using the active service
     */
    public function generateFeesWithManager(Request $request): JsonResponse
    {
        try {
            $data = $this->prepareGenerationData($request);
            
            // Use service manager to generate fees with active service
            $generation = $this->serviceManager->generateFees($data);
            
            return response()->json([
                'success' => true,
                'message' => ___('fees.generation_initiated'),
                'data' => [
                    'generation_id' => $generation->id,
                    'batch_id' => $generation->batch_id,
                    'active_system' => $this->serviceManager->isEnhancedSystemEnabled() ? 'enhanced' : 'legacy'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prepare generation data from request
     */
    private function prepareGenerationData(Request $request): array
    {
        return [
            'batch_id' => $this->batchIdService->generateBatchId(),
            'selection_method' => $request->input('selection_method', 'class_section'),
            'classes' => $request->input('classes', []),
            'sections' => $request->input('sections', []),
            'grades' => $request->input('grades', []),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
            'fees_groups' => $request->input('fees_groups', []),
            'selected_students' => $request->input('selected_students', []),
            'notes' => $request->input('notes'),
            'created_by' => auth()->id(),
            'school_id' => auth()->user()->school_id ?? null
        ];
    }

    /**
     * Preview service-based fee generation
     */
    public function previewServiceBased(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'generation_month' => 'required|date_format:Y-m',
                'academic_year_id' => 'nullable|integer',
                'class_ids' => 'nullable|array',
                'section_ids' => 'nullable|array',
                'fee_type_ids' => 'nullable|array',
                'include_one_time_fees' => 'nullable|boolean'
            ]);

            $month = Carbon::createFromFormat('Y-m', $request->generation_month);
            $filters = $this->buildServiceFilters($request);

            $preview = $this->enhancedService->previewMonthlyFees($month, $filters);

            return response()->json([
                'status' => 'success',
                'message' => 'Service-based fee preview generated successfully',
                'data' => $preview
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate preview: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Generate service-based fees for students
     */
    public function generateServiceBased(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'generation_month' => 'required|date_format:Y-m',
                'academic_year_id' => 'nullable|integer',
                'class_ids' => 'nullable|array',
                'section_ids' => 'nullable|array',
                'fee_type_ids' => 'nullable|array',
                'include_one_time_fees' => 'nullable|boolean',
                'use_prorated' => 'nullable|boolean',
                'notes' => 'nullable|string|max:500'
            ]);

            $month = Carbon::createFromFormat('Y-m', $request->generation_month);
            $filters = $this->buildServiceFilters($request);

            // Add generation metadata
            $filters['notes'] = $request->notes ?? 'Service-based fee generation for ' . $month->format('F Y');

            if ($request->use_prorated) {
                $result = $this->enhancedService->generateProRatedMonthlyFees($month, $filters);
            } else {
                $result = $this->enhancedService->generateMonthlyFees($month, $filters);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Service-based fees generated successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate fees: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Generate monthly fees for current month
     */
    public function generateCurrentMonth(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'academic_year_id' => 'nullable|integer',
                'class_ids' => 'nullable|array',
                'section_ids' => 'nullable|array',
                'use_prorated' => 'nullable|boolean'
            ]);

            $currentMonth = now();
            $filters = $this->buildServiceFilters($request);

            if ($request->use_prorated) {
                $result = $this->enhancedService->generateProRatedMonthlyFees($currentMonth, $filters);
            } else {
                $result = $this->enhancedService->generateMonthlyFees($currentMonth, $filters);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Current month fees generated successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate current month fees: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get service-based generation status
     */
    public function serviceBasedStatus($generationId): JsonResponse
    {
        try {
            $generation = $this->repo->find($generationId);

            if (!$generation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Generation record not found'
                ], 404);
            }

            $status = $this->enhancedService->getGenerationStatus($generation);

            return response()->json([
                'status' => 'success',
                'data' => $status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get generation status: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Cancel service-based fee generation
     */
    public function cancelServiceBased($generationId): JsonResponse
    {
        try {
            $generation = $this->repo->find($generationId);

            if (!$generation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Generation record not found'
                ], 404);
            }

            $this->enhancedService->cancelGeneration($generation);

            return response()->json([
                'status' => 'success',
                'message' => 'Fee generation cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to cancel generation: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Auto-subscribe students to mandatory services before generation
     */
    public function autoSubscribeMandatory(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'academic_year_id' => 'nullable|integer',
                'class_ids' => 'nullable|array',
                'section_ids' => 'nullable|array'
            ]);

            $filters = $this->buildServiceFilters($request);
            $result = $this->enhancedService->autoSubscribeMandatoryServices($filters);

            return response()->json([
                'status' => 'success',
                'message' => 'Mandatory services subscribed successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to subscribe mandatory services: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Build service filters from request
     */
    private function buildServiceFilters(Request $request): array
    {
        $filters = [
            'academic_year_id' => $request->academic_year_id ?? session('academic_year_id'),
            'school_id' => auth()->user()->school_id ?? null
        ];

        if ($request->class_ids) {
            $filters['class_ids'] = $request->class_ids;
        }

        if ($request->section_ids) {
            $filters['section_ids'] = $request->section_ids;
        }

        if ($request->fee_type_ids) {
            $filters['fee_type_ids'] = $request->fee_type_ids;
        }

        if ($request->include_one_time_fees) {
            $filters['include_one_time_fees'] = $request->include_one_time_fees;
        }

        return $filters;
    }

    /**
     * Service-based fee generation reports
     */
    public function serviceReports()
    {
        $data['title'] = ___('fees.service_based_reports');
        $data['classes'] = $this->classRepo->assignedAll();
        $data['sections'] = [];
        $data['billing_periods'] = $this->getAvailableBillingPeriods();
        $data['academic_years'] = $this->getAcademicYears();

        return view('backend.fees.generation.service-reports', compact('data'));
    }

    /**
     * Search service-based fee generation data
     */
    public function serviceReportsSearch(Request $request)
    {
        $data['title'] = ___('fees.service_based_reports');
        $data['classes'] = $this->classRepo->assignedAll();
        $data['billing_periods'] = $this->getAvailableBillingPeriods();
        $data['academic_years'] = $this->getAcademicYears();

        // Build filters
        $filters = [
            'class_id' => $request->input('class'),
            'section_id' => $request->input('section'),
            'student_search' => $request->input('name'),
            'payment_status' => $request->input('payment_status'),
            'billing_periods' => $request->input('billing_periods', []),
            'academic_year_id' => $request->input('academic_year_id'),
        ];

        // Get service-based fee data with filters
        $data['students'] = $this->getServiceBasedStudents($filters);

        // Get sections for selected class
        if ($request->input('class')) {
            $data['sections'] = $this->classSetupRepo->getSectionsByClasses([$request->input('class')]);
        }

        return view('backend.fees.generation.service-reports', compact('data'));
    }

    /**
     * Get students with service-based fees
     */
    private function getServiceBasedStudents(array $filters)
    {
        $query = \DB::table('fees_generation_logs as fgl')
            ->join('fees_generations as fg', 'fg.id', '=', 'fgl.fees_generation_id')
            ->join('students as s', 's.id', '=', 'fgl.student_id')
            ->leftJoin('session_class_students as ssd', function($join) {
                $join->on('ssd.student_id', '=', 's.id')
                     ->where('ssd.session_id', '=', session('session_id', 1));
            })
            ->leftJoin('classes as c', 'c.id', '=', 'ssd.classes_id')
            ->leftJoin('sections as sec', 'sec.id', '=', 'ssd.section_id')
            ->leftJoin('fees_collects as fc', function($join) {
                $join->on('fc.student_id', '=', 's.id')
                     ->on('fc.generation_batch_id', '=', 'fg.batch_id');
            })
            ->leftJoin('fees_types as ft', 'ft.id', '=', 'fc.fee_type_id')
            ->leftJoin('parent_guardians as pg', 'pg.id', '=', 's.parent_guardian_id')
            ->where('fc.generation_method', 'service_based')
            ->select([
                's.id as student_id',
                's.first_name',
                's.last_name',
                's.mobile',
                'c.name as class_name',
                'sec.name as section_name',
                'pg.guardian_name',
                'fg.batch_id',
                'fg.created_at as generation_date',
                'fg.status as generation_status',
                'fc.amount',
                'fc.payment_method',
                'fc.billing_period',
                'fc.academic_year_id',
                'ft.name as fee_type_name',
                'ft.category as fee_category',
                'fgl.status as log_status',
                'fgl.error_message'
            ]);

        // Apply filters
        if (!empty($filters['class_id'])) {
            $query->where('ssd.classes_id', $filters['class_id']);
        }

        if (!empty($filters['section_id'])) {
            $query->where('ssd.section_id', $filters['section_id']);
        }

        if (!empty($filters['student_search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('s.first_name', 'like', '%' . $filters['student_search'] . '%')
                  ->orWhere('s.last_name', 'like', '%' . $filters['student_search'] . '%');
            });
        }

        if (!empty($filters['payment_status'])) {
            switch ($filters['payment_status']) {
                case 'paid':
                    // Use enhanced payment tracking: payment_method OR payment_status = 'paid' OR total_paid >= amount
                    $query->where(function($paymentQuery) {
                        $paymentQuery->whereNotNull('fc.payment_method')
                                   ->orWhere('fc.payment_status', 'paid')
                                   ->orWhereColumn('fc.total_paid', '>=', 'fc.amount');
                    });
                    break;
                case 'unpaid':
                case 'overdue':
                    // Fee exists but is not paid (opposite of paid logic)
                    $query->whereNull('fc.payment_method')
                          ->where(function($paymentQuery) {
                              $paymentQuery->where('fc.payment_status', '!=', 'paid')
                                           ->orWhereNull('fc.payment_status');
                          })
                          ->whereColumn('fc.total_paid', '<', 'fc.amount');
                    break;
            }
        }

        if (!empty($filters['billing_periods']) && is_array($filters['billing_periods'])) {
            $query->whereIn('fc.billing_period', $filters['billing_periods']);
        }

        if (!empty($filters['academic_year_id'])) {
            $query->where('fc.academic_year_id', $filters['academic_year_id']);
        }

        return $query->orderBy('fg.created_at', 'desc')
                     ->orderBy('s.first_name')
                     ->paginate(50);
    }

    /**
     * Get available billing periods from service-based fees
     */
    private function getAvailableBillingPeriods()
    {
        $periods = \DB::table('fees_collects as fc')
            ->where('fc.generation_method', 'service_based')
            ->whereNotNull('fc.billing_period')
            ->select('fc.billing_period')
            ->distinct()
            ->orderBy('fc.billing_period', 'desc')
            ->limit(24) // Last 24 months
            ->get()
            ->map(function($item) {
                try {
                    $date = \Carbon\Carbon::createFromFormat('Y-m', $item->billing_period);
                    return [
                        'value' => $item->billing_period,
                        'label' => $date->format('F Y'), // e.g., "October 2024"
                        'short_label' => $date->format('M Y'), // e.g., "Oct 2024"
                        'year' => $date->year,
                        'month' => $date->month,
                        'is_current' => $date->format('Y-m') === now()->format('Y-m'),
                        'is_past' => $date->lt(now()->startOfMonth()),
                    ];
                } catch (\Exception $e) {
                    return null;
                }
            })
            ->filter() // Remove null values
            ->values();

        return $periods;
    }

    /**
     * Get available academic years
     */
    private function getAcademicYears()
    {
        $years = \DB::table('sessions')
            ->select('id', 'name', 'start_date', 'end_date')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function($session) {
                return [
                    'id' => $session->id,
                    'name' => $session->name,
                    'is_current' => $session->id == session('session_id', 1)
                ];
            });

        // If no sessions found, create a fallback
        if ($years->isEmpty()) {
            $currentYear = now()->year;
            $years = collect([
                [
                    'id' => 1,
                    'name' => ($currentYear - 1) . '-' . $currentYear,
                    'is_current' => true
                ]
            ]);
        }

        return $years;
    }

    /**
     * Generate preview for grade-based fee generation
     */
    public function previewByGrades(Request $request): JsonResponse
    {
        try {
            $filters = $request->validate([
                'grades' => ['required', 'array'],
                'grades.*' => [Rule::in(Student::getAllGrades())],
                'classes' => ['nullable', 'array'],
                'classes.*' => ['exists:classes,id'],
                'sections' => ['nullable', 'array'],
                'sections.*' => ['exists:sections,id'],
                'month' => 'required|integer|between:1,12',
                'year' => 'required|integer|in:' . date('Y'),
                'fees_groups' => ['nullable', 'array'],
                'fees_groups.*' => ['exists:fees_groups,id']
            ]);

            $filters['selection_method'] = 'grade';
            $filters['classes'] = $filters['classes'] ?? [];
            $filters['sections'] = $filters['sections'] ?? [];
            $filters['fees_groups'] = $filters['fees_groups'] ?? [];

            $preview = $this->service->generatePreview($filters);

            return response()->json([
                'success' => true,
                'data' => $preview
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Generate fees for students filtered by grades
     */
    public function generateByGrades(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'grades' => ['required', 'array'],
                'grades.*' => [Rule::in(Student::getAllGrades())],
                'classes' => ['nullable', 'array'],
                'classes.*' => ['exists:classes,id'],
                'sections' => ['nullable', 'array'],
                'sections.*' => ['exists:sections,id'],
                'month' => 'required|integer|between:1,12',
                'year' => 'required|integer|in:' . date('Y'),
                'fees_groups' => ['required', 'array'],
                'fees_groups.*' => ['exists:fees_groups,id'],
                'selected_students' => ['nullable', 'array'],
                'selected_students.*' => ['exists:students,id'],
                'notes' => 'nullable|string|max:500'
            ]);

            $data['selection_method'] = 'grade';
            $data['classes'] = $data['classes'] ?? [];
            $data['sections'] = $data['sections'] ?? [];
            $data['fees_groups'] = $data['fees_groups'] ?? [];
            $data['selected_students'] = $data['selected_students'] ?? [];

            // Add required data for generation
            $data['batch_id'] = $this->batchIdService->generateBatchId();
            $data['created_by'] = auth()->id();
            $data['school_id'] = auth()->user()->school_id ?? 1;

            $generation = $this->service->generateFees($data);

            return response()->json([
                'success' => true,
                'message' => ___('fees.generation_started'),
                'data' => [
                    'generation_id' => $generation->id,
                    'batch_id' => $generation->batch_id
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get student count by grades
     */
    public function getStudentCountByGrades(Request $request): JsonResponse
    {
        try {
            $filters = $request->validate([
                'grades' => ['required', 'array'],
                'grades.*' => [Rule::in(Student::getAllGrades())],
                'classes' => ['nullable', 'array'],
                'classes.*' => ['exists:classes,id'],
                'sections' => ['nullable', 'array'],
                'sections.*' => ['exists:sections,id']
            ]);

            $filters['selection_method'] = 'grade';
            $filters['classes'] = $filters['classes'] ?? [];
            $filters['sections'] = $filters['sections'] ?? [];

            $data = $this->service->getStudentCountByGrades($filters);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get grade-wise student distribution
     */
    public function getGradeDistribution(): JsonResponse
    {
        try {
            $distribution = $this->service->getGradeDistribution();

            return response()->json([
                'success' => true,
                'data' => $distribution
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available grades with academic level grouping
     */
    public function getAvailableGrades(): JsonResponse
    {
        try {
            $gradeOptions = \App\Models\StudentInfo\Student::getGradeOptions();
            $distribution = $this->service->getGradeDistribution();

            // Add student counts to grade options
            $gradesWithCounts = [];
            foreach ($gradeOptions as $level => $grades) {
                $gradesWithCounts[$level] = [];
                foreach ($grades as $gradeValue => $gradeName) {
                    $studentCount = collect($distribution)->where('grade', $gradeValue)->first()['count'] ?? 0;
                    $gradesWithCounts[$level][] = [
                        'value' => $gradeValue,
                        'name' => $gradeName,
                        'student_count' => $studentCount,
                        'academic_level' => $level
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'grade_options' => $gradesWithCounts,
                    'distribution' => $distribution
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate fees for all students in specific grades (bulk operation)
     */
    public function bulkGenerateByGrades(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'grades' => ['required', 'array'],
                'grades.*' => [Rule::in(Student::getAllGrades())],
                'fees_groups' => 'required|array',
                'fees_groups.*' => 'exists:fees_groups,id',
                'month' => 'required|integer|between:1,12',
                'year' => 'required|integer|in:' . date('Y'),
                'auto_assign_mandatory' => 'boolean',
                'notes' => 'nullable|string|max:500'
            ]);

            // Auto-assign mandatory services if requested
            if ($data['auto_assign_mandatory'] ?? false) {
                $assignmentResults = $this->serviceManager->bulkSubscribeByGrade(
                    $data['grades'],
                    session('academic_year_id')
                );

                if (!empty($assignmentResults['errors'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Some mandatory service assignments failed',
                        'data' => $assignmentResults
                    ], 422);
                }
            }

            // Generate fees for all grades
            $data['batch_id'] = $this->batchIdService->generateBatchId();
            $data['created_by'] = auth()->id();
            $data['school_id'] = auth()->user()->school_id ?? 1;
            $data['selection_method'] = 'grade';
            $data['classes'] = $data['classes'] ?? [];
            $data['sections'] = $data['sections'] ?? [];
            $data['selected_students'] = $data['selected_students'] ?? [];

            $generation = $this->service->generateFees($data);

            return response()->json([
                'success' => true,
                'message' => ___('fees.bulk_generation_started'),
                'data' => [
                    'generation_id' => $generation->id,
                    'batch_id' => $generation->batch_id,
                    'total_students' => $generation->total_students,
                    'assignment_results' => $assignmentResults ?? null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
