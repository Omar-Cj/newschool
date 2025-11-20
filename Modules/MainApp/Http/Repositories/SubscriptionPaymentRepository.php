<?php

declare(strict_types=1);

namespace Modules\MainApp\Http\Repositories;

use Exception;
use Throwable;
use Carbon\Carbon;
use App\Enums\Settings;
use App\Enums\PricingDuration;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Modules\MainApp\Entities\Package;
use Modules\MainApp\Entities\Subscription;
use Modules\MainApp\Entities\SubscriptionPayment;

/**
 * SubscriptionPayment Repository
 *
 * Handles all subscription payment operations including approval workflow,
 * payment tracking, and subscription extension logic.
 */
class SubscriptionPaymentRepository
{
    use ReturnFormatTrait;

    private SubscriptionPayment $model;

    /**
     * SubscriptionPaymentRepository constructor.
     *
     * @param SubscriptionPayment $model
     */
    public function __construct(SubscriptionPayment $model)
    {
        $this->model = $model;
    }

    /**
     * Get all pending payments for admin review.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPendingPayments()
    {
        return $this->model
            ->pending()
            ->with([
                'school:id,name,email,phone',
                'subscription.package:id,name,price,duration,duration_number',
            ])
            ->latest('payment_date')
            ->latest('created_at')
            ->paginate(Settings::PAGINATE);
    }

    /**
     * Get all payments (all statuses) for admin.
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPayments(array $filters = [])
    {
        $query = $this->model
            ->with([
                'school:id,name,email,phone',
                'subscription.package:id,name,price',
                'approver:id,name',
            ])
            ->latest('payment_date')
            ->latest('created_at');

        // Apply filters
        if (isset($filters['status']) && $filters['status'] !== '') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['school_id'])) {
            $query->forSchool($filters['school_id']);
        }

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->dateRange($filters['start_date'], $filters['end_date']);
        }

        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        return $query->paginate(Settings::PAGINATE);
    }

    /**
     * Get payment history for a specific school.
     *
     * @param int $schoolId
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getSchoolPaymentHistory(int $schoolId)
    {
        return $this->model
            ->forSchool($schoolId)
            ->with([
                'subscription.package:id,name,price,duration,duration_number',
                'approver:id,name',
            ])
            ->latest('payment_date')
            ->latest('created_at')
            ->paginate(Settings::PAGINATE);
    }

    /**
     * Find payment by ID with relationships.
     *
     * @param int $id
     * @return SubscriptionPayment|null
     */
    public function findById(int $id): ?SubscriptionPayment
    {
        return $this->model
            ->with([
                'school:id,name,email,phone',
                'subscription.package:id,name,price,duration,duration_number',
                'approver:id,name',
            ])
            ->find($id);
    }

