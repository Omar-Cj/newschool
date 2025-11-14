<?php

declare(strict_types=1);

namespace App\Services\Report;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Enums\Status;

/**
 * BranchParameterService
 *
 * Handles global branch parameter injection and permission management for the reporting system.
 * Automatically adds branch filtering to all reports based on user's assigned branch and role.
 */
class BranchParameterService
{
    /**
     * Branch parameter name used in stored procedures
     */
    const BRANCH_PARAM_NAME = 'p_branch_id';

    /**
     * Value representing "All Branches" selection
     */
    const ALL_BRANCHES_VALUE = null;

    /**
     * Roles that can access "All Branches" option
     * Note: Only Super Admin can view all branches. Regular admins see only their assigned branch.
     */
    const ALL_BRANCHES_ROLES = [
        'Super Admin',  // Only Super Admin has access to view all branches
    ];

    /**
     * Get branch parameter definition for UI rendering
     *
     * @return array Parameter configuration array
     */
    public function getBranchParameterDefinition(): array
    {
        $user = Auth::user();
        $canViewAllBranches = $this->canViewAllBranches($user);

        return [
            'name' => self::BRANCH_PARAM_NAME,
            'label' => 'Branch',
            'type' => 'select',
            'is_required' => true,
            'default_value' => $user->branch_id ?? 1,
            'is_system_parameter' => true,
            'display_order' => -1, // Show first in parameter list
            'placeholder' => 'Select Branch',
            'values' => json_encode([
                'query' => $this->getBranchOptionsQuery($canViewAllBranches, $user->branch_id ?? 1, $user->school_id)
            ])
        ];
    }

