<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the exam_entry permission to include publish
        $permission = Permission::where('attribute', 'exam_entry')->first();

        if ($permission) {
            // Add publish to existing exam_entry keywords
            $keywords = $permission->keywords;
            $keywords['publish'] = 'exam_entry_publish';
            $permission->keywords = $keywords;
            $permission->save();
        }

        // Add exam_entry_publish permission to role_id 1 (Super Admin) and role_id 2 (Admin)
        $roles = Role::whereIn('id', [1, 2])->get();

        foreach ($roles as $role) {
            $permissions = $role->permissions;

            // Add exam_entry_publish if not already present
            if (!in_array('exam_entry_publish', $permissions)) {
                $permissions[] = 'exam_entry_publish';
                $role->permissions = $permissions;
                $role->save();
            }
        }

        // Remove exam_entry_publish from all other roles (role_id > 2)
        $otherRoles = Role::where('id', '>', 2)->get();

        foreach ($otherRoles as $role) {
            $permissions = $role->permissions;

            // Remove exam_entry_publish if present
            if (in_array('exam_entry_publish', $permissions)) {
                $permissions = array_diff($permissions, ['exam_entry_publish']);
                $role->permissions = array_values($permissions); // Re-index array
                $role->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove publish from exam_entry permission
        $permission = Permission::where('attribute', 'exam_entry')->first();

        if ($permission) {
            $keywords = $permission->keywords;
            unset($keywords['publish']);
            $permission->keywords = $keywords;
            $permission->save();
        }

        // Remove exam_entry_publish from all roles
        $roles = Role::all();

        foreach ($roles as $role) {
            $permissions = $role->permissions;

            if (in_array('exam_entry_publish', $permissions)) {
                $permissions = array_diff($permissions, ['exam_entry_publish']);
                $role->permissions = array_values($permissions);
                $role->save();
            }
        }
    }
};
