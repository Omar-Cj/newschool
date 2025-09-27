<?php

namespace App\Repositories\Fees;

use App\Models\AssignFeesDiscount;
use App\Models\EarlyPaymentDiscount;
use App\Models\Setting;
use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Accounts\Income;
use App\Models\Fees\FeesCollect;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Fees\FeesAssignChildren;
use App\Interfaces\Fees\FeesMasterInterface;
use App\Interfaces\Fees\FeesCollectInterface;
use App\Models\Accounts\AccountHead;
use App\Models\StudentInfo\SessionClassStudent;
use App\Services\PartialPaymentService;
use App\Services\EnhancedFeeCollectionService;
use Illuminate\Support\Facades\Schema;

class FeesCollectRepository implements FeesCollectInterface
{
    use ReturnFormatTrait;

    private $model;
    private $feesMasterRepo;
    private $partialPaymentService;
    private $enhancedFeeService;

    public function __construct(
        FeesCollect $model,
        FeesMasterInterface $feesMasterRepo,
        PartialPaymentService $partialPaymentService,
        EnhancedFeeCollectionService $enhancedFeeService
    ) {
        $this->model          = $model;
        $this->feesMasterRepo = $feesMasterRepo;
        $this->partialPaymentService = $partialPaymentService;
        $this->enhancedFeeService = $enhancedFeeService;
    }

    public function all()
    {
        return $this->model->active()->get();
    }

