<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use App\Enums\RoleEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class SchoolContext
{
    /**
     * Handle an incoming request.
     *
     * Establishes school context from authenticated user and shares
     * context data with views. Handles both school users and admin users.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only process authenticated requests
        if (Auth::check()) {
            $user = Auth::user();
            $schoolId = $this->determineSchoolId($user);
            $isAdmin = $this->isAdminUser($user);

            // Store school_id in session for later access
            session(['school_id' => $schoolId]);

            // Determine current school context
            $currentSchool = $this->getCurrentSchool($schoolId, $isAdmin);

            // Share context with all views
            View::share([
                'school_id' => $schoolId,
                'currentSchool' => $currentSchool,
                'isAdmin' => $isAdmin,
                'currentUser' => $user,
            ]);

            // Store in request for programmatic access
            $request->attributes->set('school_id', $schoolId);
            $request->attributes->set('current_school', $currentSchool);
            $request->attributes->set('is_admin', $isAdmin);
        }

        return $next($request);
    }

    /**
     * Determine the school ID for the authenticated user.
     *
     * For admin users: returns the explicitly set school context or default.
     * For school users: returns the school_id from user record.
     *
     * @param \App\Models\User $user
     * @return int|null
     */
    protected function determineSchoolId($user): ?int
    {
        // Admin users can have an explicit school context in session
        if ($this->isAdminUser($user)) {
            return session('admin_school_context') ?? $user->school_id ?? null;
        }

        // School users use their school_id as school identifier
        return $user->school_id ?? null;
    }

    /**
     * Check if user is an administrator.
     *
     * Determines if the user has admin-level permissions
     * based on their role_id. Supports single-database multi-tenant
     * architecture where System Admin (role_id=0) has global access.
     *
     * @param \App\Models\User $user
     * @return bool
     */
    protected function isAdminUser($user): bool
    {
        return $user->role_id == RoleEnum::MAIN_SYSTEM_ADMIN ||  // System Admin with school_id=NULL
               $user->role_id == RoleEnum::SUPERADMIN ||
               $user->role_id == RoleEnum::ADMIN;
    }

    /**
     * Get current school information.
     *
     * Retrieves the school details for the given ID.
     * Returns null if school cannot be found or ID is invalid.
     *
     * @param int|null $schoolId
     * @param bool $isAdmin
     * @return object|null
     */
    protected function getCurrentSchool(?int $schoolId, bool $isAdmin): ?object
    {
        if (!$schoolId) {
            return null;
        }

        try {
            // Query for school information
            $school = \DB::table('schools')
                ->where('id', $schoolId)
                ->first();

            // If no school found and user is admin, return generic context
            if (!$school && $isAdmin) {
                return (object)[
                    'id' => $schoolId,
                    'name' => 'System Administration',
                    'status' => 'active',
                    'is_system_admin' => true,
                ];
            }

            return $school;
        } catch (\Exception $e) {
            // Log error in production
            \Log::warning('Failed to retrieve school context', [
                'school_id' => $schoolId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get school context from request.
     *
     * Provides programmatic access to school context data
     * stored during middleware execution.
     *
     * @param Request $request
     * @return array
     */
    public static function getSchoolContext(Request $request): array
    {
        return [
            'school_id' => $request->attributes->get('school_id'),
            'current_school' => $request->attributes->get('current_school'),
            'is_admin' => $request->attributes->get('is_admin'),
        ];
    }

    /**
     * Check if authenticated user belongs to a specific school.
     *
     * Useful for authorization and data access control in controllers.
     *
     * @param Request $request
     * @param int $schoolId
     * @return bool
     */
    public static function userBelongsToSchool(Request $request, int $schoolId): bool
    {
        $context = self::getSchoolContext($request);

        // Admin users can access any school
        if ($context['is_admin']) {
            return true;
        }

        // Regular users must match school context
        return $context['school_id'] === $schoolId;
    }

    /**
     * Get session school ID for non-request contexts.
     *
     * Useful in jobs, event listeners, and other non-HTTP contexts
     * where request object is not available.
     *
     * @return int|null
     */
    public static function getSessionSchoolId(): ?int
    {
        return session('school_id');
    }

    /**
     * Set temporary school context for admin users.
     *
     * Allows admins to switch context temporarily during a request.
     * Changes are only stored in session and not persisted.
     *
     * @param int $schoolId
     * @return void
     */
    public static function setAdminSchoolContext(int $schoolId): void
    {
        if (Auth::check() && (Auth::user()->role_id == RoleEnum::MAIN_SYSTEM_ADMIN || Auth::user()->role_id == RoleEnum::SUPERADMIN || Auth::user()->role_id == RoleEnum::ADMIN)) {
            session(['admin_school_context' => $schoolId]);
        }
    }

    /**
     * Clear temporary admin school context.
     *
     * Resets admin user back to their default school context.
     *
     * @return void
     */
    public static function clearAdminSchoolContext(): void
    {
        session()->forget('admin_school_context');
    }
}
