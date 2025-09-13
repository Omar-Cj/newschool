<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fees_types', function (Blueprint $table) {
            // Add fee frequency field
            $table->enum('fee_frequency', ['monthly', 'semester', 'annual', 'one_time'])
                  ->default('monthly')
                  ->after('category');

            // Add pro-rating support flag
            $table->boolean('is_prorated')
                  ->default(false)
                  ->after('fee_frequency');

            // Add index for efficient querying
            $table->index(['fee_frequency', 'status']);
            $table->index(['is_prorated', 'fee_frequency']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fees_types', function (Blueprint $table) {
            // Remove indexes first
            $table->dropIndex(['fee_frequency', 'status']);
            $table->dropIndex(['is_prorated', 'fee_frequency']);

            // Remove columns
            $table->dropColumn(['fee_frequency', 'is_prorated']);
        });
    }
};