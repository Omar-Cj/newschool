<?php

namespace App\Http\Controllers\Fees;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\Collect\FeesCollectStoreRequest;
use App\Http\Requests\Fees\Collect\FeesCollectUpdateRequest;
use App\Interfaces\Fees\FeesCollectInterface;
use App\Models\EarlyPaymentDiscount;
use App\Models\Setting;
use App\Models\StudentInfo\Student;
use App\Repositories\Academic\ClassesRepository;
use App\Repositories\Academic\SectionRepository;
use App\Repositories\Fees\FeesMasterRepository;
use App\Repositories\StudentInfo\StudentRepository;
use App\Services\SiblingFeeCollectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class FeesCollectController extends Controller
{
    private $repo;
    private $classRepo;
    private $sectionRepo;
    private $studentRepo;
    private $feesMasterRepo;
    private $siblingFeeService;

    function __construct(
        FeesCollectInterface   $repo,
        ClassesRepository      $classRepo,
        SectionRepository      $sectionRepo,
        StudentRepository      $studentRepo,
        FeesMasterRepository   $feesMasterRepo,
        SiblingFeeCollectionService $siblingFeeService
        )
    {
        $this->repo              = $repo;
        $this->classRepo         = $classRepo;
        $this->sectionRepo       = $sectionRepo;
        $this->studentRepo       = $studentRepo;
        $this->feesMasterRepo    = $feesMasterRepo;
        $this->siblingFeeService = $siblingFeeService;
    }

    public function index()
    {
        $data['title']              = ___('fees.fees_collect');
        $data['fees_collects']      = $this->repo->getPaginateAll();
        $data['classes']            = $this->classRepo->assignedAll();
        $data['sections']           = $this->sectionRepo->all();

        return view('backend.fees.collect.index', compact('data'));
    }

    public function create()
    {
        $data['title']        = ___('fees.fees_collect');
        return view('backend.fees.collect.create', compact('data'));

    }

    public function collect($id)
    { // student id
        $data['title']          = ___('fees.fees_collect');
        $data['student']        = $this->studentRepo->show($id);
        $data['fees_assigned']  = $this->repo->feesAssigned($id);

        // Add deposit information for student
        $student = $this->studentRepo->show($id);
        $enhancedFeeService = app(\App\Services\EnhancedFeeCollectionService::class);
        $depositAmount = $enhancedFeeService->checkAvailableDeposit($student);

        $formattedDeposit = '$' . number_format($depositAmount, 2);
        $data['deposit_info'] = [
            'available_deposit' => $depositAmount,
            'formatted_deposit' => $formattedDeposit,
            'has_deposit' => $depositAmount > 0,
            'deposit_message' => $depositAmount > 0
                ? "This student has {$formattedDeposit} in deposit balance that will be used automatically to minimize cash payment."
                : "No deposit balance available for this student."
        ];

        return view('backend.fees.collect.collect', compact('data'));
    }

    public function store(Request $request)
    {
        // Debug logging
        \Log::info('Fee collection request received', [
            'is_ajax' => $request->ajax(),
            'student_id' => $request->student_id,
            'payment_method' => $request->payment_method,
            'journal_id' => $request->journal_id,
            'payment_amount' => $request->payment_amount
        ]);

        // Validate request for new modal functionality
        $validationRules = [
            'student_id' => 'required|exists:students,id',
            'payment_method' => 'required|in:cash,zaad,edahab',
            'payment_amount' => 'required|numeric|min:0.01',
            'journal_id' => [
                'required',
                Rule::exists('journals', 'id')->where(function ($query) {
                    $branchId = auth()->user()->branch_id ?? null;

                    if ($branchId && Schema::hasColumn('journals', 'branch_id')) {
                        $query->where('branch_id', $branchId);
                    }
                })
            ],
            'payment_date' => 'required|date',
            'discount_type' => 'nullable|in:fixed,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_notes' => 'nullable|string|max:500',
            'fees_source' => 'nullable|in:legacy,service_based',
        ];

        // Conditional validation based on fees_source
        $feesSource = $request->input('fees_source', 'legacy');
        if ($feesSource === 'legacy') {
            $validationRules['fees_assign_childrens'] = 'required';
        } else {
            $validationRules['fees_assign_childrens'] = 'nullable';
            // For service-based partial payments, add specific validation
            $validationRules['fees_collect_ids'] = 'nullable|array';
            $validationRules['fees_collect_ids.*'] = 'exists:fees_collects,id';
        }

        $validatedData = $request->validate($validationRules);

        // CRITICAL: Validate fee eligibility for the student before processing payment
        $student = \App\Models\StudentInfo\Student::find($validatedData['student_id']);
        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.'
            ], 404);
        }

        $eligibilityCheck = $student->validateFeeOperation('fee collection');
        if (!$eligibilityCheck['allowed']) {
            \Log::warning('Fee collection blocked for fee-exempt student', [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'payment_amount' => $validatedData['payment_amount'],
                'fees_source' => $feesSource,
                'reason' => $eligibilityCheck['reason'],
                'attempted_by' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => $eligibilityCheck['reason'],
                'error_type' => 'fee_exempt_student',
                'student_info' => $eligibilityCheck['student_info']
            ], 403);
        }

        try {
            $result = $this->repo->store($request);

            if($result['status']){
                // If it's an AJAX request, return JSON with payment details and receipt options
                if ($request->ajax()) {
                    $student = $this->studentRepo->show($request->student_id);
                    $paymentId = $result['data']['payment_id'] ?? null;

                    $response = [
                        'success' => true,
                        'message' => $result['message'],
                        'payment_id' => $paymentId,
                        'payment_details' => [
                            'student_name' => $student->first_name . ' ' . $student->last_name,
                            'admission_no' => $student->admission_no,
                            'student_id' => $student->id,
                            'payment_date' => $request->payment_date,
                            'payment_method' => $request->payment_method,
                            'amount' => number_format($request->payment_amount, 2),
                            'journal_name' => $result['data']['journal_name'] ?? 'N/A'
                        ]
                    ];

                    // Add direct print URL instead of modal for improved UX
                    if ($paymentId) {
                        try {
                            // Generate direct print URL for immediate receipt access
                            $response['direct_print_url'] = route('fees.receipt.individual', [
                                'paymentId' => $paymentId,
                                'print' => 1,
                            ]);
                            $response['print_instructions'] = 'Receipt will open in new window for printing';
                        } catch (\Exception $e) {
                            \Log::warning('Could not generate direct print URL', [
                                'payment_id' => $paymentId,
                                'error' => $e->getMessage()
                            ]);
                            // Fallback to payment_id for backward compatibility
                        }
                    }

                    \Log::info('Fee collection AJAX response', $response);

                    return response()->json($response);
                }

                // Legacy handling for non-AJAX requests
                if ($request->has('simple_payment')) {
                    $paymentId = $result['data']['payment_id'] ?? null;
                    if ($paymentId) {
                        return redirect()->route('fees.receipt.options', $paymentId)
                            ->with('success', $result['message']);
                    }
                }

                return back()->with('success', $result['message']);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 422);
            }

            return back()->with('danger', $result['message']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Fee collection error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'An error occurred while processing the payment. Please try again.';

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return back()->with('danger', $errorMessage);
        }
    }

    public function edit($id)
    {
        $data['fees_collect']  = $this->repo->show($id);
        $data['title']         = ___('fees.fees_collect');
        return view('backend.fees.collect.edit', compact('data'));
    }

    public function update(FeesCollectUpdateRequest $request, $id)
    {
        $result = $this->repo->update($request, $id);
        if($result['status']){
            return redirect()->route('fees-collect.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function delete($id)
    {

        $result = $this->repo->destroy($id);
        if($result['status']):
            $success[0] = $result['message'];
            $success[1] = 'success';
            $success[2] = ___('alert.deleted');
            $success[3] = ___('alert.OK');
            return response()->json($success);
        else:
            $success[0] = $result['message'];
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
            return response()->json($success);
        endif;
    }

    public function getFeesCollectStudents(Request $request)
    {
        $data['students'] = $this->repo->getFeesAssignStudents($request);
        $data['title']    = ___('fees.fees_collect');
        $data['classes']  = $this->classRepo->assignedAll();
        return view('backend.fees.collect.index', compact('data'));
    }

    public function feesShow(Request $request)
    {
        $data = $this->repo->feesShow($request);
        $data['is_siblings_discount'] = false;
        $isEnable = Setting::where('name', 'early_payment_discount_applicable')->first();
        if ($isEnable && $isEnable->value == 1){
            $now = date('Y-m-d');
            $discount = EarlyPaymentDiscount::whereDate('start_date', '<=', $now)
                ->whereDate('end_date', '>=', $now)
                ->first();
            if ($discount){
                $data['early_payment_discount_percentage'] = $discount->discount_percentage;
                $data['discount_name'] = $discount->name;
            }
        }
        $data['siblings_discount_percentage'] = null;
        $data['siblings_discount_name'] = null;
        $isSiblingsDiscountEnable = Setting::where('name', 'siblings_discount_applicable')->first();
        if ($isSiblingsDiscountEnable && $isSiblingsDiscountEnable->value == 1){
            $student        = $this->studentRepo->show($request->student_id);
            if ($student->siblings_discount == 1){
                $data['is_siblings_discount'] = true;
                $feesAssignChild = $data['fees_assign_children']->first();
                if ($feesAssignChild && $feesAssignChild->feesDiscount) {
                    $data['siblings_discount_percentage'] = $feesAssignChild->feesDiscount->discount_percentage;
                    $data['siblings_discount_name'] = $feesAssignChild->feesDiscount->title;
                }
            }
        }

        // Add deposit information for student
        $student = $this->studentRepo->show($request->student_id);
        $enhancedFeeService = app(\App\Services\EnhancedFeeCollectionService::class);
        $depositAmount = $enhancedFeeService->checkAvailableDeposit($student);

        $formattedDeposit = '$' . number_format($depositAmount, 2);
        $data['deposit_info'] = [
            'available_deposit' => $depositAmount,
            'formatted_deposit' => $formattedDeposit,
            'has_deposit' => $depositAmount > 0,
            'deposit_message' => $depositAmount > 0
                ? "This student has {$formattedDeposit} in deposit balance that will be used automatically to minimize cash payment."
                : "No deposit balance available for this student."
        ];

        // Return JSON for AJAX requests (Service-based only)
        if ($request->ajax()) {
            try {
                $fees = [];
                $totalAmount = 0;
                $academicYearId = session('academic_year_id') ?: \App\Models\Session::active()->value('id');

                $generated = \App\Models\Fees\FeesCollect::query()
                    ->where('student_id', $request->student_id)
                    ->when($academicYearId, function($q) use ($academicYearId) {
                        $q->where('academic_year_id', $academicYearId);
                    })
                    ->where(function($q) {
                        // Enhanced payment detection: include fees that are unpaid or partially paid
                        $q->whereNull('payment_method')
                          ->orWhere('payment_status', '!=', 'paid')
                          ->orWhereColumn('total_paid', '<', DB::raw('(amount + COALESCE(fine_amount, 0) + COALESCE(late_fee_applied, 0) - COALESCE(discount_applied, 0))'));
                    })
                    ->get();

                foreach ($generated as $row) {
                    $balance = $row->getBalanceAmount(); // Use remaining balance instead of original amount
                    if ($balance <= 0) continue; // Skip fully paid fees
                    $fees[] = [
                        'fees_collect_id' => $row->id,
                        'name' => $row->getFeeName(),
                        'amount' => number_format($balance, 2),
                        'billing_period' => $row->billing_period,
                        'due_date' => optional($row->due_date)->format('Y-m-d'),
                    ];
                    $totalAmount += $balance;
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'source' => 'service_based',
                        'fees' => $fees,
                        'totalAmount' => $totalAmount,
                        'payableAmount' => $totalAmount,
                        'deposit_info' => $data['deposit_info']
                    ]
                ]);

            } catch (\Exception $e) {
                \Log::error('Error in feesShow AJAX request', [
                    'error' => $e->getMessage(),
                    'student_id' => $request->student_id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Unable to load student fees. Please try again.'
                ], 500);
            }
        }

        return view('backend.fees.collect.fees-show', compact('data'));
    }

    // Sibling Fee Collection Methods

    /**
     * Get sibling fee data for family payment
     */
    public function getSiblingFeeData(Request $request, $studentId)
    {
        try {
            $student = Student::findOrFail($studentId);

            // Check if student has siblings
            if (!$student->parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'No parent/guardian found for this student.'
                ], 404);
            }

            $siblingData = $this->siblingFeeService->getSiblingFeeData($student);

            // Check if there are any siblings with outstanding fees
            if (empty($siblingData['siblings'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'No siblings found with outstanding fees.',
                    'show_individual_only' => true
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => $siblingData
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting sibling fee data', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to load sibling fee data. Please try again.'
            ], 500);
        }
    }

    /**
     * Calculate payment distribution across siblings
     */
    public function calculateSiblingDistribution(Request $request)
    {
        $request->validate([
            'sibling_ids' => 'required|array|min:1',
            'sibling_ids.*' => 'exists:students,id',
            'total_amount' => 'required|numeric|min:0.01',
            'distribution_method' => 'required|in:equal,proportional,priority'
        ]);

        try {
            $distribution = $this->siblingFeeService->calculatePaymentDistribution(
                $request->sibling_ids,
                $request->total_amount,
                $request->distribution_method
            );

            return response()->json([
                'success' => true,
                'data' => $distribution
            ]);

        } catch (\Exception $e) {
            Log::error('Error calculating sibling distribution', [
                'sibling_ids' => $request->sibling_ids,
                'total_amount' => $request->total_amount,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to calculate distribution. Please try again.'
            ], 500);
        }
    }

    /**
     * Validate sibling payment data
     */
    public function validateSiblingPayment(Request $request)
    {
        $request->validate([
            'payment_mode' => 'required|in:deposit,direct',
            'sibling_payments' => 'required|array|min:1',
            'sibling_payments.*.student_id' => 'required|exists:students,id',
            'sibling_payments.*.amount' => 'required|numeric|min:0.01',
            'sibling_payments.*.fee_ids' => 'required|array|min:1',
            'sibling_payments.*.fee_ids.*' => 'exists:fees_collects,id'
        ]);

        try {
            $validation = $this->siblingFeeService->validateSiblingPayment($request->all());

            return response()->json([
                'success' => true,
                'data' => $validation
            ]);

        } catch (\Exception $e) {
            Log::error('Error validating sibling payment', [
                'request_data' => $request->all(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check your payment details.'
            ], 500);
        }
    }

    /**
     * Process sibling payment transaction
     */
    public function processSiblingPayment(Request $request)
    {
        // Validate the request
        $validationRules = [
            'payment_mode' => 'required|in:deposit,direct',
            'sibling_payments' => 'required|array|min:1',
            'sibling_payments.*.student_id' => 'required|exists:students,id',
            'sibling_payments.*.amount' => 'required|numeric|min:0.01',
            'sibling_payments.*.fee_ids' => 'required|array|min:1',
            'sibling_payments.*.fee_ids.*' => 'exists:fees_collects,id',
            'payment_date' => 'required|date',
            'payment_notes' => 'nullable|string|max:500',
        ];

        // Add validation for direct payment mode
        if ($request->payment_mode === 'direct') {
            $validationRules['payment_method'] = 'required|in:cash,zaad,edahab';
            $validationRules['journal_id'] = [
                'required',
                Rule::exists('journals', 'id')->where(function ($query) {
                    $branchId = auth()->user()->branch_id ?? null;
                    if ($branchId && Schema::hasColumn('journals', 'branch_id')) {
                        $query->where('branch_id', $branchId);
                    }
                })
            ];
        }

        $validatedData = $request->validate($validationRules);

        try {
            // Pre-validate sibling payment data
            $validation = $this->siblingFeeService->validateSiblingPayment($validatedData);

            if (!$validation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment validation failed',
                    'errors' => $validation['errors']
                ], 422);
            }

            // Validate each student's fee eligibility
            foreach ($validatedData['sibling_payments'] as $siblingPayment) {
                $student = Student::find($siblingPayment['student_id']);
                $eligibilityCheck = $student->validateFeeOperation('fee collection');

                if (!$eligibilityCheck['allowed']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Payment blocked for {$student->full_name}: {$eligibilityCheck['reason']}",
                        'error_type' => 'fee_exempt_student',
                        'student_info' => $eligibilityCheck['student_info']
                    ], 403);
                }
            }

            // Process the payment
            $result = $this->siblingFeeService->processSiblingPayment($validatedData);

            if ($result['success']) {
                Log::info('Sibling payment processed successfully', [
                    'processed_by' => auth()->id(),
                    'total_amount' => $validation['total_payment'],
                    'payment_mode' => $validatedData['payment_mode'],
                    'siblings_count' => count($validatedData['sibling_payments'])
                ]);

                // Prepare response with payment details
                $responseData = [
                    'success' => true,
                    'message' => 'Family payment processed successfully!',
                    'summary' => $result['summary'],
                    'results' => $result['results']
                ];

                // Add receipt generation options if available
                $paymentIds = [];
                foreach ($result['results'] as $siblingResult) {
                    if ($siblingResult['success'] && !empty($siblingResult['transactions'])) {
                        foreach ($siblingResult['transactions'] as $transaction) {
                            $paymentIds[] = $transaction->id;
                        }
                    }
                }

                if (!empty($paymentIds)) {
                    $responseData['payment_ids'] = $paymentIds;
                    $responseData['receipt_options'] = 'Multiple receipts available for each student';
                }

                return response()->json($responseData);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed. Please try again.',
                'results' => $result['results'] ?? []
            ], 422);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Sibling payment processing failed', [
                'request_data' => $validatedData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the family payment. Please try again.'
            ], 500);
        }
    }

    /**
     * Get sibling fee summary for a parent
     */
    public function getSiblingFeeSummary(Request $request, $studentId)
    {
        try {
            $student = Student::findOrFail($studentId);

            if (!$student->parent) {
                return response()->json([
                    'success' => false,
                    'message' => 'No parent/guardian found for this student.'
                ], 404);
            }

            $summary = $student->parent->getSiblingFeeSummary();

            return response()->json([
                'success' => true,
                'data' => $summary
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting sibling fee summary', [
                'student_id' => $studentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to load fee summary. Please try again.'
            ], 500);
        }
    }

}
