<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Add exam_type permissions to admin role (role_id=2).
     *
     * The admin role was missing exam_type_read, exam_type_create,
     * exam_type_update, and exam_type_delete permissions, causing
     * 403 Forbidden errors when admins tried to access exam types.
     */
    public function up(): void
    {
        $examTypePermissions = [
            'exam_type_read',
            'exam_type_create',
            'exam_type_update',
            'exam_type_delete',
        ];

        // Get all admin roles (role_id = 2) across all schools
        $adminRoles = DB::table('roles')
            ->where('id', 2)
            ->orWhere('name', 'Admin')
            ->get();

        $updatedCount = 0;

        foreach ($adminRoles as $role) {
            $permissions = json_decode($role->permissions, true) ?? [];
            $permissionsUpdated = false;

            // Add missing exam_type permissions
            foreach ($examTypePermissions as $perm) {
                if (!in_array($perm, $permissions)) {
                    $permissions[] = $perm;
                    $permissionsUpdated = true;
                }
            }

            // Only update if we added new permissions
            if ($permissionsUpdated) {
                DB::table('roles')
                    ->where('id', $role->id)
                    ->update([
                        'permissions' => json_encode($permissions),
                        'updated_at' => now(),
                    ]);

                Log::info('Added exam_type permissions to admin role', [
                    'role_id' => $role->id,
                    'role_name' => $role->name,
                    'school_id' => $role->school_id ?? 'global',
                    'added_permissions' => $examTypePermissions,
                ]);

                $updatedCount++;
            }
        }

        Log::info('Exam type permissions migration completed', [
            'total_roles_updated' => $updatedCount,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $examTypePermissions = [
            'exam_type_read',
            'exam_type_create',
            'exam_type_update',
            'exam_type_delete',
        ];

        // Get all admin roles
        $adminRoles = DB::table('roles')
            ->where('id', 2)
            ->orWhere('name', 'Admin')
            ->get();

        foreach ($adminRoles as $role) {
            $permissions = json_decode($role->permissions, true) ?? [];

            // Remove exam_type permissions
            $permissions = array_values(array_diff($permissions, $examTypePermissions));

            DB::table('roles')
                ->where('id', $role->id)
                ->update([
                    'permissions' => json_encode($permissions),
                    'updated_at' => now(),
                ]);
        }
    }
};
