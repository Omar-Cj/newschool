<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NotAuthenticateRoutes
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
        if (auth()->check()) {
            $user = auth()->user();

            // Role-based dashboard routing for authenticated users
            // System Admin (role_id = 0) routes to MainApp dashboard
            if ($user->role_id == 0) {
                return redirect()->route('mainapp.dashboard');
            }
            // Student role (role_id = 6)
            elseif ($user->role_id == 6) {
                return redirect()->route('student-panel-dashboard.index');
            }
            // Parent role (role_id = 7)
            elseif ($user->role_id == 7) {
                return redirect()->route('parent-panel-dashboard.index');
            }
            // All other roles (school admins, teachers, staff) - route to school dashboard
            else {
                return redirect()->route('school_dashboard');
            }
        }

        return $next($request);
    }
}
