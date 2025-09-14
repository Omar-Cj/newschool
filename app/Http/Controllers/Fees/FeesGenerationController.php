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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
        $data['classes'] = $this->classRepo->assignedAll();
        $data['sections'] = [];
        $data['fees_groups'] = $this->feesMasterRepo->allGroups();
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
            $filters = $request->validate([
                'classes' => 'nullable|array',
                'classes.*' => 'exists:classes,id',
                'sections' => 'nullable|array', 
                'sections.*' => 'exists:sections,id',
                'month' => 'required|integer|between:1,12',
                'year' => 'required|integer|in:' . date('Y'),
                'fees_groups' => 'nullable|array',
                'fees_groups.*' => 'exists:fees_groups,id'
            ]);

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
            $data = $request->validate([
                'classes' => 'nullable|array',
                'classes.*' => 'exists:classes,id',
                'sections' => 'nullable|array',
                'sections.*' => 'exists:sections,id', 
                'month' => 'required|integer|between:1,12',
                'year' => 'required|integer|in:' . date('Y'),
                'fees_groups' => 'nullable|array',
                'fees_groups.*' => 'exists:fees_groups,id',
                'selected_students' => 'nullable|array',
                'selected_students.*' => 'exists:students,id',
                'due_date' => 'nullable|date|after:today',
                'notes' => 'nullable|string|max:500'
            ]);

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
     * Get system status and compatibility report
     */
    public function getSystemStatus(): JsonResponse
    {
        try {
            $report = $this->serviceManager->getSystemCompatibilityReport();
            $statistics = $this->serviceManager->getUsageStatistics();

            return response()->json([
                'success' => true,
                'data' => [
                    'compatibility_report' => $report,
                    'usage_statistics' => $statistics
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
     * Switch between fee generation systems
     */
    public function switchSystem(Request $request): JsonResponse
    {
        try {
            $targetSystem = $request->input('system'); // 'legacy' or 'enhanced'
            
            if (!in_array($targetSystem, ['legacy', 'enhanced'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid system type. Must be "legacy" or "enhanced".'
                ], 422);
            }

            // Validate system switch
            $validation = $this->serviceManager->validateSystemSwitch($targetSystem);
            
            if (!$validation['is_valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot switch to ' . $targetSystem . ' system.',
                    'errors' => $validation['errors']
                ], 422);
            }

            // Perform the switch
            if ($targetSystem === 'enhanced') {
                $this->serviceManager->enableEnhancedSystem();
            } else {
                $this->serviceManager->enableLegacySystem();
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully switched to ' . $targetSystem . ' fee system.',
                'warnings' => $validation['warnings'] ?? []
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
                'classes' => $request->input('classes', []),
                'sections' => $request->input('sections', []),
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
            'classes' => $request->input('classes', []),
            'sections' => $request->input('sections', []),
            'month' => $request->input('month'),
            'year' => $request->input('year'),
            'fees_groups' => $request->input('fees_groups', []),
            'selected_students' => $request->input('selected_students', []),
            'notes' => $request->input('notes'),
            'due_date' => $request->input('due_date') ? 
                Carbon::parse($request->input('due_date'))->toDateString() : null,
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
                'service_categories' => 'nullable|array',
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
                'service_categories' => 'nullable|array',
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
                'service_categories' => 'nullable|array',
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

        if ($request->service_categories) {
            $filters['service_categories'] = $request->service_categories;
        }

        if ($request->fee_type_ids) {
            $filters['fee_type_ids'] = $request->fee_type_ids;
        }

        if ($request->include_one_time_fees) {
            $filters['include_one_time_fees'] = $request->include_one_time_fees;
        }

        return $filters;
    }
}