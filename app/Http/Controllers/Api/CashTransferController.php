<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashTransferRequest;
use App\Http\Requests\ApproveCashTransferRequest;
use App\Http\Requests\RejectCashTransferRequest;
use App\Http\Resources\CashTransferResource;
use App\Models\CashTransfer;
use App\Services\CashTransferService;
use App\Repositories\CashTransferRepository;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CashTransferController extends Controller
{
    public function __construct(
        private CashTransferService $transferService,
        private CashTransferRepository $transferRepository
    ) {}

    /**
     * Display a listing of cash transfers
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', CashTransfer::class);

        $filters = $request->only([
            'journal_id',
            'status',
            'transferred_by',
            'date_from',
            'date_to',
            'approved_date_from',
            'approved_date_to'
        ]);

        // Add branch_id filter from request or authenticated user
        $filters['branch_id'] = $request->get('branch_id') ?? auth()->user()->branch_id ?? null;

        $transfers = $this->transferRepository->getAll($filters, $request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => CashTransferResource::collection($transfers),
            'meta' => [
                'current_page' => $transfers->currentPage(),
                'last_page' => $transfers->lastPage(),
                'per_page' => $transfers->perPage(),
                'total' => $transfers->total(),
            ],
        ]);
    }

    /**
     * Display the specified cash transfer
     */
    public function show(CashTransfer $cashTransfer): JsonResponse
    {
        $this->authorize('view', $cashTransfer);

        $cashTransfer->load(['journal', 'transferredBy', 'approvedBy']);

        return response()->json([
            'success' => true,
            'data' => new CashTransferResource($cashTransfer),
        ]);
    }

    /**
     * Store a newly created cash transfer
     */
    public function store(StoreCashTransferRequest $request): JsonResponse
    {
        try {
            $transfer = $this->transferService->createTransfer(
                $request->journal_id,
                $request->amount,
                auth()->id(),
                $request->notes
            );

            return response()->json([
                'success' => true,
                'message' => 'Cash transfer created successfully.',
                'data' => new CashTransferResource($transfer),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create cash transfer.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Approve a cash transfer
     */
    public function approve(ApproveCashTransferRequest $request, CashTransfer $cashTransfer): JsonResponse
    {
        \Log::info('🔵 [API-APPROVE] Approve endpoint hit', [
            'transfer_id' => $cashTransfer->id,
            'transfer_status_before' => $cashTransfer->status,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'N/A',
            'request_data' => $request->all(),
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            \Log::info('📞 [API-APPROVE] Calling transferService->approveTransfer()', [
                'transfer_id' => $cashTransfer->id,
                'approved_by' => auth()->id()
            ]);

            $approvedTransfer = $this->transferService->approveTransfer(
                $cashTransfer->id,
                auth()->id()
            );

            \Log::info('✅ [API-APPROVE] Service returned successfully', [
                'transfer_id' => $approvedTransfer->id,
                'new_status' => $approvedTransfer->status,
                'approved_by' => $approvedTransfer->approved_by,
                'approved_at' => $approvedTransfer->approved_at
            ]);

            $response = [
                'success' => true,
                'message' => 'Cash transfer approved successfully.',
                'data' => new CashTransferResource($approvedTransfer),
            ];

            \Log::info('🚀 [API-APPROVE] Sending JSON response', [
                'response_structure' => array_keys($response),
                'status_code' => 200
            ]);

            return response()->json($response);
        } catch (\Exception $e) {
            \Log::error('❌ [API-APPROVE] Exception caught', [
                'transfer_id' => $cashTransfer->id,
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to approve cash transfer.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Reject a cash transfer
     */
    public function reject(RejectCashTransferRequest $request, CashTransfer $cashTransfer): JsonResponse
    {
        try {
            $rejectedTransfer = $this->transferService->rejectTransfer(
                $cashTransfer->id,
                auth()->id(),
                $request->reason
            );

            return response()->json([
                'success' => true,
                'message' => 'Cash transfer rejected.',
                'data' => new CashTransferResource($rejectedTransfer),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject cash transfer.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get cash transfer statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $this->authorize('viewStatistics', CashTransfer::class);

        // Use branch_id from request or authenticated user's branch
        $branchId = $request->get('branch_id') ?? auth()->user()->branch_id ?? null;

        $statistics = $this->transferService->getStatistics($branchId);

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Delete a pending cash transfer
     */
    public function destroy(CashTransfer $cashTransfer): JsonResponse
    {
        $this->authorize('delete', $cashTransfer);

        try {
            $this->transferService->deletePendingTransfer($cashTransfer->id);

            return response()->json([
                'success' => true,
                'message' => 'Cash transfer deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete cash transfer.',
                'error' => $e->getMessage(),
            ], 422);
        }
    }
}
