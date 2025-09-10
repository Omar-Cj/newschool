<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fees\FeesType;
use App\Models\StudentService;
use App\Models\StudentInfo\Student;
use Carbon\Carbon;

class QuickFeeTestSeeder extends Seeder
{
    /**
     * Run the database seeds for quick testing
     */
    public function run(): void
    {
        $this->command->info('🚀 Quick Fee Test Seeder Starting...');

        // Create essential fee types if they don't exist
        $this->createEssentialFeeTypes();
        
        // Add services to first 10 students for quick testing
        $this->addServicesToStudents();

        $this->command->info('✅ Quick Fee Test Seeder Completed!');
    }

    /**
     * Create essential fee types for testing
     */
    private function createEssentialFeeTypes(): void
    {
        $essentialFeeTypes = [
            [
                'name' => 'Primary Tuition',
                'code' => 'PRI_TUI',
                'description' => 'Primary school tuition fee',
                'academic_level' => 'primary',
                'category' => 'academic',
                'amount' => 200.00,
                'due_date_offset' => 30,
                'is_mandatory_for_level' => true,
                'status' => 1
            ],
            [
                'name' => 'Secondary Tuition',
                'code' => 'SEC_TUI',
                'description' => 'Secondary school tuition fee',
                'academic_level' => 'secondary',
                'category' => 'academic',
                'amount' => 300.00,
                'due_date_offset' => 30,
                'is_mandatory_for_level' => true,
                'status' => 1
            ],
            [
                'name' => 'Bus Service',
                'code' => 'BUS',
                'description' => 'School bus transportation',
                'academic_level' => 'all',
                'category' => 'transport',
                'amount' => 50.00,
                'due_date_offset' => 15,
                'is_mandatory_for_level' => false,
                'status' => 1
            ],
            [
                'name' => 'Lunch Program',
                'code' => 'LUNCH',
                'description' => 'Daily lunch meal service',
                'academic_level' => 'all',
                'category' => 'meal',
                'amount' => 75.00,
                'due_date_offset' => 7,
                'is_mandatory_for_level' => false,
                'status' => 1
            ]
        ];

        foreach ($essentialFeeTypes as $feeType) {
            FeesType::updateOrCreate(
                ['code' => $feeType['code']],
                $feeType
            );
            $this->command->line("✓ {$feeType['name']}");
        }
    }

    /**
     * Add services to first 10 students
     */
    private function addServicesToStudents(): void
    {
        $students = Student::where('status', 1)
            ->with('classes')
            ->limit(10)
            ->get();

        if ($students->isEmpty()) {
            $this->command->warn('No students found for testing');
            return;
        }

        $currentAcademicYear = session('academic_year_id') ?? 1;

        foreach ($students as $index => $student) {
            $academicLevel = $this->getAcademicLevel($student);
            
            // Get mandatory service for this level
            $mandatoryService = FeesType::where('academic_level', $academicLevel)
                ->where('is_mandatory_for_level', true)
                ->first();

            if ($mandatoryService) {
                $this->createStudentService($student, $mandatoryService, $currentAcademicYear);
            }

            // Add one optional service alternately
            if ($index % 2 == 0) {
                $optionalService = FeesType::where('is_mandatory_for_level', false)
                    ->where(function($q) use ($academicLevel) {
                        $q->where('academic_level', $academicLevel)
                          ->orWhere('academic_level', 'all');
                    })
                    ->first();

                if ($optionalService) {
                    $this->createStudentService($student, $optionalService, $currentAcademicYear);
                }
            }

            $this->command->line("✓ Services added for: {$student->first_name} {$student->last_name}");
        }
    }

    /**
     * Create student service subscription
     */
    private function createStudentService(Student $student, FeesType $feeType, int $academicYearId): void
    {
        StudentService::updateOrCreate(
            [
                'student_id' => $student->id,
                'fee_type_id' => $feeType->id,
                'academic_year_id' => $academicYearId
            ],
            [
                'amount' => $feeType->amount,
                'due_date' => now()->addDays($feeType->due_date_offset),
                'discount_type' => 'none',
                'discount_value' => 0,
                'final_amount' => $feeType->amount,
                'subscription_date' => now(),
                'is_active' => true,
                'notes' => 'Test seeder subscription',
                'created_by' => 1
            ]
        );
    }

    /**
     * Get academic level from student class
     */
    private function getAcademicLevel(Student $student): string
    {
        if (!$student->classes) {
            return 'primary';
        }

        $classNumber = $student->classes->numeric_name ?? 1;
        
        if ($classNumber <= 5) return 'primary';
        if ($classNumber <= 10) return 'secondary';
        if ($classNumber <= 12) return 'high_school';
        
        return 'primary';
    }
}
