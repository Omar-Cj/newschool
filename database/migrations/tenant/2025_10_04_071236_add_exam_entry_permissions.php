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
        $permission = new Permission();
        $permission->attribute = 'exam_entry';
        $permission->keywords = [
            'read' => 'exam_entry_read',
            'create' => 'exam_entry_create',
            'update' => 'exam_entry_update',
            'delete' => 'exam_entry_delete'
        ];
        $permission->save();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('attribute', 'exam_entry')->delete();
    }
};
