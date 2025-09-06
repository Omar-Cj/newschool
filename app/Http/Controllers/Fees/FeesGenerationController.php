<?php

namespace App\Http\Controllers\Fees;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Fees\FeesGenerationRepository;
use App\Services\FeesGenerationService;
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
    private $classRepo;
    private $sectionRepo;
    private $classSetupRepo;
    private $studentRepo;
    private $feesMasterRepo;

    public function __construct(
        FeesGenerationRepository $repo,
        FeesGenerationService $service,
        ClassesRepository $classRepo,
        SectionRepository $sectionRepo,
        ClassSetupRepository $classSetupRepo,
        StudentRepository $studentRepo,
        FeesMasterRepository $feesMasterRepo
    ) {
        $this->repo = $repo;
        $this->service = $service;
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

            // Generate batch ID
            $batchId = 'FG_' . date('YmdHis') . '_' . Str::random(6);
            
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
}