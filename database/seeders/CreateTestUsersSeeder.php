<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateTestUsersSeeder extends Seeder
{
    public function run()
    {
        // System Admin (school_id = null)
        DB::table('users')->insertOrIgnore([
            'name' => 'System Admin',
            'email' => 'admin@system.test',
            'password' => Hash::make('password'),
            'role_id' => 1, // SUPERADMIN
            'school_id' => null,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // School Admin (school_id = 1)
        DB::table('users')->insertOrIgnore([
            'name' => 'School Admin',
            'email' => 'school@test.com',
            'password' => Hash::make('password'),
            'role_id' => 2, // ADMIN
            'school_id' => 1,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // School Teacher (school_id = 1)
        DB::table('users')->insertOrIgnore([
            'name' => 'Test Teacher',
            'email' => 'teacher@test.com',
            'password' => Hash::make('password'),
            'role_id' => 5, // TEACHER
            'school_id' => 1,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}