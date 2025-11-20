<?php

namespace App\Http\Middleware;

use Closure;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Check Subscription Middleware
 *
 * FIXED: No longer uses broken global cache activeSubscriptionExpiryDate() helper
 * Now delegates to SchoolContext middleware for subscription checking
 *
 * This middleware is kept for backward compatibility with existing route groups
 * but actual subscription checking is handled by SchoolContext middleware
 */
class CheckSubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip check if not in SaaS mode
        if (!env('APP_SAAS')) {
            return $next($request);
        }

        // Skip check if user not authenticated
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Skip check for true system admins (role_id=0 with school_id=NULL)
        $isTrueSystemAdmin = (
            $user->role_id === RoleEnum::MAIN_SYSTEM_ADMIN &&
            $user->school_id === null
        );

        if ($isTrueSystemAdmin) {
            return $next($request);
        }

        // For school users, verify they have a school context
        $schoolId = $user->school_id ?? $request->attributes->get('school_id');

        if (!$schoolId) {
            Log::warning('CheckSubscription: User has no school context', [
                'user_id' => $user->id,
                'role_id' => $user->role_id,
            ]);
            abort(403, 'No school context found');
        }

        // Get active subscription for the school
        $subscription = \DB::table('subscriptions')
            ->where('school_id', $schoolId)
            ->where('status', 1)
            ->orderBy('expiry_date', 'desc')
            ->first();

        // No subscription found
        if (!$subscription) {
            Auth::logout();
            return redirect()->route('login')
                ->with('subscription_expired', 'Your school does not have an active subscription. Please contact Telesom Sales.');
        }

        // Check expiry status
        $now = now();
        $expiryDate = \Carbon\Carbon::parse($subscription->expiry_date);
        $graceExpiryDate = $subscription->grace_expiry_date
            ? \Carbon\Carbon::parse($subscription->grace_expiry_date)
            : null;

        // Active subscription - allow access
        if ($now->lte($expiryDate)) {
            return $next($request);
        }

        // Within grace period - allow access (warning handled by SchoolContext)
        if ($graceExpiryDate && $now->lte($graceExpiryDate)) {
            return $next($request);
        }

        // Expired - block access
        Auth::logout();
        return redirect()->route('login')
            ->with('subscription_expired', 'Your subscription has expired. Please contact Telesom Sales to renew.');
    }
}
