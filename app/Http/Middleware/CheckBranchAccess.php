<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\Report\BranchParameterService;

/**
 * CheckBranchAccess Middleware
 *
 * Validates that users can only access reports for branches they have permission to view.
 * Prevents unauthorized cross-branch data access in the reporting system.
 */
class CheckBranchAccess
{
    /**
     * Branch parameter service for permission checks
     *
     * @var BranchParameterService
     */
    private BranchParameterService $branchParameterService;

    /**
     * Constructor
     *
     * @param BranchParameterService $branchParameterService
     */
    public function __construct(BranchParameterService $branchParameterService)
    {
        $this->branchParameterService = $branchParameterService;
    }

    /**
     * Handle an incoming request
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Allow if user is not authenticated (will be handled by auth middleware)
        if (!$user) {
            return $next($request);
        }

        // Get requested branch from parameters
        $requestedBranch = $this->getRequestedBranch($request);

        // Log the access attempt
        Log::info('Branch access check', [
            'user_id' => $user->id,
            'user_role' => $user->role?->name,
            'user_branch' => $user->branch_id,
            'requested_branch' => $requestedBranch,
            'route' => $request->route()->getName(),
            'ip' => $request->ip()
        ]);

        // Allow if no specific branch requested (will default to user's branch)
        if ($requestedBranch === null) {
            return $next($request);
        }

        // Check if requesting "All Branches"
        if ($this->isAllBranchesRequest($requestedBranch)) {
            if ($this->branchParameterService->canViewAllBranches($user)) {
                Log::info('All branches access granted', [
                    'user_id' => $user->id,
                    'role' => $user->role->name
                ]);
                return $next($request);
            }

            Log::warning('Unauthorized all branches access attempt', [
                'user_id' => $user->id,
                'role' => $user->role?->name,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view all branches. Access restricted to your assigned branch only.',
                'error_code' => 'INSUFFICIENT_BRANCH_PERMISSIONS'
            ], 403);
        }

        // Check specific branch access
        $branchId = (int)$requestedBranch;

        // Allow if requesting own branch
        if ($branchId === $user->branch_id) {
            return $next($request);
        }

        // Allow if user has "All Branches" permission (can access any specific branch)
        if ($this->branchParameterService->canViewAllBranches($user)) {
            Log::info('Cross-branch access granted to privileged user', [
                'user_id' => $user->id,
                'role' => $user->role->name,
                'requested_branch' => $branchId
            ]);
            return $next($request);
        }

        // Deny access - user trying to access unauthorized branch
        Log::warning('Unauthorized branch access attempt blocked', [
            'user_id' => $user->id,
            'user_role' => $user->role?->name,
            'user_branch' => $user->branch_id,
            'requested_branch' => $branchId,
            'ip' => $request->ip(),
            'route' => $request->route()->getName()
        ]);

        return response()->json([
            'success' => false,
            'message' => sprintf(
                'You do not have permission to access branch #%d. You can only access branch #%d.',
                $branchId,
                $user->branch_id
            ),
            'error_code' => 'UNAUTHORIZED_BRANCH_ACCESS',
            'allowed_branch' => $user->branch_id,
            'requested_branch' => $branchId
        ], 403);
    }

    /**
     * Extract requested branch from request parameters
     *
     * @param Request $request
     * @return mixed
     */
    private function getRequestedBranch(Request $request)
    {
        // Check in different possible parameter locations
        return $request->input('parameters.' . BranchParameterService::BRANCH_PARAM_NAME)
            ?? $request->input(BranchParameterService::BRANCH_PARAM_NAME)
            ?? $request->query(BranchParameterService::BRANCH_PARAM_NAME);
    }

    /**
     * Check if request is for "All Branches"
     *
     * @param mixed $branchValue
     * @return bool
     */
    private function isAllBranchesRequest($branchValue): bool
    {
        return $branchValue === 'null'
            || $branchValue === ''
            || $branchValue === 0
            || $branchValue === '0';
    }
}
