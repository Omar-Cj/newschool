<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FeatureGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeds feature groups for organizing school management system features.
     * Idempotent - can be run multiple times safely.
     */
    public function run(): void
    {
        $featureGroups = [
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'description' => 'Dashboard and overview features',
                'icon' => 'fa-dashboard',
                'position' => 1,
                'status' => true,
            ],
            [
                'name' => 'Student Information',
                'slug' => 'student_info',
                'description' => 'Student management, categories, promotion, and admission features',
                'icon' => 'fa-users',
                'position' => 2,
                'status' => true,
            ],
            [
                'name' => 'Academic Management',
                'slug' => 'academic',
                'description' => 'Classes, sections, subjects, routines, and classroom management',
                'icon' => 'fa-graduation-cap',
                'position' => 3,
                'status' => true,
            ],
            [
                'name' => 'Fees Management',
                'slug' => 'fees',
                'description' => 'Fee collection, generation, discounts, and financial transactions',
                'icon' => 'fa-money',
                'position' => 4,
                'status' => true,
            ],
            [
                'name' => 'Examination',
                'slug' => 'examination',
                'description' => 'Exam management, marks, grades, homework, and routines',
                'icon' => 'fa-file-text',
                'position' => 5,
                'status' => true,
            ],
            [
                'name' => 'Accounts',
                'slug' => 'accounts',
                'description' => 'Account heads, income, expenses, and financial management',
                'icon' => 'fa-calculator',
                'position' => 6,
                'status' => true,
            ],
            [
                'name' => 'Attendance',
                'slug' => 'attendance',
                'description' => 'Student attendance tracking and reporting',
                'icon' => 'fa-calendar-check-o',
                'position' => 7,
                'status' => true,
            ],
            [
                'name' => 'Reports',
                'slug' => 'reports',
                'description' => 'Academic reports, marksheets, merit lists, and analytics',
                'icon' => 'fa-bar-chart',
                'position' => 8,
                'status' => true,
            ],
            [
                'name' => 'Library',
                'slug' => 'library',
                'description' => 'Library management, books, members, and issue tracking',
                'icon' => 'fa-book',
                'position' => 9,
                'status' => true,
            ],
            [
                'name' => 'Online Examination',
                'slug' => 'online_exam',
                'description' => 'Online exams, question banks, and digital assessment (Premium)',
                'icon' => 'fa-laptop',
                'position' => 10,
                'status' => true,
            ],
            [
                'name' => 'Staff Management',
                'slug' => 'staff',
                'description' => 'User management, roles, departments, and designations',
                'icon' => 'fa-user-circle',
                'position' => 11,
                'status' => true,
            ],
            [
                'name' => 'Website',
                'slug' => 'website',
                'description' => 'Website content, news, events, gallery, and contact management',
                'icon' => 'fa-globe',
                'position' => 12,
                'status' => true,
            ],
            [
                'name' => 'Settings',
                'slug' => 'settings',
                'description' => 'System settings, configurations, and administrative options',
                'icon' => 'fa-cogs',
                'position' => 13,
                'status' => true,
            ],
        ];

        DB::transaction(function () use ($featureGroups) {
            foreach ($featureGroups as $group) {
                DB::table('feature_groups')->updateOrInsert(
                    ['slug' => $group['slug']],
                    array_merge($group, [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );
            }
        });

        $this->command->info('Feature groups seeded successfully.');
    }
}
