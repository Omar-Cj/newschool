<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        // Special handling for exam_entry_publish permission - only roles 1 and 2
        if ($permission === 'exam_entry_publish') {
            Log::info('🛡️ MIDDLEWARE: Checking exam_entry_publish permission', [
                'user_id' => Auth::user()->id ?? 'guest',
                'role_id' => Auth::user()->role_id ?? 'none',
                'route' => $request->path(),
                'method' => $request->method()
            ]);

            if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {
                Log::info('✅ MIDDLEWARE: Role validation passed', [
                    'role_id' => Auth::user()->role_id
                ]);

                // Super Admin (role 1) always has permission - bypass permission array check
                if (Auth::user()->role_id == 1) {
                    Log::info('🎯 MIDDLEWARE: Super Admin bypass - GRANTED', [
                        'role_id' => 1
                    ]);
                    return $next($request);
                }

                // Admin (role 2) needs permission in array
                if (in_array($permission, Auth::user()->permissions)) {
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

            if (Auth::check() && in_array(Auth::user()->role_id, [1, 2])) {
                Log::info('✅ MIDDLEWARE: Role validation passed', [
                    'role_id' => Auth::user()->role_id
                ]);

                // Super Admin (role 1) always has permission - bypass permission array check
                if (Auth::user()->role_id == 1) {
                    Log::info('🎯 MIDDLEWARE: Super Admin bypass - GRANTED', [
                        'role_id' => 1
                    ]);
                    return $next($request);
                }

                // Admin (role 2) needs permission in array
                if (in_array($permission, Auth::user()->permissions)) {
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

        // Allow access if user is authenticated and is admin
        if (Auth::check() && Auth::user()->role_id == 1) {
            return $next($request);
        }

        // Allow access if user has the required permission
        if (Auth::check() && in_array($permission, Auth::user()->permissions)) {
            return $next($request);
        }

        // Deny access
        return abort(403, 'Access Denied');
    }

}