    /**
     * Create new payment record.
     *
     * @param array $data
     * @return array
     */
    public function createPaymentRecord(array $data): array
    {
        try {
            DB::beginTransaction();

            // Validate subscription exists and belongs to school
            $subscription = Subscription::with('package')->find($data['subscription_id']);
            if (!$subscription || $subscription->school_id != $data['school_id']) {
                throw new Exception('Invalid subscription or school mismatch');
            }

            // Calculate payment amount based on branch count
            // Use subscription's total_price if available, otherwise calculate from package price × branch count
            $amount = $this->calculatePaymentAmount($subscription, isset($data['amount']) ? (float) $data['amount'] : null);

            // Create payment record
            $payment = $this->model->create([
                'subscription_id' => $data['subscription_id'],
                'school_id' => $data['school_id'],
                'amount' => $amount,
                'payment_method' => $data['payment_method'],
                'transaction_id' => $data['transaction_id'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'payment_date' => $data['payment_date'],
                'status' => SubscriptionPayment::STATUS_PENDING,
            ]);

            // Log payment creation
            Log::channel('subscription-payments')->info('Payment record created', [
                'payment_id' => $payment->id,
                'school_id' => $payment->school_id,
                'amount' => $payment->amount,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            return $this->responseWithSuccess(___('alert.Payment record created successfully'), $payment);
        } catch (Throwable $e) {
            DB::rollBack();

            Log::channel('subscription-payments')->error('Failed to create payment record', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return $this->responseWithError(___('alert.Failed to create payment record'), null);
        }
    }

    /**
     * Approve payment and extend subscription.
     *
     * This method:
     * 1. Updates payment status to APPROVED
     * 2. Calculates new subscription dates based on package duration
     * 3. Updates subscription with new dates and active status
     * 4. Generates invoice number if not exists
     * 5. Logs approval for audit trail
     *
     * @param int $paymentId
     * @param int $userId Admin user approving the payment
     * @return array
     */
    public function approvePayment(int $paymentId, int $userId): array
    {
        try {
            DB::beginTransaction();

            // Find payment with subscription and package
            $payment = $this->model
                ->with(['subscription.package', 'school'])
                ->findOrFail($paymentId);

            // Validate payment is pending
            if (!$payment->isPending()) {
                throw new Exception('Payment is not in pending status');
            }

            $subscription = $payment->subscription;
            $package = $subscription->package;

            // Log initial state BEFORE any changes
            Log::channel('subscription-payments')->debug('=== PAYMENT APPROVAL DEBUG START ===');
            Log::channel('subscription-payments')->debug('Initial subscription state', [
                'payment_id' => $payment->id,
                'subscription_id' => $subscription->id,
                'school_id' => $payment->school_id,
                'current_expiry_date' => $subscription->expiry_date,
                'current_grace_expiry_date' => $subscription->grace_expiry_date,
                'current_status' => $subscription->status,
                'current_payment_status' => $subscription->payment_status,
            ]);

            if (!$package) {
                throw new Exception('Package not found for subscription');
            }

            // Log package information
            Log::channel('subscription-payments')->debug('Package information', [
                'payment_id' => $payment->id,
                'package_id' => $package->id,
                'package_name' => $package->name ?? 'N/A',
                'package_duration' => $package->duration,
                'package_duration_value' => $package->duration instanceof \BackedEnum ? $package->duration->value : $package->duration,
                'package_duration_number' => $package->duration_number,
                'package_price' => $package->price,
            ]);

            // Store original values for comparison
            $originalExpiryDate = $subscription->expiry_date;
            $originalGraceExpiryDate = $subscription->grace_expiry_date;
            $originalStatus = $subscription->status;
            $originalPaymentStatus = $subscription->payment_status;

            // Calculate new subscription dates
            $newDates = $this->calculateSubscriptionDates($subscription, $package);

            // Log calculated dates
            Log::channel('subscription-payments')->debug('Calculated new dates', [
                'payment_id' => $payment->id,
                'new_expiry_date' => $newDates['expiry_date'],
                'new_grace_expiry_date' => $newDates['grace_expiry_date'],
            ]);

            // Update payment status
            $payment->update([
                'status' => SubscriptionPayment::STATUS_APPROVED,
                'approved_by' => $userId,
                'approved_at' => now(),
                'invoice_number' => $payment->invoice_number ?? $this->generateInvoiceNumber(),
            ]);

            // Update subscription with new dates and active status
            $updateResult = $subscription->update([
                'expiry_date' => $newDates['expiry_date'],
                'grace_expiry_date' => $newDates['grace_expiry_date'],
                'status' => 1, // Active
                'payment_status' => 1, // Paid
            ]);

            // Reload subscription from database to verify save
            $subscription->refresh();

            // Log post-update verification
            Log::channel('subscription-payments')->debug('Subscription AFTER update - VERIFY', [
                'payment_id' => $payment->id,
                'update_result' => $updateResult,
                'old_expiry_date' => $originalExpiryDate,
                'new_expiry_date' => $subscription->expiry_date,
                'expiry_changed' => $originalExpiryDate !== $subscription->expiry_date,
                'old_grace_expiry_date' => $originalGraceExpiryDate,
                'new_grace_expiry_date' => $subscription->grace_expiry_date,
                'grace_changed' => $originalGraceExpiryDate !== $subscription->grace_expiry_date,
                'old_status' => $originalStatus,
                'new_status' => $subscription->status,
                'old_payment_status' => $originalPaymentStatus,
                'new_payment_status' => $subscription->payment_status,
                'expected_expiry' => $newDates['expiry_date'],
                'expected_grace' => $newDates['grace_expiry_date'],
                'expiry_matches_expected' => $subscription->expiry_date === $newDates['expiry_date'],
                'grace_matches_expected' => $subscription->grace_expiry_date === $newDates['grace_expiry_date'],
            ]);
            Log::channel('subscription-payments')->debug('=== PAYMENT APPROVAL DEBUG END ===');

            // Clear school feature cache to reflect updated subscription
            if (method_exists($payment->school, 'clearFeatureCache')) {
                $payment->school->clearFeatureCache();
            }

            // Log approval for audit trail
            Log::channel('subscription-payments')->info('Payment approved', [
                'payment_id' => $payment->id,
                'subscription_id' => $subscription->id,
                'school_id' => $payment->school_id,
                'school_name' => $payment->school->name,
                'amount' => $payment->amount,
                'approved_by' => $userId,
                'approved_at' => $payment->approved_at,
                'old_expiry' => $subscription->getOriginal('expiry_date'),
                'new_expiry' => $newDates['expiry_date'],
                'invoice_number' => $payment->invoice_number,
            ]);

            DB::commit();

            return $this->responseWithSuccess(
                ___('alert.Payment approved successfully. Subscription extended until') . ' ' . $newDates['expiry_date'],
                $payment->fresh(['subscription', 'approver'])
            );
        } catch (Throwable $e) {
            DB::rollBack();

            Log::channel('subscription-payments')->error('Failed to approve payment', [
                'payment_id' => $paymentId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->responseWithError(
                ___('alert.Failed to approve payment') . ': ' . $e->getMessage(),
                null
            );
        }
    }

    /**
     * Reject payment with reason.
     *
     * @param int $paymentId
     * @param int $userId Admin user rejecting the payment
     * @param string $reason Rejection reason
     * @return array
     */
    public function rejectPayment(int $paymentId, int $userId, string $reason): array
    {
        try {
            DB::beginTransaction();

            // Find payment
            $payment = $this->model->findOrFail($paymentId);

            // Validate payment is pending
            if (!$payment->isPending()) {
                throw new Exception('Payment is not in pending status');
            }

            // Update payment status
            $payment->update([
                'status' => SubscriptionPayment::STATUS_REJECTED,
                'approved_by' => $userId,
                'approved_at' => now(),
                'rejection_reason' => $reason,
            ]);

            // Log rejection for audit trail
            Log::channel('subscription-payments')->warning('Payment rejected', [
                'payment_id' => $payment->id,
                'subscription_id' => $payment->subscription_id,
                'school_id' => $payment->school_id,
                'school_name' => $payment->school->name ?? 'Unknown',
                'amount' => $payment->amount,
                'rejected_by' => $userId,
                'rejected_at' => $payment->approved_at,
                'rejection_reason' => $reason,
            ]);

            DB::commit();

            return $this->responseWithSuccess(
                ___('alert.Payment rejected successfully'),
                $payment->fresh(['approver'])
            );
        } catch (Throwable $e) {
            DB::rollBack();

            Log::channel('subscription-payments')->error('Failed to reject payment', [
                'payment_id' => $paymentId,
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return $this->responseWithError(
                ___('alert.Failed to reject payment') . ': ' . $e->getMessage(),
                null
            );
        }
    }

    /**
     * Calculate subscription dates based on package duration.
     *
     * If subscription is active: extend from current expiry_date
     * If subscription is expired: extend from today
     *
     * @param Subscription $subscription
     * @param Package $package
     * @return array ['expiry_date' => string, 'grace_expiry_date' => string]
     */
    private function calculateSubscriptionDates(Subscription $subscription, Package $package): array
    {
        // Log input parameters
        Log::channel('subscription-payments')->debug('calculateSubscriptionDates - Input', [
            'subscription_id' => $subscription->id,
            'current_expiry_date' => $subscription->expiry_date,
            'package_duration_raw' => $package->duration,
            'package_duration_type' => gettype($package->duration),
            'package_duration_number_raw' => $package->duration_number,
            'package_duration_number_type' => gettype($package->duration_number),
        ]);

        // Determine start date for extension
        $currentExpiry = $subscription->expiry_date
            ? Carbon::parse($subscription->expiry_date)
            : null;

        $isActive = $currentExpiry && $currentExpiry->isFuture();
        $startDate = $isActive ? $currentExpiry : now();

        // Log extension logic
        Log::channel('subscription-payments')->debug('calculateSubscriptionDates - Extension logic', [
            'is_active' => $isActive,
            'current_expiry_parsed' => $currentExpiry ? $currentExpiry->format('Y-m-d H:i:s') : null,
            'start_date_for_extension' => $startDate->format('Y-m-d H:i:s'),
            'extending_from' => $isActive ? 'current_expiry' : 'now',
        ]);

        // Cast duration to integer for proper comparison with PricingDuration constants
        $duration = (int) $package->duration;
        $durationNumber = (int) $package->duration_number;

        // Calculate new expiry date based on package duration
        $expiryDate = match($duration) {
            PricingDuration::DAYS => $startDate->copy()->addDays($durationNumber),
            PricingDuration::MONTHLY => $startDate->copy()->addMonths($durationNumber),
            PricingDuration::YEARLY => $startDate->copy()->addYears($durationNumber),
            default => $startDate->copy()->addMonths(1), // Default to 1 month if unknown
        };

        // Log which match case was used
        $durationMatch = match($duration) {
            PricingDuration::DAYS => 'DAYS',
            PricingDuration::MONTHLY => 'MONTHLY',
            PricingDuration::YEARLY => 'YEARLY',
            default => 'DEFAULT (1 month)',
        };

        // Calculate grace period (2 days after expiry as per spec requirement)
        $gracePeriodDays = (int) config('app.subscription_grace_period_days', 2);
        $graceExpiryDate = $expiryDate->copy()->addDays($gracePeriodDays);

        // Log calculated dates
        Log::channel('subscription-payments')->debug('calculateSubscriptionDates - Calculated dates', [
            'duration_match_case' => $durationMatch,
            'duration_number_used' => $package->duration_number,
            'expiry_date' => $expiryDate->format('Y-m-d H:i:s'),
            'grace_period_days' => $gracePeriodDays,
            'grace_expiry_date' => $graceExpiryDate->format('Y-m-d H:i:s'),
        ]);

        return [
            'expiry_date' => $expiryDate->format('Y-m-d H:i:s'),
            'grace_expiry_date' => $graceExpiryDate->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Calculate payment amount based on subscription's branch count.
     *
     * Priority:
     * 1. Use subscription's total_price if already calculated
     * 2. Calculate: package_price × branch_count
     * 3. Fall back to provided amount or package price
     *
     * @param Subscription $subscription
     * @param float|null $providedAmount Optional amount from input
     * @return float
     */
    private function calculatePaymentAmount(Subscription $subscription, ?float $providedAmount = null): float
    {
        // If subscription has pre-calculated total_price, use it
        if ($subscription->total_price && $subscription->total_price > 0) {
            return (float) $subscription->total_price;
        }

        // Get package price
        $package = $subscription->package;
        if (!$package) {
            // Fall back to provided amount if no package found
            return $providedAmount ?? 0.0;
        }

        $packagePrice = (float) $package->price;
        $branchCount = (int) ($subscription->branch_count ?? 1);

        // Ensure at least 1 branch
        $branchCount = max(1, $branchCount);

        // Calculate total: package_price × branch_count
        $totalAmount = $packagePrice * $branchCount;

        return $totalAmount;
    }

    /**
     * Generate unique invoice number.
     *
     * Format: INV-YYYY-MM-XXXXX
     * Example: INV-2025-11-00001
     *
     * @return string
     */
    private function generateInvoiceNumber(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        // Get last invoice number for current month
        $lastPayment = $this->model
            ->whereNotNull('invoice_number')
            ->where('invoice_number', 'like', "INV-{$year}-{$month}-%")
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastPayment && preg_match('/INV-\d{4}-\d{2}-(\d+)/', $lastPayment->invoice_number, $matches)) {
            $lastNumber = (int) $matches[1];
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('INV-%s-%s-%05d', $year, $month, $newNumber);
    }

    /**
     * Get payment statistics/report.
     *
     * @param array $filters
     * @return array
     */
    public function getPaymentReport(array $filters = []): array
    {
        $query = $this->model->query();

        // Apply filters
        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->dateRange($filters['start_date'], $filters['end_date']);
        }

        if (isset($filters['school_id'])) {
            $query->forSchool($filters['school_id']);
        }

        // Calculate statistics
        $totalPayments = $query->count();
        $pendingPayments = (clone $query)->pending()->count();
        $approvedPayments = (clone $query)->approved()->count();
        $rejectedPayments = (clone $query)->rejected()->count();
        $totalAmount = (clone $query)->approved()->sum('amount');

        // Get payments by method
        $paymentsByMethod = (clone $query)
            ->approved()
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        return [
            'total_payments' => $totalPayments,
            'pending_payments' => $pendingPayments,
            'approved_payments' => $approvedPayments,
            'rejected_payments' => $rejectedPayments,
            'total_amount' => number_format((float) $totalAmount, 2),
            'payments_by_method' => $paymentsByMethod,
        ];
    }

    /**
     * Delete payment record (only if pending).
     *
     * @param int $id
     * @return array
     */
    public function destroy(int $id): array
    {
        try {
            $payment = $this->model->findOrFail($id);

            // Only allow deletion of pending payments
            if (!$payment->isPending()) {
                throw new Exception('Only pending payments can be deleted');
            }

            $payment->delete();

            Log::channel('subscription-payments')->info('Payment deleted', [
                'payment_id' => $id,
                'deleted_by' => Auth::id(),
            ]);

            return $this->responseWithSuccess(___('alert.Payment deleted successfully'), null);
        } catch (Throwable $e) {
            Log::channel('subscription-payments')->error('Failed to delete payment', [
                'payment_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->responseWithError(___('alert.Failed to delete payment'), null);
        }
    }
}
