<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Fees\FeesGeneration;
use App\Models\Fees\FeesCollect;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's add a temporary column to store old batch_ids for rollback
        Schema::table('fees_generations', function (Blueprint $table) {
            $table->string('old_batch_id')->nullable()->after('batch_id');
        });

        // Get all existing fee generations ordered by creation date
        $generations = FeesGeneration::orderBy('created_at', 'asc')->get();

        if ($generations->isNotEmpty()) {
            DB::transaction(function () use ($generations) {
                $sequenceNumber = 1; // Start sequential numbering from 1
                
                foreach ($generations as $generation) {
                    $oldBatchId = $generation->batch_id;
                    $newBatchId = 'BATCH_ID_' . $sequenceNumber;

                    // Safety check: ensure new batch_id doesn't already exist
                    while (FeesGeneration::where('batch_id', $newBatchId)->where('id', '!=', $generation->id)->exists()) {
                        $sequenceNumber++;
                        $newBatchId = 'BATCH_ID_' . $sequenceNumber;
                        echo "Collision detected, incrementing to: {$newBatchId}\n";
                    }

                    // Store old batch_id for rollback capability
                    $generation->update([
                        'old_batch_id' => $oldBatchId,
                        'batch_id' => $newBatchId
                    ]);

                    // Update corresponding fees_collect records
                    $updatedCount = FeesCollect::where('generation_batch_id', $oldBatchId)
                        ->update(['generation_batch_id' => $newBatchId]);

                    echo "Updated batch_id: {$oldBatchId} -> {$newBatchId} (sequence: {$sequenceNumber}, fees_collect records: {$updatedCount})\n";
                    
                    $sequenceNumber++; // Increment for next iteration
                }
            });

            echo "Successfully updated " . $generations->count() . " batch IDs to sequential format.\n";
            echo "Next batch ID from BatchIdService will be: BATCH_ID_" . (FeesGeneration::max('id') + 1) . "\n";
            echo "Last migrated sequence was: BATCH_ID_" . ($sequenceNumber - 1) . "\n";
            
            if (($sequenceNumber - 1) < FeesGeneration::max('id')) {
                echo "⚠️  NOTE: There will be a gap in sequence numbers due to deleted records.\n";
                echo "   This is expected and won't cause issues.\n";
            }
        } else {
            echo "No existing fee generations found to update.\n";
        }

        // Add index on batch_id if it doesn't exist (it should exist from create table migration)
        if (!Schema::hasIndex('fees_generations', ['batch_id'])) {
            Schema::table('fees_generations', function (Blueprint $table) {
                $table->index('batch_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            // Restore old batch_ids from the temporary column
            $generations = FeesGeneration::whereNotNull('old_batch_id')->get();

            foreach ($generations as $generation) {
                $oldBatchId = $generation->old_batch_id;
                $currentBatchId = $generation->batch_id;

                // Update fees_collect records back to old batch_id
                FeesCollect::where('generation_batch_id', $currentBatchId)
                    ->update(['generation_batch_id' => $oldBatchId]);

                // Restore the original batch_id
                $generation->update([
                    'batch_id' => $oldBatchId
                ]);
            }

            echo "Restored " . $generations->count() . " batch IDs to original format.\n";
        });

        // Remove the temporary column
        Schema::table('fees_generations', function (Blueprint $table) {
            $table->dropColumn('old_batch_id');
        });
    }
};
