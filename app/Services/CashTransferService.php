<?php

namespace App\Services;

use App\Models\CashTransfer;
use App\Repositories\CashTransferRepository;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\JournalNotActiveException;
use App\Exceptions\TransferAlreadyApprovedException;
use App\Events\CashTransferCreated;
use App\Events\CashTransferApproved;
use Modules\Journals\Entities\Journal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashTransferService
{
    public function __construct(
        private CashTransferRepository $transferRepository
    ) {}

    /**
     * Create a new cash transfer
     * Validates against user-specific balance (only fees THEY collected)
     */
    public function createTransfer(
        int $journalId,
        float $amount,
        int $transferredBy,
        ?string $notes = null
    ): CashTransfer {
        $journal = Journal::findOrFail($journalId);

        // Check if journal is active
        if (!$journal->isActive()) {
            throw new JournalNotActiveException(
                "Cannot transfer to an inactive journal. Journal: {$journal->name}"
            );
        }

        // Use user-specific remaining balance instead of total journal balance
        // This ensures users can only transfer fees THEY collected
        $remainingBalance = $journal->getUserRemainingBalance($transferredBy);
        $userCollected = $journal->getUserTotalCollected($transferredBy);
        $userTransferred = $journal->approvedTransfers()
            ->where('transferred_by', $transferredBy)
            ->sum('amount') ?? 0;

        Log::info('💰 [CASH-TRANSFER] User balance validation', [
            'journal_id' => $journalId,
            'user_id' => $transferredBy,
            'user_collected' => $userCollected,
            'user_already_transferred' => $userTransferred,
            'user_remaining_balance' => $remainingBalance,
            'requested_amount' => $amount
        ]);

        // Check if user has collected any fees from this journal
        if ($userCollected == 0) {
            throw new \Exception(
                "You have not collected any fees from this journal ({$journal->name}). " .
                "Only fees you personally collected can be transferred."
            );
        }

        // Check if amount exceeds user's available balance
        if ($amount > $remainingBalance) {
            $message = sprintf(
                "Insufficient balance. You collected $%.2f, already transferred $%.2f. " .
                "Remaining balance: $%.2f (Requested: $%.2f)",
                $userCollected,
                $userTransferred,
                $remainingBalance,
                $amount
            );

            Log::warning('⚠️ [CASH-TRANSFER] Insufficient user balance', [
                'journal_id' => $journalId,
                'user_id' => $transferredBy,
                'collected' => $userCollected,
                'transferred' => $userTransferred,
                'remaining' => $remainingBalance,
                'requested' => $amount
            ]);

            throw new InsufficientBalanceException(
                required: $amount,
                available: $remainingBalance
            );
        }

        return DB::transaction(function () use ($journalId, $amount, $transferredBy, $notes, $journal) {
            $transfer = $this->transferRepository->create([
                'journal_id' => $journalId,
                'branch_id' => $journal->branch_id,  // Auto-populate from journal
                'amount' => $amount,
                'transferred_by' => $transferredBy,
                'notes' => $notes,
                'status' => 'pending',
            ]);

            // Clear journal cache
            $journal->clearCache();

            // Log the transfer creation
            Log::channel('daily')->info('Cash transfer created', [
                'transfer_id' => $transfer->id,
                'journal_id' => $journalId,
                'amount' => $amount,
                'transferred_by' => $transferredBy,
            ]);

            // Fire event
            event(new CashTransferCreated($transfer));

            return $transfer->fresh(['journal', 'transferredBy']);
        });
    }

    /**
     * Approve a cash transfer
     */
    public function approveTransfer(int $transferId, int $approvedBy): CashTransfer
    {
        \Log::info('🔵 [SERVICE-APPROVE] Starting approval process', [
            'transfer_id' => $transferId,
            'approved_by' => $approvedBy
        ]);

        $transfer = $this->transferRepository->findById($transferId);

        if (!$transfer) {
            \Log::error('❌ [SERVICE-APPROVE] Transfer not found', [
                'transfer_id' => $transferId
            ]);
            throw new \Exception("Transfer not found.");
        }

        \Log::info('📋 [SERVICE-APPROVE] Transfer found', [
            'transfer_id' => $transfer->id,
            'current_status' => $transfer->status,
            'journal_id' => $transfer->journal_id,
            'amount' => $transfer->amount
        ]);

        if ($transfer->status === 'approved') {
            \Log::warning('⚠️ [SERVICE-APPROVE] Transfer already approved', [
                'transfer_id' => $transfer->id
            ]);
            throw new TransferAlreadyApprovedException(
                "Transfer #{$transfer->id} is already approved."
            );
        }

        return DB::transaction(function () use ($transfer, $approvedBy) {
            \Log::info('🔄 [SERVICE-APPROVE] Starting database transaction');

            $updateData = [
                'status' => 'approved',
                'approved_by' => $approvedBy,
                'approved_at' => now(),
            ];

            \Log::info('💾 [SERVICE-APPROVE] Calling repository update', [
                'transfer_id' => $transfer->id,
                'update_data' => $updateData
            ]);

            $this->transferRepository->update($transfer, $updateData);

            \Log::info('✅ [SERVICE-APPROVE] Repository update completed');

            // Verify update worked
            $transfer->refresh();
            \Log::info('🔍 [SERVICE-APPROVE] Transfer after refresh', [
                'id' => $transfer->id,
                'status' => $transfer->status,
                'approved_by' => $transfer->approved_by,
                'approved_at' => $transfer->approved_at
            ]);

            // Clear journal cache
            \Log::info('🗑️ [SERVICE-APPROVE] Clearing journal cache');
            $transfer->journal->clearCache();

            // Log the approval
            Log::channel('daily')->info('Cash transfer approved', [
                'transfer_id' => $transfer->id,
                'journal_id' => $transfer->journal_id,
                'amount' => $transfer->amount,
                'approved_by' => $approvedBy,
            ]);

            // Fire event
            \Log::info('📢 [SERVICE-APPROVE] Firing CashTransferApproved event');
            event(new CashTransferApproved($transfer->fresh()));

            $result = $transfer->fresh(['journal', 'transferredBy', 'approvedBy']);

            \Log::info('🏁 [SERVICE-APPROVE] Returning approved transfer', [
                'transfer_id' => $result->id,
                'final_status' => $result->status
            ]);

            return $result;
        });
    }

    /**
     * Reject a cash transfer
     */
    public function rejectTransfer(int $transferId, int $rejectedBy, string $reason): CashTransfer
    {
        $transfer = $this->transferRepository->findById($transferId);

        if (!$transfer) {
            throw new \Exception("Transfer not found.");
        }

        if ($transfer->status === 'approved') {
            throw new \Exception("Cannot reject an approved transfer.");
        }

        return DB::transaction(function () use ($transfer, $reason) {
            $this->transferRepository->update($transfer, [
                'status' => 'rejected',
                'rejection_reason' => $reason,
            ]);

            // Log the rejection
            Log::channel('daily')->info('Cash transfer rejected', [
                'transfer_id' => $transfer->id,
                'journal_id' => $transfer->journal_id,
                'amount' => $transfer->amount,
                'reason' => $reason,
            ]);

            return $transfer->fresh(['journal', 'transferredBy']);
        });
    }

    /**
     * Get statistics for cash transfers
     *
     * @param int|null $branchId Filter by branch ID (optional)
     */
    public function getStatistics(?int $branchId = null): array
    {
        return $this->transferRepository->getStatistics($branchId);
    }

    /**
     * Calculate journal progress
     */
    public function calculateJournalProgress(Journal $journal): float
    {
        return $journal->progress_percentage;
    }

    /**
     * Check if journal can be closed
     */
    public function canCloseJournal(Journal $journal): bool
    {
        return $journal->canBeClosed();
    }

    /**
     * Get payment method breakdown for a transfer
     */
    public function getTransferBreakdown(CashTransfer $transfer): array
    {
        return $transfer->payment_method_breakdown;
    }

    /**
     * Delete a pending transfer
     */
    public function deletePendingTransfer(int $transferId): bool
    {
        $transfer = $this->transferRepository->findById($transferId);

        if (!$transfer) {
            throw new \Exception("Transfer not found.");
        }

        if ($transfer->status !== 'pending') {
            throw new \Exception("Can only delete pending transfers.");
        }

        $journal = $transfer->journal;

        return DB::transaction(function () use ($transfer, $journal) {
            $result = $this->transferRepository->delete($transfer);

            // Clear journal cache
            $journal->clearCache();

            // Log the deletion
            Log::channel('daily')->info('Cash transfer deleted', [
                'transfer_id' => $transfer->id,
                'journal_id' => $transfer->journal_id,
                'amount' => $transfer->amount,
            ]);

            return $result;
        });
    }
}
