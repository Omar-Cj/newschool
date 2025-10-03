<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('terms', function (Blueprint $table) {
            // First, check if branch_id column exists (it should from the global migration)
            // If not, add it with default value
            if (!Schema::hasColumn('terms', 'branch_id')) {
                $table->unsignedBigInteger('branch_id')->default(1)->after('session_id');
            }
        });

        // Now ensure all existing terms have a valid branch_id (in case some are null)
        DB::table('terms')->whereNull('branch_id')->update(['branch_id' => 1]);

        // CRITICAL: Find ALL foreign keys ON the terms table itself
        // These prevent us from dropping the unique constraint
        $termsForeignKeys = DB::select("
            SELECT
                CONSTRAINT_NAME,
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE CONSTRAINT_SCHEMA = DATABASE()
            AND TABLE_NAME = 'terms'
            AND REFERENCED_TABLE_NAME IS NOT NULL
            AND CONSTRAINT_NAME != 'PRIMARY'
        ");

        // Drop all foreign keys ON the terms table
        foreach ($termsForeignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `terms` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        }

        // Find any foreign keys in other tables that reference the unique_term_session index
        // Store outside the closure so we can recreate them later
        $dependentForeignKeys = DB::select("
            SELECT
                TABLE_NAME,
                CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME = 'terms'
            AND TABLE_NAME != 'terms'
            AND CONSTRAINT_NAME != 'PRIMARY'
        ");

        // Drop dependent foreign keys before modifying the unique constraint
        foreach ($dependentForeignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        }

        Schema::table('terms', function (Blueprint $table) {

            // Now check if the old unique constraint exists
            $uniqueExists = DB::select("
                SELECT COUNT(*) as count
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                AND TABLE_NAME = 'terms'
                AND CONSTRAINT_NAME = 'unique_term_session'
                AND CONSTRAINT_TYPE = 'UNIQUE'
            ");

            if ($uniqueExists[0]->count > 0) {
                $table->dropUnique('unique_term_session');
            }

            // Check if new unique constraint already exists
            $newUniqueExists = DB::select("
                SELECT COUNT(*) as count
                FROM information_schema.TABLE_CONSTRAINTS
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                AND TABLE_NAME = 'terms'
                AND CONSTRAINT_NAME = 'unique_term_session_branch'
                AND CONSTRAINT_TYPE = 'UNIQUE'
            ");

            if ($newUniqueExists[0]->count == 0) {
                $table->unique(['term_definition_id', 'session_id', 'branch_id'], 'unique_term_session_branch');
            }

            // Add foreign key constraint to branches table (if not exists)
            if (Schema::hasTable('branches')) {
                $fkExists = DB::select("
                    SELECT COUNT(*) as count
                    FROM information_schema.TABLE_CONSTRAINTS
                    WHERE CONSTRAINT_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'terms'
                    AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                    AND CONSTRAINT_NAME = 'terms_branch_id_foreign'
                ");

                if ($fkExists[0]->count == 0) {
                    $table->foreign('branch_id')
                        ->references('id')
                        ->on('branches')
                        ->onDelete('restrict');
                }
            }

            // Add index for better query performance (if not exists)
            $indexExists = DB::select("
                SELECT COUNT(*) as count
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'terms'
                AND INDEX_NAME = 'terms_branch_id_index'
            ");

            if ($indexExists[0]->count == 0) {
                $table->index('branch_id', 'terms_branch_id_index');
            }
        });

        // Recreate the foreign keys ON the terms table
        foreach ($termsForeignKeys as $fk) {
            try {
                DB::statement("
                    ALTER TABLE `terms`
                    ADD CONSTRAINT `{$fk->CONSTRAINT_NAME}`
                    FOREIGN KEY (`{$fk->COLUMN_NAME}`)
                    REFERENCES `{$fk->REFERENCED_TABLE_NAME}` (`{$fk->REFERENCED_COLUMN_NAME}`)
                    ON DELETE RESTRICT
                    ON UPDATE CASCADE
                ");
            } catch (\Exception $e) {
                // If recreation fails, log but continue
                // Some foreign keys might have different names now
            }
        }

        // Recreate the foreign keys from other tables that were dropped earlier
        foreach ($dependentForeignKeys as $fk) {
            // Get the column details for the foreign key
            $columns = DB::select("
                SELECT COLUMN_NAME, REFERENCED_COLUMN_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE CONSTRAINT_SCHEMA = DATABASE()
                AND TABLE_NAME = ?
                AND CONSTRAINT_NAME = ?
            ", [$fk->TABLE_NAME, $fk->CONSTRAINT_NAME]);

            if (!empty($columns)) {
                $columnName = $columns[0]->COLUMN_NAME;
                $referencedColumn = $columns[0]->REFERENCED_COLUMN_NAME;

                try {
                    // Recreate the foreign key
                    DB::statement("
                        ALTER TABLE `{$fk->TABLE_NAME}`
                        ADD CONSTRAINT `{$fk->CONSTRAINT_NAME}`
                        FOREIGN KEY (`{$columnName}`)
                        REFERENCES `terms` (`{$referencedColumn}`)
                        ON DELETE RESTRICT
                        ON UPDATE CASCADE
                    ");
                } catch (\Exception $e) {
                    // If recreation fails, log but continue
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('terms', function (Blueprint $table) {
            // Drop the branch-aware unique constraint
            $table->dropUnique('unique_term_session_branch');

            // Drop foreign key if it exists
            if (Schema::hasTable('branches')) {
                $table->dropForeign(['branch_id']);
            }

            // Drop index
            $table->dropIndex('terms_branch_id_index');

            // Restore original unique constraint
            $table->unique(['term_definition_id', 'session_id'], 'unique_term_session');
        });
    }
};