    public function getPaginateAll()
    {
        return $this->model::latest()->paginate(10);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            $firstPaymentId = null;
            $journal = null;

            // Get journal information for response
            if ($request->journal_id) {
                $journalQuery = \Modules\Journals\Entities\Journal::query()->where('id', $request->journal_id);

                $branchId = Auth::user()->branch_id ?? null;
                if ($branchId && Schema::hasColumn('journals', 'branch_id')) {
                    $journalQuery->where('branch_id', $branchId);
                }

                $journal = $journalQuery->first();
            }

            // Handle new modal payment processing
            if ($request->has('payment_amount') && $request->has('fees_assign_childrens') && is_string($request->fees_assign_childrens)) {
                $feesAssignChildrens = json_decode($request->fees_assign_childrens, true);

                // Map payment method string to integer for legacy compatibility
                $paymentMethodMap = [
                    'cash' => 1,
                    'zaad' => 3,
                    'edahab' => 4,
                    'stripe' => 2, // Legacy
                    'paypal' => 5  // Legacy
                ];

                $paymentMethodInt = $paymentMethodMap[$request->payment_method] ?? 1;

                // Enhanced service-based processing with automatic deposit utilization
                if ($request->fees_source === 'service_based') {
                    $academicYearId = session('academic_year_id') ?: \App\Models\Session::active()->value('id');

                    // Get unpaid fees for the student
                    $unpaidFees = \App\Models\Fees\FeesCollect::query()
                        ->where('student_id', $request->student_id)
                        ->when($academicYearId, function($q) use ($academicYearId) {
                            $q->where('academic_year_id', $academicYearId);
                        })
                        ->where(function($q) {
                            $q->whereNull('payment_method')
                              ->orWhere('payment_status', '!=', 'paid')
                              ->orWhereColumn('total_paid', '<', DB::raw('(amount + COALESCE(fine_amount, 0) + COALESCE(late_fee_applied, 0) - COALESCE(discount_applied, 0))'));
                        })
                        ->orderBy('due_date')
                        ->get();

                    if ($unpaidFees->isEmpty()) {
                        DB::rollBack();
                        return $this->responseWithError('No outstanding fees found for this student.', []);
                    }

                    $payAmount = (float) $request->payment_amount;
                    $totalOutstanding = $unpaidFees->sum(fn($fee) => $fee->getBalanceAmount());

                    // Validate payment amount
                    if ($payAmount <= 0) {
                        DB::rollBack();
                        return $this->responseWithError('Payment amount must be greater than zero.', []);
                    }

                    if ($payAmount > $totalOutstanding) {
                        DB::rollBack();
                        return $this->responseWithError('Payment amount cannot exceed total outstanding balance.', []);
                    }

                    // Use enhanced fee collection service for deposit optimization
                    $remainingAmount = $payAmount;
                    $processedPayments = [];
                    $totalDepositUsed = 0;
                    $totalCashPayment = 0;
                    $isPartialPayment = $payAmount < $totalOutstanding;

                    foreach ($unpaidFees as $fee) {
                        if ($remainingAmount <= 0) break;

                        $feeBalance = $fee->getBalanceAmount();
                        if ($feeBalance <= 0) continue;

                        $paymentForThisFee = min($remainingAmount, $feeBalance);

                        // Prepare payment data for enhanced service
                        $paymentData = [
                            'amount' => $paymentForThisFee,
                            'payment_method' => $paymentMethodInt,
                            'payment_date' => $request->payment_date ?? now()->toDateString(),
                            'transaction_reference' => $request->transaction_reference,
                            'payment_notes' => $request->payment_notes,
                        ];

                        try {
                            // Use enhanced fee collection service with automatic deposit utilization
                            $result = $this->enhancedFeeService->collectFeeWithDeposit($fee, $paymentData);

                            if ($result['success']) {
                                $processedPayments[] = [
                                    'fee_id' => $fee->id,
                                    'payment_transactions' => $result['transactions'],
                                    'amount_paid' => $paymentForThisFee,
                                    'deposit_used' => $result['deposit_used'],
                                    'cash_payment' => $result['cash_payment'],
                                    'fee_name' => $fee->getFeeName()
                                ];

                                $totalDepositUsed += $result['deposit_used'];
                                $totalCashPayment += $result['cash_payment'];

                                // Set first payment ID for receipt generation
                                if ($firstPaymentId === null && !empty($result['transactions'])) {
                                    $firstPaymentId = $result['transactions'][0]->id;
                                }
                            } else {
                                // Fallback to regular partial payment service if enhanced fails
                                $fallbackData = [
                                    'amount' => $paymentForThisFee,
                                    'payment_method' => $request->payment_method,
                                    'payment_date' => $request->payment_date ?? now()->toDateString(),
                                    'transaction_reference' => $request->transaction_reference,
                                    'payment_notes' => $request->payment_notes,
                                    'journal_id' => $request->journal_id,
                                ];

                                $fallbackResult = $this->partialPaymentService->processPayment($fallbackData, $fee->id);

                                if (!$fallbackResult['success']) {
                                    DB::rollBack();
                                    return $this->responseWithError($fallbackResult['message'], []);
                                }

                                $processedPayments[] = [
                                    'fee_id' => $fee->id,
                                    'payment_id' => $fallbackResult['data']['payment_id'],
                                    'amount_paid' => $paymentForThisFee,
                                    'deposit_used' => 0,
                                    'cash_payment' => $paymentForThisFee,
                                    'fee_name' => $fee->getFeeName()
                                ];

                                $totalCashPayment += $paymentForThisFee;

                                if ($firstPaymentId === null) {
                                    $firstPaymentId = $fallbackResult['data']['payment_id'];
                                }
                            }
                        } catch (\Exception $e) {
                            \Log::warning('Enhanced fee collection failed, using fallback', [
                                'fee_id' => $fee->id,
                                'error' => $e->getMessage()
                            ]);

                            // Fallback to standard processing
                            $fallbackData = [
                                'amount' => $paymentForThisFee,
                                'payment_method' => $request->payment_method,
                                'payment_date' => $request->payment_date ?? now()->toDateString(),
                                'transaction_reference' => $request->transaction_reference,
                                'payment_notes' => $request->payment_notes,
                                'journal_id' => $request->journal_id,
                            ];

                            $fallbackResult = $this->partialPaymentService->processPayment($fallbackData, $fee->id);

                            if (!$fallbackResult['success']) {
                                DB::rollBack();
                                return $this->responseWithError($fallbackResult['message'], []);
                            }

                            $processedPayments[] = [
                                'fee_id' => $fee->id,
                                'payment_id' => $fallbackResult['data']['payment_id'],
                                'amount_paid' => $paymentForThisFee,
                                'deposit_used' => 0,
                                'cash_payment' => $paymentForThisFee,
                                'fee_name' => $fee->getFeeName()
                            ];

                            $totalCashPayment += $paymentForThisFee;

                            if ($firstPaymentId === null) {
                                $firstPaymentId = $fallbackResult['data']['payment_id'];
                            }
                        }

                        $remainingAmount -= $paymentForThisFee;
                    }

                    DB::commit();

                    $responseMessage = $isPartialPayment
                        ? ___('fees.partial_payment_processed_successfully')
                        : ___('alert.created_successfully');

                    return $this->responseWithSuccess($responseMessage, [
                        'payment_id' => $firstPaymentId,
                        'journal_name' => $journal ? $journal->display_name : null,
                        'is_partial_payment' => $isPartialPayment,
                        'remaining_balance' => $totalOutstanding - $payAmount,
                        'processed_payments' => $processedPayments,
                        'total_amount_paid' => $payAmount,
                        'total_deposit_used' => $totalDepositUsed,
                        'total_cash_payment' => $totalCashPayment,
                        'deposit_optimization' => $totalDepositUsed > 0 ? "Used \${$totalDepositUsed} from deposits, saved cash payment!" : null
                    ]);
                }

                // Calculate discount amount
                $discountAmount = 0;
                if ($request->discount_type && $request->discount_amount) {
                    if ($request->discount_type === 'percentage') {
                        $discountAmount = ($request->payment_amount * $request->discount_amount) / 100;
                    } else {
                        $discountAmount = $request->discount_amount;
                    }
                }

                // Check if this is a partial payment for a single fee
                if (count($feesAssignChildrens) === 1) {
                    $feeData = $feesAssignChildrens[0];
                    $feeId = $feeData['id'];

                    // Find existing fee record
                    $existingFee = $this->model::where('fees_assign_children_id', $feeId)
                        ->where('student_id', $request->student_id)
                        ->where('session_id', setting('session'))
                        ->first();

                    if ($existingFee) {
                        $feeAmount = $existingFee->getNetAmount();
                        $paymentAmount = (float) $request->payment_amount;

                        // If payment is less than fee amount or full payment, use enhanced service with deposit optimization
                        if (($paymentAmount <= $feeAmount) && !$existingFee->isPaid()) {
                            $paymentData = [
                                'amount' => $paymentAmount,
                                'payment_method' => $paymentMethodInt,
                                'payment_date' => $request->payment_date ?? $request->date ?? date('Y-m-d'),
                                'transaction_reference' => $request->transaction_reference,
                                'payment_notes' => $request->payment_notes,
                            ];

                            try {
                                // Try enhanced fee collection service first for deposit optimization
                                $result = $this->enhancedFeeService->collectFeeWithDeposit($existingFee, $paymentData);

                                if ($result['success']) {
                                    DB::commit();
                                    return $this->responseWithSuccess(___('alert.created_successfully'), [
                                        'payment_id' => !empty($result['transactions']) ? $result['transactions'][0]->id : null,
                                        'total_amount_paid' => $result['total_amount'],
                                        'deposit_used' => $result['deposit_used'],
                                        'cash_payment' => $result['cash_payment'],
                                        'remaining_deposit' => $result['remaining_deposit'],
                                        'journal_name' => $journal ? $journal->display_name : null,
                                        'is_partial_payment' => $paymentAmount < $feeAmount,
                                        'deposit_optimization' => $result['deposit_used'] > 0 ? "Used \${$result['deposit_used']} from deposits!" : null
                                    ]);
                                }
                            } catch (\Exception $e) {
                                \Log::warning('Enhanced fee collection failed for single payment, using fallback', [
                                    'fee_id' => $existingFee->id,
                                    'error' => $e->getMessage()
                                ]);
                            }

                            // Fallback to partial payment service
                            $fallbackData = [
                                'amount' => $paymentAmount,
                                'payment_method' => $request->payment_method,
                                'payment_date' => $request->payment_date ?? $request->date ?? date('Y-m-d'),
                                'transaction_reference' => $request->transaction_reference,
                                'payment_notes' => $request->payment_notes,
                                'journal_id' => $request->journal_id,
                            ];

                            $result = $this->partialPaymentService->processPayment($fallbackData, $existingFee->id);

                            if ($result['success']) {
                                DB::commit();
                                return $this->responseWithSuccess($result['message'], [
                                    'payment_id' => $result['data']['payment_id'],
                                    'transaction_number' => $result['data']['transaction_number'],
                                    'amount_paid' => $result['data']['amount_paid'],
                                    'remaining_balance' => $result['data']['remaining_balance'],
                                    'payment_status' => $result['data']['payment_status'],
                                    'journal_name' => $journal ? $journal->display_name : null,
                                    'is_partial_payment' => $paymentAmount < $feeAmount
                                ]);
                            } else {
                                DB::rollBack();
                                return $this->responseWithError($result['message'], []);
                            }
                        }
                    }
                }

                foreach ($feesAssignChildrens as $feeData) {
                    $feeId = $feeData['id'];

                    // Check for existing payment record
                    $existingFee = $this->model::where('fees_assign_children_id', $feeId)
                        ->where('student_id', $request->student_id)
                        ->where('session_id', setting('session'))
                        ->whereNull('payment_method')
                        ->first();

                    if ($existingFee) {
                        $row = $existingFee;
                    } else {
                        $row = new $this->model;
                        $row->fees_assign_children_id = $feeId;
                        $row->student_id = $request->student_id;
                        $row->session_id = setting('session');
                        $row->generation_method = 'manual';
                    }

                    // Update payment details
                    $row->date = $request->payment_date ?? $request->date ?? date('Y-m-d');
                    $row->payment_method = $paymentMethodInt;
                    $row->payment_gateway = $request->payment_method;
                    $row->amount = $request->payment_amount;
                    $row->fine_amount = $request->fine_amount ?? 0;
                    $row->discount_amount = $discountAmount;
                    $row->discount_type = $request->discount_type;
                    $row->transaction_reference = $request->transaction_reference;
                    $row->payment_notes = $request->payment_notes;
                    $row->journal_id = $request->journal_id;
                    $row->fees_collect_by = Auth::user()->id;

                    $row->save();

                    if ($firstPaymentId === null) {
                        $firstPaymentId = $row->id;
                    }

                    // Create income record
                    $ac_head = AccountHead::where('type', 1)->where('status', 1)->first();
                    if ($ac_head) {
                        $incomeStore = new Income();
                        $incomeStore->fees_collect_id = $row->id;
                        $incomeStore->name = $feeData['name'] ?? "Fee Payment";
                        $incomeStore->session_id = setting('session');
                        $incomeStore->income_head = $ac_head->id;
                        $incomeStore->date = $row->date;
                        $incomeStore->amount = $row->amount - $discountAmount;
                        $incomeStore->invoice_number = 'fees_collect_' . $row->id;
                        $incomeStore->save();
                    }

                    // Handle tax if applicable
                    $tax = calculateTax($row->amount);
                    if ($tax > 0) {
                        $settings = Setting::whereIn('name', ['tax_income_head'])->pluck('value', 'name');
                        $accountHead = AccountHead::where('name', $settings['tax_income_head'])->first();
                        if ($accountHead) {
                            $incomeStore = new Income();
                            $incomeStore->name = "Fees-Tax";
                            $incomeStore->session_id = setting('session');
                            $incomeStore->income_head = $accountHead->id;
                            $incomeStore->date = $row->date;
                            $incomeStore->amount = $tax;
                            $incomeStore->save();
                        }
                    }
                }

                DB::commit();
                return $this->responseWithSuccess(___('alert.created_successfully'), [
                    'payment_id' => $firstPaymentId,
                    'journal_name' => $journal ? $journal->display_name : null
                ]);
            }

            // Legacy processing for old format
            foreach ($request->fees_assign_childrens as $key => $item) {
                $existingFee = $this->model::where('fees_assign_children_id', $item)
                    ->where('student_id', $request->student_id)
                    ->where('session_id', setting('session'))
                    ->where('generation_method', 'bulk')
                    ->whereNull('payment_method')
                    ->first();

                if ($existingFee) {
                    $row = $existingFee;
                    $row->date = $request->date;
                    $row->payment_method = $request->payment_method;
                    $row->amount = $request->amounts[$key] + ($request->fine_amounts[$key] ?? 0);
                    $row->fine_amount = $request->fine_amounts[$key] ?? 0;
                    $row->fees_collect_by = Auth::user()->id;
                } else {
                    $row = new $this->model;
                    $row->date = $request->date;
                    $row->payment_method = $request->payment_method;
                    $row->fees_assign_children_id = $item;
                    $row->amount = $request->amounts[$key] + ($request->fine_amounts[$key] ?? 0);
                    $row->fine_amount = $request->fine_amounts[$key] ?? 0;
                    $row->fees_collect_by = Auth::user()->id;
                    $row->student_id = $request->student_id;
                    $row->session_id = setting('session');
                    $row->generation_method = 'manual';
                }

                $row->save();

                if ($firstPaymentId === null) {
                    $firstPaymentId = $row->id;
                }

                // Legacy income and tax handling
                $ac_head = AccountHead::where('type', 1)->where('status', 1)->first();
                if ($ac_head) {
                    $incomeStore = new Income();
                    $incomeStore->fees_collect_id = $row->id;
                    $incomeStore->name = $item;
                    $incomeStore->session_id = setting('session');
                    $incomeStore->income_head = $ac_head->id;
                    $incomeStore->date = $request->date;
                    $incomeStore->amount = $row->amount;
                    $incomeStore->invoice_number = 'fees_collect_' . $item;
                    $incomeStore->save();
                }

                $tax = calculateTax($row->amount);
                $settings = Setting::whereIn('name', ['tax_income_head'])->pluck('value', 'name');
                $accountHead = AccountHead::where('name', $settings['tax_income_head'])->first();
                if ($tax > 0 && $settings) {
                    $incomeStore = new Income();
                    $incomeStore->name = "Fees-Tax";
                    $incomeStore->session_id = setting('session');
                    $incomeStore->income_head = $accountHead->id;
                    $incomeStore->date = $request->date;
                    $incomeStore->amount = $tax;
                    $incomeStore->save();
                }

                if ($request->early_payment_percentage > 0) {
                    $feesDiscount = new AssignFeesDiscount();
                    $feesDiscount->fees_assign_children_id = $item;
                    $feesDiscount->title = 'Early Payment Fees Discount';
                    $feesDiscount->discount_amount = calculateDiscount($row->amount, $request->early_payment_percentage);
                    $feesDiscount->discount_percentage = $request->early_payment_percentage;
                    $feesDiscount->discount_source = 'Early Payment Fees Discount';
                    $feesDiscount->save();
                }
            }

            DB::commit();
            return $this->responseWithSuccess(___('alert.created_successfully'), ['payment_id' => $firstPaymentId]);
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::error('Fee collection store error', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function show($id)
    {
        return $this->model->find($id);
    }

    public function feesAssigned($id) // student id
    {

        $groups = FeesAssignChildren::withCount('feesCollect')->with(['feesCollect', 'feesDiscount'])->where('student_id', $id);
        $groups = $groups->whereHas('feesAssign', function ($query) {
            return $query->where('session_id', setting('session'));
        });

        return $groups->get();
    }

    public function update($request, $id)
    {
        try {
            $row                = $this->model->findOrfail($id);
            $row->name          = $request->name;
            $row->code          = $request->code;
            $row->description   = $request->description;
            $row->status        = $request->status;
            $row->save();
            return $this->responseWithSuccess(___('alert.updated_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $row = $this->model->find($id);
            $row->delete();

            $income = Income::where('invoice_number', 'fees_collect_'.$row->fees_assign_children_id)->first();
            if($income){
                $income->delete();
            }
            DB::commit();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function getFeesAssignStudents($request)
    {
        $students = SessionClassStudent::query();
        $students = $students->where('session_id', setting('session'));
        if($request->class != "") {

            $students = $students->where('classes_id', $request->class);
        }

        if($request->section != "") {

            $students = $students->where('section_id', $request->section);
        }

        if($request->name != "") {
            $students = $students->whereHas('student', function ($query) use ($request) {
                return $query->where('first_name', $request->name)->orWhere('last_name', $request->name);
            });
        }

        if($request->student != "") {
            $students = $students->where('student_id', $request->student);
        }

        return $students->paginate(10);
    }

    public function feesShow($request)
    {
        $allAssigned = $this->feesAssigned($request->student_id);

        // If specific fees were requested, filter to those; otherwise include all unpaid
        if ($request->has('fees_assign_childrens') && is_array($request->fees_assign_childrens) && count($request->fees_assign_childrens) > 0) {
            $data['fees_assign_children'] = $allAssigned->whereIn('id', $request->fees_assign_childrens);
        } else {
            $data['fees_assign_children'] = $allAssigned->filter(function ($child) {
                return !$child->feesCollect || !$child->feesCollect->isPaid();
            })->values();
        }

        // Enhance each fee with partial payment information
        $data['fees_assign_children'] = $data['fees_assign_children']->map(function ($child) {
            if ($child->feesCollect) {
                $feeCollect = $child->feesCollect;

                // Add partial payment data
                $child->partial_payment_info = [
                    'total_amount' => $feeCollect->getNetAmount(),
                    'paid_amount' => $feeCollect->getPaidAmount(),
                    'balance_amount' => $feeCollect->getBalanceAmount(),
                    'payment_status' => $feeCollect->payment_status ?? 'unpaid',
                    'payment_percentage' => $feeCollect->getPaymentPercentage(),
                    'is_partially_paid' => $feeCollect->isPartiallyPaid(),
                    'is_paid' => $feeCollect->isPaid(),
                    'payment_history' => $feeCollect->paymentTransactions()->with('collector')->get()->map(function ($transaction) {
                        return [
                            'id' => $transaction->id,
                            'date' => $transaction->payment_date->format('Y-m-d'),
                            'amount' => $transaction->amount,
                            'method' => $transaction->getPaymentMethodName(),
                            'reference' => $transaction->transaction_reference,
                            'collected_by' => $transaction->getCollectorName(),
                            'notes' => $transaction->payment_notes,
                        ];
                    })
                ];
            }

            return $child;
        });

        $data['student_id']           = $request->student_id;
        $data['discount_amount']      = $request->discount_amount;

        // Add summary information
        $data['payment_summary'] = $this->partialPaymentService->getStudentPaymentSummary($request->student_id);

        return $data;
    }

    public function payWithStripeStore($request)
    {
        DB::transaction(function () use ($request) {
            Stripe::setApiKey(Setting('stripe_secret'));
            $feesAssignChildren = optional(FeesAssignChildren::with('feesMaster')->where('id', $request->fees_assign_children_id)->first());
            $description = 'Pay ' . ($request->amount + $request->fine_amount) . ' for ' . $feesAssignChildren->feesMaster?->type?->name . ' of ' . env('APP_NAME');

            $amount = ($request->amount + $request->fine_amount) * 100;
            $amount += calculateTax($amount);
            $now = date('Y-m-d');
            $discount = EarlyPaymentDiscount::whereDate('start_date', '<=', $now)
                ->whereDate('end_date', '>=', $now)
                ->first();

            if ($discount) {
                $amount -= calculateDiscount($amount, $discount->discount_percentage);
            }
            $amount = (int)round($amount);

            $charge = Charge::create([
                "amount" => $amount,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => $description
            ]);

            $this->feeCollectStoreByStripe($request, @$charge->balance_transaction);
        });
    }

    protected function feeCollectStoreByStripe($request, $transaction_id)
    {
        $feesCollect = FeesCollect::create([
            'date'                      => $request->date,
            'payment_method'            => 2,
            'payment_gateway'           => 'Stripe',
            'transaction_id'            => $transaction_id,
            'fees_assign_children_id'   => $request->fees_assign_children_id,
            'amount'                    => $request->amount + $request->fine_amount ?? 0,
            'fine_amount'               => $request->fine_amount,
            'fees_collect_by'           => 1, // Because student/parent can not be collect so that's why we use first admin user id.
            'student_id'                => $request->student_id,
            'session_id'                => setting('session')
        ]);

            $ac_head =  AccountHead::where('type', 1)->where('status', 1)->first();

            if($ac_head){
                $incomeStore                   = new Income();
                $incomeStore->fees_collect_id  = $feesCollect->id;
                $incomeStore->name             = env('APP_NAME').'_'.$request->fees_assign_children_id;
                $incomeStore->session_id       = setting('session');
                $incomeStore->income_head      = $ac_head->id; // Because, Fees id 1.
                $incomeStore->date             = $request->date;
                $incomeStore->amount           = $feesCollect->amount;
                $incomeStore->save();
            }
    }




    public function paypalOrderData($invoice_no, $success_route, $cancel_route)
    {
        $feesAssignChildren = optional(FeesAssignChildren::with('feesMaster')->where('id', session()->get('FeesAssignChildrenID'))->first());

        $total = $feesAssignChildren->feesMaster?->amount;
        $now = date('Y-m-d');
        $discount = EarlyPaymentDiscount::whereDate('start_date', '<=', $now)
            ->whereDate('end_date', '>=', $now)
            ->first();

        $total += calculateTax($total);

        if ($discount) {
            $total -= calculateDiscount($total, $discount->discount_percentage);
        }

        if (date('Y-m-d') > $feesAssignChildren->feesMaster?->due_date && (!$feesAssignChildren->feesCollect || !$feesAssignChildren->feesCollect->isPaid())) {
            $total += $feesAssignChildren->feesMaster?->fine_amount;
        }

        $description = 'Pay ' . $total . ' for ' . $feesAssignChildren->feesMaster?->type?->name;

        $data                           = [];
        $data['items']                  = [];
        $data['invoice_id']             = $invoice_no;
        $data['invoice_description']    = $description;
        $data['return_url']             = $success_route;
        $data['cancel_url']             = $cancel_route;
        $data['total']                  = $total;

        return $data;
    }





    public function feeCollectStoreByPaypal($response, $feesAssignChildren)
    {
        DB::transaction(function () use ($response, $feesAssignChildren) {

            $amount = $feesAssignChildren->feesMaster?->amount;
            $fine_amount = 0;

            if (date('Y-m-d') > $feesAssignChildren->feesMaster?->due_date && (!$feesAssignChildren->feesCollect || !$feesAssignChildren->feesCollect->isPaid())) {
                $fine_amount = $feesAssignChildren->feesMaster?->fine_amount;
                $amount += $fine_amount;
            }

            $date = date('Y-m-d', strtotime($response['PAYMENTINFO_0_ORDERTIME']));

            $feesCollect = FeesCollect::create([
                'date'                      => $date,
                'payment_method'            => 2,
                'payment_gateway'           => 'PayPal',
                'transaction_id'            => $response['PAYMENTINFO_0_TRANSACTIONID'],
                'fees_assign_children_id'   => $feesAssignChildren->id,
                'amount'                    => $amount,
                'fine_amount'               => $fine_amount,
                'fees_collect_by'           => 1, // Because student/parent can not be collect so that's why we use first admin user id.
                'student_id'                => $feesAssignChildren->student_id,
                'session_id'                => setting('session')
            ]);

            Income::create([
                'fees_collect_id'           => $feesCollect->id,
                'name'                      => $feesAssignChildren->id,
                'session_id'                => setting('session'),
                'income_head'               => 1, // Because, Fees id 1.
                'date'                      => $date,
                'amount'                    => $amount
            ]);
        });
    }
}
