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
            'father_name' => 'Hassan Abdi',
            'mother_name' => 'Amina Ahmed',
            'specialization' => 'English',
            'department' => 'Languages',
            'staff_id' => 1001,
            'gender' => 'Male',
            'phone' => '252634001001',
            'email' => 'ahmed.hassan@school.edu.so',
            'address' => 'Jigiga Yare, Hargeisa',
            'salary' => 35000
        ],
        [
            'first_name' => 'Fatima',
            'last_name' => 'Osman',
            'father_name' => 'Osman Mohamed',
            'mother_name' => 'Khadija Ibrahim',
            'specialization' => 'Arabic',
            'department' => 'Languages',
            'staff_id' => 1002,
            'gender' => 'Female',
            'phone' => '252634002002',
            'email' => 'fatima.osman@school.edu.so',
            'address' => 'Ahmed Dhagah, Hargeisa',
            'salary' => 32000
        ],
        [
            'first_name' => 'Ibrahim',
            'last_name' => 'Ali',
            'father_name' => 'Ali Yusuf',
            'mother_name' => 'Sahra Hassan',
            'specialization' => 'Islamic Studies',
            'department' => 'Religious Studies',
            'staff_id' => 1003,
            'gender' => 'Male',
            'phone' => '252634003003',
            'email' => 'ibrahim.ali@school.edu.so',
            'address' => 'Ga\'an Libah, Burao',
            'salary' => 33000
        ],
        [
            'first_name' => 'Amina',
            'last_name' => 'Mohamed',
            'father_name' => 'Mohamed Ismail',
            'mother_name' => 'Halima Omar',
            'specialization' => 'Science',
            'department' => 'Sciences',
            'staff_id' => 1004,
            'gender' => 'Female',
            'phone' => '252634004004',
            'email' => 'amina.mohamed@school.edu.so',
            'address' => 'Masalaha, Berbera',
            'salary' => 34000
        ],
        [
            'first_name' => 'Omar',
            'last_name' => 'Abdi',
            'father_name' => 'Abdi Ahmed',
            'mother_name' => 'Mariam Ali',
            'specialization' => 'Social Studies',
            'department' => 'Social Sciences',
            'staff_id' => 1005,
            'gender' => 'Male',
            'phone' => '252634005005',
            'email' => 'omar.abdi@school.edu.so',
            'address' => 'Dilla, Borama',
            'salary' => 31000
        ],
        [
            'first_name' => 'Khadija',
            'last_name' => 'Yusuf',
            'father_name' => 'Yusuf Hassan',
            'mother_name' => 'Habiba Mohamed',
            'specialization' => 'Mathematics',
            'department' => 'Mathematics',
            'staff_id' => 1006,
            'gender' => 'Female',
            'phone' => '252634006006',
            'email' => 'khadija.yusuf@school.edu.so',
            'address' => 'Sheikh Madar, Sheikh',
            'salary' => 36000
        ],
        [
            'first_name' => 'Yusuf',
            'last_name' => 'Ibrahim',
            'father_name' => 'Ibrahim Omar',
            'mother_name' => 'Zeinab Ali',
            'specialization' => 'Somali',
            'department' => 'Languages',
            'staff_id' => 1007,
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
            'father_name' => 'Mohamed Hassan',
            'mother_name' => 'Aisha Abdi',
            'specialization' => 'English',
            'department' => 'Languages',
            'staff_id' => 1008,
            'gender' => 'Male',
            'phone' => '252634008008',
            'email' => 'saeed.mohamed@school.edu.so',
            'address' => 'Gacan Libaax, Erigabo',
            'salary' => 42000
        ],
        [
            'first_name' => 'Hodan',
            'last_name' => 'Ahmed',
            'father_name' => 'Ahmed Ali',
            'mother_name' => 'Fadumo Hassan',
            'specialization' => 'Mathematics',
            'department' => 'Mathematics',
            'staff_id' => 1009,
            'gender' => 'Female',
            'phone' => '252634009009',
            'email' => 'hodan.ahmed@school.edu.so',
            'address' => 'Taleex, Las Anod',
            'salary' => 45000
        ],
        [
            'first_name' => 'Hassan',
            'last_name' => 'Omar',
            'father_name' => 'Omar Ibrahim',
            'mother_name' => 'Safia Mohamed',
            'specialization' => 'Chemistry',
            'department' => 'Sciences',
            'staff_id' => 1010,
            'gender' => 'Male',
            'phone' => '252634010010',
            'email' => 'hassan.omar@school.edu.so',
            'address' => 'Port Area, Zeila',
            'salary' => 48000
        ],
        [
            'first_name' => 'Mariam',
            'last_name' => 'Ismail',
            'father_name' => 'Ismail Yusuf',
            'mother_name' => 'Naima Ahmed',
            'specialization' => 'Physics',
            'department' => 'Sciences',
            'staff_id' => 1011,
            'gender' => 'Female',
            'phone' => '252634011011',
            'email' => 'mariam.ismail@school.edu.so',
            'address' => 'October, Hargeisa',
            'salary' => 47000
        ],
        [
            'first_name' => 'Abdi',
            'last_name' => 'Hassan',
            'father_name' => 'Hassan Mohamed',
            'mother_name' => 'Asha Ibrahim',
            'specialization' => 'Biology',
            'department' => 'Sciences',
            'staff_id' => 1012,
            'gender' => 'Male',
            'phone' => '252634012012',
            'email' => 'abdi.hassan@school.edu.so',
            'address' => 'New Hargeisa, Hargeisa',
            'salary' => 46000
        ],
        [
            'first_name' => 'Sahra',
            'last_name' => 'Ali',
            'father_name' => 'Ali Ahmed',
            'mother_name' => 'Hawa Omar',
            'specialization' => 'Islamic Studies',
            'department' => 'Religious Studies',
            'staff_id' => 1013,
            'gender' => 'Female',
            'phone' => '252634013013',
            'email' => 'sahra.ali@school.edu.so',
            'address' => 'Darasalaam, Borama',
            'salary' => 38000
        ],
        [
            'first_name' => 'Ismail',
            'last_name' => 'Abdi',
            'father_name' => 'Abdi Osman',
            'mother_name' => 'Faduma Hassan',
            'specialization' => 'Somali',
            'department' => 'Languages',
            'staff_id' => 1014,
            'gender' => 'Male',
            'phone' => '252634014014',
            'email' => 'ismail.abdi@school.edu.so',
            'address' => 'Mohamed Moge, Hargeisa',
            'salary' => 35000
        ],
        [
            'first_name' => 'Habiba',
            'last_name' => 'Omar',
            'father_name' => 'Omar Ali',
            'mother_name' => 'Amran Mohamed',
            'specialization' => 'History',
            'department' => 'Social Sciences',
            'staff_id' => 1015,
            'gender' => 'Female',
            'phone' => '252634015015',
            'email' => 'habiba.omar@school.edu.so',
            'address' => 'Shacabka, Burao',
            'salary' => 37000
        ],
        [
            'first_name' => 'Ali',
            'last_name' => 'Mohamed',
            'father_name' => 'Mohamed Yusuf',
            'mother_name' => 'Rukia Hassan',
            'specialization' => 'Geography',
            'department' => 'Social Sciences',
            'staff_id' => 1016,
            'gender' => 'Male',
            'phone' => '252634016016',
            'email' => 'ali.mohamed@school.edu.so',
            'address' => 'Laas Geel, Berbera',
            'salary' => 36000
        ],
        [
            'first_name' => 'Zeinab',
            'last_name' => 'Ahmed',
            'father_name' => 'Ahmed Hassan',
            'mother_name' => 'Cawo Ali',
            'specialization' => 'Arabic',
            'department' => 'Languages',
            'staff_id' => 1017,
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
            'father_name' => 'Hassan Ali',
            'mother_name' => 'Maryam Omar',
            'specialization' => 'Administration',
            'department' => 'Administration',
            'designation' => 'Head Teacher',
            'staff_id' => 1018,
            'gender' => 'Male',
            'phone' => '252634018018',
            'email' => 'mohamed.hassan@school.edu.so',
            'address' => 'Central District, Hargeisa',
            'salary' => 65000
        ],
        [
            'first_name' => 'Halima',
            'last_name' => 'Ibrahim',
            'father_name' => 'Ibrahim Ahmed',
            'mother_name' => 'Shamis Hassan',
            'specialization' => 'Academic Coordination',
            'department' => 'Administration',
            'designation' => 'Deputy Head Teacher',
            'staff_id' => 1019,
            'gender' => 'Female',
            'phone' => '252634019019',
            'email' => 'halima.ibrahim@school.edu.so',
            'address' => 'University District, Hargeisa',
            'salary' => 55000
        ],
        [
            'first_name' => 'Abdullahi',
            'last_name' => 'Osman',
            'father_name' => 'Osman Abdi',
            'mother_name' => 'Hodan Mohamed',
            'specialization' => 'Academic Planning',
            'department' => 'Administration',
            'designation' => 'Academic Coordinator',
            'staff_id' => 1020,
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
            $existingTeachers = Staff::whereIn('staff_id', array_column($teachersToSeed, 'staff_id'))->get();
            if ($existingTeachers->isNotEmpty() && !$replaceExisting) {
                $this->warn('⚠️  Found ' . $existingTeachers->count() . ' existing teachers with matching staff IDs:');
                foreach ($existingTeachers as $teacher) {
                    $this->line("   - {$teacher->first_name} {$teacher->last_name} (ID: {$teacher->staff_id})");
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
                $existingTeacher = Staff::where('staff_id', $teacherData['staff_id'])->first();
            }

            if ($existingTeacher && !$replaceExisting) {
                $this->line("   ⏭️  Skipped: {$teacherData['first_name']} {$teacherData['last_name']} (ID: {$teacherData['staff_id']}) - already exists");
                $this->skippedCount++;
                continue;
            }

            if ($isDryRun) {
                $this->line("   📝 Would create: {$teacherData['first_name']} {$teacherData['last_name']} ({$teacherData['specialization']} Teacher, ID: {$teacherData['staff_id']})");
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
                    'father_name' => $teacherData['father_name'],
                    'mother_name' => $teacherData['mother_name'],
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

                $this->line("   🔄 Replaced: {$teacherData['first_name']} {$teacherData['last_name']} ({$teacherData['specialization']}, ID: {$teacherData['staff_id']})");
                $this->replacedCount++;
            } else {
                // Create new user
                $user = User::create($userData);

                // Create new staff
                Staff::create([
                    'user_id' => $user->id,
                    'staff_id' => $teacherData['staff_id'],
                    'role_id' => $userData['role_id'],
                    'designation_id' => $designation->id,
                    'department_id' => $department->id,
                    'first_name' => $teacherData['first_name'],
                    'last_name' => $teacherData['last_name'],
                    'father_name' => $teacherData['father_name'],
                    'mother_name' => $teacherData['mother_name'],
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

                $this->line("   ✅ Created: {$teacherData['first_name']} {$teacherData['last_name']} ({$teacherData['specialization']}, ID: {$teacherData['staff_id']})");
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