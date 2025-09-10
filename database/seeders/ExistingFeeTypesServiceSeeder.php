<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fees\FeesType;
use App\Models\StudentService;
use App\Models\StudentInfo\Student;
use Carbon\Carbon;

class ExistingFeeTypesServiceSeeder extends Seeder
{
    /**
     * Run the database seeds using existing fee types
     */
    public function run(): void
    {
        $this->command->info('🚀 Assigning Services Based on Existing Fee Types...');

        // Get your existing fee types
        $feeTypes = $this->getExistingFeeTypes();
        
        if ($feeTypes->isEmpty()) {
            $this->command->error('No fee types found. Please create fee types first.');
            return;
        }

        $this->command->info('Found existing fee types:');
        foreach ($feeTypes as $ft) {
            $this->command->line("  - {$ft->name} ($" . number_format($ft->amount, 2) . ")");
        }

        // Add services to students based on existing fee types
        $this->addServicesToStudents($feeTypes);

        $this->command->info('✅ Service assignment completed!');
    }

    /**
     * Get your existing fee types
     */
    private function getExistingFeeTypes()
    {
        return collect([
            // Your existing fee types with their IDs
            FeesType::find(1), // Full Tution Fee Secondary
            FeesType::find(2), // Bus Fee  
            FeesType::find(3), // Full Tution Fee Primary
            FeesType::find(4), // Full Tution Fee KG
        ])->filter(); // Remove any null values
    }

    /**
     * Add services to students based on their academic level
     */
    private function addServicesToStudents($feeTypes): void
    {
        $students = Student::where('status', 1)
            ->with(['sessionStudentDetails.class'])
            ->limit(20) // Process 20 students for testing
            ->get();

        if ($students->isEmpty()) {
            $this->command->warn('No students found for testing');
            return;
        }

        $currentAcademicYear = session('academic_year_id') ?? 1;
        $servicesAssigned = 0;

        foreach ($students as $student) {
            $academicLevel = $this->getAcademicLevel($student);
            $this->command->line("Processing: {$student->first_name} {$student->last_name} (Level: {$academicLevel})");

            // Assign appropriate tuition fee based on academic level
            $tuitionFee = $this->getTuitionFeeForLevel($academicLevel, $feeTypes);
            if ($tuitionFee) {
                if ($this->createStudentService($student, $tuitionFee, $currentAcademicYear)) {
                    $servicesAssigned++;
                    $this->command->line("  ✓ Tuition: {$tuitionFee->name}");
                }
            }

            // Randomly assign bus fee (50% chance)
            $busFee = $feeTypes->where('code', 'Bus')->first();
            if ($busFee && rand(1, 2) == 1) {
                if ($this->createStudentService($student, $busFee, $currentAcademicYear)) {
                    $servicesAssigned++;
                    $this->command->line("  ✓ Transport: {$busFee->name}");
                }
            }
        }

        $this->command->info("📊 Total services assigned: {$servicesAssigned}");
    }

    /**
     * Get appropriate tuition fee based on academic level
     */
    private function getTuitionFeeForLevel(string $level, $feeTypes)
    {
        return match($level) {
            'kg' => $feeTypes->where('code', 'KG Tuition')->first(),
            'primary' => $feeTypes->where('code', 'Primary Tuition')->first(),
            'secondary', 'high_school' => $feeTypes->where('code', 'Secondary Tuition')->first(),
            default => $feeTypes->where('code', 'Primary Tuition')->first()
        };
    }

    /**
     * Create student service subscription
     */
    private function createStudentService(Student $student, FeesType $feeType, int $academicYearId): bool
    {
        // Check if already subscribed
        $existing = StudentService::where('student_id', $student->id)
            ->where('fee_type_id', $feeType->id)
            ->where('academic_year_id', $academicYearId)
            ->exists();

        if ($existing) {
            return false; // Already subscribed
        }

        // Create subscription with existing fee type amount
        StudentService::create([
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'academic_year_id' => $academicYearId,
            'amount' => $feeType->amount, // Use existing amount
            'due_date' => now()->addDays(30), // 30 days from now
            'discount_type' => 'none',
            'discount_value' => 0,
            'final_amount' => $feeType->amount, // Use existing amount
            'subscription_date' => now(),
            'is_active' => true,
            'notes' => 'Assigned based on existing fee types',
            'created_by' => 1 // System user
        ]);

        return true;
    }

    /**
     * Get academic level from student class
     */
    private function getAcademicLevel(Student $student): string
    {
        if (!$student->sessionStudentDetails || !$student->sessionStudentDetails->class) {
            return 'primary';
        }

        $className = $student->sessionStudentDetails->class->name ?? '';
        
        // Extract class level from class name patterns
        if (preg_match('/^Form([1-4])/i', $className, $matches)) {
            // Form1-Form4 are secondary level
            return 'secondary';
        }
        
        if (preg_match('/^Grade([1-8])/i', $className, $matches)) {
            $gradeNumber = (int)$matches[1];
            // Grade1-Grade5 are primary, Grade6-Grade8 are secondary
            return $gradeNumber <= 5 ? 'primary' : 'secondary';
        }
        
        if (preg_match('/^(KG|Kindergarten|Pre)/i', $className)) {
            return 'kg';
        }
        
        if (preg_match('/^(Form5|Form6|Grade9|Grade10|Grade11|Grade12)/i', $className)) {
            return 'high_school';
        }
        
        // Default fallback based on common patterns
        if (stripos($className, 'form') !== false) {
            return 'secondary';
        }
        
        if (stripos($className, 'grade') !== false) {
            return 'primary';
        }
        
        return 'primary';
    }
}
