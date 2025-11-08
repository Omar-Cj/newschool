<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\MainApp\Entities\School;
use Modules\MainApp\Entities\Package;
use Modules\MainApp\Entities\Subscription;
use App\Enums\Status;

class FixMigrationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // List of migrations that are already applied (tables exist)
        $completedMigrations = [
            '2023_08_10_083847_create_packages_table',
            '2023_08_10_083848_create_schools_table',
            '2023_08_10_095949_create_package_children_table',
            '2023_08_14_100130_create_testimonials_table',
            '2023_08_16_052151_create_contacts_table',
            '2023_08_16_052418_create_subscribes_table',
            '2023_08_16_084459_create_sections_table',
            '2023_08_18_051726_create_frequently_asked_questions_table',
            '2023_08_18_093828_create_settings_table',
            '2023_08_18_102920_create_currencies_table',
            '2023_08_18_103633_create_languages_table',
            '2023_08_18_111510_create_flag_icons_table',
            '2023_08_21_070509_create_subscriptions_table',
            '2023_08_21_102229_create_users_table',
            '2025_05_09_092214_create_jobs_table',
            '2025_05_12_090223_create_failed_jobs_table',
            '2025_09_14_074026_update_batch_id_format_to_sequential',
            '2024_09_13_000001_add_fee_frequency_to_fees_types_table',
        ];

        $this->command->info('Starting migration status fix...');

        // Mark all completed migrations as batch 100
        foreach ($completedMigrations as $migration) {
            $exists = DB::table('migrations')
                ->where('migration', $migration)
                ->exists();

            if (!$exists) {
                DB::table('migrations')->insert([
                    'migration' => $migration,
                    'batch' => 100,
                ]);
                $this->command->info("Marked migration as completed: {$migration}");
            } else {
                $this->command->info("Migration already exists: {$migration}");
            }
        }

        $this->command->line('');
        $this->command->info('Creating default Package...');

        // Create default package if not exists
        $defaultPackage = Package::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Basic Package',
                'price' => 0,
                'per_student_price' => 0,
                'student_limit' => 1000,
                'staff_limit' => 100,
                'duration' => 1,
                'duration_number' => 12,
                'description' => 'Default basic package for schools',
                'popular' => 0,
                'status' => Status::ACTIVE,
            ]
        );

        if ($defaultPackage->wasRecentlyCreated) {
            $this->command->info('Default package created successfully with ID: ' . $defaultPackage->id);
        } else {
            $this->command->info('Default package already exists with ID: ' . $defaultPackage->id);
        }

        $this->command->line('');
        $this->command->info('Creating Main School...');

        // Create main school if not exists
        $mainSchool = School::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Main School',
                'email' => 'admin@mainschool.com',
                'package_id' => $defaultPackage->id,
                'phone' => '+1-000-0000-0000',
                'address' => 'School Address',
                'status' => Status::ACTIVE,
            ]
        );

        if ($mainSchool->wasRecentlyCreated) {
            $this->command->info('Main school created successfully with ID: ' . $mainSchool->id);
            $this->command->info('School Email: ' . $mainSchool->email);
        } else {
            $this->command->info('Main school already exists with ID: ' . $mainSchool->id);
        }

        $this->command->line('');
        $this->command->info('Creating Subscription for Main School...');

        // Create subscription for the school if not exists
        $subscription = Subscription::firstOrCreate(
            [
                'school_id' => $mainSchool->id,
                'package_id' => $defaultPackage->id,
            ],
            [
                'price' => $defaultPackage->price,
                'student_limit' => $defaultPackage->student_limit,
                'staff_limit' => $defaultPackage->staff_limit,
                'expiry_date' => now()->addYear(),
                'status' => 1, // approved
                'payment_status' => 1, // paid
                'features_name' => json_encode([
                    'Academic Management',
                    'Student Information System',
                    'Attendance Tracking',
                    'Fee Management',
                    'Examination System',
                ]),
                'features' => json_encode([
                    'academic_management' => true,
                    'sis' => true,
                    'attendance' => true,
                    'fees' => true,
                    'examination' => true,
                ]),
            ]
        );

        if ($subscription->wasRecentlyCreated) {
            $this->command->info('Subscription created successfully with ID: ' . $subscription->id);
            $this->command->info('Expiry Date: ' . $subscription->expiry_date->format('Y-m-d'));
        } else {
            $this->command->info('Subscription already exists with ID: ' . $subscription->id);
        }

        $this->command->line('');
        $this->command->info('Migration status fix completed successfully!');
        $this->command->comment('Main School Details:');
        $this->command->comment('  - ID: ' . $mainSchool->id);
        $this->command->comment('  - Name: ' . $mainSchool->name);
        $this->command->comment('  - Email: ' . $mainSchool->email);
        $this->command->comment('  - Status: Active');
    }
}
