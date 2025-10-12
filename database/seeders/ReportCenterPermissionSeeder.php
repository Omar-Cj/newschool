<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportCenterPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        // Get the current branch_id (default to 1)
        $branchId = 1;

        // Check if permission already exists
        $exists = DB::table('permissions')
            ->where('attribute', 'report_center')
            ->exists();

        if (!$exists) {
            // Insert the permission with the correct structure
            // Format: attribute = 'report_center', keywords = JSON with permission keys
            $permissionId = DB::table('permissions')->insertGetId([
                'attribute' => 'report_center',
                'keywords' => json_encode([
                    'read' => 'report_center_read'
                ]),
                'created_at' => $now,
                'updated_at' => $now,
                'branch_id' => $branchId,
            ]);

            $this->command->info('✓ Permission "report_center" created successfully with ID: ' . $permissionId);
            $this->command->info('  - Attribute: report_center');
            $this->command->info('  - Keywords: {"read":"report_center_read"}');

            // Now we need to assign this permission to roles
            // The system checks hasPermission('report_center_read')
            // We need to update role permissions

            $this->command->info('');
            $this->command->info('Permission created! Now you need to assign it to roles via the admin panel:');
            $this->command->info('1. Go to Settings → Roles');
            $this->command->info('2. Edit the role (Admin, Manager, etc.)');
            $this->command->info('3. Check the "Report Center" permission');
            $this->command->info('4. Save the role');

        } else {
            $this->command->warn('⚠ Permission "report_center" already exists');

            // Show existing permission
            $permission = DB::table('permissions')
                ->where('attribute', 'report_center')
                ->first();

            if ($permission) {
                $this->command->info('Existing permission details:');
                $this->command->info('  - ID: ' . $permission->id);
                $this->command->info('  - Attribute: ' . $permission->attribute);
                $this->command->info('  - Keywords: ' . $permission->keywords);
            }
        }

        $this->command->info('');
        $this->command->info('========================================');
        $this->command->info('Report Center Permission Setup Complete');
        $this->command->info('========================================');
        $this->command->info('');
        $this->command->info('Next steps:');
        $this->command->info('1. Assign permission to roles in admin panel (Settings → Roles)');
        $this->command->info('2. Access the sidebar → Report → Report Center');
        $this->command->info('3. Start using the metadata-driven reporting system!');
        $this->command->info('');
    }
}
