<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add fees generation permissions
        $permission = new Permission();
        $permission->attribute = 'fees_generation';
        $permission->keywords = [
            'read' => 'fees_generate_read',
            'create' => 'fees_generate_create', 
            'update' => 'fees_generate_update',
            'delete' => 'fees_generate_delete'
        ];
        $permission->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove fees generation permissions
        Permission::where('attribute', 'fees_generation')->delete();
    }
};
