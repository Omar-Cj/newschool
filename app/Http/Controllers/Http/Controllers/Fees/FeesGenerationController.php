<?php

namespace App\Http\Controllers\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Services\BranchAwareFeesGenerationService;
use App\Services\StudentFeesService;
use App\Models\Fees\FeesGeneration;
use App\Models\Fees\FeesType;
use App\Models\Academic\Session;
use Modules\MultiBranch\Entities\Branch;
use App\Http\Requests\Fees\Generation\FeesGenerationStoreRequest;
use App\Http\Requests\Fees\Generation\BulkFeesGenerationRequest;
use App\Http\Requests\Fees\Generation\ActiveStudentsCountRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FeesGenerationController extends Controller
{
    private BranchAwareFeesGenerationService $branchAwareService;
    private StudentFeesService $studentFeesService;

    public function __construct(
        BranchAwareFeesGenerationService $branchAwareService,
        StudentFeesService $studentFeesService
    ) {
        $this->branchAwareService = $branchAwareService;
        $this->studentFeesService = $studentFeesService;
    }

    /**
     * Display fee generation dashboard
     */
    public function index(): View
    {
        $data = [
            'title' => ___('fees.fee_generation'),
            'branches' => Branch::active()->get(),
            'generations' => FeesGeneration::with(['branch', 'creator'])
                ->latest()
                ->paginate(20),
            'academicYears' => Session::active()->get(),
        ];

        return view('backend.fees.generation.index', compact('data'));
    }

    /**
     * Show fee generation form
     */
    public function create(): View
    {
        $data = [
            'title' => ___('fees.generate_fees'),
            'branches' => Branch::active()->get(),
            'feeTypes' => FeesType::active()->get(),
            'academicYears' => Session::active()->get(),
        ];

        return view('backend.fees.generation.create', compact('data'));
    }

    /**
     * Generate fees for branch
     */
    public function store(FeesGenerationStoreRequest $request): JsonResponse
    {
        try {
            $serviceData = $request->getServiceData();

            $generation = $this->branchAwareService->generateFeesForBranch(
                $serviceData['branch_id'],
                $serviceData['academic_year_id'],
                $serviceData['fee_type_ids'],
                $serviceData['filters'],
                $serviceData['notes']
            );

            return response()->json([
                'success' => true,
                'message' => 'Fee generation initiated successfully',
                'data' => [
                    'generation_id' => $generation->id,
                    'batch_id' => $generation->batch_id,
                    'branch_name' => $generation->getBranchName(),
                    'total_students' => $generation->total_students,
                    'status' => $generation->status,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fee generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show generation details
     */
    public function show(int $id): View
    {
        $generation = FeesGeneration::with(['branch', 'creator', 'logs.student', 'feesCollects'])
            ->findOrFail($id);

        $data = [
            'title' => ___('fees.generation_details'),
            'generation' => $generation,
            'branchInfo' => $generation->getBranchInfo(),
            'logs' => $generation->logs()->with('student')->paginate(50),
            'summary' => [
                'total_amount' => $generation->feesCollects->sum('amount'),
                'paid_amount' => $generation->feesCollects->where('payment_method', '!=', null)->sum('amount'),
                'outstanding_amount' => $generation->feesCollects->where('payment_method', null)->sum('amount'),
                'collection_rate' => $generation->feesCollects->count() > 0 ?
                    ($generation->feesCollects->where('payment_method', '!=', null)->count() / $generation->feesCollects->count()) * 100 : 0,
            ]
        ];

        return view('backend.fees.generation.show', compact('data'));
    }

    /**
     * Get active students count for branch
     */
    public function getActiveStudentsCount(ActiveStudentsCountRequest $request): JsonResponse
    {
        try {
            $students = $this->studentFeesService->getActiveStudentsForBranch(
                $request->getBranchId(),
                $request->getFilters()
            );

            return response()->json([
                'success' => true,
                'data' => [
                    'total_students' => $students->count(),
                    'branch_name' => $students->first()?->branch?->name ?? 'Unknown',
                    'filters_applied' => $request->hasFilters(),
                    'filter_summary' => $request->getFilterSummary(),
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
     * Get branch statistics
     */
    public function getBranchStats(int $branchId): JsonResponse
    {
        try {
            $academicYearId = request('academic_year_id');

            $generationStats = $this->branchAwareService->getBranchGenerationStats($branchId, $academicYearId);
            $collectionStats = $this->branchAwareService->getBranchFeeCollectionSummary($branchId, $academicYearId);

            return response()->json([
                'success' => true,
                'data' => [
                    'generation_stats' => $generationStats,
                    'collection_stats' => $collectionStats,
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
     * Cancel generation (if in progress)
     */
    public function cancel(int $id): JsonResponse
    {
        try {
            $generation = FeesGeneration::findOrFail($id);

            if (!$generation->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Generation cannot be cancelled in current status'
                ], 400);
            }

            $generation->update([
                'status' => 'cancelled',
                'notes' => ($generation->notes ?? '') . ' | Cancelled by ' . auth()->user()->name . ' at ' . now(),
                'completed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fee generation cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate fees for all branches
     */
    public function generateForAllBranches(BulkFeesGenerationRequest $request): JsonResponse
    {
        try {
            $serviceData = $request->getServiceData();

            $generations = $this->branchAwareService->generateFeesForAllBranches(
                $serviceData['academic_year_id'],
                $serviceData['fee_type_ids'],
                $serviceData['filters']
            );

            return response()->json([
                'success' => true,
                'message' => "Fee generation initiated for {$generations->count()} branches",
                'data' => [
                    'total_branches' => $generations->count(),
                    'generations' => $generations->map(function ($generation) {
                        return [
                            'generation_id' => $generation->id,
                            'batch_id' => $generation->batch_id,
                            'branch_name' => $generation->getBranchName(),
                            'total_students' => $generation->total_students,
                            'status' => $generation->status,
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk generation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get generation progress
     */
    public function getProgress(int $id): JsonResponse
    {
        try {
            $generation = FeesGeneration::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $generation->id,
                    'batch_id' => $generation->batch_id,
                    'status' => $generation->status,
                    'progress_percentage' => $generation->progress_percentage,
                    'success_rate' => $generation->success_rate,
                    'total_students' => $generation->total_students,
                    'processed_students' => $generation->processed_students,
                    'successful_students' => $generation->successful_students,
                    'failed_students' => $generation->failed_students,
                    'total_amount' => $generation->total_amount,
                    'branch_name' => $generation->getBranchName(),
                    'is_completed' => $generation->isCompleted(),
                    'is_failed' => $generation->isFailed(),
                    'can_be_cancelled' => $generation->canBeCancelled(),
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
     * Export generation report
     */
    public function exportReport(int $id)
    {
        $generation = FeesGeneration::with(['branch', 'creator', 'feesCollects.student'])
            ->findOrFail($id);

        $data = [
            'generation' => $generation,
            'fees' => $generation->feesCollects,
            'branchInfo' => $generation->getBranchInfo(),
        ];

        return view('backend.fees.generation.export', compact('data'));
    }
}
