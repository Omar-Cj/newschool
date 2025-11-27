<?php

declare(strict_types=1);

namespace Modules\MainApp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;
use Modules\MainApp\Entities\SubscriptionPayment;
use Modules\MainApp\Http\Repositories\SchoolRepository;
use Modules\MainApp\Http\Repositories\SubscriptionRepository;
use Modules\MainApp\Http\Repositories\SubscriptionPaymentRepository;
use Modules\MainApp\Http\Requests\SubscriptionPayment\StoreRequest;
use Modules\MainApp\Http\Requests\SubscriptionPayment\RejectRequest;

/**
 * SubscriptionPayment Controller
 *
 * Handles subscription payment management including approval workflow,
 * payment history, and receipt generation.
 */
class SubscriptionPaymentController extends Controller
{
    private SubscriptionPaymentRepository $paymentRepo;
    private SchoolRepository $schoolRepo;
    private SubscriptionRepository $subscriptionRepo;

    /**
     * SubscriptionPaymentController constructor.
     *
     * @param SubscriptionPaymentRepository $paymentRepo
     * @param SchoolRepository $schoolRepo
     * @param SubscriptionRepository $subscriptionRepo
     */
    public function __construct(
        SubscriptionPaymentRepository $paymentRepo,
        SchoolRepository $schoolRepo,
        SubscriptionRepository $subscriptionRepo
    ) {
        // Check if required tables exist
        if (!Schema::hasTable('settings') && !Schema::hasTable('users')) {
            abort(400);
        }

        $this->paymentRepo = $paymentRepo;
        $this->schoolRepo = $schoolRepo;
        $this->subscriptionRepo = $subscriptionRepo;
    }

    /**
     * Display list of all pending payments for admin review.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['status', 'school_id', 'start_date', 'end_date', 'payment_method']);

        // Always use getAllPayments with filters - this respects "All" status selection
        $data['payments'] = $this->paymentRepo->getAllPayments($filters);

        $data['title'] = ___('settings.Subscription Payments');
        $data['schools'] = $this->schoolRepo->getAll();
        $data['statusOptions'] = [
            SubscriptionPayment::STATUS_PENDING => 'Pending',
            SubscriptionPayment::STATUS_APPROVED => 'Approved',
            SubscriptionPayment::STATUS_REJECTED => 'Rejected',
        ];
        $data['paymentMethods'] = [
            SubscriptionPayment::METHOD_CASH => 'Cash',
            SubscriptionPayment::METHOD_BANK_TRANSFER => 'Bank Transfer',
            SubscriptionPayment::METHOD_MOBILE_MONEY => 'Mobile Money',
            SubscriptionPayment::METHOD_CHEQUE => 'Cheque',
            SubscriptionPayment::METHOD_CREDIT_CARD => 'Credit Card',
            SubscriptionPayment::METHOD_PAYPAL => 'PayPal',
        ];

        return view('mainapp::subscription-payment.index', compact('data'));
    }

    /**
     * Display payment history for a specific school.
     *
     * @param int $schoolId
     * @return \Illuminate\Contracts\View\View
     */
    public function history(int $schoolId): View
    {
        $data['payments'] = $this->paymentRepo->getSchoolPaymentHistory($schoolId);
        $data['school'] = $this->schoolRepo->getSchoolWithPackage($schoolId);
        $data['title'] = ___('settings.Payment History') . ' - ' . $data['school']->name;

        return view('mainapp::subscription-payment.history', compact('data'));
    }

