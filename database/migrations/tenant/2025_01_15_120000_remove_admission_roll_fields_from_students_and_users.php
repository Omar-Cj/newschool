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
        // Remove admission_no and roll_no from students table
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['admission_no', 'roll_no']);
        });

        // Remove admission_no from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admission_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restore admission_no and roll_no to students table
        Schema::table('students', function (Blueprint $table) {
            $table->string('admission_no')->nullable()->after('id');
            $table->integer('roll_no')->nullable()->after('admission_no');
        });

        // Restore admission_no to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('admission_no')->nullable()->comment('For student login')->after('email');
        });
    }
};