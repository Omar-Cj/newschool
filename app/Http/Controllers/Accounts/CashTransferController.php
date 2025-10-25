<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Repositories\CashTransferRepository;
use App\Services\CashTransferService;
use App\Http\Requests\StoreCashTransferRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Http;

class CashTransferController extends Controller
{
    private $transferRepository;

    public function __construct(CashTransferRepository $transferRepository)
    {
        if (!Schema::hasTable('settings') && !Schema::hasTable('users')) {
            abort(400);
        }
        $this->transferRepository = $transferRepository;
    }

    /**
     * Display a listing of cash transfers
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $data['title'] = ___('cash_transfer.cash_transfers');
        return view('backend.accounts.cash-transfers.index', compact('data'));
    }

    /**
     * Handle AJAX request for DataTables server-side processing
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxCashTransferData(Request $request)
    {
        // LOG 1: Entry point confirmation
        \Log::info('🟢 [CASH-TRANSFER] AJAX Entry Point Hit', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'N/A',
            'user_role' => auth()->user()->role_id ?? 'N/A',
            'ip' => $request->ip(),
            'timestamp' => now()->toDateTimeString()
        ]);

        // LOG 2: Request parameters
        \Log::info('📊 [CASH-TRANSFER] DataTables Request Parameters', [
            'draw' => $request->input('draw'),
            'start' => $request->input('start'),
            'length' => $request->input('length'),
            'search_value' => $request->input('search.value'),
            'order_column' => $request->input('order.0.column'),
            'order_dir' => $request->input('order.0.dir'),
            'filters' => [
                'journal_id' => $request->input('journal_id'),
                'status' => $request->input('status'),
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to')
            ]
        ]);

        try {
            // LOG 3: Before repository call
            \Log::info('🔄 [CASH-TRANSFER] Calling Repository getAjaxData', [
                'repository_class' => get_class($this->transferRepository)
            ]);

            $result = $this->transferRepository->getAjaxData($request);

            // LOG 4: After repository call with result preview
            \Log::info('✅ [CASH-TRANSFER] Repository Success', [
                'draw' => $result['draw'] ?? 0,
                'recordsTotal' => $result['recordsTotal'] ?? 0,
                'recordsFiltered' => $result['recordsFiltered'] ?? 0,
                'data_count' => count($result['data'] ?? []),
                'has_error' => isset($result['error'])
            ]);

            // LOG 5: Response structure validation
            \Log::info('🔍 [CASH-TRANSFER] Response Structure', [
                'keys_present' => array_keys($result),
                'first_row_sample' => isset($result['data'][0]) ? array_keys($result['data'][0]) : 'no data'
            ]);

            // LOG 6: Final response confirmation
            \Log::info('🚀 [CASH-TRANSFER] Sending JSON Response', [
                'status_code' => 200,
                'content_type' => 'application/json'
            ]);

            return response()->json($result);

        } catch (\Throwable $th) {
            // Enhanced error logging with full context
            \Log::error('❌ [CASH-TRANSFER] AJAX Request FAILED', [
                'error_message' => $th->getMessage(),
                'error_class' => get_class($th),
                'error_file' => $th->getFile(),
                'error_line' => $th->getLine(),
                'request_url' => $request->fullUrl(),
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email ?? 'N/A',
                'stack_trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => ___('alert.something_went_wrong_please_try_again')
            ], 500);
        }
    }

    /**
     * Show the form for creating a new cash transfer
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $data['title'] = ___('cash_transfer.create_cash_transfer');
        return view('backend.accounts.cash-transfers.create', compact('data'));
    }

    /**
     * Store a newly created cash transfer
     *
     * @param StoreCashTransferRequest $request
     * @param CashTransferService $transferService
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCashTransferRequest $request, CashTransferService $transferService)
    {
        try {
            $transfer = $transferService->createTransfer(
                $request->journal_id,
                $request->amount,
                auth()->id(),
                $request->notes
            );

            return response()->json([
                'status' => true,
                'message' => ___('cash_transfer.transfer_created'),
                'data' => $transfer
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Cash transfer creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'journal_id' => $request->journal_id,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get statistics data for dashboard cards
     * This method is called via AJAX from the frontend
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Request $request)
    {
        try {
            // Get branch ID from authenticated user
            $branchId = auth()->user()->branch_id ?? null;

            // Get statistics from repository
            $stats = $this->transferRepository->getStatistics($branchId);

            return response()->json([
                'status' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load cash transfer statistics', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => ___('alert.something_went_wrong')
            ], 500);
        }
    }

    /**
     * Get single transfer details
     * This method is called via AJAX to show transfer details in modal
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        \Log::info('📋 [CASH-TRANSFER] show() method called', [
            'transfer_id' => $id,
            'user_id' => auth()->id(),
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            // Get transfer from repository with eager-loaded relationships
            $transfer = $this->transferRepository->findById((int)$id);

            if (!$transfer) {
                \Log::warning('⚠️ [CASH-TRANSFER] Transfer not found', [
                    'transfer_id' => $id
                ]);

                return response()->json([
                    'status' => false,
                    'message' => ___('alert.not_found')
                ], 404);
            }

            \Log::info('✅ [CASH-TRANSFER] Transfer found, preparing response', [
                'transfer_id' => $transfer->id,
                'status' => $transfer->status,
                'journal_id' => $transfer->journal_id
            ]);

            // Format transfer data for frontend
            $transferData = [
                'id' => $transfer->id,
                'journal' => [
                    'id' => $transfer->journal->id ?? null,
                    'name' => $transfer->journal->name ?? '-',
                ],
                'amount' => number_format($transfer->amount, 2, '.', ''),
                'status' => $transfer->status,
                'transferred_by' => [
                    'id' => $transfer->transferredBy->id ?? null,
                    'name' => $transfer->transferredBy->name ?? '-',
                ],
                'created_at' => $transfer->created_at ? $transfer->created_at->format('Y-m-d H:i:s') : null,
                'approved_by' => $transfer->approvedBy ? [
                    'id' => $transfer->approvedBy->id,
                    'name' => $transfer->approvedBy->name,
                ] : null,
                'approved_at' => $transfer->approved_at ? $transfer->approved_at->format('Y-m-d H:i:s') : null,
                'notes' => $transfer->notes ?? '',
                'rejection_reason' => $transfer->rejection_reason ?? null,
            ];

            \Log::info('🚀 [CASH-TRANSFER] Sending transfer details response', [
                'status_code' => 200
            ]);

            return response()->json([
                'status' => true,
                'data' => $transferData
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ [CASH-TRANSFER] show() method failed', [
                'transfer_id' => $id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => ___('alert.something_went_wrong')
            ], 500);
        }
    }
}