    /**
     * Show form to record a new payment.
     *
     * @param int $schoolId
     * @return \Illuminate\Contracts\View\View
     */
    public function create(int $schoolId): View
    {
        $data['school'] = $this->schoolRepo->getSchoolWithPackage($schoolId);
        $data['subscription'] = $data['school']->subscriptions()->latest()->first();
        $data['title'] = ___('settings.Record Payment') . ' - ' . $data['school']->name;
        $data['paymentMethods'] = [
            SubscriptionPayment::METHOD_CASH => 'Cash',
            SubscriptionPayment::METHOD_BANK_TRANSFER => 'Bank Transfer',
            SubscriptionPayment::METHOD_MOBILE_MONEY => 'Mobile Money',
            SubscriptionPayment::METHOD_CHEQUE => 'Cheque',
            SubscriptionPayment::METHOD_CREDIT_CARD => 'Credit Card',
            SubscriptionPayment::METHOD_PAYPAL => 'PayPal',
        ];

        // Calculate payment amount: use total_price or calculate from package price × branch count
        if ($data['subscription']) {
            $data['paymentAmount'] = $data['subscription']->total_price
                ?? ($data['subscription']->package->price * max(1, $data['subscription']->branch_count ?? 1));
        } else {
            $data['paymentAmount'] = 0;
        }

        return view('mainapp::subscription-payment.create', compact('data'));
    }

    /**
     * Store a newly created payment record.
     *
     * @param StoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request): RedirectResponse
    {
        $result = $this->paymentRepo->createPaymentRecord($request->validated());

        if ($result['status']) {
            return redirect()
                ->route('subscription-payments.index')
                ->with('success', $result['message']);
        }

        return back()
            ->withInput()
            ->with('danger', $result['message']);
    }

    /**
     * Approve payment and extend subscription.
     *
     * Workflow:
     * 1. Validate payment exists and is pending
     * 2. Calculate new subscription dates
     * 3. Update payment status to approved
     * 4. Update subscription with new expiry dates
     * 5. Generate invoice number
     * 6. Log approval for audit
     *
     * @param int $id Payment ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(int $id): RedirectResponse
    {
        // Get current admin user ID
        $userId = Auth::id();

        // Approve payment and extend subscription
        $result = $this->paymentRepo->approvePayment($id, $userId);

        if ($result['status']) {
            return redirect()
                ->route('subscription-payments.index')
                ->with('success', $result['message']);
        }

        return back()->with('danger', $result['message']);
    }

    /**
     * Reject payment with reason.
     *
     * @param RejectRequest $request
     * @param int $id Payment ID
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(RejectRequest $request, int $id): RedirectResponse
    {
        // Get current admin user ID
        $userId = Auth::id();

        // Reject payment with reason
        $result = $this->paymentRepo->rejectPayment(
            $id,
            $userId,
            $request->validated('rejection_reason')
        );

        if ($result['status']) {
            return redirect()
                ->route('subscription-payments.index')
                ->with('success', $result['message']);
        }

        return back()->with('danger', $result['message']);
    }

    /**
     * Download payment receipt as PDF.
     *
     * @param int $id Payment ID
     * @return \Illuminate\Http\Response
     */
    public function downloadReceipt(int $id)
    {
        $payment = $this->paymentRepo->findById($id);

        if (!$payment) {
            abort(404, 'Payment not found');
        }

        // Only approved payments can have receipts
        if (!$payment->isApproved()) {
            return back()->with('danger', ___('alert.Only approved payments can have receipts'));
        }

        $data['payment'] = $payment;
        $data['title'] = 'Payment Receipt - ' . $payment->invoice_number;

        // Generate PDF
        $pdf = Pdf::loadView('mainapp::subscription-payment.receipt', compact('data'));

        return $pdf->download('receipt-' . $payment->invoice_number . '.pdf');
    }

    /**
     * Delete payment record (only pending payments).
     *
     * @param int $id Payment ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(int $id)
    {
        $result = $this->paymentRepo->destroy($id);

        if ($result['status']) {
            return response()->json([
                $result['message'],
                'success',
                ___('alert.deleted'),
                ___('alert.OK')
            ]);
        }

        return response()->json([
            $result['message'],
            'error',
            ___('alert.oops')
        ]);
    }

    /**
     * Get payment report/statistics.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function report(Request $request): View
    {
        $filters = $request->only(['start_date', 'end_date', 'school_id']);

        $data['report'] = $this->paymentRepo->getPaymentReport($filters);
        $data['title'] = ___('settings.Payment Report');
        $data['schools'] = $this->schoolRepo->getAll();

        return view('mainapp::subscription-payment.report', compact('data'));
    }
}
