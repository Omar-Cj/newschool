<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PermissionCheck
{
    /**
     * Feature name mapping for singular to plural conversion
     * Maps auto-derived feature names to actual package feature names
     */
    private $featureNameMap = [
        'user' => 'users',
        'role' => 'roles',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission, $featureName = null)
    {
        // Special handling for exam_entry_publish permission - only roles 1 and 2
        if ($permission === 'exam_entry_publish') {
            Log::info('🛡️ MIDDLEWARE: Checking exam_entry_publish permission', [
                'user_id' => Auth::user()->id ?? 'guest',
                'role_id' => Auth::user()->role_id ?? 'none',
                'school_id' => Auth::user()->school_id ?? 'none',
                'route' => $request->path(),
                'method' => $request->method()
            ]);

            // System Admin (school_id === null) has full access
            if (Auth::check() && Auth::user()->school_id === null) {
                Log::info('🎯 MIDDLEWARE: System Admin bypass - GRANTED', [
                    'user_id' => Auth::user()->id,
                    'school_id' => null
                ]);
                return $next($request);
            }

            // School-level admins (roles 1 and 2) need permission check
            if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {
                Log::info('✅ MIDDLEWARE: Role validation passed', [
                    'role_id' => Auth::user()->role_id
                ]);

                // Check if permission exists in user's permissions array
                if (in_array($permission, Auth::user()->permissions ?? [])) {
                    Log::info('✅ MIDDLEWARE: Admin permission granted', [
                        'role_id' => Auth::user()->role_id,
                        'permission' => $permission
                    ]);
                    return $next($request);
                }

                Log::warning('❌ MIDDLEWARE: Permission not in permissions array', [
                    'role_id' => Auth::user()->role_id,
                    'permission' => $permission,
                    'user_permissions' => Auth::user()->permissions
                ]);
            }

            Log::warning('❌ MIDDLEWARE: Access denied - aborting with 403', [
                'role_id' => Auth::user()->role_id ?? 'none',
                'required_roles' => [1, 2]
            ]);

            return abort(403, 'Only Super Admin and Admin can publish exam entries');
        }

        // Special handling for exam_entry_delete permission - only roles 1 and 2
        if ($permission === 'exam_entry_delete') {
            Log::info('🛡️ MIDDLEWARE: Checking exam_entry_delete permission', [
                'user_id' => Auth::user()->id ?? 'guest',
                'role_id' => Auth::user()->role_id ?? 'none',
                'route' => $request->path(),
                'method' => $request->method()
            ]);

            // System Admin (school_id === null) has full access
            if (Auth::check() && Auth::user()->school_id === null) {
                Log::info('🎯 MIDDLEWARE: System Admin bypass - GRANTED', [
                    'user_id' => Auth::user()->id,
                    'school_id' => null
                ]);
                return $next($request);
            }

            if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {
                Log::info('✅ MIDDLEWARE: Role validation passed', [
                    'role_id' => Auth::user()->role_id
                ]);

                // School admins (roles 1 and 2) need permission in array
                if (in_array($permission, Auth::user()->permissions ?? [])) {
                    Log::info('✅ MIDDLEWARE: Admin permission granted', [
                        'role_id' => Auth::user()->role_id,
                        'permission' => $permission
                    ]);
                    return $next($request);
                }

                Log::warning('❌ MIDDLEWARE: Permission not in permissions array', [
                    'role_id' => Auth::user()->role_id,
                    'permission' => $permission,
                    'user_permissions' => Auth::user()->permissions
                ]);
            }

            Log::warning('❌ MIDDLEWARE: Access denied - aborting with 403', [
                'role_id' => Auth::user()->role_id ?? 'none',
                'required_roles' => [1, 2]
            ]);

            return abort(403, 'Only Super Admin and Admin can delete exam entries');
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            return abort(403, 'Unauthorized');
        }

        $user = Auth::user();

        // Super Admin (role_id = 1) bypass - full access to all features
        if ($user->role_id == 1) {
            return $next($request);
        }

        // System admin (school_id === null): Check permission only
        if ($user->school_id === null) {
            if (in_array($permission, $user->permissions ?? [])) {
                return $next($request);
            }
            return abort(403, 'Access Denied - Insufficient permissions');
        }

        // School admin (has school_id): Require BOTH permission AND feature (AND logic)
        $hasPermission = in_array($permission, $user->permissions ?? []);

        // Determine feature name
        if ($featureName === null) {
            // Auto-detect feature name from permission
            // Remove common suffixes: _read, _create, _update, _delete
            $featureName = preg_replace('/_read|_create|_update|_delete$/', '', $permission);

            // Apply feature name mapping (singular to plural conversion)
            $featureName = $this->featureNameMap[$featureName] ?? $featureName;
        }

        // DEBUG LOGGING - COMPREHENSIVE
        Log::info('🔍 PERMISSION_CHECK_DEBUG', [
            'permission' => $permission,
            'featureName' => $featureName,
            'hasPermission' => $hasPermission,
            'user_id' => $user->id,
            'role_id' => $user->role_id,
            'school_id' => $user->school_id,
            'user_permissions_count' => count($user->permissions ?? []),
            'user_permissions_sample' => array_slice($user->permissions ?? [], 0, 10),
            'has_school_relation' => $user->school !== null,
            'school_package_id' => $user->school?->package_id,
            'route' => $request->path(),
        ]);

        // Check if feature exists in package
        $hasFeature = hasFeature($featureName);

        Log::info('🔍 FEATURE_CHECK_RESULT', [
            'featureName' => $featureName,
            'hasFeature' => $hasFeature,
            'hasPermission' => $hasPermission,
            'will_pass' => ($hasFeature && $hasPermission),
        ]);

        // Both required for school admins (AND logic)
        if ($hasFeature && $hasPermission) {
            return $next($request);
        }

        // Deny access with specific message
        if (!$hasFeature) {
            Log::warning('❌ DENIED: Feature not in package', ['featureName' => $featureName]);
            return abort(403, 'Access Denied - Feature not included in your package');
        }

        if (!$hasPermission) {
            Log::warning('❌ DENIED: Permission not in role', ['permission' => $permission]);
            return abort(403, 'Access Denied - Insufficient permissions for your role');
        }

        return abort(403, 'Access Denied');
    }

}
