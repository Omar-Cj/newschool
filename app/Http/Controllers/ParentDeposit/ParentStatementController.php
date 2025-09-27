<?php

namespace App\Http\Controllers\ParentDeposit;

use App\Http\Controllers\Controller;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\StudentInfo\Student;
use App\Services\ParentStatementService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Exception;

class ParentStatementController extends Controller
{
    protected ParentStatementService $statementService;

    public function __construct(ParentStatementService $statementService)
    {
        $this->statementService = $statementService;
    }

    /**
     * Show statement dashboard
     */
    public function index(Request $request)
    {
        if (!hasPermission('parent_statement_view')) {
            return abort(403, 'Access Denied');
        }

        $data['title'] = 'Parent Statements';
        $data['parents'] = ParentGuardian::with(['user', 'children'])
            ->active()
            ->paginate(20);

        return view('backend.parent-deposits.statements.index', compact('data'));
    }

    /**
     * Show specific parent statement
     */
    public function show(ParentGuardian $parent, Request $request)
    {
        if (!hasPermission('parent_statement_view')) {
            return abort(403, 'Access Denied');
        }

        $student = $request->student_id ? Student::findOrFail($request->student_id) : null;
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->subMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now();

        $statementData = $this->statementService->generateStatement($parent, $student, $startDate, $endDate);

        $data['title'] = 'Statement for ' . $parent->user->name;
        $data['statement'] = $statementData;
        $data['parent'] = $parent;

        return view('backend.parent-deposits.statements.show', compact('data'));
    }

    /**
     * Search statements with filters
     */
    public function search(Request $request)
    {
        if (!hasPermission('parent_statement_view')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'parent_id' => 'nullable|exists:parent_guardians,id',
            'student_id' => 'nullable|exists:students,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'transaction_type' => 'nullable|in:deposit,withdrawal,allocation,refund',
            'amount_min' => 'nullable|numeric|min:0',
            'amount_max' => 'nullable|numeric|min:0',
            'keyword' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            if ($request->parent_id) {
                $parent = ParentGuardian::findOrFail($request->parent_id);
                $student = $request->student_id ? Student::findOrFail($request->student_id) : null;

                $transactions = $this->statementService->getTransactionHistory($parent, $student, [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'transaction_type' => $request->transaction_type,
                    'amount_min' => $request->amount_min,
                    'amount_max' => $request->amount_max,
                    'search' => $request->keyword,
                ]);

                $balanceSummary = $this->statementService->getBalanceSummary($parent);

                $data['title'] = 'Statement Search Results';
                $data['transactions'] = $transactions;
                $data['balance_summary'] = $balanceSummary;
                $data['parent'] = $parent;
                $data['student'] = $student;
                $data['request'] = $request;

                return view('backend.parent-deposits.statements.search-results', compact('data'));
            } else {
                // General search across all parents
                $data['title'] = 'Statement Search';
                $data['parents'] = ParentGuardian::with(['user', 'children'])
                    ->when($request->keyword, function($query) use ($request) {
                        $query->whereHas('user', function($q) use ($request) {
                            $q->where('name', 'like', '%' . $request->keyword . '%')
                              ->orWhere('email', 'like', '%' . $request->keyword . '%');
                        });
                    })
                    ->active()
                    ->paginate(20);
                $data['request'] = $request;

                return view('backend.parent-deposits.statements.index', compact('data'));
            }

        } catch (Exception $e) {
            return back()->with('error', 'Error processing search: ' . $e->getMessage());
        }
    }

    /**
     * Export statement to PDF
     */
    public function export(Request $request)
    {
        if (!hasPermission('parent_statement_export')) {
            return abort(403, 'Access Denied');
        }

        $validator = Validator::make($request->all(), [
            'parent_id' => 'required|exists:parent_guardians,id',
            'student_id' => 'nullable|exists:students,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'format' => 'required|in:pdf,excel',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $parent = ParentGuardian::findOrFail($request->parent_id);

            switch ($request->format) {
                case 'pdf':
                    return $this->statementService->exportStatementToPDF($parent, $request->validated());
                case 'excel':
                    return $this->exportToExcel($parent, $request->validated());
                default:
                    return back()->with('error', 'Invalid export format');
            }

        } catch (Exception $e) {
            return back()->with('error', 'Error exporting statement: ' . $e->getMessage());
        }
    }

    /**
     * Get transaction details via AJAX
     */
    public function getTransactionDetails(Request $request): JsonResponse
    {
        if (!hasPermission('parent_statement_view')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        try {
            $parent = ParentGuardian::findOrFail($request->parent_id);
            $student = $request->student_id ? Student::findOrFail($request->student_id) : null;

            $transactions = $this->statementService->getTransactionHistory($parent, $student, [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'transaction_type' => $request->transaction_type,
            ]);

            $html = view('backend.parent-deposits.statements.transaction-details', [
                'transactions' => $transactions,
                'parent' => $parent,
                'student' => $student,
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $transactions->count(),
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading transaction details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get balance trend data for charts
     */
    public function getBalanceTrend(Request $request): JsonResponse
    {
        if (!hasPermission('parent_statement_view')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        try {
            $parent = ParentGuardian::findOrFail($request->parent_id);
            $student = $request->student_id ? Student::findOrFail($request->student_id) : null;
            $startDate = Carbon::parse($request->start_date ?? now()->subMonth());
            $endDate = Carbon::parse($request->end_date ?? now());

            $trend = $this->statementService->getDailyBalanceTrend($parent, $student, $startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $trend
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating balance trend: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly summary
     */
    public function getMonthlySummary(Request $request): JsonResponse
    {
        if (!hasPermission('parent_statement_view')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        try {
            $parent = ParentGuardian::findOrFail($request->parent_id);
            $year = $request->year ?? now()->year;

            $monthlyStatements = $this->statementService->getMonthlyStatements($parent, $year);

            return response()->json([
                'success' => true,
                'data' => $monthlyStatements
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating monthly summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show statement modal
     */
    public function statementModal(Request $request): JsonResponse
    {
        if (!hasPermission('parent_statement_view')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        try {
            $parent = ParentGuardian::with(['user', 'children'])->findOrFail($request->parent_id);

            $html = view('backend.parent-deposits.statements.statement-modal', [
                'parent' => $parent,
                'statementRoute' => route('parent-statements.show', $parent),
                'exportRoute' => route('parent-statements.export'),
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading statement form: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get summary statistics
     */
    public function getSummaryStatistics(Request $request): JsonResponse
    {
        if (!hasPermission('parent_statement_view')) {
            return response()->json([
                'success' => false,
                'message' => 'Access Denied'
            ], 403);
        }

        try {
            $parent = ParentGuardian::findOrFail($request->parent_id);
            $startDate = Carbon::parse($request->start_date ?? now()->subMonth());
            $endDate = Carbon::parse($request->end_date ?? now());

            $summary = $this->statementService->getTransactionSummary($parent, $startDate, $endDate);
            $balanceSummary = $this->statementService->getBalanceSummary($parent);

            return response()->json([
                'success' => true,
                'data' => [
                    'transaction_summary' => $summary,
                    'balance_summary' => $balanceSummary,
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating summary statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to Excel (placeholder for future implementation)
     */
    protected function exportToExcel(ParentGuardian $parent, array $filters): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        // TODO: Implement Excel export using Laravel Excel package
        // For now, fallback to PDF export
        return $this->statementService->exportStatementToPDF($parent, $filters);
    }
}