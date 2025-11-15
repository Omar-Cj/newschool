<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FeatureAccessService;
use App\Exceptions\FeatureAccessDeniedException;

/**
 * Feature Access Middleware
 *
 * Protects routes based on school package features
 * - Integrates with existing PermissionCheck middleware
 * - Zero-trust security model (deny by default)
 * - Super admin bypass (role_id = 1)
 * - Multi-tenant school isolation
 */
class FeatureAccessMiddleware
{
    /**
     * Feature access service
     */
    private FeatureAccessService $featureAccessService;

    /**
     * Create a new middleware instance
     *
     * @param FeatureAccessService $featureAccessService
     */
    public function __construct(FeatureAccessService $featureAccessService)
    {
        $this->featureAccessService = $featureAccessService;
    }

    /**
     * Handle an incoming request
     *
     * @param Request $request
     * @param Closure $next
     * @param string $featureAttribute Feature attribute to check
     * @return mixed
     * @throws FeatureAccessDeniedException
     */
    public function handle(Request $request, Closure $next, string $featureAttribute)
    {
        // Require authentication
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this feature.');
        }

        $user = Auth::user();

        // Non-SaaS mode - all features enabled
        if (!env('APP_SAAS', false)) {
            return $next($request);
        }

        // Check feature access
        $hasAccess = $this->featureAccessService->checkAccess($user, $featureAttribute);

        if (!$hasAccess) {
            // Get current package for error message
            $stats = $this->featureAccessService->getAccessStats($user->school_id);
            $currentPackage = $stats['package_name'] ?? null;

            throw new FeatureAccessDeniedException(
                $featureAttribute,
                $currentPackage,
                $user->school_id,
                config('features.access_denied_message', 'This feature is not available in your current package.')
            );
        }

        return $next($request);
    }
}
