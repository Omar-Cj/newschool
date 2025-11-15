<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Create pivot table for many-to-many relationship between packages and permission features.
     * Enables flexible feature assignment to subscription packages.
     */
    public function up(): void
    {
        Schema::create('package_permission_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')
                ->constrained('packages')
                ->cascadeOnDelete()
                ->comment('Package receiving the feature');
            $table->foreignId('permission_feature_id')
                ->constrained('permission_features')
                ->cascadeOnDelete()
                ->comment('Feature being assigned to package');
            $table->timestamps();

            // Prevent duplicate feature assignments to same package
            $table->unique(['package_id', 'permission_feature_id'], 'pkg_feature_unique');

            // Indexes for efficient relationship queries
            $table->index('package_id');
            $table->index('permission_feature_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_permission_features');
    }
};
