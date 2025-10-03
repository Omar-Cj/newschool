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
        Schema::create('term_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // First Term, Second Term, etc.
            $table->string('code', 20)->unique()->nullable(); // TERM1, TERM2, etc.
            $table->integer('sequence')->default(1); // Order: 1, 2, 3
            $table->integer('typical_duration_weeks')->default(12);
            $table->integer('typical_start_month')->nullable(); // Suggested month (1-12)
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('sequence');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('term_definitions');
    }
};