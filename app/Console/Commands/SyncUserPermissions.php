<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync
                            {--school= : Sync only users from specific school ID}
                            {--role= : Sync only users with specific role ID}
                            {--dry-run : Preview changes without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync user permissions from their assigned roles';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $schoolId = $this->option('school');
        $roleId = $this->option('role');
        $dryRun = $this->option('dry-run');

        // Build query
        $query = User::query()
            ->whereNotNull('school_id')
            ->whereNotNull('role_id');

        if ($schoolId) {
            $query->where('school_id', $schoolId);
            $this->info("Filtering by school ID: {$schoolId}");
        }

        if ($roleId) {
            $query->where('role_id', $roleId);
            $this->info("Filtering by role ID: {$roleId}");
        }

        $users = $query->with('role')->get();
        $totalUsers = $users->count();

        if ($totalUsers === 0) {
            $this->warn('No users found matching the criteria.');
            return Command::SUCCESS;
        }

        $this->info("Found {$totalUsers} users to sync.");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be saved.');
            $this->newLine();
        }

        // Group users by role for summary
        $roleStats = [];
        $updated = 0;
        $skipped = 0;

        $bar = $this->output->createProgressBar($totalUsers);
        $bar->start();

        foreach ($users as $user) {
            if (!$user->role) {
                $skipped++;
                $bar->advance();
                continue;
            }

            $roleName = $user->role->name ?? "Role {$user->role_id}";
            $rolePermissions = $user->role->permissions ?? [];

            // Track stats
            if (!isset($roleStats[$roleName])) {
                $roleStats[$roleName] = 0;
            }
            $roleStats[$roleName]++;

            // Update user permissions
            if (!$dryRun) {
                $user->permissions = $rolePermissions;
                $user->save();
            }

            $updated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Display summary
        $this->info('Sync Summary:');
        $this->table(
            ['Role', 'Users Synced'],
            collect($roleStats)->map(function ($count, $role) {
                return [$role, $count];
            })->toArray()
        );

        $this->newLine();
        $this->info("Total users synced: {$updated}");

        if ($skipped > 0) {
            $this->warn("Users skipped (no role): {$skipped}");
        }

        if ($dryRun) {
            $this->newLine();
            $this->warn('This was a dry run. Run without --dry-run to apply changes.');
        } else {
            $this->info('Permissions synced successfully!');
        }

        return Command::SUCCESS;
    }
}
