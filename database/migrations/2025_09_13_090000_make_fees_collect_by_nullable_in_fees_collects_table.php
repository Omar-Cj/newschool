<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make fees_collect_by column nullable
        DB::statement('ALTER TABLE fees_collects MODIFY COLUMN fees_collect_by BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to NOT NULL (but this might fail if there are null values)
        DB::statement('ALTER TABLE fees_collects MODIFY COLUMN fees_collect_by BIGINT UNSIGNED NOT NULL');
    }
};
