<?php

namespace Database\Seeders\Staff;

use App\Models\Staff\Staff;
use App\Models\Staff\Department;
use App\Models\Staff\Designation;
use App\Models\User;
use App\Models\Upload;
use App\Models\Gender;
use App\Enums\Status;
use App\Enums\MaritalStatus;
use Illuminate\Database\Seeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GeneralTeacherSeeder extends Seeder
{
    protected ?Command $customCommand = null;
    private array $options = [];
    private int $createdCount = 0;
    private int $skippedCount = 0;
    private int $replacedCount = 0;

    /**
     * Authentic Somaliland Primary Level Teachers (7 subjects)
     */
    private array $primaryTeachers = [
        [
            'first_name' => 'Ahmed',
            'last_name' => 'Hassan',
            'specialization' => 'English',
            'department' => 'Languages',
            'gender' => 'Male',
            'phone' => '252634001001',
            'email' => 'ahmed.hassan@school.edu.so',
            'address' => 'Jigiga Yare, Hargeisa',
            'salary' => 35000
        ],
        [
            'first_name' => 'Fatima',
            'last_name' => 'Osman',
            'specialization' => 'Arabic',
            'department' => 'Languages',
            'gender' => 'Female',
            'phone' => '252634002002',
            'email' => 'fatima.osman@school.edu.so',
            'address' => 'Ahmed Dhagah, Hargeisa',
            'salary' => 32000
        ],
        [
            'first_name' => 'Ibrahim',
            'last_name' => 'Ali',
            'specialization' => 'Islamic Studies',
            'department' => 'Religious Studies',
            'gender' => 'Male',
            'phone' => '252634003003',
            'email' => 'ibrahim.ali@school.edu.so',
            'address' => 'Ga\'an Libah, Burao',
            'salary' => 33000
        ],
        [
            'first_name' => 'Amina',
            'last_name' => 'Mohamed',
            'specialization' => 'Science',
            'department' => 'Sciences',
            'gender' => 'Female',
            'phone' => '252634004004',
            'email' => 'amina.mohamed@school.edu.so',
            'address' => 'Masalaha, Berbera',
            'salary' => 34000
        ],
        [
            'first_name' => 'Omar',
            'last_name' => 'Abdi',
            'specialization' => 'Social Studies',
            'department' => 'Social Sciences',
            'gender' => 'Male',
            'phone' => '252634005005',
            'email' => 'omar.abdi@school.edu.so',
            'address' => 'Dilla, Borama',
            'salary' => 31000
        ],
        [
            'first_name' => 'Khadija',
            'last_name' => 'Yusuf',
            'specialization' => 'Mathematics',
            'department' => 'Mathematics',
            'gender' => 'Female',
            'phone' => '252634006006',
            'email' => 'khadija.yusuf@school.edu.so',
            'address' => 'Sheikh Madar, Sheikh',
            'salary' => 36000
        ],
        [
            'first_name' => 'Yusuf',
            'last_name' => 'Ibrahim',
            'specialization' => 'Somali',
            'department' => 'Languages',
            'gender' => 'Male',
            'phone' => '252634007007',
            'email' => 'yusuf.ibrahim@school.edu.so',
            'address' => 'Wadajir, Hargeisa',
            'salary' => 30000
        ]
    ];

    /**
     * Authentic Somaliland Secondary Level Teachers (10 subjects)
     */
    private array $secondaryTeachers = [
        [
            'first_name' => 'Saeed',
            'last_name' => 'Mohamed',
            'specialization' => 'English',
            'department' => 'Languages',
            'gender' => 'Male',
            'phone' => '252634008008',
            'email' => 'saeed.mohamed@school.edu.so',
            'address' => 'Gacan Libaax, Erigabo',
            'salary' => 42000
        ],
        [
            'first_name' => 'Hodan',
            'last_name' => 'Ahmed',
            'specialization' => 'Mathematics',
            'department' => 'Mathematics',
            'gender' => 'Female',
            'phone' => '252634009009',
            'email' => 'hodan.ahmed@school.edu.so',
            'address' => 'Taleex, Las Anod',
            'salary' => 45000
        ],
        [
            'first_name' => 'Hassan',
            'last_name' => 'Omar',
            'specialization' => 'Chemistry',
            'department' => 'Sciences',
            'gender' => 'Male',
            'phone' => '252634010010',
            'email' => 'hassan.omar@school.edu.so',
            'address' => 'Port Area, Zeila',
            'salary' => 48000
        ],
        [
            'first_name' => 'Mariam',
            'last_name' => 'Ismail',
            'specialization' => 'Physics',
            'department' => 'Sciences',
            'gender' => 'Female',
            'phone' => '252634011011',
            'email' => 'mariam.ismail@school.edu.so',
            'address' => 'October, Hargeisa',
            'salary' => 47000
        ],
        [
            'first_name' => 'Abdi',
            'last_name' => 'Hassan',
            'specialization' => 'Biology',
            'department' => 'Sciences',
            'gender' => 'Male',
            'phone' => '252634012012',
            'email' => 'abdi.hassan@school.edu.so',
            'address' => 'New Hargeisa, Hargeisa',
            'salary' => 46000
        ],
        [
            'first_name' => 'Sahra',
            'last_name' => 'Ali',
            'specialization' => 'Islamic Studies',
            'department' => 'Religious Studies',
            'gender' => 'Female',
            'phone' => '252634013013',
            'email' => 'sahra.ali@school.edu.so',
            'address' => 'Darasalaam, Borama',
            'salary' => 38000
        ],
        [
            'first_name' => 'Ismail',
            'last_name' => 'Abdi',
            'specialization' => 'Somali',
            'department' => 'Languages',
            'gender' => 'Male',
            'phone' => '252634014014',
            'email' => 'ismail.abdi@school.edu.so',
            'address' => 'Mohamed Moge, Hargeisa',
            'salary' => 35000
        ],
        [
            'first_name' => 'Habiba',
            'last_name' => 'Omar',
            'specialization' => 'History',
            'department' => 'Social Sciences',
            'gender' => 'Female',
            'phone' => '252634015015',
            'email' => 'habiba.omar@school.edu.so',
            'address' => 'Shacabka, Burao',
            'salary' => 37000
        ],
        [
            'first_name' => 'Ali',
            'last_name' => 'Mohamed',
            'specialization' => 'Geography',
            'department' => 'Social Sciences',
            'gender' => 'Male',
            'phone' => '252634016016',
            'email' => 'ali.mohamed@school.edu.so',
            'address' => 'Laas Geel, Berbera',
            'salary' => 36000
        ],
        [
            'first_name' => 'Zeinab',
            'last_name' => 'Ahmed',
            'specialization' => 'Arabic',
            'department' => 'Languages',
            'gender' => 'Female',
            'phone' => '252634017017',
            'email' => 'zeinab.ahmed@school.edu.so',
            'address' => 'Salahley, Maroodi Jeeh',
            'salary' => 34000
        ]
    ];

    /**
     * Administrative Staff (3 positions)
     */
    private array $adminTeachers = [
        [
            'first_name' => 'Mohamed',
            'last_name' => 'Hassan',
            'specialization' => 'Administration',
            'department' => 'Administration',
            'designation' => 'Head Teacher',
            'gender' => 'Male',
            'phone' => '252634018018',
            'email' => 'mohamed.hassan@school.edu.so',
            'address' => 'Central District, Hargeisa',
            'salary' => 65000
        ],
        [
            'first_name' => 'Halima',
            'last_name' => 'Ibrahim',
            'specialization' => 'Academic Coordination',
            'department' => 'Administration',
            'designation' => 'Deputy Head Teacher',
            'gender' => 'Female',
            'phone' => '252634019019',
            'email' => 'halima.ibrahim@school.edu.so',
            'address' => 'University District, Hargeisa',
            'salary' => 55000
        ],
        [
            'first_name' => 'Abdullahi',
            'last_name' => 'Osman',
            'specialization' => 'Academic Planning',
            'department' => 'Administration',
            'designation' => 'Academic Coordinator',
            'gender' => 'Male',
            'phone' => '252634020020',
            'email' => 'abdullahi.osman@school.edu.so',
            'address' => 'Jigjiga Yare, Hargeisa',
            'salary' => 50000
        ]
    ];

    public function setCommand(Command $command): void
    {
        $this->customCommand = $command;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    private function info(string $message): void
    {
        if ($this->customCommand) {
            $this->customCommand->info($message);
        }
    }

    private function line(string $message): void
    {
        if ($this->customCommand) {
            $this->customCommand->line($message);
        }
    }

    private function warn(string $message): void
    {
        if ($this->customCommand) {
            $this->customCommand->warn($message);
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $level = $this->options['level'] ?? 'all';
        $count = $this->options['count'] ?? null;
        $branchId = $this->options['branch_id'] ?? 1;
        $isDryRun = $this->options['dry_run'] ?? false;
        $replaceExisting = $this->options['replace_existing'] ?? false;
        $withSpecializations = $this->options['with_specializations'] ?? true;

        // Determine which teachers to seed based on level
        $teachersToSeed = [];
        
        switch ($level) {
            case 'primary':
                $teachersToSeed = $this->primaryTeachers;
                $this->info('👨‍🏫 Seeding Primary Education Teachers (7 teachers)');
                break;
                
            case 'secondary':
                $teachersToSeed = $this->secondaryTeachers;
                $this->info('👩‍🏫 Seeding Secondary Education Teachers (10 teachers)');
                break;

            case 'admin':
                $teachersToSeed = $this->adminTeachers;
                $this->info('👔 Seeding Administrative Staff (3 positions)');
                break;
                
            case 'all':
                $teachersToSeed = array_merge($this->primaryTeachers, $this->secondaryTeachers, $this->adminTeachers);
                $this->info('🏫 Seeding All Education Staff (20 teachers total)');
                $this->line('   👨‍🏫 Primary: 7 teachers');
                $this->line('   👩‍🏫 Secondary: 10 teachers');
                $this->line('   👔 Administrative: 3 staff');
                break;
        }

        // Apply count limit if specified
        if ($count && $count < count($teachersToSeed)) {
            $teachersToSeed = array_slice($teachersToSeed, 0, $count);
            $this->line("   📊 Limited to first {$count} teachers");
        }

        $this->line('');

        // Check for existing teachers
        if (!$isDryRun) {
            $existingTeachers = Staff::whereIn('email', array_column($teachersToSeed, 'email'))->get();
            if ($existingTeachers->isNotEmpty() && !$replaceExisting) {
                $this->warn('⚠️  Found ' . $existingTeachers->count() . ' existing teachers with matching emails:');
                foreach ($existingTeachers as $teacher) {
                    $this->line("   - {$teacher->first_name} {$teacher->last_name} ({$teacher->email})");
                }
                $this->line('   Use --replace-existing=true to replace them');
                $this->line('');
            }
        }

        // Seed teachers
        $this->seedTeachers($teachersToSeed, $branchId, $isDryRun, $replaceExisting, $withSpecializations);

        // Summary
        if ($isDryRun) {
            $this->info("🔍 DRY RUN SUMMARY:");
            $this->line("   📝 Would create: {$this->createdCount} teachers");
            $this->line("   ⏭️  Would skip: {$this->skippedCount} existing teachers");
            if ($replaceExisting) {
                $this->line("   🔄 Would replace: {$this->replacedCount} teachers");
            }
        } else {
            $this->info("✅ SEEDING SUMMARY:");
            $this->line("   📝 Created: {$this->createdCount} teachers");
            $this->line("   ⏭️  Skipped: {$this->skippedCount} existing teachers");
            $this->line("   🔄 Replaced: {$this->replacedCount} teachers");
        }
    }

    private function seedTeachers(array $teachers, int $branchId, bool $isDryRun, bool $replaceExisting, bool $withSpecializations): void
    {
        foreach ($teachers as $teacherData) {
            $existingTeacher = null;
            
            if (!$isDryRun) {
                $existingTeacher = Staff::where('email', $teacherData['email'])->first();
            }

            if ($existingTeacher && !$replaceExisting) {
                $this->line("   ⏭️  Skipped: {$teacherData['first_name']} {$teacherData['last_name']} ({$teacherData['email']}) - already exists");
                $this->skippedCount++;
                continue;
            }

            if ($isDryRun) {
                $this->line("   📝 Would create: {$teacherData['first_name']} {$teacherData['last_name']} ({$teacherData['specialization']} Teacher)");
                $this->createdCount++;
                continue;
            }

            // Get or create required reference data
            $gender = Gender::where('name', $teacherData['gender'])->first();
            $department = $this->getOrCreateDepartment($teacherData['department']);
            $designation = $this->getOrCreateDesignation($teacherData['designation'] ?? 'Teacher');

            if (!$gender) {
                $this->warn("   ⚠️  Warning: Gender '{$teacherData['gender']}' not found, skipping {$teacherData['first_name']} {$teacherData['last_name']}");
                continue;
            }

            // Create User account
            $userData = [
                'name' => $teacherData['first_name'] . ' ' . $teacherData['last_name'],
                'email' => $teacherData['email'],
                'phone' => $teacherData['phone'],
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
                'role_id' => ($teacherData['designation'] ?? 'Teacher') === 'Head Teacher' ? 2 : 5, // Admin role for Head Teacher, Teacher role for others
                'uuid' => Str::uuid(),
                'permissions' => $this->getTeacherPermissions($teacherData['designation'] ?? 'Teacher')
            ];

            // Create or replace teacher
            if ($existingTeacher && $replaceExisting) {
                // Update existing user
                $existingTeacher->user->update($userData);
                
                // Update existing staff
                $existingTeacher->update([
                    'role_id' => $userData['role_id'],
                    'designation_id' => $designation->id,
                    'department_id' => $department->id,
                    'first_name' => $teacherData['first_name'],
                    'last_name' => $teacherData['last_name'],
                    'email' => $teacherData['email'],
                    'gender_id' => $gender->id,
                    'dob' => '1985-01-01', // Default birth date
                    'joining_date' => now()->format('Y-m-d'),
                    'phone' => $teacherData['phone'],
                    'emergency_contact' => $teacherData['phone'],
                    'marital_status' => MaritalStatus::MARRIED,
                    'status' => Status::ACTIVE,
                    'current_address' => $teacherData['address'],
                    'permanent_address' => $teacherData['address'],
                    'basic_salary' => $teacherData['salary'],
                    'upload_documents' => []
                ]);

                $this->line("   🔄 Replaced: {$teacherData['first_name']} {$teacherData['last_name']} ({$teacherData['specialization']}, {$teacherData['email']})");
                $this->replacedCount++;
            } else {
                // Create new user
                $user = User::create($userData);

                // Create new staff
                Staff::create([
                    'user_id' => $user->id,
                    'role_id' => $userData['role_id'],
                    'designation_id' => $designation->id,
                    'department_id' => $department->id,
                    'first_name' => $teacherData['first_name'],
                    'last_name' => $teacherData['last_name'],
                    'email' => $teacherData['email'],
                    'gender_id' => $gender->id,
                    'dob' => '1985-01-01', // Default birth date
                    'joining_date' => now()->format('Y-m-d'),
                    'phone' => $teacherData['phone'],
                    'emergency_contact' => $teacherData['phone'],
                    'marital_status' => MaritalStatus::MARRIED,
                    'status' => Status::ACTIVE,
                    'current_address' => $teacherData['address'],
                    'permanent_address' => $teacherData['address'],
                    'basic_salary' => $teacherData['salary'],
                    'upload_documents' => []
                ]);

                $this->line("   ✅ Created: {$teacherData['first_name']} {$teacherData['last_name']} ({$teacherData['specialization']}, {$teacherData['email']})");
                $this->createdCount++;
            }
        }
    }

    private function getOrCreateDepartment(string $departmentName): Department
    {
        return Department::firstOrCreate(['name' => $departmentName]);
    }

    private function getOrCreateDesignation(string $designationName): Designation
    {
        return Designation::firstOrCreate(['name' => $designationName]);
    }

    private function getTeacherPermissions(string $designation): array
    {
        $basePermissions = [
            'attendance_read',
            'attendance_create',
            'class_routine_read',
            'exam_routine_read',
            'marks_register_read',
            'marks_register_create',
            'marks_register_update',
            'homework_read',
            'homework_create',
            'homework_update',
            'report_marksheet_read',
            'report_attendance_read',
            'report_class_routine_read',
            'report_exam_routine_read'
        ];

        if (in_array($designation, ['Head Teacher', 'Deputy Head Teacher', 'Academic Coordinator'])) {
            $basePermissions = array_merge($basePermissions, [
                'user_read',
                'student_read',
                'classes_read',
                'subject_read',
                'subject_assign_read',
                'exam_assign_read',
                'fees_collect_read',
                'general_settings_read'
            ]);
        }

        return $basePermissions;
    }
}