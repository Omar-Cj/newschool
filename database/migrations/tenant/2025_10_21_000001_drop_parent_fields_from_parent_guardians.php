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
        Schema::table('parent_guardians', function (Blueprint $table) {
            // Drop parent-specific columns
            $table->dropColumn([
                'father_name',
                'father_mobile',
                'father_profession',
                'father_image',
                'father_nationality',
                'father_id',
                'mother_name',
                'mother_mobile',
                'mother_profession',
                'mother_image',
                'mother_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parent_guardians', function (Blueprint $table) {
            $table->string('father_name')->nullable();
            $table->string('father_mobile')->nullable();
            $table->string('father_profession')->nullable();
            $table->string('father_image')->nullable();
            $table->string('father_nationality')->nullable();
            $table->string('father_id')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_mobile')->nullable();
            $table->string('mother_profession')->nullable();
            $table->string('mother_image')->nullable();
            $table->string('mother_id')->nullable();
        });
    }
};
