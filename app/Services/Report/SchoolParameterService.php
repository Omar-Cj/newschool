<?php

declare(strict_types=1);

namespace App\Services\Report;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

/**
 * SchoolParameterService
 *
 * Handles global school parameter injection for the reporting system.
 * Automatically adds school filtering to all reports based on user's assigned school.
 * This parameter is HIDDEN from the UI but automatically injected into stored procedures.
 */
class SchoolParameterService
{
    /**
     * School parameter name used in stored procedures
     */
    const SCHOOL_PARAM_NAME = 'p_school_id';

    /**
     * Value representing "All Schools" selection (System Admin only)
     */
    const ALL_SCHOOLS_VALUE = null;

    /**
     * Roles that can access "All Schools" option
     * Note: Only System Admin (school_id = NULL) can view all schools
     */
    const ALL_SCHOOLS_ROLES = [
        'System Admin',  // Only System Admin has access to view all schools
    ];

    /**
     * Get school parameter definition for backend processing
     * This parameter is HIDDEN from the UI
     *
     * @return array Parameter configuration array
     */
    public function getSchoolParameterDefinition(): array
    {
        $user = Auth::user();

        return [
            'name' => self::SCHOOL_PARAM_NAME,
            'label' => 'School',
            'type' => 'hidden',
            'is_required' => true,
            'default_value' => $user->school_id ?? null,
            'is_system_parameter' => true,
            'is_hidden' => true, // This parameter is hidden from the UI
            'display_order' => -2, // Before branch parameter (which is -1)
            'placeholder' => '',
            'values' => json_encode([]) // No options needed for hidden field
        ];
    }

    /**
     * Get school ID for report execution with security validation
     * Always returns the authenticated user's school_id for security
     *
     * @param array $parameters Report parameters from user input
     * @return int|null School ID or NULL for System Admin (all schools)
     */
    public function getSchoolIdForExecution(array $parameters): ?int
    {
        $user = Auth::user();

        if (!$user) {
            Log::error('No authenticated user found for school parameter resolution');
            return null;
        }

        // System Admin with school_id = NULL can view all schools
        if ($user->school_id === null) {
            Log::info('System Admin accessing all schools', [
                'user_id' => $user->id,
                'role' => $user->role->name ?? 'Unknown'
            ]);
            return self::ALL_SCHOOLS_VALUE;
        }

        // All other users (including Super Admin, School Admin) are restricted to their school
        Log::info('User restricted to assigned school', [
            'user_id' => $user->id,
            'school_id' => $user->school_id,
            'role' => $user->role->name ?? 'Unknown'
        ]);

        return $user->school_id;
    }

    /**
     * Check if user has permission to view all schools
     * Only System Admin (school_id = NULL) has this permission
     *
     * @param User|null $user
     * @return bool
     */
    public function canViewAllSchools(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // System Admin has school_id = NULL
        $isSystemAdmin = $user->school_id === null;

        // Optional: Also check role name for double validation
        if ($user->role) {
            $hasRole = in_array($user->role->name, self::ALL_SCHOOLS_ROLES, true);
            $hasPermission = $isSystemAdmin && $hasRole;
        } else {
            $hasPermission = $isSystemAdmin;
        }

        Log::info('School permission check', [
            'user_id' => $user->id,
            'school_id' => $user->school_id,
            'role' => $user->role->name ?? 'No Role',
            'can_view_all_schools' => $hasPermission
        ]);

        return $hasPermission;
    }

    /**
     * Get user-friendly school name for display
     *
     * @param int|null $schoolId
     * @return string
     */
    public function getSchoolDisplayName(?int $schoolId): string
    {
        if ($schoolId === self::ALL_SCHOOLS_VALUE) {
            return 'All Schools';
        }

        // Fetch school name from database
        $school = \App\Models\School::find($schoolId);
        return $school ? $school->name : "School #{$schoolId}";
    }

    /**
     * Validate school parameter for report execution
     *
     * @param int|null $schoolId
     * @param User|null $user
     * @throws \Exception If school parameter is invalid
     * @return void
     */
    public function validateSchoolParameter(?int $schoolId, ?User $user = null): void
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            throw new \Exception('User authentication required for school validation');
        }

        // NULL is valid only for System Admin
        if ($schoolId === self::ALL_SCHOOLS_VALUE) {
            if (!$this->canViewAllSchools($user)) {
                throw new \Exception('You do not have permission to view all schools');
            }
            return;
        }

        // Validate that user is accessing their assigned school
        if ($user->school_id !== null && $user->school_id !== $schoolId) {
            throw new \Exception(
                "You do not have permission to access school #{$schoolId}. " .
                "You can only access school #{$user->school_id}"
            );
        }
    }
}
