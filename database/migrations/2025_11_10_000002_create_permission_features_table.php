<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create permission_features table to map permissions to feature groups.
     * Links Permission entities to FeatureGroups with additional metadata.
     */
    public function up(): void
    {
        Schema::create('permission_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')
                ->constrained('permissions')
                ->cascadeOnDelete()
                ->comment('Reference to the permission entity');
            $table->foreignId('feature_group_id')
                ->constrained('feature_groups')
                ->cascadeOnDelete()
                ->comment('Feature group this permission belongs to');
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->boolean('is_premium')->default(false)->comment('Premium feature flag for package differentiation');
            $table->unsignedSmallInteger('position')->default(0)->comment('Display order within group');
            $table->boolean('status')->default(true)->comment('Active/Inactive status');
            $table->timestamps();

            // Indexes for efficient querying
            $table->index('feature_group_id');
            $table->index('status');
            $table->index('is_premium');
            $table->index(['feature_group_id', 'status', 'position']); // Composite for group features listing
            $table->index(['permission_id', 'status']); // For permission-based lookups
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_features');
    }
};
