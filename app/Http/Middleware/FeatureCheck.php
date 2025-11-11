<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeatureCheck
{
    /**
     * Map OLD feature keys to NEW permission attributes
     *
     * This mapping bridges the deprecated features system with the new
     * permission-based features system.
     */
    private $featureMap = [
        'staff_manage' => ['users', 'roles', 'department', 'designation'],
        // Add more mappings as needed:
        // 'library' => ['library_members', 'library_books'],
        // 'online_examination' => ['online_exam'],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $feature  OLD feature key to check
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $feature)
    {
        // Bypass feature checks for single-school installations
        if (!env('APP_SAAS')) {
            return $next($request);
        }

        // Require authentication
        if (!Auth::check()) {
            return abort(403, 'Authentication required');
        }

        // Translate OLD feature key to NEW permission attributes
        // If no mapping exists, use the feature key as-is (for future-proofing)
        $newFeatures = $this->featureMap[$feature] ?? [$feature];

        // Check if user has ANY of the mapped features in their package
        if (hasAnyFeature($newFeatures)) {
            return $next($request);
        }

        return abort(403, 'Access Denied - Feature not available in your package');
    }
}
