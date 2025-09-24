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
        Schema::table('student_categories', function (Blueprint $table) {
            $table->boolean('is_fee_exempt')
                  ->default(false)
                  ->after('status')
                  ->comment('Determines if students in this category are exempt from fees (e.g., scholarship students)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_categories', function (Blueprint $table) {
            $table->dropColumn('is_fee_exempt');
        });
    }
};