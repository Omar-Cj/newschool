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
     * SECURITY: School users (role_id >= 1) ALWAYS use their user->school_id.
     * Only System Admins (role_id = 0) can use session-based school context switching.
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
            $isAdmin = $this->isAdminUser($user);

            // CRITICAL FIX: Clear any stale session data first
            $this->cleanupStaleSessionData($user, $isAdmin);

            $schoolId = $this->determineSchoolId($user);

            // SECURITY FIX: Only store in session for admin users who can switch context
            // School users should NEVER have their school_id stored in session to prevent contamination
            if ($isAdmin && $schoolId !== null) {
                session(['school_id' => $schoolId]);
            } elseif (!$isAdmin) {
                // For school users, remove any session school_id to prevent contamination
                session()->forget('school_id');
            }

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
        } else {
            // User not authenticated - clear all session context
            session()->forget(['school_id', 'admin_school_context']);
        }

        return $next($request);
    }

    /**
     * Determine the school ID for the authenticated user.
     *
     * SECURITY CRITICAL: Precedence logic ensures data isolation:
     * - System Admin (role_id=0, school_id=NULL): Can switch context via session
     * - School Users (role_id>=1): MUST use their user->school_id, session is IGNORED
     *
     * @param \App\Models\User $user
     * @return int|null Returns school_id or NULL for System Admin viewing all schools
     */
    protected function determineSchoolId($user): ?int
    {
        // SECURITY FIX: System Admin (role_id=0) with NULL school_id can use session context
        if ($user->role_id === RoleEnum::MAIN_SYSTEM_ADMIN && $user->school_id === null) {
            // System Admin can switch context or see all schools (NULL)
            return session('admin_school_context') ?? null;
        }

        // CRITICAL: School users (including school-level admins) MUST use their assigned school_id
        // Session is NEVER used for school users to prevent data leakage
        if ($user->school_id !== null) {
            // Log security violation if session attempted to override school user's school_id
            if (session()->has('admin_school_context') && session('admin_school_context') != $user->school_id) {
                \Log::warning('Session school context mismatch detected for school user', [
                    'user_id' => $user->id,
                    'user_school_id' => $user->school_id,
                    'session_school_id' => session('admin_school_context'),
                    'role_id' => $user->role_id,
                ]);
                // Clear the invalid session
                session()->forget('admin_school_context');
            }

            return $user->school_id;
        }

        // School-level admins with school_id can optionally switch context
        if ($this->isAdminUser($user) && $user->school_id !== null) {
            // School admins can only view their own school
            return $user->school_id;
        }

        // Fallback: No school context
        return null;
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

    /**
     * Clean up stale session data to prevent context contamination.
     *
     * CRITICAL: Ensures session data doesn't persist incorrectly between user switches
     * or when non-admin users have session data they shouldn't have.
     *
     * @param \App\Models\User $user Current authenticated user
     * @param bool $isAdmin Whether user is admin
     * @return void
     */
    protected function cleanupStaleSessionData($user, bool $isAdmin): void
    {
        // For school users (non-System Admin), always clear admin context session
        if (!($user->role_id === RoleEnum::MAIN_SYSTEM_ADMIN && $user->school_id === null)) {
            // School users should never have admin_school_context
            if (session()->has('admin_school_context')) {
                \Log::warning('Clearing admin_school_context for non-System Admin user', [
                    'user_id' => $user->id,
                    'role_id' => $user->role_id,
                    'user_school_id' => $user->school_id,
                ]);
                session()->forget('admin_school_context');
            }
        }

        // Validate school_id in session matches user's school_id for school users
        if (!$isAdmin && $user->school_id !== null) {
            $sessionSchoolId = session('school_id');
            if ($sessionSchoolId !== null && $sessionSchoolId !== $user->school_id) {
                \Log::warning('Session school_id mismatch for school user - clearing session', [
                    'user_id' => $user->id,
                    'user_school_id' => $user->school_id,
                    'session_school_id' => $sessionSchoolId,
                ]);
                session()->forget('school_id');
            }
        }

        // For System Admin with NULL school_id, validate admin_school_context is a valid school
        if ($user->role_id === RoleEnum::MAIN_SYSTEM_ADMIN && $user->school_id === null) {
            $adminContext = session('admin_school_context');
            if ($adminContext !== null) {
                // Optionally validate that the school exists
                $schoolExists = \DB::table('schools')->where('id', $adminContext)->exists();
                if (!$schoolExists) {
                    \Log::warning('Invalid admin_school_context detected - clearing', [
                        'user_id' => $user->id,
                        'invalid_school_id' => $adminContext,
                    ]);
                    session()->forget('admin_school_context');
                }
            }
        }
    }
}
