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
        // System admins (school_id === null) have full access
        if ($user->school_id === null) {
            return true;
        }

        // School users need permission
        return hasPermission('cash_transfer_read');
    }

    /**
     * Determine if the user can view a cash transfer
     */
    public function view(User $user, CashTransfer $transfer): bool
    {
        // System admins (school_id === null) have full access
        if ($user->school_id === null) {
            return true;
        }

        // School users need permission and branch match
        return hasPermission('cash_transfer_read')
            && $user->branch_id === $transfer->journal->branch_id;
    }

    /**
     * Determine if the user can create cash transfers
     */
    public function create(User $user): bool
    {
        // System admins (school_id === null) have full access
        if ($user->school_id === null) {
            return true;
        }

        // School users need permission
        return hasPermission('cash_transfer_create');
    }

    /**
     * Determine if the user can approve a cash transfer
     */
    public function approve(User $user, CashTransfer $transfer): bool
    {
        // System admins (school_id === null) have full access
        if ($user->school_id === null) {
            return $transfer->status === 'pending';
        }

        // School users need permission, branch match, and pending status
        return hasPermission('cash_transfer_approve')
            && $user->branch_id === $transfer->journal->branch_id
            && $transfer->status === 'pending';
    }

    /**
     * Determine if the user can reject a cash transfer
     */
    public function reject(User $user, CashTransfer $transfer): bool
    {
        // System admins (school_id === null) have full access
        if ($user->school_id === null) {
            return $transfer->status === 'pending';
        }

        // School users need permission, branch match, and pending status
        return hasPermission('cash_transfer_reject')
            && $user->branch_id === $transfer->journal->branch_id
            && $transfer->status === 'pending';
    }

    /**
     * Determine if the user can delete a cash transfer
     */
    public function delete(User $user, CashTransfer $transfer): bool
    {
        // System admins (school_id === null) have full access
        if ($user->school_id === null) {
            return $transfer->status === 'pending';
        }

        // School users need permission, branch match, and pending status
        return hasPermission('cash_transfer_delete')
            && $user->branch_id === $transfer->journal->branch_id
            && $transfer->status === 'pending';
    }

    /**
     * Determine if the user can view transfer statistics
     */
    public function viewStatistics(User $user): bool
    {
        // System admins (school_id === null) have full access
        if ($user->school_id === null) {
            return true;
        }

        // School users need permission
        return hasPermission('cash_transfer_statistics');
    }
}
