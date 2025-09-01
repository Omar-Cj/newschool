<?php

namespace Database\Seeders\StudentInfo;

use App\Models\User;
use App\Models\Gender;
use App\Models\Religion;
use App\Models\BloodGroup;
use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\StudentInfo\SessionClassStudent;
use App\Models\Academic\Classes;
use App\Models\Session;
use App\Models\Academic\Shift;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;

class SomalilandStudentSeeder extends Seeder
{
    protected ?Command $customCommand = null;
    private array $options = [];
    private array $createdParents = [];
    private int $createdStudentCount = 0;
    private int $createdParentCount = 0;
    private array $availableShiftIds = [];

    /**
     * Authentic Somaliland demographic data
     */
    private array $somaliMaleNames = [
        'Ahmed', 'Mohamed', 'Abdi', 'Farah', 'Omar', 'Hassan', 'Ali', 'Yusuf',
        'Ibrahim', 'Abdullahi', 'Ismail', 'Osman', 'Abdirashid', 'Mustafe',
        'Saeed', 'Jama', 'Dahir', 'Cabdi', 'Maxamed', 'Cumar', 'Cali', 'Axmed'
    ];

    private array $somaliFemaleNames = [
        'Amina', 'Hodan', 'Sahra', 'Faduma', 'Halima', 'Khadija', 'Maryan',
        'Asli', 'Ayan', 'Warsan', 'Caasha', 'Faadumo', 'Xalimo', 'Saynab',
        'Canab', 'Fowsiya', 'Ikraan', 'Sucaad', 'Naima', 'Zamzam', 'Hibo'
    ];

    private array $somaliFamilyNames = [
        'Hersi', 'Mohamed', 'Ahmed', 'Ali', 'Hassan', 'Osman', 'Farah',
        'Abdi', 'Omar', 'Yusuf', 'Ibrahim', 'Ismail', 'Jama', 'Dahir',
        'Hussein', 'Abdullahi', 'Saeed', 'Mustafe', 'Cabdi', 'Maxamed'
    ];

    private array $somalilandCities = [
        'Hargeisa', 'Berbera', 'Burao', 'Borama', 'Erigavo', 'Sheikh',
        'Las Anod', 'Gabiley', 'Wajaale', 'Zeila', 'Caynabo', 'Oodweyne'
    ];

    private array $somalilandDistricts = [
        'October', 'Ahmed Dhagah', 'Ga\'an Libah', 'Ibrahim Kodbuur',
        'Mohamed Moge', 'Gacan Libaax', 'Shacab', 'Daami', 'Jigaale',
        'Maxamed Haybe', 'Cabdi Bile', 'Kalabaydh', 'Maroodi Jeex'
    ];

    private array $phonesPrefixes = ['+252 63', '+252 64', '+252 65'];

    private array $somaliProfessions = [
        'Teacher', 'Trader', 'Driver', 'Farmer', 'Shopkeeper', 'Civil Servant',
        'Healthcare Worker', 'Engineer', 'Accountant', 'Business Owner',
        'Mechanic', 'Tailor', 'Chef', 'Security Guard', 'Cleaner'
    ];

    private array $educationHistory = [
        'Hargeisa Primary School',
        'Burao Secondary School',
        'Ahmed Dhagah Elementary',
        'Sheikh Technical School',
        'Berbera Intermediate School',
        'Borama Community School',
        'Erigavo Modern School',
        'International School of Somaliland'
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

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $count = $this->options['count'] ?? 50;
        $withParents = $this->options['with_parents'] ?? true;
        $branchId = $this->options['branch_id'] ?? 1;
        $isDryRun = $this->options['dry_run'] ?? false;

        $this->info('🇸🇴 Generating authentic Somaliland student data...');

        // Get available classes for the branch
        $availableClasses = Classes::where('branch_id', $branchId)
            ->where('status', 1)
            ->get();

        if ($availableClasses->isEmpty()) {
            throw new \Exception("No active classes found for branch {$branchId}");
        }

        // Get available shift IDs
        $this->availableShiftIds = Shift::where('branch_id', $branchId)
            ->where('status', 1)
            ->pluck('id')
            ->toArray();

        if (empty($this->availableShiftIds)) {
            throw new \Exception("No active shifts found for branch {$branchId}");
        }

        $this->line("   📚 Found {$availableClasses->count()} classes");
        $this->line("   ⏰ Found " . count($this->availableShiftIds) . " shifts: " . implode(', ', $this->availableShiftIds));

        // Get current session
        $currentSession = Session::first();
        if (!$currentSession) {
            throw new \Exception('No academic session found');
        }

        // Generate parents first (if requested)
        $parentIds = [];
        if ($withParents) {
            $parentIds = $this->generateParents($count, $isDryRun);
        }

        // Generate students distributed across classes
        $this->generateStudents($count, $availableClasses, $parentIds, $currentSession, $isDryRun);

        if (!$isDryRun) {
            $this->info("✅ Successfully created {$this->createdStudentCount} Somaliland students");
            if ($withParents) {
                $this->info("✅ Successfully created {$this->createdParentCount} parent/guardian records");
            }
        } else {
            $this->info("🔍 DRY RUN: Would have created {$count} students with authentic Somaliland data");
        }
    }

