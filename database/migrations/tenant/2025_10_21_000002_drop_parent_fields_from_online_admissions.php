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
        Schema::table('online_admissions', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['father_image_id']);
            $table->dropForeign(['mother_image_id']);

            // Drop parent-specific columns
            $table->dropColumn([
                'father_name',
                'father_phone',
                'father_profession',
                'father_image_id',
                'father_nationality',
                'mother_name',
                'mother_phone',
                'mother_profession',
                'mother_image_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_admissions', function (Blueprint $table) {
            $table->string('father_name')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('father_profession')->nullable();
            $table->foreignId('father_image_id')->nullable()->constrained('uploads')->cascadeOnDelete();
            $table->string('father_nationality')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_profession')->nullable();
            $table->foreignId('mother_image_id')->nullable()->constrained('uploads')->cascadeOnDelete();
        });
    }
};
