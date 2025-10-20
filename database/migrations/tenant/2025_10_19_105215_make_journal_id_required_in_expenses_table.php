<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Drop existing foreign key and index
            $table->dropForeign(['journal_id']);
            $table->dropIndex(['journal_id']);

            // Modify column to be required (not nullable)
            $table->foreignId('journal_id')
                  ->nullable(false)
                  ->change();

            // Re-add foreign key with cascade on delete
            $table->foreign('journal_id')
                  ->references('id')
                  ->on('journals')
                  ->cascadeOnDelete();

            $table->index('journal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            // Drop foreign key and index
            $table->dropForeign(['journal_id']);
            $table->dropIndex(['journal_id']);

            // Make column nullable again
            $table->foreignId('journal_id')
                  ->nullable()
                  ->change();

            // Re-add foreign key with null on delete
            $table->foreign('journal_id')
                  ->references('id')
                  ->on('journals')
                  ->nullOnDelete();

            $table->index('journal_id');
        });
    }
};
