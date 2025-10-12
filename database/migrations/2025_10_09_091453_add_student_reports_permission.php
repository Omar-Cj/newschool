<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add Student Reports permission
        $permissions = [
            'student_reports_read',
        ];

        foreach ($permissions as $permission) {
            // Check if permission already exists
            $exists = DB::table('permissions')
                ->where('attribute', $permission)
                ->exists();

            if (!$exists) {
                DB::table('permissions')->insert([
                    'attribute' => $permission,
                    'keywords' => json_encode([$permission]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove Student Reports permission
        $permissions = [
            'student_reports_read',
        ];

        DB::table('permissions')
            ->whereIn('attribute', $permissions)
            ->delete();
    }
};
