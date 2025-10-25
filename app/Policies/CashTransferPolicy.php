<?php

namespace App\Policies;

use App\Models\CashTransfer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CashTransferPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if the user can view any cash transfers
     */
    public function viewAny(User $user): bool
    {
        // Users with permission to view cash transfers
        return hasPermission('cash_transfer_read') || $user->role_id == 1;
    }

    /**
     * Determine if the user can view a cash transfer
     */
    public function view(User $user, CashTransfer $transfer): bool
    {
        // User can view if they have permission and it belongs to their branch
        return (hasPermission('cash_transfer_read') || $user->role_id == 1)
            && $user->branch_id === $transfer->journal->branch_id;
    }

    /**
     * Determine if the user can create cash transfers
     */
    public function create(User $user): bool
    {
        // Users with permission to create cash transfers
        return hasPermission('cash_transfer_create') || $user->role_id == 1;
    }

    /**
     * Determine if the user can approve a cash transfer
     */
    public function approve(User $user, CashTransfer $transfer): bool
    {
        // Only super admins (role_id = 1) or users with approve permission can approve
        return (hasPermission('cash_transfer_approve') || $user->role_id == 1)
            && $user->branch_id === $transfer->journal->branch_id
            && $transfer->status === 'pending';
    }

    /**
     * Determine if the user can reject a cash transfer
     */
    public function reject(User $user, CashTransfer $transfer): bool
    {
        // Only super admins (role_id = 1) or users with reject permission can reject
        return (hasPermission('cash_transfer_reject') || $user->role_id == 1)
            && $user->branch_id === $transfer->journal->branch_id
            && $transfer->status === 'pending';
    }

    /**
     * Determine if the user can delete a cash transfer
     */
    public function delete(User $user, CashTransfer $transfer): bool
    {
        // User can delete if they have permission and transfer is pending
        return (hasPermission('cash_transfer_delete') || $user->role_id == 1)
            && $user->branch_id === $transfer->journal->branch_id
            && $transfer->status === 'pending';
    }

    /**
     * Determine if the user can view transfer statistics
     */
    public function viewStatistics(User $user): bool
    {
        // Users with permission to view statistics or super admins
        return hasPermission('cash_transfer_statistics') || $user->role_id == 1;
    }
}
