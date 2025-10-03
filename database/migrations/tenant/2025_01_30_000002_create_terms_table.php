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
        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_definition_id')->constrained('term_definitions');
            $table->foreignId('session_id')->constrained('sessions');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['draft', 'upcoming', 'active', 'closed'])->default('upcoming');

            // Tracking fields
            $table->foreignId('opened_by')->nullable()->constrained('users');
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->boolean('auto_closed')->default(false);

            // Optional enhancements
            $table->integer('holiday_count')->default(0);
            $table->integer('actual_weeks')->nullable(); // Calculated duration
            $table->text('notes')->nullable();

            $table->timestamps();

            // Constraints
            $table->unique(['term_definition_id', 'session_id'], 'unique_term_session');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terms');
    }
};