<?php

namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Models\Fees\FeesCollect;
use App\Models\Student;
use App\Models\User;
use App\Repositories\StudentInfo\StudentRepository;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use PDF;
use Carbon\Carbon;

class ReceiptController extends Controller
{
    private $studentRepo;
    private $receiptService;

    public function __construct(StudentRepository $studentRepo, ReceiptService $receiptService)
    {
        $this->studentRepo = $studentRepo;
        $this->receiptService = $receiptService;
    }

    /**
     * Generate individual student receipt for a specific payment
     */
    public function generateIndividualReceipt($paymentId)
    {
        try {
            // Get standardized receipt data from service
            $receiptData = $this->receiptService->getReceiptData($paymentId);

            if (!$receiptData) {
                return back()->with('danger', ___('fees.receipt_not_found'));
            }

            $data = [
                'title' => ___('fees.payment_receipt'),
                'receipt' => $receiptData,
                'school_info' => $this->receiptService->getSchoolInfo(),
            ];

            // Check if this is a print preview request
            if (request()->has('print') && request()->get('print') == '1') {
                return view('backend.fees.receipts.individual-transaction', compact('data'));
            }

            // Generate PDF
            $pdf = PDF::loadView('backend.fees.receipts.individual-transaction', compact('data'));
            $pdf->setPaper('A4', 'portrait');

            $fileName = 'receipt_' . $receiptData->student->admission_no . '_' . date('Y-m-d', strtotime($receiptData->payment_date)) . '.pdf';

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return back()->with('danger', ___('fees.receipt_generation_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Generate student receipt for all payments in a date range
     */
    public function generateStudentSummaryReceipt($studentId, Request $request)
    {
        try {
            $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

            $student = Student::findOrFail($studentId);
            
            $payments = FeesCollect::with([
                'feesAssignChildren.feesMaster.type',
                'feesAssignChildren.feesMaster.group',
                'collectBy'
            ])
            ->where('student_id', $studentId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get();

            if ($payments->isEmpty()) {
                return back()->with('warning', ___('fees.no_payments_found_for_period'));
            }

            $data = [
                'title' => ___('fees.student_payment_summary'),
                'student' => $student,
                'payments' => $payments,
                'period' => [
                    'start' => $startDate,
                    'end' => $endDate
                ],
                'school_info' => $this->getSchoolInfo(),
                'totals' => $this->calculateTotals($payments)
            ];

            $pdf = PDF::loadView('backend.fees.receipts.student-summary', compact('data'));
            $pdf->setPaper('A4', 'portrait');
            
            $fileName = 'student_receipt_summary_' . $student->admission_no . '_' . date('Y-m-d') . '.pdf';
            
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return back()->with('danger', ___('fees.receipt_generation_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Generate group receipt for multiple students paid in same session
     */
    public function generateGroupReceipt(Request $request)
    {
        try {
            $paymentIds = $request->get('payment_ids', []);
            
            if (empty($paymentIds)) {
                return back()->with('warning', ___('fees.no_payments_selected'));
            }

            $payments = FeesCollect::with([
                'student',
                'feesAssignChildren.feesMaster.type',
                'feesAssignChildren.feesMaster.group',
                'collectBy'
            ])
            ->whereIn('id', $paymentIds)
            ->orderBy('date', 'desc')
            ->orderBy('student_id')
            ->get();

            $data = [
                'title' => ___('fees.group_payment_receipt'),
                'payments' => $payments,
                'school_info' => $this->getSchoolInfo(),
                'batch_info' => $this->generateBatchInfo($payments),
                'totals' => $this->calculateTotals($payments),
                'summary_by_type' => $this->summarizeByFeeType($payments)
            ];

            $pdf = PDF::loadView('backend.fees.receipts.group', compact('data'));
            $pdf->setPaper('A4', 'portrait');
            
            $fileName = 'group_receipt_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return back()->with('danger', ___('fees.receipt_generation_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Generate daily collection receipt for all payments by a collector
     */
    public function generateDailyCollectionReceipt(Request $request)
    {
        try {
            $date = $request->get('date', Carbon::now()->format('Y-m-d'));
            $collectorId = $request->get('collector_id', auth()->id());

            $payments = FeesCollect::with([
                'student',
                'feesAssignChildren.feesMaster.type',
                'feesAssignChildren.feesMaster.group',
                'collectBy'
            ])
            ->where('date', $date)
            ->where('fees_collect_by', $collectorId)
            ->orderBy('created_at')
            ->get();

            if ($payments->isEmpty()) {
                return back()->with('warning', ___('fees.no_payments_found_for_date'));
            }

            $data = [
                'title' => ___('fees.daily_collection_receipt'),
                'payments' => $payments,
                'date' => $date,
                'collector' => $payments->first()->collectBy,
                'school_info' => $this->getSchoolInfo(),
                'totals' => $this->calculateTotals($payments),
                'summary_by_type' => $this->summarizeByFeeType($payments),
                'summary_by_method' => $this->summarizeByPaymentMethod($payments)
            ];

            $pdf = PDF::loadView('backend.fees.receipts.daily-collection', compact('data'));
            $pdf->setPaper('A4', 'portrait');
            
            $fileName = 'daily_collection_' . $date . '_' . $collectorId . '.pdf';
            
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return back()->with('danger', ___('fees.receipt_generation_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Show receipt options page after payment
     */
    public function showReceiptOptions($paymentId)
    {
        // Get standardized receipt data from service
        $receiptData = $this->receiptService->getReceiptData($paymentId);

        if (!$receiptData) {
            return back()->with('danger', ___('fees.receipt_not_found'));
        }

        if (request()->ajax()) {
            $html = view('backend.fees.receipts.options-modal', ['receipt' => $receiptData])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'meta' => [
                    'receipt_number' => $receiptData->receipt_number,
                    'amount_paid' => $receiptData->amount_paid,
                    'currency' => setting('currency_symbol'),
                    'payment_status' => $receiptData->payment_status,
                ],
            ]);
        }

        return view('backend.fees.receipts.options-page', ['receipt' => $receiptData]);
    }

    /**
     * Show receipt options for partial payment (PaymentTransaction)
     */
    private function showPartialPaymentReceiptOptions($paymentTransaction)
    {
        // Get related payment transactions for the same payment session
        // Scoped to billing period to prevent cross-period aggregation
        $query = \App\Models\Fees\PaymentTransaction::with([
            'feesCollect.feeType',
            'feesCollect'
        ])
        ->where('student_id', $paymentTransaction->student_id)
        ->where('payment_date', $paymentTransaction->payment_date)
        ->where('collected_by', $paymentTransaction->collected_by);

        // Add billing period scoping through related FeesCollect
        if ($paymentTransaction->feesCollect && $paymentTransaction->feesCollect->billing_period) {
            $query->whereHas('feesCollect', function($q) use ($paymentTransaction) {
                $q->where('billing_period', $paymentTransaction->feesCollect->billing_period);
            });
        }

        $relatedTransactions = $query->get();

        $partialContext = $this->buildPartialPaymentContext($paymentTransaction, $relatedTransactions);

        // Calculate totals
        $totalAmount = $relatedTransactions->sum('amount');
        $totalFine = 0; // PaymentTransactions don't typically have separate fine amounts

        // Create a payment object compatible with existing template
        $payment = (object) [
            'id' => $paymentTransaction->id,
            'student' => $paymentTransaction->student,
            'date' => $paymentTransaction->payment_date,
            'amount' => $paymentTransaction->amount,
            'payment_method' => $paymentTransaction->payment_method,
            'payment_gateway' => $paymentTransaction->payment_gateway,
            'transaction_reference' => $paymentTransaction->transaction_reference,
            'payment_notes' => $paymentTransaction->payment_notes,
            'collectBy' => $paymentTransaction->collector,
            'total_amount' => $totalAmount,
            'total_fine' => $totalFine,
            'grand_total' => $totalAmount + $totalFine,
            'receipt_number' => $this->generatePartialPaymentReceiptNumber($paymentTransaction),
            'payment_method_label' => $paymentTransaction->getPaymentMethodName(),
            'is_partial_payment' => $partialContext['is_partial_payment'],
            'related_transactions' => $relatedTransactions,
            'outstanding_items' => $partialContext['outstanding_items'],
            'outstanding_total' => $partialContext['outstanding_total'],
        ];

        if (request()->ajax()) {
            $html = view('backend.fees.receipts.options-modal', compact('payment'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'meta' => [
                    'receipt_number' => $payment->receipt_number,
                    'grand_total' => $payment->grand_total,
                    'total_amount' => $payment->total_amount,
                    'total_fine' => $payment->total_fine,
                    'currency' => setting('currency_symbol'),
                    'is_partial_payment' => $partialContext['is_partial_payment'],
                    'outstanding_total' => $partialContext['outstanding_total'],
                ],
            ]);
        }

        return view('backend.fees.receipts.options-page', compact('payment'));
    }

    /**
     * Generate receipt number for partial payment
     */
    private function generatePartialPaymentReceiptNumber($paymentTransaction)
    {
        return 'RCT-PP-' . date('Y') . '-' . str_pad($paymentTransaction->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Build contextual data to determine if the payment session is partial
     * and capture any outstanding fee balances.
     */
    private function buildPartialPaymentContext($paymentTransaction, $relatedTransactions): array
    {
        $relatedFeeCollects = $this->getRelatedFeeCollectsForTransaction($paymentTransaction);

        $outstandingFees = $relatedFeeCollects
            ->filter(function ($feeCollect) {
                return $feeCollect->getBalanceAmount() > 0;
            });

        // Fallback to transaction-level inspection when no related fee collects were found
        if ($outstandingFees->isEmpty()) {
            $outstandingFees = $relatedTransactions
                ->map(function ($transaction) {
                    return $transaction->feesCollect;
                })
                ->filter(function ($feeCollect) {
                    return $feeCollect && $feeCollect->getBalanceAmount() > 0;
                });
        }

        $outstandingItems = $outstandingFees
            ->map(function ($feeCollect) {
                return [
                    'name' => $feeCollect->getFeeName(),
                    'balance' => round($feeCollect->getBalanceAmount(), 2),
                    'total' => round($feeCollect->getNetAmount(), 2),
                ];
            })
            ->values();

        return [
            'is_partial_payment' => $outstandingItems->isNotEmpty(),
            'outstanding_items' => $outstandingItems->toArray(),
            'outstanding_total' => round($outstandingItems->sum('balance'), 2),
        ];
    }

    /**
     * Fetch all fee collect records that belong to the same payment context
     * as the provided transaction (same student + billing scope).
     */
    private function getRelatedFeeCollectsForTransaction($paymentTransaction)
    {
        $feeCollect = $paymentTransaction->feesCollect;

        if (!$feeCollect) {
            return collect();
        }

        $query = FeesCollect::query()
            ->where('student_id', $paymentTransaction->student_id);

        if ($feeCollect->generation_batch_id) {
            $query->where('generation_batch_id', $feeCollect->generation_batch_id);
        } else {
            $query->where('date', $feeCollect->date)
                ->where('fees_collect_by', $feeCollect->fees_collect_by);
        }

        if ($feeCollect->billing_period) {
            $query->where('billing_period', $feeCollect->billing_period);
        }

        if ($feeCollect->academic_year_id) {
            $query->where('academic_year_id', $feeCollect->academic_year_id);
        }

        return $query->get();
    }

    /**
     * Generate individual receipt for partial payment (PaymentTransaction)
     */
    private function generatePartialPaymentReceipt($paymentTransaction)
    {
        try {
            // Get related payment transactions for the same payment session
            // Scoped to billing period to prevent cross-period aggregation
            $query = \App\Models\Fees\PaymentTransaction::with([
                'feesCollect.feeType',
                'feesCollect'
            ])
            ->where('student_id', $paymentTransaction->student_id)
            ->where('payment_date', $paymentTransaction->payment_date)
            ->where('collected_by', $paymentTransaction->collected_by);

            // Add billing period scoping through related FeesCollect
            if ($paymentTransaction->feesCollect && $paymentTransaction->feesCollect->billing_period) {
                $query->whereHas('feesCollect', function($q) use ($paymentTransaction) {
                    $q->where('billing_period', $paymentTransaction->feesCollect->billing_period);
                });
            }

            $relatedTransactions = $query->get();

            // Use standardized data calculation for consistency
            $standardizedTransaction = $this->standardizeReceiptData($paymentTransaction, 'partial');
            $partialContext = $this->buildPartialPaymentContext($paymentTransaction, $standardizedTransaction->all_payments);

            $data = [
                'title' => ___('fees.payment_receipt'),
                'payment' => $standardizedTransaction,
                'all_payments' => $standardizedTransaction->all_payments,
                'school_info' => $this->getSchoolInfo(),
                'receipt_number' => $standardizedTransaction->receipt_number,
                'qr_code' => $this->generateQRCode($standardizedTransaction),
                'total_amount' => $standardizedTransaction->total_amount,
                'total_fine' => $standardizedTransaction->total_fine,
                'is_partial_payment' => $partialContext['is_partial_payment'],
                'outstanding_items' => $partialContext['outstanding_items'],
                'outstanding_total' => $partialContext['outstanding_total'],
            ];

            // Check if this is a print preview request
            if (request()->has('print') && request()->get('print') == '1') {
                // Return HTML view for browser printing
                return view('backend.fees.receipts.partial-payment-individual', compact('data'));
            }

            // Generate PDF
            $pdf = PDF::loadView('backend.fees.receipts.partial-payment-individual', compact('data'));
            $pdf->setPaper('A4', 'portrait');

            $fileName = 'receipt_partial_' . $paymentTransaction->student->admission_no . '_' . date('Y-m-d', strtotime($paymentTransaction->payment_date)) . '.pdf';

            return $pdf->download($fileName);
        } catch (\Exception $e) {
            return back()->with('danger', ___('fees.receipt_generation_failed') . ': ' . $e->getMessage());
        }
    }

    /**
     * Display paginated list of receipts with filtering options.
     * Now simplified to show individual payment transactions only.
     */
    public function index(Request $request)
    {
        // Get transaction-centric receipt listing using the service
        $receipts = $this->receiptService->getReceiptListing($request);

        // Get payment methods and collectors for filters
        $paymentMethods = config('site.payment_methods');
        $collectors = $this->receiptService->getCollectorsForFilter();

        // Get school info for context
        $schoolInfo = $this->receiptService->getSchoolInfo();
        $currency = $schoolInfo['currency'] ?? setting('currency_symbol');

        return view('backend.fees.receipts.index', [
            'receipts' => $receipts,
            'availableMethods' => $paymentMethods,
            'collectors' => $collectors,
            'school_info' => $schoolInfo,
            'currency' => $currency,
        ]);
    }

    /**
     * Standardize receipt data calculation for consistency across all views
     * This ensures the listing shows exactly what appears on printed receipts
     */
    private function standardizeReceiptData($payment, $paymentType = 'legacy')
    {
        if ($paymentType === 'partial') {
            // For PaymentTransaction records
            $relatedTransactions = \App\Models\Fees\PaymentTransaction::with(['feesCollect.feeType'])
                ->where('student_id', $payment->student_id)
                ->where('payment_date', $payment->payment_date)
                ->where('collected_by', $payment->collected_by)
                ->get();

            $payment->receipt_number = $this->generatePartialPaymentReceiptNumber($payment);
            $payment->total_amount = $relatedTransactions->sum('amount');
            $payment->total_fine = 0; // PaymentTransactions typically don't have separate fines
            $payment->grand_total = $payment->total_amount;
            $payment->related_payment_count = $relatedTransactions->count();
            $payment->all_payments = $relatedTransactions;
            $payment->is_session_payment = true; // This payment represents a specific payment session
        } else {
            // For FeesCollect records - use session-specific logic
            $sessionPayments = $this->getSessionSpecificPayments($payment);
            
            $payment->receipt_number = $this->generateReceiptNumber($payment);
            $payment->total_amount = $sessionPayments->sum('amount');
            $payment->total_fine = $sessionPayments->sum('fine_amount');
            $payment->grand_total = $payment->total_amount + $payment->total_fine;
            $payment->related_payment_count = $sessionPayments->count();
            $payment->all_payments = $sessionPayments;
            $payment->is_session_payment = false; // This might aggregate multiple sessions
        }

        return $payment;
    }

    /**
     * Get payments specific to the same payment session (date + collector)
     * This matches what appears on the actual receipt
     */
    private function getSessionSpecificPayments($mainPayment)
    {
        // Get payments from the same session (same date and collector)
        return FeesCollect::with([
            'feesAssignChildren.feesMaster.type',
            'feesAssignChildren.feesMaster.group'
        ])
        ->where('student_id', $mainPayment->student_id)
        ->where('date', $mainPayment->date)
        ->where('fees_collect_by', $mainPayment->fees_collect_by)
        ->whereNotNull('payment_method') // Only paid fees - using direct condition instead of scope
        ->get();
    }

    /**
     * Get unified receipt listing combining FeesCollect and PaymentTransaction records
     */
    private function getUnifiedReceiptListing(Request $request)
    {
        $paymentMethods = config('site.payment_methods');

        // Get current session context (same as used during payment creation)
        $academicYearId = session('academic_year_id') ?: \App\Models\Session::active()->value('id');
        $currentSessionId = setting('session');

        // Get FeesCollect records (legacy full payments and enhanced partial tracking)
        $feesCollectQuery = FeesCollect::with([
            'student.sessionStudentDetails.class',
            'student.sessionStudentDetails.section',
            'collectBy'
        ])->whereNotNull('payment_method') // Only paid fees - direct condition
        ->orderBy('date', 'desc')
        ->orderBy('id', 'desc');

        // Get PaymentTransaction records (partial payments) with same scope as payment creation
        $paymentTransactionQuery = \App\Models\Fees\PaymentTransaction::with([
            'student.sessionStudentDetails.class',
            'student.sessionStudentDetails.section',
            'collector'
        ])
        // Apply academic year scope (same as payment creation)
        ->when($academicYearId, function($query) use ($academicYearId) {
            $query->whereHas('feesCollect', function($subQuery) use ($academicYearId) {
                $subQuery->where('academic_year_id', $academicYearId);
            });
        })
        // Apply session scope filtering
        ->when($currentSessionId, function($query) use ($currentSessionId) {
            $query->whereHas('feesCollect', function($subQuery) use ($currentSessionId) {
                $subQuery->where('session_id', $currentSessionId);
            });
        })
        // PaymentTransaction records are implicitly successful when created, no status column needed
        // Apply branch/tenant filtering if available
        ->when(auth()->user()->branch_id ?? null, function($query, $branchId) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('payment_transactions', 'branch_id')) {
                $query->where('branch_id', $branchId);
            }
        })
        ->orderBy('payment_date', 'desc')
        ->orderBy('id', 'desc');

        // Apply filters to both queries
        [$feesCollectQuery, $paymentTransactionQuery] = $this->applyFiltersToUnifiedQueries(
            $feesCollectQuery,
            $paymentTransactionQuery,
            $request
        );

        // Get the results
        $feesCollectResults = $feesCollectQuery->get();
        $paymentTransactionResults = $paymentTransactionQuery->get();

        // Transform FeesCollect records with standardized receipt calculation
        $feesCollectTransformed = $feesCollectResults->map(function ($payment) use ($paymentMethods) {
            // Use standardized calculation that matches receipt generation
            $payment = $this->standardizeReceiptData($payment, 'legacy');

            // Add listing-specific attributes
            $payment->payment_method_label = $paymentMethods[$payment->payment_method] ?? 'Unknown';
            $payment->receipt_type = 'legacy';
            $payment->sort_date = $payment->date;
            $payment->sort_id = $payment->id;

            // Add additional receipt context for consistency
            $payment->qr_code = $this->generateQRCode($payment);
            $payment->formatted_payment_method = ___(\Config::get('site.payment_methods')[$payment->payment_method] ?? 'Cash');

            // Use existing system logic: if balance > 0, it's partial payment
            $payment->is_partial_payment = $payment->getBalanceAmount() > 0;

            return $payment;
        });

        // Transform PaymentTransaction records with standardized receipt calculation
        $paymentTransactionTransformed = $paymentTransactionResults->map(function ($transaction) use ($paymentMethods) {
            // Use standardized calculation that matches receipt generation
            $transaction->date = $transaction->payment_date;
            $transaction = $this->standardizeReceiptData($transaction, 'partial');
            
            // Add additional attributes for listing compatibility
            $transaction->fine_amount = 0; // PaymentTransactions typically don't have separate fines
            $transaction->collectBy = $transaction->collector;
            $transaction->fees_collect_by = $transaction->collected_by;
            $transaction->total_fine = 0;
            $transaction->grand_total = $transaction->amount;
            $transaction->related_payment_count = 1;
            $transaction->payment_method_label = $transaction->getPaymentMethodName();
            $transaction->receipt_type = 'partial';
            $transaction->sort_date = $transaction->payment_date;
            $transaction->sort_id = $transaction->id;
            $transaction->is_partial_payment = true;

            // Add additional receipt context for consistency
            $transaction->qr_code = $this->generateQRCode($transaction);
            $transaction->formatted_payment_method = $transaction->getPaymentMethodName();

            // Get related transactions for partial payment context (simplified for listing)
            $relatedTransactions = collect([$transaction]); // For listing, we show individual transaction
            $transaction->all_payments = $relatedTransactions;

            return $transaction;
        });

        // Combine all receipts
        $combinedReceipts = $feesCollectTransformed->merge($paymentTransactionTransformed);

        // Determine the most recent payment type to set sorting priority
        $mostRecentPayment = $combinedReceipts
            ->sortByDesc(function ($item) {
                // Safe date parsing with fallback
                try {
                    $sortDate = $item->sort_date ?? $item->created_at ?? now();
                    return \Carbon\Carbon::parse($sortDate)->timestamp;
                } catch (\Exception $e) {
                    return 0; // Fallback timestamp
                }
            })
            ->first();

        $mostRecentIsPartial = $mostRecentPayment ? ($mostRecentPayment->is_partial_payment ?? false) : false;

        // Apply dynamic sorting: most recent payment type appears first
        return $combinedReceipts
            ->sortBy([
                function ($item) use ($mostRecentIsPartial) {
                    // Dynamic priority based on most recent payment type
                    $isItemPartial = $item->is_partial_payment ?? false;

                    if ($mostRecentIsPartial) {
                        return $isItemPartial ? 0 : 1; // Partial payments first
                    } else {
                        return $isItemPartial ? 1 : 0; // Full payments first
                    }
                },
                function ($item) {
                    // Safe date parsing with fallback
                    try {
                        $sortDate = $item->sort_date ?? $item->created_at ?? now();
                        return -\Carbon\Carbon::parse($sortDate)->timestamp; // Latest date first
                    } catch (\Exception $e) {
                        return 0; // Fallback for invalid dates
                    }
                },
                function ($item) {
                    // Safe sort_id access with fallback
                    $sortId = $item->sort_id ?? $item->id ?? 0;
                    return -$sortId; // Latest ID first within same date
                }
            ])
            ->values();
    }

    /**
     * Apply filters to both FeesCollect and PaymentTransaction queries
     */
    private function applyFiltersToUnifiedQueries($feesCollectQuery, $paymentTransactionQuery, Request $request)
    {
        // Search filter
        if ($request->filled('q')) {
            $search = trim($request->get('q', ''));

            if ($search !== '') {
                $feesCollectQuery->where(function ($subQuery) use ($search) {
                    $subQuery->where('id', (int) $search)
                        ->orWhere('transaction_reference', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($studentQuery) use ($search) {
                            $studentQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('admission_no', 'like', "%{$search}%");
                        });
                });

                $paymentTransactionQuery->where(function ($subQuery) use ($search) {
                    $subQuery->where('id', (int) $search)
                        ->orWhere('transaction_reference', 'like', "%{$search}%")
                        ->orWhereHas('student', function ($studentQuery) use ($search) {
                            $studentQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%")
                                ->orWhere('admission_no', 'like', "%{$search}%");
                        });
                });
            }
        }

        // Date filters
        if ($request->filled('from_date')) {
            $feesCollectQuery->whereDate('date', '>=', $request->get('from_date'));
            $paymentTransactionQuery->whereDate('payment_date', '>=', $request->get('from_date'));
        }

        if ($request->filled('to_date')) {
            $feesCollectQuery->whereDate('date', '<=', $request->get('to_date'));
            $paymentTransactionQuery->whereDate('payment_date', '<=', $request->get('to_date'));
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $feesCollectQuery->where('payment_method', $request->get('payment_method'));
            $paymentTransactionQuery->where('payment_method', $request->get('payment_method'));
        }

        // Collector filter
        if ($request->filled('collector_id')) {
            $feesCollectQuery->where('fees_collect_by', $request->get('collector_id'));
            $paymentTransactionQuery->where('collected_by', $request->get('collector_id'));
        }

        return [$feesCollectQuery, $paymentTransactionQuery];
    }

    /**
     * Get school information for receipts
     */
    private function getSchoolInfo()
    {
        return [
            'name' => setting('application_name'),
            'address' => setting('address'),
            'phone' => setting('phone'),
            'email' => setting('email'),
            'logo' => setting('logo'),
            'currency' => setting('currency_symbol')
        ];
    }

    /**
     * Generate unique receipt number
     */
    private function generateReceiptNumber($payment)
    {
        return 'RCT-' . date('Y') . '-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate QR code for receipt verification
     */
    private function generateQRCode($payment)
    {
        $verificationUrl = url('/verify-receipt/' . base64_encode($payment->id));
        return $verificationUrl; // In a real implementation, you'd generate actual QR code image
    }

    /**
     * Generate batch information for group receipts
     */
    private function generateBatchInfo($payments)
    {
        return [
            'batch_id' => 'BATCH-' . date('Ymd-His'),
            'payment_count' => $payments->count(),
            'student_count' => $payments->pluck('student_id')->unique()->count(),
            'date_range' => [
                'start' => $payments->min('date'),
                'end' => $payments->max('date')
            ]
        ];
    }

    /**
     * Calculate payment totals
     */
    private function calculateTotals($payments)
    {
        return [
            'total_amount' => $payments->sum('amount'),
            'total_fine' => $payments->sum('fine_amount'),
            'grand_total' => $payments->sum(function($payment) {
                return $payment->amount + $payment->fine_amount;
            }),
            'payment_count' => $payments->count()
        ];
    }

    /**
     * Summarize payments by fee type
     */
    private function summarizeByFeeType($payments)
    {
        return $payments->groupBy(function($payment) {
            return $payment->feesAssignChildren->feesMaster->type->name ?? 'Unknown';
        })->map(function($typePayments) {
            return [
                'count' => $typePayments->count(),
                'amount' => $typePayments->sum('amount'),
                'fine' => $typePayments->sum('fine_amount'),
                'total' => $typePayments->sum(function($payment) {
                    return $payment->amount + $payment->fine_amount;
                })
            ];
        });
    }

    /**
     * Summarize payments by payment method
     */
    private function summarizeByPaymentMethod($payments)
    {
        $paymentMethods = config('site.payment_methods');
        
        return $payments->groupBy('payment_method')->map(function($methodPayments, $methodId) use ($paymentMethods) {
            return [
                'name' => $paymentMethods[$methodId] ?? 'Unknown',
                'count' => $methodPayments->count(),
                'total' => $methodPayments->sum(function($payment) {
                    return $payment->amount + $payment->fine_amount;
                })
            ];
        });
    }

    /**
     * Check if group receipt is available (multiple payments today)
     */
    public function checkGroupReceiptAvailability()
    {
        $todayPayments = FeesCollect::where('date', Carbon::now()->format('Y-m-d'))
            ->where('fees_collect_by', auth()->id())
            ->count();

        return response()->json([
            'has_multiple_payments' => $todayPayments > 1
        ]);
    }

    /**
     * Get today's payment IDs for group receipt
     */
    public function getTodayPayments()
    {
        $paymentIds = FeesCollect::where('date', Carbon::now()->format('Y-m-d'))
            ->where('fees_collect_by', auth()->id())
            ->pluck('id')
            ->toArray();

        return response()->json([
            'payment_ids' => $paymentIds
        ]);
    }

    /**
     * Fetch payments that are part of the same receipt transaction.
     * Scoped to billing period to prevent cross-period payment aggregation.
     */
    private function getRelatedPayments(FeesCollect $payment)
    {
        $relations = [
            'feesAssignChildren.feesMaster.type',
            'feesAssignChildren.feesMaster.group'
        ];

        $query = FeesCollect::with($relations)
            ->where('student_id', $payment->student_id)
            ->where(function($q) {
                // Enhanced payment detection: include fees that are paid through any method
                $q->whereNotNull('payment_method')
                  ->orWhere('payment_status', 'paid')
                  ->orWhereColumn('total_paid', '>=', 'amount');
            });

        // Primary grouping: Use generation_batch_id for fees from same generation cycle
        if ($payment->generation_batch_id) {
            $query->where('generation_batch_id', $payment->generation_batch_id);
        } else {
            // Fallback grouping: Use date + collector + billing period for session context
            $query->where('date', $payment->date)
                ->where('fees_collect_by', $payment->fees_collect_by);
        }

        // Critical: Add billing period scoping to prevent cross-period aggregation
        // This ensures receipts only show fees from the same billing period
        if ($payment->billing_period) {
            $query->where('billing_period', $payment->billing_period);
        }

        // Additional academic year scoping for extra safety
        if ($payment->academic_year_id) {
            $query->where('academic_year_id', $payment->academic_year_id);
        }

        return $query->get();
    }

    /**
     * Email receipt to student/parent (placeholder for future implementation)
     */
    public function emailReceipt(Request $request)
    {
        // This is a placeholder for email functionality
        // You can implement actual email sending here
        
        return response()->json([
            'success' => false,
            'message' => ___('fees.email_feature_coming_soon')
        ]);
    }
}