    private function generateParents(int $studentCount, bool $isDryRun): array
    {
        // Generate approximately half as many parents as students (siblings share parents)
        $parentCount = max(1, (int) ceil($studentCount / 2.5));
        
        $this->info("👨‍👩‍👧‍👦 Generating {$parentCount} parent/guardian records...");
        
        $parentIds = [];

        for ($i = 0; $i < $parentCount; $i++) {
            $fatherName = Arr::random($this->somaliMaleNames) . ' ' . Arr::random($this->somaliFamilyNames);
            $motherName = Arr::random($this->somaliFemaleNames) . ' ' . Arr::random($this->somaliFamilyNames);
            $guardianName = $fatherName; // Father is usually the guardian
            $city = Arr::random($this->somalilandCities);
            $district = Arr::random($this->somalilandDistricts);

            if ($isDryRun) {
                $this->line("   👤 Would create parent: {$guardianName} from {$city}");
                $parentIds[] = $i + 1000; // Mock ID for dry run
                continue;
            }

            // Create user account for parent
            $parentUser = User::create([
                'name' => $guardianName,
                'email' => $this->generateEmail($guardianName, 'parent'),
                'phone' => $this->generatePhoneNumber(),
                'password' => Hash::make('123456'),
                'role_id' => 7, // Parent role
                'date_of_birth' => now()->subYears(rand(25, 45))->format('Y-m-d'),
                'uuid' => Str::uuid(),
                'permissions' => [],
                'status' => 1
            ]);

            // Create parent guardian record
            $parent = ParentGuardian::create([
                'user_id' => $parentUser->id,
                'father_name' => $fatherName,
                'father_mobile' => $this->generatePhoneNumber(),
                'father_profession' => Arr::random($this->somaliProfessions),
                'father_nationality' => 'Somaliland',
                'mother_name' => $motherName,
                'mother_mobile' => $this->generatePhoneNumber(),
                'mother_profession' => Arr::random($this->somaliProfessions),
                'guardian_name' => $guardianName,
                'guardian_email' => $parentUser->email,
                'guardian_mobile' => $parentUser->phone,
                'guardian_profession' => Arr::random($this->somaliProfessions),
                'guardian_relation' => 'Father',
                'guardian_address' => "{$district} District, {$city}, Somaliland",
                'guardian_place_of_work' => "{$city} " . Arr::random(['Office', 'Market', 'School', 'Hospital', 'Ministry']),
                'guardian_position' => Arr::random($this->somaliProfessions),
                'status' => 1
            ]);

            $parentIds[] = $parent->id;
            $this->createdParentCount++;
            
            if ($i % 10 === 0) {
                $this->line("   ✓ Created {$i}/{$parentCount} parent records");
            }
        }

        return $parentIds;
    }

