<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixUserSchoolIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Fix all users with school_id = NULL to have school_id = 1
     * EXCEPT for users with role_id = 1 (school super admin who should keep NULL for now)
     * and user ID 1 (the main system admin)
     */
    public function run()
    {
        $this->command->info('Starting user school_id fix...');

        // Count users with NULL school_id before fix
        $nullSchoolIdCount = DB::table('users')
            ->whereNull('school_id')
            ->count();

        $this->command->info("Found {$nullSchoolIdCount} users with school_id = NULL");

        // Fix all users with NULL school_id EXCEPT:
        // 1. User ID = 1 (main system admin)
        // 2. Users with role_id = 1 (school super admins - will be fixed after role migration)
        $updated = DB::table('users')
            ->where('id', '!=', 1)  // Exclude main system admin
            ->where(function($query) {
                $query->where('role_id', '!=', 1)
                      ->orWhereNull('role_id');
            })
            ->whereNull('school_id')
            ->update(['school_id' => 1]);

        $this->command->info("Updated {$updated} users to school_id = 1");

        // Show remaining NULL school_id users (should be system admins only)
        $remainingNull = DB::table('users')
            ->whereNull('school_id')
            ->select('id', 'name', 'email', 'role_id')
            ->get();

        $this->command->info("\nRemaining users with school_id = NULL:");
        foreach ($remainingNull as $user) {
            $this->command->line("  - ID: {$user->id}, Name: {$user->name}, Role: {$user->role_id}");
        }

        $this->command->info("\n✅ User school_id fix completed!");
    }
}
