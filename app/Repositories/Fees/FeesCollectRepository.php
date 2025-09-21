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
use Illuminate\Support\Facades\Schema;

class FeesCollectRepository implements FeesCollectInterface
{
    use ReturnFormatTrait;

    private $model;
    private $feesMasterRepo;

    public function __construct(FeesCollect $model, FeesMasterInterface $feesMasterRepo)
    {
        $this->model          = $model;
        $this->feesMasterRepo = $feesMasterRepo;
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

                // Service-based processing: mark generated unpaid fees as paid (full payment only for now)
                if ($request->fees_source === 'service_based') {
                    // Only support full outstanding payment to avoid partial allocation ambiguity
                    $academicYearId = session('academic_year_id') ?: \App\Models\Session::active()->value('id');
                    $unpaid = \App\Models\Fees\FeesCollect::query()
                        ->where('student_id', $request->student_id)
                        ->when($academicYearId, function($q) use ($academicYearId) {
                            $q->where('academic_year_id', $academicYearId);
                        })
                        ->whereNull('payment_method')
                        ->orderBy('due_date')
                        ->get();

                    $outstandingTotal = 0;
                    foreach ($unpaid as $row) {
                        $outstandingTotal += $row->getNetAmount();
                    }

                    // Normalize floats
                    $payAmount = (float) $request->payment_amount;
                    $epsilon = 0.01;

                    if (abs($payAmount - $outstandingTotal) > $epsilon) {
                        DB::rollBack();
                        return $this->responseWithError('Payment amount must equal the outstanding total for service-based fees.', []);
                    }

                    foreach ($unpaid as $row) {
                        $row->date = $request->payment_date ?? date('Y-m-d');
                        $row->payment_method = $paymentMethodInt;
                        $row->payment_gateway = $request->payment_method;
                        $row->fees_collect_by = Auth::user()->id;
                        $row->journal_id = $request->journal_id;
                        $row->transaction_reference = $request->transaction_reference;
                        $row->payment_notes = $request->payment_notes;
                        $row->save();

                        if ($firstPaymentId === null) {
                            $firstPaymentId = $row->id;
                        }
                    }

                    DB::commit();
                    return $this->responseWithSuccess(___('alert.created_successfully'), [
                        'payment_id' => $firstPaymentId,
                        'journal_name' => $journal ? $journal->display_name : null
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

        $data['student_id']           = $request->student_id;
        $data['discount_amount']      = $request->discount_amount;
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
