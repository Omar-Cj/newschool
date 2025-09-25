<?php

namespace App\Services;

use App\Models\Fees\Receipt;
use App\Models\Fees\PaymentTransaction;
use App\Models\Fees\FeesCollect;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Enhanced unified receipt numbering service
 * Provides consistent receipt numbering across PaymentTransaction and FeesCollect
 * with gap prevention and performance optimization
 */
class ReceiptNumberingService
{
    private const CACHE_KEY_PREFIX = 'receipt_sequence_';
    private const LOCK_TIMEOUT = 30;
    private const RESERVATION_TTL = 900; // 15 minutes
    private const CACHE_TTL = 3600; // 1 hour

    /**
     * Generate unified receipt number across all payment types
     * Format: RCT-YYYY-NNNNNN (chronological sequence)
     */
    public function generateReceiptNumber(Carbon $paymentDate = null): string
    {
        $paymentDate = $paymentDate ?? now();
        $year = $paymentDate->format('Y');

        return Cache::lock("receipt_numbering_{$year}", self::LOCK_TIMEOUT)
            ->block(10, function () use ($year) {
                $cacheKey = self::CACHE_KEY_PREFIX . $year;

                // Get current sequence number for the year across all sources
                $currentSequence = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($year) {
                    return $this->getCurrentYearlySequence($year);
                });

                $nextSequence = $currentSequence + 1;
                $receiptNumber = sprintf('RCT-%s-%06d', $year, $nextSequence);

                // Ensure uniqueness across all tables
                while ($this->isReceiptNumberTaken($receiptNumber)) {
                    $nextSequence++;
                    $receiptNumber = sprintf('RCT-%s-%06d', $year, $nextSequence);
                }

                // Reserve the number temporarily
                $this->reserveReceiptNumber($receiptNumber);

                // Update cache for next request
                Cache::put($cacheKey, $nextSequence, self::CACHE_TTL);

                return $receiptNumber;
            });
    }

    /**
     * Get current sequence number across all receipt sources (unified approach)
     */
    private function getCurrentYearlySequence(string $year): int
    {
        $maxSequences = [];

        // Check PaymentTransaction records
        $ptMax = DB::table('payment_transactions')
            ->where('receipt_number', 'like', "RCT-{$year}-%")
            ->max(DB::raw('CAST(SUBSTRING(receipt_number, -6) AS UNSIGNED)'));

        if ($ptMax) {
            $maxSequences[] = $ptMax;
        }

        // Check FeesCollect records (legacy)
        $fcMax = DB::table('fees_collects')
            ->where('receipt_number', 'like', "RCT-{$year}-%")
            ->whereNotNull('payment_method') // Only paid fees have receipts
            ->max(DB::raw('CAST(SUBSTRING(receipt_number, -6) AS UNSIGNED)'));

        if ($fcMax) {
            $maxSequences[] = $fcMax;
        }

        // Check unified receipts table if it exists
        if (DB::getSchemaBuilder()->hasTable('receipts')) {
            $receiptMax = DB::table('receipts')
                ->where('receipt_number', 'like', "RCT-{$year}-%")
                ->max(DB::raw('CAST(SUBSTRING(receipt_number, -6) AS UNSIGNED)'));

            if ($receiptMax) {
                $maxSequences[] = $receiptMax;
            }
        }

        // Check reservations
        $reservationMax = DB::table('receipt_number_reservations')
            ->where('receipt_number', 'like', "RCT-{$year}-%")
            ->max(DB::raw('CAST(SUBSTRING(receipt_number, -6) AS UNSIGNED)'));

        if ($reservationMax) {
            $maxSequences[] = $reservationMax;
        }

        return empty($maxSequences) ? 0 : max($maxSequences);
    }

    /**
     * Check if receipt number is already taken across all sources
     */
    private function isReceiptNumberTaken(string $receiptNumber): bool
    {
        // Check PaymentTransaction table
        if (DB::table('payment_transactions')->where('receipt_number', $receiptNumber)->exists()) {
            return true;
        }

        // Check FeesCollect table
        if (DB::table('fees_collects')->where('receipt_number', $receiptNumber)->exists()) {
            return true;
        }

        // Check unified receipts table if exists
        if (DB::getSchemaBuilder()->hasTable('receipts') &&
            DB::table('receipts')->where('receipt_number', $receiptNumber)->exists()) {
            return true;
        }

        // Check active reservations
        if (DB::table('receipt_number_reservations')
            ->where('receipt_number', $receiptNumber)
            ->where('expires_at', '>', now())
            ->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Validate receipt number format
     */
    public function validateReceiptNumber(string $receiptNumber): bool
    {
        return preg_match('/^RCT-\d{4}-\d{6}$/', $receiptNumber) === 1;
    }

    /**
     * Extract payment date from receipt number
     */
    public function extractYearFromReceiptNumber(string $receiptNumber): ?int
    {
        if (!$this->validateReceiptNumber($receiptNumber)) {
            return null;
        }

        return (int) substr($receiptNumber, 4, 4);
    }

    /**
     * Reserve receipt number for transaction (prevents gaps in numbering)
     */
    public function reserveReceiptNumber(string $receiptNumber, int $userId = null): bool
    {
        try {
            DB::table('receipt_number_reservations')->insert([
                'receipt_number' => $receiptNumber,
                'reserved_by' => $userId ?? auth()->id(),
                'reserved_at' => now(),
                'expires_at' => now()->addSeconds(self::RESERVATION_TTL),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::debug('Receipt number reserved', ['receipt_number' => $receiptNumber]);
            return true;
        } catch (\Exception $e) {
            Log::warning('Failed to reserve receipt number', [
                'receipt_number' => $receiptNumber,
                'error' => $e->getMessage()
            ]);
            return false; // Number already reserved
        }
    }

    /**
     * Confirm usage of reserved receipt number
     */
    public function confirmReceiptNumber(string $receiptNumber): void
    {
        DB::table('receipt_number_reservations')
            ->where('receipt_number', $receiptNumber)
            ->delete();

        // Clear relevant cache to force refresh
        $year = $this->extractYearFromReceiptNumber($receiptNumber);
        if ($year) {
            Cache::forget(self::CACHE_KEY_PREFIX . $year);
        }

        Log::debug('Receipt number confirmed', ['receipt_number' => $receiptNumber]);
    }

    /**
     * Release reserved receipt number if not used
     */
    public function releaseReceiptNumber(string $receiptNumber): void
    {
        $deleted = DB::table('receipt_number_reservations')
            ->where('receipt_number', $receiptNumber)
            ->delete();

        if ($deleted) {
            Log::debug('Receipt number reservation released', ['receipt_number' => $receiptNumber]);
        }
    }

    /**
     * Clean up expired receipt number reservations
     * Should be called by scheduled task
     */
    public function cleanupExpiredReservations(): int
    {
        $deleted = DB::table('receipt_number_reservations')
            ->where('expires_at', '<', now())
            ->delete();

        if ($deleted > 0) {
            Log::info("Cleaned up {$deleted} expired receipt number reservations");
        }

        return $deleted;
    }

    /**
     * Get receipt numbering statistics for admin dashboard
     */
    public function getReceiptStats(): array
    {
        $currentYear = date('Y');
        $lastUsedNumber = $this->getCurrentYearlySequence($currentYear);

        $activeReservations = DB::table('receipt_number_reservations')
            ->where('expires_at', '>', now())
            ->count();

        $totalReceiptsThisYear = 0;

        // Count from PaymentTransaction
        $ptCount = DB::table('payment_transactions')
            ->whereNotNull('receipt_number')
            ->whereYear('created_at', $currentYear)
            ->count();
        $totalReceiptsThisYear += $ptCount;

        // Count from FeesCollect
        $fcCount = DB::table('fees_collects')
            ->whereNotNull('receipt_number')
            ->whereNotNull('payment_method')
            ->whereYear('created_at', $currentYear)
            ->count();
        $totalReceiptsThisYear += $fcCount;

        return [
            'current_year' => $currentYear,
            'last_used_sequence' => $lastUsedNumber,
            'next_available_sequence' => $lastUsedNumber + 1,
            'active_reservations' => $activeReservations,
            'total_receipts_this_year' => $totalReceiptsThisYear,
            'format_example' => sprintf('RCT-%s-%06d', $currentYear, $lastUsedNumber + 1),
            'payment_transaction_receipts' => $ptCount,
            'legacy_fees_collect_receipts' => $fcCount,
        ];
    }

    /**
     * Migrate existing receipts to unified numbering system
     * This helps transition from legacy numbering
     */
    public function migrateExistingReceipts(): array
    {
        $stats = [
            'payment_transactions_updated' => 0,
            'fees_collects_updated' => 0,
            'errors' => [],
        ];

        DB::transaction(function () use (&$stats) {
            // Migrate PaymentTransaction records without receipt numbers
            $ptRecords = PaymentTransaction::whereNull('receipt_number')
                ->orWhere('receipt_number', '')
                ->orderBy('payment_date')
                ->orderBy('id')
                ->get();

            foreach ($ptRecords as $transaction) {
                try {
                    $receiptNumber = $this->generateReceiptNumber($transaction->payment_date);
                    $transaction->update(['receipt_number' => $receiptNumber]);
                    $this->confirmReceiptNumber($receiptNumber);
                    $stats['payment_transactions_updated']++;
                } catch (\Exception $e) {
                    $stats['errors'][] = "PaymentTransaction {$transaction->id}: " . $e->getMessage();
                }
            }

            // Migrate FeesCollect records without receipt numbers (only paid ones)
            $fcRecords = FeesCollect::whereNotNull('payment_method')
                ->where(function ($query) {
                    $query->whereNull('receipt_number')->orWhere('receipt_number', '');
                })
                ->orderBy('date')
                ->orderBy('id')
                ->get();

            foreach ($fcRecords as $payment) {
                try {
                    $receiptNumber = $this->generateReceiptNumber(Carbon::parse($payment->date));
                    $payment->update(['receipt_number' => $receiptNumber]);
                    $this->confirmReceiptNumber($receiptNumber);
                    $stats['fees_collects_updated']++;
                } catch (\Exception $e) {
                    $stats['errors'][] = "FeesCollect {$payment->id}: " . $e->getMessage();
                }
            }
        });

        Log::info('Receipt number migration completed', $stats);
        return $stats;
    }

    /**
     * Bulk generate receipt numbers for batch operations
     */
    public function generateBulkReceiptNumbers(int $count, Carbon $paymentDate = null): array
    {
        $receiptNumbers = [];
        $paymentDate = $paymentDate ?? now();

        for ($i = 0; $i < $count; $i++) {
            $receiptNumbers[] = $this->generateReceiptNumber($paymentDate);
        }

        return $receiptNumbers;
    }
}