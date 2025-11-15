<?php

namespace App\Services;

use App\Models\Fees\FeesGeneration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BatchIdService
{
    /**
     * Generate a new sequential batch ID (school-scoped for multitenancy)
     *
     * @param int|null $schoolId Optional school ID (defaults to current authenticated user's school)
     * @return string The generated batch ID in format "BATCH_ID_X"
     */
    public function generateBatchId(?int $schoolId = null): string
    {
        return DB::transaction(function () use ($schoolId) {
            try {
                // Get school ID from parameter or authenticated user
                $schoolId = $schoolId ?? auth()->user()->school_id ?? null;

                if (!$schoolId) {
                    Log::error('Cannot generate batch ID without school context', [
                        'user_id' => auth()->id(),
                        'authenticated' => auth()->check()
                    ]);
                    throw new \Exception('School context required for batch ID generation');
                }

                // Get the maximum sequence number from existing batch_ids for this school
                // Use lockForUpdate to ensure thread safety
                $maxBatchNum = FeesGeneration::where('school_id', $schoolId)
                    ->lockForUpdate()
                    ->get()
                    ->map(function ($generation) {
                        // Extract numeric part from batch_id (e.g., "BATCH_ID_5" -> 5)
                        return (int) str_replace('BATCH_ID_', '', $generation->batch_id);
                    })
                    ->max();

                // If no records exist for this school, start from 1
                $nextSequence = ($maxBatchNum ?? 0) + 1;

                // Generate the batch ID in the requested format
                $batchId = 'BATCH_ID_' . $nextSequence;

                Log::info('Generated new batch ID (school-scoped)', [
                    'batch_id' => $batchId,
                    'sequence' => $nextSequence,
                    'school_id' => $schoolId,
                    'max_existing_sequence' => $maxBatchNum,
                    'user_id' => auth()->id()
                ]);

                return $batchId;

            } catch (\Exception $e) {
                Log::error('Failed to generate batch ID', [
                    'error' => $e->getMessage(),
                    'school_id' => $schoolId ?? 'not_set',
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
     * Check if a batch ID already exists for a specific school
     *
     * @param string $batchId
     * @param int|null $schoolId
     * @return bool
     */
    public function batchIdExists(string $batchId, ?int $schoolId = null): bool
    {
        $schoolId = $schoolId ?? auth()->user()->school_id ?? null;

        $query = FeesGeneration::where('batch_id', $batchId);

        if ($schoolId) {
            $query->where('school_id', $schoolId);
        }

        return $query->exists();
    }

    /**
     * Get the next expected sequence number for a school (for preview/testing purposes)
     *
     * @param int|null $schoolId
     * @return int
     */
    public function getNextSequenceNumber(?int $schoolId = null): int
    {
        $schoolId = $schoolId ?? auth()->user()->school_id ?? null;

        if (!$schoolId) {
            return 1;
        }

        $maxBatchNum = FeesGeneration::where('school_id', $schoolId)
            ->get()
            ->map(function ($generation) {
                return (int) str_replace('BATCH_ID_', '', $generation->batch_id);
            })
            ->max();

        return ($maxBatchNum ?? 0) + 1;
    }

    /**
     * Generate a batch ID with custom prefix (for special cases)
     *
     * @param string $prefix
     * @param int|null $schoolId
     * @return string
     */
    public function generateCustomBatchId(string $prefix = 'BATCH_ID', ?int $schoolId = null): string
    {
        return DB::transaction(function () use ($prefix, $schoolId) {
            $schoolId = $schoolId ?? auth()->user()->school_id ?? null;

            if (!$schoolId) {
                throw new \Exception('School context required for batch ID generation');
            }

            $maxBatchNum = FeesGeneration::where('school_id', $schoolId)
                ->lockForUpdate()
                ->get()
                ->map(function ($generation) use ($prefix) {
                    // Extract numeric part from batch_id with this prefix
                    $pattern = preg_quote($prefix, '/') . '_';
                    if (preg_match('/' . $pattern . '(\d+)/', $generation->batch_id, $matches)) {
                        return (int) $matches[1];
                    }
                    return 0;
                })
                ->max();

            $nextSequence = ($maxBatchNum ?? 0) + 1;

            return $prefix . '_' . $nextSequence;
        });
    }
}