    /**
     * Get SQL query for branch dropdown options
     *
     * @param bool $includeAllOption Whether to include "All Branches" option
     * @param int $userBranchId User's assigned branch ID
     * @param int|null $userSchoolId User's assigned school ID (NULL for System Admin)
     * @return string SQL query
     */
    private function getBranchOptionsQuery(bool $includeAllOption, int $userBranchId, ?int $userSchoolId): string
    {
        if ($includeAllOption) {
            // Users with "All Branches" permission
            if ($userSchoolId === null) {
                // System Admin (school_id = NULL): Show all branches from ALL schools
                return "SELECT * FROM (
                            SELECT NULL as value, '-- All Branches --' as label
                            UNION ALL
                            SELECT id as value, name as label
                            FROM branches
                            WHERE status = " . Status::ACTIVE . "
                        ) AS branch_options
                        ORDER BY label ASC";
            } else {
                // Super Admin (school_id = X): Show all branches from THEIR school only
                return "SELECT * FROM (
                            SELECT NULL as value, '-- All Branches --' as label
                            UNION ALL
                            SELECT id as value, name as label
                            FROM branches
                            WHERE status = " . Status::ACTIVE . "
                              AND school_id = " . $userSchoolId . "
                        ) AS branch_options
                        ORDER BY label ASC";
            }
        }

        // Regular users: Only show their assigned branch
        return "SELECT id as value, name as label
                FROM branches
                WHERE status = " . Status::ACTIVE . "
                  AND id = " . $userBranchId . "
                ORDER BY label ASC";
    }

    /**
     * Check if user has permission to view all branches
     *
     * @param User|null $user
     * @return bool
     */
    public function canViewAllBranches(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        // Check if user has a role
        if (!$user->role) {
            Log::warning('User has no role assigned', [
                'user_id' => $user->id
            ]);
            return false;
        }

        // Check if user's role is in the allowed list
        $hasPermission = in_array($user->role->name, self::ALL_BRANCHES_ROLES, true);

        Log::info('Branch permission check', [
            'user_id' => $user->id,
            'role' => $user->role->name,
            'can_view_all_branches' => $hasPermission
        ]);

        return $hasPermission;
    }

    /**
     * Get branch ID for report execution with permission validation
     *
     * @param array $parameters Report parameters from user input
     * @return int|null Branch ID or NULL for all branches
     */
    public function getBranchIdForExecution(array $parameters): ?int
    {
        $user = Auth::user();

        if (!$user) {
            Log::error('No authenticated user found for branch parameter resolution');
            return 1; // Fallback to default branch
        }

        // Check if branch parameter was provided
        if (isset($parameters[self::BRANCH_PARAM_NAME])) {
            $selectedBranch = $parameters[self::BRANCH_PARAM_NAME];

            Log::info('Branch parameter provided by user', [
                'user_id' => $user->id,
                'selected_branch' => $selectedBranch,
                'parameter_type' => gettype($selectedBranch)
            ]);

            // Handle "All Branches" selection
            if ($this->isAllBranchesValue($selectedBranch)) {
                if ($this->canViewAllBranches($user)) {
                    Log::info('User authorized for all branches access', [
                        'user_id' => $user->id,
                        'role' => $user->role->name
                    ]);
                    return self::ALL_BRANCHES_VALUE;
                }

                // User requested all branches but doesn't have permission
                Log::warning('Unauthorized all branches access attempt', [
                    'user_id' => $user->id,
                    'role' => $user->role->name,
                    'fallback_to' => $user->branch_id
                ]);
                return $user->branch_id;
            }

            // Specific branch selected - validate access
            $branchId = (int)$selectedBranch;
            if ($this->canAccessBranch($user, $branchId)) {
                Log::info('User authorized for specific branch', [
                    'user_id' => $user->id,
                    'branch_id' => $branchId
                ]);
                return $branchId;
            }

            // User requested branch they don't have access to
            Log::warning('Unauthorized branch access attempt', [
                'user_id' => $user->id,
                'requested_branch' => $branchId,
                'user_branch' => $user->branch_id,
                'fallback_to' => $user->branch_id
            ]);
        }

        // Default to user's assigned branch
        Log::info('Using user assigned branch', [
            'user_id' => $user->id,
            'branch_id' => $user->branch_id
        ]);
        return $user->branch_id ?? 1;
    }

    /**
     * Check if value represents "All Branches" selection
     *
     * @param mixed $value
     * @return bool
     */
    private function isAllBranchesValue($value): bool
    {
        return $value === null
            || $value === 'null'
            || $value === ''
            || $value === 0
            || $value === '0';
    }

    /**
     * Validate if user can access specific branch
     *
     * @param User $user
     * @param int $branchId
     * @return bool
     */
    private function canAccessBranch(User $user, int $branchId): bool
    {
        // Users with "All Branches" permission can access any specific branch
        if ($this->canViewAllBranches($user)) {
            return true;
        }

        // Regular users can only access their assigned branch
        return $user->branch_id === $branchId;
    }

    /**
     * Get user-friendly branch name for display
     *
     * @param int|null $branchId
     * @return string
     */
    public function getBranchDisplayName(?int $branchId): string
    {
        if ($branchId === self::ALL_BRANCHES_VALUE) {
            return 'All Branches';
        }

        // Fetch branch name from database
        $branch = \Modules\MultiBranch\Entities\Branch::find($branchId);
        return $branch ? $branch->name : "Branch #{$branchId}";
    }

    /**
     * Validate branch parameter for report execution
     *
     * @param int|null $branchId
     * @param User|null $user
     * @throws \Exception If branch parameter is invalid
     * @return void
     */
    public function validateBranchParameter(?int $branchId, ?User $user = null): void
    {
        $user = $user ?? Auth::user();

        if (!$user) {
            throw new \Exception('User authentication required for branch validation');
        }

        // NULL is valid for users with "All Branches" permission
        if ($branchId === self::ALL_BRANCHES_VALUE) {
            if (!$this->canViewAllBranches($user)) {
                throw new \Exception('You do not have permission to view all branches');
            }
            return;
        }

        // Validate specific branch access
        if (!$this->canAccessBranch($user, $branchId)) {
            throw new \Exception(
                "You do not have permission to access branch #{$branchId}. " .
                "You can only access branch #{$user->branch_id}"
            );
        }
    }
}
