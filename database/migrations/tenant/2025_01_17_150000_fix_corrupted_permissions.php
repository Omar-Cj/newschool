<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix corrupted permissions with null keywords.
     * Remove invalid permission records that have null keywords.
     *
     * @return void
     */
    public function up()
    {
        // Delete permissions with null keywords
        Permission::whereNull('keywords')->delete();

        // Delete permissions with empty string keywords
        Permission::where('keywords', '')->delete();

        // Log the cleanup
        \Log::info('Corrupted permissions cleanup completed', [
            'null_keywords_deleted' => Permission::whereNull('keywords')->count(),
            'empty_keywords_deleted' => Permission::where('keywords', '')->count(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * Note: We cannot reverse data deletion, so this is a no-op.
     * The corrupted data should be regenerated from seeders if needed.
     *
     * @return void
     */
    public function down()
    {
        // Cannot reverse data deletion
        // Run the PermissionSeeder if you need to restore default permissions
    }
};
