<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $settings = [
            [
                'name' => 'application_name',
                'value' => 'School Management System',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'footer_text',
                'value' => '© 2025 School Management System. All rights reserved.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'phone',
                'value' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'email',
                'value' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'light_logo',
                'value' => 'backend/uploads/default-images/logo-light.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'dark_logo',
                'value' => 'backend/uploads/default-images/logo-dark.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'favicon',
                'value' => 'backend/uploads/default-images/favicon.ico',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('main_settings')->insert($settings);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('main_settings')->whereIn('name', [
            'application_name',
            'footer_text',
            'phone',
            'email',
            'light_logo',
            'dark_logo',
            'favicon',
        ])->delete();
    }
};
