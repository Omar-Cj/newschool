<?php

namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Models\Fees\FeesCollect;
use App\Models\Student;
use App\Models\User;
use App\Repositories\StudentInfo\StudentRepository;
use Illuminate\Http\Request;
use PDF;
use Carbon\Carbon;

class ReceiptController extends Controller
{
    private $studentRepo;

    public function __construct(StudentRepository $studentRepo)
    {
        $this->studentRepo = $studentRepo;
    }

    /**
     * Generate individual student receipt for a specific payment
     */
    public function generateIndividualReceipt($paymentId)
    {
        try {
            // Get the main payment record
            $mainPayment = FeesCollect::with([
                'student.sessionStudentDetails.class',
                'student.sessionStudentDetails.section',
                'feesAssignChildren.feesMaster.type',
                'feesAssignChildren.feesMaster.group',
                'collectBy'
            ])->findOrFail($paymentId);

            $allPayments = $this->getRelatedPayments($mainPayment);

            $data = [
                'title' => ___('fees.payment_receipt'),
                'payment' => $mainPayment,
                'all_payments' => $allPayments,
                'school_info' => $this->getSchoolInfo(),
                'receipt_number' => $this->generateReceiptNumber($mainPayment),
                'qr_code' => $this->generateQRCode($mainPayment),
                'total_amount' => $allPayments->sum('amount'),
                'total_fine' => $allPayments->sum('fine_amount')
            ];

            // Check if this is a print preview request
            if (request()->has('print') && request()->get('print') == '1') {
                // Return HTML view for browser printing
                return view('backend.fees.receipts.individual', compact('data'));
            }

            // Continue with existing PDF download functionality
            $pdf = PDF::loadView('backend.fees.receipts.individual', compact('data'));
            $pdf->setPaper('A4', 'portrait');
            
            $fileName = 'receipt_' . $mainPayment->student->admission_no . '_' . date('Y-m-d', strtotime($mainPayment->date)) . '.pdf';
            
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
        $payment = FeesCollect::with([
            'student.sessionStudentDetails.class',
            'student.sessionStudentDetails.section',
            'feesAssignChildren.feesMaster.type',
            'feesAssignChildren.feesMaster.group',
        'collectBy'
        ])->findOrFail($paymentId);

        $allPayments = $this->getRelatedPayments($payment);

        $payment->total_amount = $allPayments->sum('amount');
        $payment->total_fine = $allPayments->sum('fine_amount');
        $payment->grand_total = $payment->total_amount + $payment->total_fine;
        $payment->receipt_number = $this->generateReceiptNumber($payment);
        $payment->payment_method_label = config('site.payment_methods')[$payment->payment_method] ?? 'Unknown';

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
                ],
            ]);
        }

        return view('backend.fees.receipts.options-page', compact('payment'));
    }

    /**
     * Display paginated list of receipts with filtering options.
     */
    public function index(Request $request)
    {
        $paymentMethods = config('site.payment_methods');

        $receiptsQuery = FeesCollect::with([
            'student.sessionStudentDetails.class',
            'student.sessionStudentDetails.section',
            'collectBy'
        ])->paid();

        $receiptsQuery->when($request->filled('q'), function ($query) use ($request) {
            $search = trim($request->get('q', ''));

            if ($search === '') {
                return;
            }

            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('id', (int) $search)
                    ->orWhere('transaction_reference', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('admission_no', 'like', "%{$search}%");
                    });
            });
        });

        $receiptsQuery->when($request->filled('from_date'), function ($query) use ($request) {
            $query->whereDate('date', '>=', $request->get('from_date'));
        });

        $receiptsQuery->when($request->filled('to_date'), function ($query) use ($request) {
            $query->whereDate('date', '<=', $request->get('to_date'));
        });

        $receiptsQuery->when($request->filled('payment_method'), function ($query) use ($request) {
            $query->where('payment_method', $request->get('payment_method'));
        });

        $receiptsQuery->when($request->filled('collector_id'), function ($query) use ($request) {
            $query->where('fees_collect_by', $request->get('collector_id'));
        });

        $receipts = $receiptsQuery
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $receipts->getCollection()->transform(function ($payment) use ($paymentMethods) {
            $relatedPayments = $this->getRelatedPayments($payment);

            $payment->receipt_number = $this->generateReceiptNumber($payment);
            $payment->total_amount = $relatedPayments->sum('amount');
            $payment->total_fine = $relatedPayments->sum('fine_amount');
            $payment->grand_total = $payment->total_amount + $payment->total_fine;
            $payment->related_payment_count = $relatedPayments->count();
            $payment->payment_method_label = $paymentMethods[$payment->payment_method] ?? 'Unknown';

            return $payment;
        });

        $collectorIds = FeesCollect::paid()->distinct()->pluck('fees_collect_by')->filter()->unique();
        $collectors = $collectorIds->isEmpty()
            ? collect()
            : User::whereIn('id', $collectorIds)->orderBy('name')->get();

        return view('backend.fees.receipts.index', [
            'receipts' => $receipts,
            'availableMethods' => $paymentMethods,
            'collectors' => $collectors,
        ]);
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
     */
    private function getRelatedPayments(FeesCollect $payment)
    {
        $relations = [
            'feesAssignChildren.feesMaster.type',
            'feesAssignChildren.feesMaster.group'
        ];

        $query = FeesCollect::with($relations)
            ->where('student_id', $payment->student_id)
            ->whereNotNull('payment_method');

        if ($payment->generation_batch_id) {
            $query->where('generation_batch_id', $payment->generation_batch_id);
        } else {
            $query->where('date', $payment->date)
                ->where('fees_collect_by', $payment->fees_collect_by);
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
