<?php

namespace App\Http\Controllers\ParentDeposit;

use App\Http\Controllers\Controller;
use App\Models\ParentDeposit\ParentDeposit;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\StudentInfo\Student;
use App\Services\ParentDepositService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Exception;

class ParentDepositController extends Controller
{
    protected ParentDepositService $depositService;

    public function __construct(ParentDepositService $depositService)
    {
        $this->depositService = $depositService;
    }

    /**
     * Display deposit form/interface
     */
    public function index(Request $request)
    {
        if (!hasPermission('parent_deposit_view')) {
            return abort(403, 'Access Denied');
        }

        $data['title'] = 'Parent Deposits';
        $data['deposits'] = ParentDeposit::with(['parentGuardian.user', 'student', 'collector'])
            ->latest()
            ->paginate(20);

        return view('backend.parent-deposits.index', compact('data'));
    }

    /**
     * Show deposit modal for specific parent
     */
    public function depositModal(Request $request): JsonResponse
    {
        if (!hasPermission('parent_deposit_create')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        try {
            $parent = ParentGuardian::with(['user', 'children'])->findOrFail($request->parent_id);

            $html = view('backend.parent-deposits.deposit-modal', [
                'parent' => $parent,
                'formRoute' => route('parent-deposits.store'),
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading deposit form: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new deposit
     */
    public function store(Request $request): JsonResponse
    {
        if (!hasPermission('parent_deposit_create')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'parent_guardian_id' => 'required|exists:parent_guardians,id',
            'student_id' => 'nullable|exists:students,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:1,3,4', // Cash, Zaad, Edahab
            'deposit_reason' => 'nullable|string|max:500',
            'journal_id' => 'nullable|exists:journals,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $parent = ParentGuardian::findOrFail($request->parent_guardian_id);

            // Verify student belongs to parent if specified
            if ($request->student_id) {
                $student = Student::findOrFail($request->student_id);
                if ($student->parent_guardian_id !== $parent->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected student does not belong to this parent'
                    ], 422);
                }
            }

            $sanitizedData = $this->sanitizeNullableFields($validator->validated());
            $deposit = $this->depositService->createDeposit($parent, $sanitizedData);

            return response()->json([
                'success' => true,
                'message' => 'Deposit created successfully',
                'data' => [
                    'deposit_id' => $deposit->id,
                    'deposit_number' => $deposit->deposit_number,
                    'amount' => $deposit->getFormattedAmount(),
                    'balance' => $parent->getFormattedAvailableBalance(),
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Parent deposit creation failed', [
                'error' => $e->getMessage(),
                'parent_id' => $request->parent_guardian_id,
                'amount' => $request->amount,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create deposit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific deposit details
     */
    public function show(ParentDeposit $deposit)
    {
        if (!hasPermission('parent_deposit_view')) {
            return abort(403, 'Access Denied');
        }

        $deposit->load(['parentGuardian.user', 'student', 'collector', 'transactions']);

        $data['title'] = 'Deposit Details';
        $data['deposit'] = $deposit;

        return view('backend.parent-deposits.show', compact('data'));
    }

    /**
     * Get parent balance information
     */
    public function getBalance(Request $request): JsonResponse
    {
        if (!hasPermission('parent_deposit_view')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        try {
            $parent = ParentGuardian::findOrFail($request->parent_id);
            $student = $request->student_id ? Student::findOrFail($request->student_id) : null;

            $balance = $this->depositService->getAvailableBalance($parent, $student);
            $summary = $this->depositService->getBalanceSummary($parent);

            return response()->json([
                'success' => true,
                'data' => [
                    'available_balance' => $balance,
                    'formatted_balance' => '$' . number_format($balance, 2),
                    'summary' => $summary,
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving balance: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available journals for deposit allocation
     */
    public function getJournals(Request $request): JsonResponse
    {
        if (!hasPermission('parent_deposit_view')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        try {
            // Load journals based on the system's journal module, filtered by branch
            $branchId = $request->get('branch_id', activeBranch());
            
            $query = \Modules\Journals\Entities\Journal::where('status', 'active');
            
            // Filter by branch if branch_id column exists
            if (Schema::hasColumn('journals', 'branch_id')) {
                $query->where('branch_id', $branchId);
            }
            
            $journals = $query->orderBy('name')
                ->get(['id', 'name', 'description']);

            $journalData = $journals->map(function ($journal) {
                return [
                    'id' => $journal->id,
                    'name' => $journal->name,
                    'description' => $journal->description ?? '',
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $journalData
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving journals: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get parent's children for deposit allocation
     */
    public function getChildren(Request $request): JsonResponse
    {
        if (!hasPermission('parent_deposit_view')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        try {
            $parent = ParentGuardian::with('children')->findOrFail($request->parent_id);

            $children = $parent->children->map(function ($child) use ($parent) {
                return [
                    'id' => $child->id,
                    'name' => $child->full_name,
                    'class' => $child->class?->name ?? 'N/A',
                    'section' => $child->section?->name ?? 'N/A',
                    'available_balance' => $parent->getAvailableBalance($child),
                    'formatted_balance' => $parent->getFormattedAvailableBalance($child),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $children
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving children: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process deposit with local payment methods
     */
    public function processLocalPayment(Request $request): JsonResponse
    {
        if (!hasPermission('parent_deposit_create')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'deposit_id' => 'required|exists:parent_deposits,id',
            'payment_method' => 'required|in:1,3,4',
            'transaction_reference' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $deposit = ParentDeposit::findOrFail($request->deposit_id);

            $success = $this->depositService->processPayment(
                $deposit,
                $request->payment_method,
                $request->only(['transaction_reference'])
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'data' => [
                        'deposit_number' => $deposit->deposit_number,
                        'status' => $deposit->status,
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment processing failed'
                ], 400);
            }

        } catch (Exception $e) {
            Log::error('Payment processing failed', [
                'error' => $e->getMessage(),
                'deposit_id' => $request->deposit_id,
                'payment_method' => $request->payment_method,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transfer balance between student accounts
     */
    public function transferBalance(Request $request): JsonResponse
    {
        if (!hasPermission('parent_deposit_create')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'parent_guardian_id' => 'required|exists:parent_guardians,id',
            'from_student_id' => 'nullable|exists:students,id',
            'to_student_id' => 'nullable|exists:students,id',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $parent = ParentGuardian::findOrFail($request->parent_guardian_id);
            $fromStudent = $request->from_student_id ? Student::findOrFail($request->from_student_id) : null;
            $toStudent = $request->to_student_id ? Student::findOrFail($request->to_student_id) : null;

            $success = $this->depositService->transferBalance(
                $parent,
                $fromStudent,
                $toStudent,
                $request->amount,
                $request->reason
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Balance transferred successfully',
                    'data' => [
                        'from_balance' => $parent->getFormattedAvailableBalance($fromStudent),
                        'to_balance' => $parent->getFormattedAvailableBalance($toStudent),
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Balance transfer failed'
                ], 400);
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transfer error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete deposit (soft delete)
     */
    public function destroy(ParentDeposit $deposit): JsonResponse
    {
        if (!hasPermission('parent_deposit_delete')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        try {
            // Only allow deletion of pending deposits
            if ($deposit->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending deposits can be deleted'
                ], 422);
            }

            $deposit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deposit deleted successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting deposit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search deposits with filters
     */
    public function search(Request $request)
    {
        if (!hasPermission('parent_deposit_view')) {
            return abort(403, 'Access Denied');
        }

        $query = ParentDeposit::with(['parentGuardian.user', 'student', 'collector']);

        // Apply filters
        if ($request->parent_id) {
            $query->where('parent_guardian_id', $request->parent_id);
        }

        if ($request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->start_date) {
            $query->whereDate('deposit_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('deposit_date', '<=', $request->end_date);
        }

        if ($request->keyword) {
            $query->where(function ($q) use ($request) {
                $q->where('deposit_number', 'like', '%' . $request->keyword . '%')
                  ->orWhere('transaction_reference', 'like', '%' . $request->keyword . '%')
                  ->orWhere('deposit_reason', 'like', '%' . $request->keyword . '%');
            });
        }

        $data['title'] = 'Deposit Search Results';
        $data['deposits'] = $query->latest()->paginate(20);
        $data['request'] = $request;

        return view('backend.parent-deposits.index', compact('data'));
    }

    /**
     * Sanitize nullable fields by converting empty strings to null
     */
    private function sanitizeNullableFields(array $data): array
    {
        $nullableFields = ['student_id', 'journal_id', 'deposit_reason'];

        foreach ($nullableFields as $field) {
            if (isset($data[$field]) && $data[$field] === '') {
                $data[$field] = null;
            }
        }

        // Ensure transaction_reference is always null since we don't use it
        $data['transaction_reference'] = null;

        return $data;
    }
}