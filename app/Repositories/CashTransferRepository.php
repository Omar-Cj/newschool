<?php

namespace App\Repositories;

use App\Models\CashTransfer;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CashTransferRepository
{
    public function __construct(
        private CashTransfer $model
    ) {}

    /**
     * Find a cash transfer by ID
     */
    public function findById(int $id): ?CashTransfer
    {
        return $this->model
            ->with(['journal', 'transferredBy', 'approvedBy'])
            ->find($id);
    }

    /**
     * Get all cash transfers with filters and pagination
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model
            ->with(['journal', 'transferredBy', 'approvedBy']);

        // Filter by branch_id if provided or use authenticated user's branch
        $branchId = $filters['branch_id'] ?? auth()->user()->branch_id ?? null;

        if ($branchId) {
            $query->whereHas('journal', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        // Apply other filters
        if (isset($filters['journal_id'])) {
            $query->where('journal_id', $filters['journal_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['transferred_by'])) {
            $query->where('transferred_by', $filters['transferred_by']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['approved_date_from'])) {
            $query->whereDate('approved_at', '>=', $filters['approved_date_from']);
        }

        if (isset($filters['approved_date_to'])) {
            $query->whereDate('approved_at', '<=', $filters['approved_date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new cash transfer
     */
    public function create(array $data): CashTransfer
    {
        return $this->model->create($data);
    }

    /**
     * Update a cash transfer
     */
    public function update(CashTransfer $transfer, array $data): bool
    {
        return $transfer->update($data);
    }

    /**
     * Delete a cash transfer
     */
    public function delete(CashTransfer $transfer): bool
    {
        return $transfer->delete();
    }

    /**
     * Get statistics for cash transfers
     *
     * @param int|null $branchId Filter by branch ID (optional, uses all active journals if null)
     */
    public function getStatistics(?int $branchId = null): array
    {
        // Get branch ID from authenticated user if not provided
        $effectiveBranchId = $branchId ?? auth()->user()->branch_id ?? null;

        // Build journal IDs query based on branch_id (or all active journals if no branch specified)
        $journalIdsQuery = function ($query) use ($effectiveBranchId) {
            $query->select('id')->from('journals')->where('status', 'active');

            if ($effectiveBranchId) {
                $query->where('branch_id', $effectiveBranchId);
            }
        };

        // Get total paid amount from payment transactions
        $receiptCash = DB::table('payment_transactions')
            ->whereIn('journal_id', $journalIdsQuery)
            ->sum('amount');

        // Get total approved transfers
        $previousTransfers = DB::table('cash_transfers')
            ->whereIn('journal_id', $journalIdsQuery)
            ->where('status', 'approved')
            ->sum('amount');

        // Get deposits (payment method 6 = deposit)
        $deposits = DB::table('payment_transactions')
            ->whereIn('journal_id', $journalIdsQuery)
            ->where('payment_method', 6) // Deposit payment method
            ->sum('amount');

        return [
            'receipt_cash' => round($receiptCash, 2),
            'previous_transfers' => round($previousTransfers, 2),
            'deposits' => round($deposits, 2),
            'total_amount' => round($receiptCash, 2),
            'pending_transfers' => round($receiptCash - $previousTransfers, 2),
        ];
    }

    /**
     * Get sum of approved transfers for a journal
     */
    public function getApprovedTransferSum(int $journalId): float
    {
        return $this->model
            ->where('journal_id', $journalId)
            ->where('status', 'approved')
            ->sum('amount');
    }

    /**
     * Get pending transfers count
     *
     * @param int|null $branchId Filter by branch ID (optional)
     */
    public function getPendingCount(?int $branchId = null): int
    {
        $effectiveBranchId = $branchId ?? auth()->user()->branch_id ?? null;

        $query = $this->model->where('status', 'pending');

        if ($effectiveBranchId) {
            $query->whereIn('journal_id', function ($subquery) use ($effectiveBranchId) {
                $subquery->select('id')
                    ->from('journals')
                    ->where('branch_id', $effectiveBranchId);
            });
        }

        return $query->count();
    }

    /**
     * Get recent transfers
     *
     * @param int|null $branchId Filter by branch ID (optional)
     * @param int $limit Number of records to retrieve
     */
    public function getRecent(?int $branchId = null, int $limit = 10)
    {
        $effectiveBranchId = $branchId ?? auth()->user()->branch_id ?? null;

        $query = $this->model->with(['journal', 'transferredBy']);

        if ($effectiveBranchId) {
            $query->whereIn('journal_id', function ($subquery) use ($effectiveBranchId) {
                $subquery->select('id')
                    ->from('journals')
                    ->where('branch_id', $effectiveBranchId);
            });
        }

        return $query->latest()->limit($limit)->get();
    }

    /**
     * Get cash transfers data for DataTables AJAX server-side processing
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function getAjaxData($request)
    {
        try {
            // LOG 1: Entry confirmation
            \Log::info('📦 [CASH-TRANSFER-REPO] getAjaxData Method Called', [
                'user_id' => auth()->id(),
                'user_branch_id' => auth()->user()->branch_id ?? 'none',
                'model_class' => get_class($this->model),
                'filters_received' => [
                    'journal_id' => $request->input('journal_id'),
                    'status' => $request->input('status'),
                    'date_from' => $request->input('date_from'),
                    'date_to' => $request->input('date_to'),
                    'search' => $request->input('search.value')
                ]
            ]);

            // Enable query logging for performance tracking
            DB::enableQueryLog();

            // Base query with eager loading
            $query = $this->model->with(['journal', 'transferredBy', 'approvedBy', 'branch']);

            // LOG 2: Base query setup
            \Log::info('🔍 [CASH-TRANSFER-REPO] Base Query Created with Eager Loading', [
                'relationships' => ['journal', 'transferredBy', 'approvedBy']
            ]);

            // Super admins see all transfers, regular users only see their own transfers
            if (!isSuperAdmin()) {
                $query->where('transferred_by', auth()->id());
                \Log::info('🔒 [CASH-TRANSFER-REPO] User Filter Applied', [
                    'user_id' => auth()->id()
                ]);
            } else {
                // Super admin: filter by their current branch (set via global branch switching)
                $currentBranchId = auth()->user()->branch_id;
                if ($currentBranchId) {
                    $query->where('branch_id', $currentBranchId);
                    \Log::info('🏢 [CASH-TRANSFER-REPO] Super Admin Branch Filter Applied', [
                        'branch_id' => $currentBranchId,
                        'filter_type' => 'user_branch'
                    ]);
                } else {
                    \Log::info('🌐 [CASH-TRANSFER-REPO] Super Admin - All Branches (no branch assigned)');
                }
            }

            // Apply filters from request
            $appliedFilters = [];
            if ($request->filled('journal_id')) {
                $query->where('journal_id', $request->journal_id);
                $appliedFilters[] = 'journal_id=' . $request->journal_id;
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
                $appliedFilters[] = 'status=' . $request->status;
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
                $appliedFilters[] = 'date_from=' . $request->date_from;
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
                $appliedFilters[] = 'date_to=' . $request->date_to;
            }

            \Log::info('🔧 [CASH-TRANSFER-REPO] Filters Applied', [
                'filter_count' => count($appliedFilters),
                'filters' => $appliedFilters
            ]);

            // DataTables global search
            if ($request->filled('search.value')) {
                $searchValue = $request->input('search.value');
                \Log::info('🔎 [CASH-TRANSFER-REPO] Global Search Applied', [
                    'search_term' => $searchValue,
                    'search_fields' => ['amount', 'notes', 'journal.name', 'transferredBy.name']
                ]);
                $query->where(function ($q) use ($searchValue) {
                    $q->where('amount', 'LIKE', "%{$searchValue}%")
                      ->orWhere('notes', 'LIKE', "%{$searchValue}%")
                      ->orWhereHas('journal', function ($journalQuery) use ($searchValue) {
                          $journalQuery->where('name', 'LIKE', "%{$searchValue}%");
                      })
                      ->orWhereHas('transferredBy', function ($userQuery) use ($searchValue) {
                          $userQuery->where('name', 'LIKE', "%{$searchValue}%");
                      });
                });
            }

            // LOG 3: Before count queries
            \Log::info('📊 [CASH-TRANSFER-REPO] Executing Count Queries');

            // Get total count before filtering
            $recordsTotal = $this->model->count();

            // Get filtered count
            $recordsFiltered = $query->count();

            \Log::info('📈 [CASH-TRANSFER-REPO] Count Query Results', [
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'filter_reduced_by' => $recordsTotal - $recordsFiltered
            ]);

            // Apply ordering
            $orderColumnIndex = $request->input('order.0.column', 0);
            $orderDirection = $request->input('order.0.dir', 'desc');

            $columns = ['id', 'created_at', 'transferred_by', 'journal_id', 'amount', 'approved_by', 'approved_at', 'status', 'actions'];
            $orderColumn = $columns[$orderColumnIndex] ?? 'created_at';

            \Log::info('🔀 [CASH-TRANSFER-REPO] Applying Sorting', [
                'column_index' => $orderColumnIndex,
                'column_name' => $orderColumn,
                'direction' => $orderDirection
            ]);

            // Handle relationship-based ordering
            if ($orderColumn === 'transferred_by') {
                \Log::info('🔗 [CASH-TRANSFER-REPO] Using JOIN for transferred_by sorting');
                $query->leftJoin('users as transferred_users', 'cash_transfers.transferred_by', '=', 'transferred_users.id')
                      ->orderBy('transferred_users.name', $orderDirection)
                      ->select('cash_transfers.*');
            } elseif ($orderColumn === 'journal_id') {
                \Log::info('🔗 [CASH-TRANSFER-REPO] Using JOIN for journal sorting');
                $query->leftJoin('journals', 'cash_transfers.journal_id', '=', 'journals.id')
                      ->orderBy('journals.name', $orderDirection)
                      ->select('cash_transfers.*');
            } elseif ($orderColumn === 'approved_by') {
                \Log::info('🔗 [CASH-TRANSFER-REPO] Using JOIN for approved_by sorting');
                $query->leftJoin('users as approved_users', 'cash_transfers.approved_by', '=', 'approved_users.id')
                      ->orderBy('approved_users.name', $orderDirection)
                      ->select('cash_transfers.*');
            } else {
                $query->orderBy($orderColumn, $orderDirection);
            }

            // Apply pagination
            $start = $request->input('start', 0);
            $length = $request->input('length', 25);

            \Log::info('📥 [CASH-TRANSFER-REPO] Fetching Transfer Records', [
                'start' => $start,
                'length' => $length,
                'expected_page' => ($start / $length) + 1
            ]);

            $transfers = $query->skip($start)->take($length)->get();

            \Log::info('📦 [CASH-TRANSFER-REPO] Records Retrieved', [
                'count' => $transfers->count(),
                'first_id' => $transfers->first()->id ?? 'none',
                'last_id' => $transfers->last()->id ?? 'none'
            ]);

            // LOG 4: Query execution details
            $queries = DB::getQueryLog();
            \Log::info('🗄️ [CASH-TRANSFER-REPO] Database Queries Executed', [
                'query_count' => count($queries),
                'total_time_ms' => array_sum(array_column($queries, 'time')),
                'queries' => collect($queries)->map(fn($q) => [
                    'sql' => $q['query'],
                    'time_ms' => $q['time']
                ])->toArray()
            ]);

            // Format data for DataTables
            \Log::info('🔄 [CASH-TRANSFER-REPO] Starting Data Formatting', [
                'transfer_count' => $transfers->count()
            ]);

            $data = [];
            $formattingErrors = [];

            foreach ($transfers as $index => $transfer) {
                try {
                    $data[] = [
                        'id' => $transfer->id,
                        'created_at' => $transfer->created_at->format('Y-m-d'),
                        'transferred_by' => [
                            'name' => $transfer->transferredBy->name ?? '-'
                        ],
                        'journal' => [
                            'name' => $transfer->journal->name ?? '-'
                        ],
                        'amount' => $transfer->amount,
                        'approved_by' => [
                            'name' => $transfer->approvedBy->name ?? '-'
                        ],
                        'approved_at' => $transfer->approved_at ? $transfer->approved_at->format('Y-m-d') : null,
                        'status' => $transfer->status,
                        'notes' => $transfer->notes,
                        'rejection_reason' => $transfer->rejection_reason,
                    ];
                } catch (\Throwable $formatError) {
                    $formattingErrors[] = "Row {$index}: " . $formatError->getMessage();
                    \Log::warning('⚠️ [CASH-TRANSFER-REPO] Row Formatting Error', [
                        'row_index' => $index,
                        'transfer_id' => $transfer->id ?? 'unknown',
                        'error' => $formatError->getMessage()
                    ]);
                }
            }

            if (!empty($formattingErrors)) {
                \Log::warning('⚠️ [CASH-TRANSFER-REPO] Some Rows Had Formatting Errors', [
                    'error_count' => count($formattingErrors),
                    'errors' => $formattingErrors
                ]);
            }

            // LOG 5: Final response structure
            $response = [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ];

            \Log::info('✅ [CASH-TRANSFER-REPO] Response Ready for DataTables', [
                'draw' => $response['draw'],
                'recordsTotal' => $response['recordsTotal'],
                'recordsFiltered' => $response['recordsFiltered'],
                'data_rows' => count($response['data']),
                'response_keys' => array_keys($response),
                'first_row_keys' => !empty($response['data']) ? array_keys($response['data'][0]) : 'no data'
            ]);

            // Return DataTables formatted response
            return $response;

        } catch (\Throwable $th) {
            \Log::error('❌ [CASH-TRANSFER-REPO] getAjaxData Method FAILED', [
                'error_message' => $th->getMessage(),
                'error_class' => get_class($th),
                'error_file' => $th->getFile(),
                'error_line' => $th->getLine(),
                'request_data' => $request->all(),
                'user_id' => auth()->id(),
                'stack_trace' => $th->getTraceAsString()
            ]);

            throw $th;
        }
    }
}
