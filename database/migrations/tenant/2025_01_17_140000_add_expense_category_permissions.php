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
     * Add expense_category permissions for proper access control
     * separate from regular expense permissions.
     *
     * @return void
     */
    public function up()
    {
        // Add expense category permissions
        $permission = new Permission();
        $permission->attribute = 'expense_category';
        $permission->keywords = [
            'read' => 'expense_category_read',
            'create' => 'expense_category_create',
            'update' => 'expense_category_update',
            'delete' => 'expense_category_delete'
        ];
        $permission->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove expense category permissions
        Permission::where('attribute', 'expense_category')->delete();
    }
};
