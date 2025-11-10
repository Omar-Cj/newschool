<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create feature_groups table for organizing permission features into logical groups.
     * Used for package management and feature assignment.
     */
    public function up(): void
    {
        Schema::create('feature_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique()->comment('Unique identifier for programmatic access');
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable()->comment('Icon class for UI display');
            $table->unsignedSmallInteger('position')->default(0)->comment('Display order');
            $table->boolean('status')->default(true)->comment('Active/Inactive status');
            $table->timestamps();

            // Indexes for performance
            $table->index('slug');
            $table->index('status');
            $table->index(['status', 'position']); // Composite for sorted active groups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_groups');
    }
};