    private function generateStudents(int $count, $availableClasses, array $parentIds, $currentSession, bool $isDryRun): void
    {
        $this->info("👥 Generating {$count} Somaliland student records...");

        // Create age mappings for realistic grade assignments
        $gradeAgeMap = $this->getGradeAgeMapping();

        for ($i = 0; $i < $count; $i++) {
            $gender = rand(1, 2); // 1 = Male, 2 = Female
            $islamReligionId = Religion::where('name', 'Islam')->first()?->id ?? 1;
            
            // Select class (distribute evenly)
            $selectedClass = $availableClasses[$i % $availableClasses->count()];
            $appropriateAge = $this->getAgeForGrade($selectedClass->name, $gradeAgeMap);
            
            // Generate authentic Somali name
            $firstName = $gender === 1 
                ? Arr::random($this->somaliMaleNames)
                : Arr::random($this->somaliFemaleNames);
            $lastName = Arr::random($this->somaliFamilyNames);
            $fullName = "{$firstName} {$lastName}";
            
            $dob = now()->subYears($appropriateAge)->subDays(rand(1, 365));
            $city = Arr::random($this->somalilandCities);
            
            if ($isDryRun) {
                $this->line("   👤 Would create student: {$fullName}, Age: {$appropriateAge}, Class: {$selectedClass->name}, City: {$city}");
                continue;
            }

            // Create user account
            $user = User::create([
                'name' => $fullName,
                'email' => $this->generateEmail($fullName, 'student'),
                'phone' => $this->generatePhoneNumber(),
                'password' => Hash::make('123456'),
                'role_id' => 6, // Student role
                'date_of_birth' => $dob->format('Y-m-d'),
                'uuid' => Str::uuid(),
                'gender' => $gender,
                'permissions' => [],
                'status' => 1
            ]);

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'admission_no' => $this->generateAdmissionNumber($selectedClass->id, $i),
                'roll_no' => ($i % 30) + 1, // Roll numbers 1-30 per class
                'first_name' => $firstName,
                'last_name' => $lastName,
                'mobile' => $user->phone,
                'email' => $user->email,
                'dob' => $dob->format('Y-m-d'),
                'admission_date' => now()->subMonths(rand(1, 12))->format('Y-m-d'),
                'religion_id' => $islamReligionId,
                'blood_group_id' => rand(1, 8),
                'gender_id' => $gender,
                'parent_guardian_id' => !empty($parentIds) ? Arr::random($parentIds) : null,
                'student_category_id' => rand(1, 2),
                'nationality' => 'Somaliland',
                'place_of_birth' => $city . ', Somaliland',
                'spoken_lang_at_home' => 'Somali',
                'residance_address' => Arr::random($this->somalilandDistricts) . " District, {$city}, Somaliland",
                'previous_school_info' => Arr::random($this->educationHistory),
                'previous_school' => rand(0, 1),
                'health_status' => Arr::random(['Excellent', 'Good', 'Fair']),
                'rank_in_family' => rand(1, 5),
                'siblings' => rand(0, 4),
                'cpr_no' => $this->generateCprNumber(),
                'status' => 1,
                'upload_documents' => []
            ]);

            // Create session class student (current enrollment)
            SessionClassStudent::create([
                'session_id' => $currentSession->id,
                'student_id' => $student->id,
                'classes_id' => $selectedClass->id,
                'section_id' => $this->getSectionForClass($selectedClass->name),
                'shift_id' => Arr::random($this->availableShiftIds),
                'roll' => ($i % 30) + 1
            ]);

            $this->createdStudentCount++;
            
            if ($i > 0 && $i % 10 === 0) {
                $this->line("   ✓ Created {$i}/{$count} student records");
            }
        }
    }

    private function generateEmail(string $name, string $type): string
    {
        $cleanName = strtolower(str_replace(' ', '', $name));
        $cleanName = preg_replace('/[^a-z0-9]/', '', $cleanName);
        $random = rand(100, 999);
        
        return "{$cleanName}{$random}@{$type}.somaliland.edu";
    }

    private function generatePhoneNumber(): string
    {
        $prefix = Arr::random($this->phonesPrefixes);
        $number = rand(1000000, 9999999);
        
        return "{$prefix} {$number}";
    }

    private function generateAdmissionNumber(int $classId, int $sequence): string
    {
        $year = date('Y');
        return "{$year}{$classId}" . str_pad($sequence + 1, 3, '0', STR_PAD_LEFT);
    }

    private function generateCprNumber(): string
    {
        return 'SL' . rand(100000000, 999999999);
    }

    private function getGradeAgeMapping(): array
    {
        return [
            'Grade-1' => 6,
            'Grade-2' => 7,
            'Grade-3' => 8,
            'Grade-4' => 9,
            'Grade-5' => 10,
            'Grade-6' => 11,
            'Grade-7' => 12,
            'Grade-8' => 13,
            'Form-1' => 14
        ];
    }

    private function getAgeForGrade(string $className, array $gradeAgeMap): int
    {
        // Extract grade level from class name (e.g., "Grade-1A" -> "Grade-1")
        foreach ($gradeAgeMap as $grade => $baseAge) {
            if (strpos($className, $grade) !== false) {
                return $baseAge + rand(0, 1); // Add 0-1 years variation
            }
        }
        
        return 10; // Default age if no match
    }

    private function getSectionForClass(string $className): int
    {
        // Extract section from class name (e.g., "Grade-1A" -> 1, "Grade-1B" -> 2)
        if (preg_match('/([A-Z])$/', $className, $matches)) {
            return ord($matches[1]) - ord('A') + 1;
        }
        
        return 1; // Default to section 1
    }
}