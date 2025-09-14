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
        // Make fees_assign_children_id nullable for service-based fee generation
        DB::statement('ALTER TABLE fees_collects MODIFY COLUMN fees_assign_children_id BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Make fees_assign_children_id required again
        DB::statement('ALTER TABLE fees_collects MODIFY COLUMN fees_assign_children_id BIGINT UNSIGNED NOT NULL');
    }
};
