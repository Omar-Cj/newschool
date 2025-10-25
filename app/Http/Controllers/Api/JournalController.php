<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Journals\Entities\Journal;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    /**
     * Get journals list with optional filtering
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // LOG 1: Entry point confirmation
        \Log::info('📚 [JOURNALS-API] Entry Point Hit', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'N/A',
            'user_branch_id' => auth()->user()->branch_id ?? 'none',
            'ip' => $request->ip(),
            'filters' => [
                'status' => $request->input('status')
            ],
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            $query = Journal::query();

            // Filter by status if provided
            if ($request->filled('status')) {
                $query->where('status', $request->status);
                \Log::info('📚 [JOURNALS-API] Status Filter Applied', [
                    'status' => $request->status
                ]);
            }

            // Apply branch filter based on authenticated user
            $branchId = auth()->user()->branch_id ?? null;
            if ($branchId) {
                $query->where('branch_id', $branchId);
                \Log::info('📚 [JOURNALS-API] Branch Filter Applied', [
                    'branch_id' => $branchId
                ]);
            } else {
                \Log::info('📚 [JOURNALS-API] No Branch Filter (All branches)');
            }

            // LOG 2: Before query execution
            \Log::info('📚 [JOURNALS-API] Executing Query');

            // Select only necessary fields
            $journals = $query->select('id', 'name', 'status', 'branch_id')
                ->orderBy('name')
                ->get();

            // LOG 3: Query results
            \Log::info('📚 [JOURNALS-API] Journals Retrieved', [
                'count' => $journals->count(),
                'journals' => $journals->toArray()
            ]);

            $response = [
                'status' => true,
                'data' => $journals
            ];

            // LOG 4: Response structure
            \Log::info('✅ [JOURNALS-API] Sending Response', [
                'status_code' => 200,
                'journal_count' => $journals->count(),
                'response_keys' => array_keys($response)
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('❌ [JOURNALS-API] Failed to fetch journals', [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'user_id' => auth()->id(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch journals',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get individual journal details with statistics
     *
     * @param Request $request
     * @param int $id Journal ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        \Log::info('📚 [JOURNALS-API] Show Journal Details', [
            'journal_id' => $id,
            'user_id' => auth()->id(),
            'filter_by_collector' => $request->boolean('filter_by_collector', false),
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            $journal = Journal::findOrFail($id);

            // Check branch authorization
            $userBranchId = auth()->user()->branch_id ?? null;
            if ($userBranchId && $journal->branch_id != $userBranchId) {
                \Log::warning('⚠️ [JOURNALS-API] Unauthorized branch access attempt', [
                    'journal_id' => $id,
                    'journal_branch_id' => $journal->branch_id,
                    'user_branch_id' => $userBranchId
                ]);

                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized access to journal'
                ], 403);
            }

            // Check if filtering by collector (for cash transfer functionality)
            $filterByCollector = $request->boolean('filter_by_collector', false);
            $userId = auth()->id();

            if ($filterByCollector) {
                // User-specific amounts - show only what THIS user collected
                $userTransferred = $journal->approvedTransfers()
                    ->where('transferred_by', $userId)
                    ->sum('amount') ?? 0;

                $userCollected = $journal->getUserTotalCollected($userId);

                $response = [
                    'status' => true,
                    'data' => [
                        'id' => $journal->id,
                        'name' => $journal->name,
                        'status' => $journal->status,
                        'branch_id' => $journal->branch_id,
                        'receipt_cash' => $journal->getUserReceiptCash($userId),
                        'deposit_amount' => $journal->getUserDepositAmount($userId),
                        'total_collected' => $userCollected,
                        'transferred_amount' => $userTransferred,
                        'remaining_balance' => $journal->getUserRemainingBalance($userId),
                        'progress_percentage' => $journal->getUserTransferProgress($userId),
                        'is_fully_transferred' => $userCollected > 0 && $userTransferred >= $userCollected,
                        'can_be_closed' => false, // User cannot close journal
                        'filtered_by_collector' => true,
                        'collector_id' => $userId,
                        'collector_name' => auth()->user()->name,
                    ]
                ];

                \Log::info('✅ [JOURNALS-API] User-Filtered Journal Details Retrieved', [
                    'journal_id' => $id,
                    'user_id' => $userId,
                    'user_receipt_cash' => $journal->getUserReceiptCash($userId),
                    'user_deposit_amount' => $journal->getUserDepositAmount($userId),
                    'user_remaining_balance' => $journal->getUserRemainingBalance($userId)
                ]);

            } else {
                // Original: All amounts (total journal statistics)
                $response = [
                    'status' => true,
                    'data' => [
                        'id' => $journal->id,
                        'name' => $journal->name,
                        'status' => $journal->status,
                        'branch_id' => $journal->branch_id,
                        'receipt_cash' => $journal->receipt_cash,
                        'deposit_amount' => $journal->deposit_amount,
                        'total_collected' => $journal->total_collected,
                        'transferred_amount' => $journal->transferred_amount,
                        'remaining_balance' => $journal->remaining_balance,
                        'progress_percentage' => $journal->progress_percentage,
                        'is_fully_transferred' => $journal->isFullyTransferred(),
                        'can_be_closed' => $journal->canBeClosed(),
                        'filtered_by_collector' => false,
                    ]
                ];

                \Log::info('✅ [JOURNALS-API] Total Journal Details Retrieved', [
                    'journal_id' => $id,
                    'receipt_cash' => $journal->receipt_cash,
                    'deposit_amount' => $journal->deposit_amount,
                    'remaining_balance' => $journal->remaining_balance
                ]);
            }

            return response()->json($response);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('❌ [JOURNALS-API] Journal not found', [
                'journal_id' => $id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Journal not found'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('❌ [JOURNALS-API] Failed to fetch journal details', [
                'journal_id' => $id,
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Failed to fetch journal details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
