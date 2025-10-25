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
        // Check if cash_transfer permission already exists
        $exists = DB::table('permissions')
            ->where('attribute', 'cash_transfer')
            ->exists();

        if (!$exists) {
            // Add Cash Transfer permissions following PermissionSeeder pattern
            DB::table('permissions')->insert([
                'attribute' => 'cash_transfer',
                'keywords' => json_encode([
                    'read' => 'cash_transfer_read',
                    'create' => 'cash_transfer_create',
                    'approve' => 'cash_transfer_approve',
                    'reject' => 'cash_transfer_reject',
                    'delete' => 'cash_transfer_delete',
                    'statistics' => 'cash_transfer_statistics',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove Cash Transfer permission
        DB::table('permissions')
            ->where('attribute', 'cash_transfer')
            ->delete();
    }
};
