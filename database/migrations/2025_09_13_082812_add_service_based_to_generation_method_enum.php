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
        // Add 'service_based' to the generation_method enum
        DB::statement("ALTER TABLE fees_collects MODIFY COLUMN generation_method ENUM('manual', 'bulk', 'automated', 'service_based') NOT NULL DEFAULT 'manual'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'service_based' from the generation_method enum
        DB::statement("ALTER TABLE fees_collects MODIFY COLUMN generation_method ENUM('manual', 'bulk', 'automated') NOT NULL DEFAULT 'manual'");
    }
};
