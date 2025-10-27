<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
    /**
     * Set the selected branch in session
     * Used by the branch dropdown in the header to filter data
     */
    public function setSelectedBranch(Request $request): JsonResponse
    {
        $branchId = $request->input('branch_id');

        // Store in session
        session(['selected_branch_id' => $branchId]);

        \Log::info('🏢 [BRANCH-CONTROLLER] Branch selection updated', [
            'user_id' => auth()->id(),
            'branch_id' => $branchId,
            'session_id' => session()->getId()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Branch selection updated successfully',
            'branch_id' => $branchId
        ]);
    }
}
