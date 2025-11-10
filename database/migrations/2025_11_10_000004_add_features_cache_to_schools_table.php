<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add caching columns to schools table for optimized feature access.
     * Reduces database queries by caching computed features from package assignments.
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->json('cached_features')->nullable()->after('status')
                ->comment('Cached array of permission feature IDs for quick access');
            $table->timestamp('features_cache_expires_at')->nullable()->after('cached_features')
                ->comment('Cache expiration timestamp for invalidation strategy');

            // Index for cache expiration queries
            $table->index('features_cache_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropIndex(['features_cache_expires_at']);
            $table->dropColumn(['cached_features', 'features_cache_expires_at']);
        });
    }
};
