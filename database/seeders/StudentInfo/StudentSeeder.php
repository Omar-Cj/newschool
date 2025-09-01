<?php

namespace Database\Seeders\StudentInfo;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\Staff\Department;
use App\Models\StudentInfo\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\StudentInfo\SessionClassStudent;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Fetch active departments dynamically for balanced distribution
        $departmentIds = Department::active()->pluck('id')->toArray();
        
        if (empty($departmentIds)) {
            Log::error('StudentSeeder: No active departments found. Please seed departments first.');
            throw new \Exception('No active departments available for student assignment. Run DepartmentSeeder first.');
        }
        
        Log::info('StudentSeeder: Starting with ' . count($departmentIds) . ' active departments: [' . implode(',', $departmentIds) . ']');
        
        $studentCount = 0;
        $departmentDistribution = array_fill_keys($departmentIds, 0);
        
        for ($c = 1; $c <= 3; $c++) { // class
            for ($s=1; $s <= 2 ; $s++) { // sections
                for ($i = 1; $i <= 14; $i++) { // students

                    $dob = date('Y-m-d', strtotime("-".$c.$s.$i." day"));
                    $admissionNo = '2023'.$c.$s.$i;
                    
                    // Get student role and its permissions
                    $role = Role::find(6); // student role id
                    if (!$role) {
                        Log::error('StudentSeeder: Student role (ID: 6) not found. Please seed roles first.');
                        throw new \Exception('Student role not found. Run RoleSeeder first.');
                    }
                    
                    $user = User::create([
                        'name'              => 'Student'.$c.$s.$i,
                        'phone'             => '0147852'.$c.$s.$i,
                        'email'             => 'student'.$c.$s.$i.'@gmail.com',
                        'username'          => 'STU-'.$admissionNo,
                        'email_verified_at' => now(),
                        'password'          => Hash::make('123456'),
                        'role_id'           => $role->id,
                        'date_of_birth'     => $dob,
                        "uuid"              => Str::uuid(),
                        'permissions'       => $role->permissions
                    ]);
                    $student = Student::create([
                        'user_id'                 => $user->id,
                        'admission_no'            => $admissionNo,
                        'roll_no'                 => $i,
                        'first_name'              => 'Student',
                        'last_name'               => ''.$c.$s.$i,
                        'mobile'                  => '0147852'.$c.$s.$i,
                        'email'                   => 'student'.$c.$s.$i.'@gmail.com',
                        'dob'                     => $dob,
                        'admission_date'          => date('Y-m-d', strtotime("+".$c.$s.$i." day")),
                        'religion_id'             => rand(1, 3),
                        'department_id'           => $this->getBalancedDepartmentId($departmentIds, $studentCount, $departmentDistribution),
                        'blood_group_id'          => rand(1, 8),
                        'gender_id'               => rand(1, 2),
                        'parent_guardian_id'      => rand(1, 10),
                        'student_category_id'     => rand(1, 2),
                        'status'                  => 1,
                        'previous_school_info'    => 'Cambridge International School, London',
                        'previous_school'         => 1,
                        'emergency_contact'       => '+112345690'.$i+100,
                        'spoken_lang_at_home'     =>  Arr::random(['English', 'Hindi', 'Arabic', 'Spanish']),
                        'nationality'             =>  Arr::random(['Bangladeshi', 'Canadian', 'British', 'American']),
                        'place_of_birth'          =>  Arr::random(['Dhaka Bangladeshi', 'Delhi India', 'New York USA', 'London UK']),
                        'residance_address'          =>  Arr::random(['Dhaka Bangladeshi', 'Delhi India', 'New York USA', 'London UK']),
                        'upload_documents'        => []
                    ]);
                    SessionClassStudent::create([
                        'session_id'                 => setting('session'),
                        'student_id'                 => $student->id,
                        'classes_id'                 => $c,
                        'section_id'                 => $s,
                        'shift_id'                   => rand(1, 3),
                        'roll'                       => $i
                    ]);
                    
                    $studentCount++;
                }
            }
        }
        
        // Log final department distribution results
        Log::info('StudentSeeder: Completed seeding ' . $studentCount . ' students with balanced department distribution:', $departmentDistribution);
        
        // Display distribution summary
        $this->command->info('Student-Department Distribution Summary:');
        foreach ($departmentDistribution as $deptId => $count) {
            $department = Department::find($deptId);
            $this->command->line("  Department {$deptId} ({$department->name}): {$count} students");
        }
    }
    
    /**
     * Get balanced department ID using cycling distribution algorithm
     * Ensures even distribution of students across all active departments
     */
    private function getBalancedDepartmentId(array $departmentIds, int $studentCount, array &$departmentDistribution): int
    {
        // Use modulo operation to cycle through departments evenly
        $departmentIndex = $studentCount % count($departmentIds);
        $selectedDepartmentId = $departmentIds[$departmentIndex];
        
        // Track distribution for logging
        $departmentDistribution[$selectedDepartmentId]++;
        
        return $selectedDepartmentId;
    }
}
