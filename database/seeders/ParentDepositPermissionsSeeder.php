<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ParentDepositPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions for parent deposits
        $permissions = [
            [
                'name' => 'parent_deposit_create',
                'guard_name' => 'web',
                'group_name' => 'Parent Deposits',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'parent_deposit_view',
                'guard_name' => 'web',
                'group_name' => 'Parent Deposits',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'parent_deposit_edit',
                'guard_name' => 'web',
                'group_name' => 'Parent Deposits',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'parent_deposit_delete',
                'guard_name' => 'web',
                'group_name' => 'Parent Deposits',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'parent_statement_view',
                'guard_name' => 'web',
                'group_name' => 'Parent Statements',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'parent_statement_export',
                'guard_name' => 'web',
                'group_name' => 'Parent Statements',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name']],
                $permission
            );
        }

        // Assign permissions to Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $permissionNames = array_column($permissions, 'name');
            $superAdminRole->givePermissionTo($permissionNames);
        }

        // Assign basic permissions to Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminPermissions = [
                'parent_deposit_view',
                'parent_deposit_create',
                'parent_statement_view',
                'parent_statement_export',
            ];
            $adminRole->givePermissionTo($adminPermissions);
        }

        // Assign view permissions to Accountant role
        $accountantRole = Role::where('name', 'Accountant')->first();
        if ($accountantRole) {
            $accountantPermissions = [
                'parent_deposit_view',
                'parent_deposit_create',
                'parent_statement_view',
                'parent_statement_export',
            ];
            $accountantRole->givePermissionTo($accountantPermissions);
        }

        $this->command->info('Parent deposit permissions created and assigned successfully!');
    }
}