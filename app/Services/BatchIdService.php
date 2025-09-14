<?php

namespace App\Services;

use App\Models\Fees\FeesGeneration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BatchIdService
{
    /**
     * Generate a new sequential batch ID
     *
     * @return string The generated batch ID in format "BATCH_ID_X"
     */
    public function generateBatchId(): string
    {
        return DB::transaction(function () {
            try {
                // Get the maximum ID from fees_generations table with row locking
                // This ensures thread safety by locking the table during read
                $maxId = FeesGeneration::lockForUpdate()->max('id');

                // If no records exist, start from 1, otherwise increment by 1
                $nextSequence = ($maxId ?? 0) + 1;

                // Generate the batch ID in the requested format
                $batchId = 'BATCH_ID_' . $nextSequence;

                Log::info('Generated new batch ID', [
                    'batch_id' => $batchId,
                    'sequence' => $nextSequence,
                    'max_existing_id' => $maxId
                ]);

                return $batchId;

            } catch (\Exception $e) {
                Log::error('Failed to generate batch ID', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                throw new \Exception('Failed to generate batch ID: ' . $e->getMessage());
            }
        });
    }

    /**
     * Validate if a batch ID follows the new format
     *
     * @param string $batchId
     * @return bool
     */
    public function isValidBatchIdFormat(string $batchId): bool
    {
        return preg_match('/^BATCH_ID_\d+$/', $batchId) === 1;
    }

    /**
     * Extract the sequence number from a batch ID
     *
     * @param string $batchId
     * @return int|null
     */
    public function extractSequenceNumber(string $batchId): ?int
    {
        if (!$this->isValidBatchIdFormat($batchId)) {
            return null;
        }

        return (int) str_replace('BATCH_ID_', '', $batchId);
    }

    /**
     * Check if a batch ID already exists
     *
     * @param string $batchId
     * @return bool
     */
    public function batchIdExists(string $batchId): bool
    {
        return FeesGeneration::where('batch_id', $batchId)->exists();
    }

    /**
     * Get the next expected sequence number (for preview/testing purposes)
     *
     * @return int
     */
    public function getNextSequenceNumber(): int
    {
        $maxId = FeesGeneration::max('id');
        return ($maxId ?? 0) + 1;
    }

    /**
     * Generate a batch ID with custom prefix (for special cases)
     *
     * @param string $prefix
     * @return string
     */
    public function generateCustomBatchId(string $prefix = 'BATCH_ID'): string
    {
        return DB::transaction(function () use ($prefix) {
            $maxId = FeesGeneration::lockForUpdate()->max('id');
            $nextSequence = ($maxId ?? 0) + 1;

            return $prefix . '_' . $nextSequence;
        });
    }
}