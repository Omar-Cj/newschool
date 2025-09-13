<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Fees\FeesType;
use App\Models\StudentService;
use App\Models\StudentInfo\Student;
use App\Services\StudentServiceManager;
use App\Services\AcademicLevelDetectionService;
use Carbon\Carbon;

class EnhancedFeeSystemTestSeeder extends Seeder
{
    private StudentServiceManager $serviceManager;
    private AcademicLevelDetectionService $levelService;

    public function __construct()
    {
        $this->serviceManager = app(StudentServiceManager::class);
        $this->levelService = app(AcademicLevelDetectionService::class);
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Enhanced Fee System Test Data Seeding...');

        // Step 1: Create sample fee types with enhanced fields
        $this->createSampleFeeTypes();

        // Step 2: Add services to existing students
        $this->addServicesToExistingStudents();

        $this->command->info('✅ Enhanced Fee System Test Data Seeding Completed!');
    }

    /**
     * Create sample fee types for testing
     */
    private function createSampleFeeTypes(): void
    {
        $this->command->info('📝 Creating sample fee types...');

        $sampleFeeTypes = [
            // Academic Services - Mandatory
            [
                'name' => 'KG Tuition Fee',
                'code' => 'KG_TUITION',
                'description' => 'Tuition fee for Kindergarten students',
                'academic_level' => 'kg',
                'category' => 'academic',
                'amount' => 150.00,
                'due_date_offset' => 15,
                'is_mandatory_for_level' => true,
                'status' => 1
            ],
            [
                'name' => 'Primary Tuition Fee',
                'code' => 'PRI_TUITION',
                'description' => 'Tuition fee for Primary school students (Classes 1-5)',
                'academic_level' => 'primary',
                'category' => 'academic',
                'amount' => 200.00,
                'due_date_offset' => 15,
                'is_mandatory_for_level' => true,
                'status' => 1
            ],
            [
                'name' => 'Secondary Tuition Fee',
                'code' => 'SEC_TUITION',
                'description' => 'Tuition fee for Secondary school students (Classes 6-10)',
                'academic_level' => 'secondary',
                'category' => 'academic',
                'amount' => 300.00,
                'due_date_offset' => 15,
                'is_mandatory_for_level' => true,
                'status' => 1
            ],
            [
                'name' => 'High School Tuition Fee',
                'code' => 'HS_TUITION',
                'description' => 'Tuition fee for High School students (Classes 11-12)',
                'academic_level' => 'high_school',
                'category' => 'academic',
                'amount' => 400.00,
                'due_date_offset' => 15,
                'is_mandatory_for_level' => true,
                'status' => 1
            ],

            // Academic Services - Optional
            [
                'name' => 'Library Access Fee',
                'code' => 'LIBRARY',
                'description' => 'Access to library resources and study materials',
                'academic_level' => 'all',
                'category' => 'academic',
                'amount' => 25.00,
                'due_date_offset' => 30,
                'is_mandatory_for_level' => false,
                'status' => 1
            ],
            [
                'name' => 'Laboratory Fee',
                'code' => 'LAB_FEE',
                'description' => 'Laboratory usage and materials fee',
                'academic_level' => 'secondary',
                'category' => 'academic',
                'amount' => 50.00,
                'due_date_offset' => 30,
                'is_mandatory_for_level' => false,
                'status' => 1
            ],

            // Transportation Services
            [
                'name' => 'Bus Route A',
                'code' => 'BUS_A',
                'description' => 'Transportation service for Route A (Downtown)',
                'academic_level' => 'all',
                'category' => 'transport',
                'amount' => 80.00,
                'due_date_offset' => 10,
                'is_mandatory_for_level' => false,
                'status' => 1
            ],
            [
                'name' => 'Bus Route B',
                'code' => 'BUS_B',
                'description' => 'Transportation service for Route B (Suburbs)',
                'academic_level' => 'all',
                'category' => 'transport',
                'amount' => 100.00,
                'due_date_offset' => 10,
                'is_mandatory_for_level' => false,
                'status' => 1
            ],

            // Meal Services
            [
                'name' => 'Full Meal Plan',
                'code' => 'MEAL_FULL',
                'description' => 'Breakfast, lunch, and snacks included',
                'academic_level' => 'all',
                'category' => 'meal',
                'amount' => 120.00,
                'due_date_offset' => 5,
                'is_mandatory_for_level' => false,
                'status' => 1
            ],
            [
                'name' => 'Lunch Only Plan',
                'code' => 'MEAL_LUNCH',
                'description' => 'Lunch meals only',
                'academic_level' => 'all',
                'category' => 'meal',
                'amount' => 60.00,
                'due_date_offset' => 5,
                'is_mandatory_for_level' => false,
                'status' => 1
            ],

            // Activity Services
            [
                'name' => 'Sports Activities',
                'code' => 'SPORTS',
                'description' => 'Access to sports facilities and activities',
                'academic_level' => 'primary',
                'category' => 'activity',
                'amount' => 40.00,
                'due_date_offset' => 45,
                'is_mandatory_for_level' => false,
                'status' => 1
            ],
            [
                'name' => 'Arts & Crafts',
                'code' => 'ARTS',
                'description' => 'Arts, crafts, and creative activities',
                'academic_level' => 'kg',
                'category' => 'activity',
                'amount' => 30.00,
                'due_date_offset' => 45,
                'is_mandatory_for_level' => false,
                'status' => 1
            ],

            // Other Services
            [
                'name' => 'Annual Exam Fee',
                'code' => 'EXAM_ANNUAL',
                'description' => 'Annual examination processing fee',
                'academic_level' => 'all',
                'category' => 'other',
                'amount' => 35.00,
                'due_date_offset' => 60,
                'is_mandatory_for_level' => false,
                'status' => 1
            ]
        ];

        foreach ($sampleFeeTypes as $feeType) {
            // Check if fee type already exists
            $existing = FeesType::where('code', $feeType['code'])->first();
            if (!$existing) {
                FeesType::create($feeType);
                $this->command->line("✓ Created: {$feeType['name']} ({$feeType['code']})");
            } else {
                $this->command->line("⚠ Skipped: {$feeType['name']} - already exists");
            }
        }

        $this->command->info('📝 Sample fee types creation completed!');
    }

    /**
     * Add services to existing students for testing
     */
    private function addServicesToExistingStudents(): void
    {
        $this->command->info('👥 Adding services to existing students...');

        // Get all active students
        $students = Student::where('status', 1)
            ->with(['classes', 'section'])
            ->limit(50) // Limit for testing purposes
            ->get();

        if ($students->isEmpty()) {
            $this->command->warn('⚠ No active students found. Please add students first.');
            return;
        }

        $this->command->info("Found {$students->count()} students for service assignment");

        $currentAcademicYear = session('academic_year_id') ?? 1;
        $servicesAssigned = 0;
        $studentsProcessed = 0;

        foreach ($students as $student) {
            try {
                $studentsProcessed++;
                
                // Determine student's academic level
                $academicLevel = $this->determineAcademicLevel($student);
                
                $this->command->line("Processing: {$student->first_name} {$student->last_name} (Level: {$academicLevel})");

                // 1. Auto-subscribe to mandatory services
                $mandatoryServices = FeesType::active()
                    ->mandatoryForLevel($academicLevel)
                    ->get();

                foreach ($mandatoryServices as $service) {
                    $subscription = $this->subscribeStudentToService($student, $service, $currentAcademicYear, true);
                    if ($subscription) {
                        $servicesAssigned++;
                        $this->command->line("  ✓ Mandatory: {$service->name}");
                    }
                }

                // 2. Randomly assign some optional services (for testing variety)
                $optionalServices = FeesType::active()
                    ->optionalForLevel($academicLevel)
                    ->get();

                $numberOfOptionalServices = rand(1, min(3, $optionalServices->count())); // Assign 1-3 optional services
                $selectedOptional = $optionalServices->random(min($numberOfOptionalServices, $optionalServices->count()));

                foreach ($selectedOptional as $service) {
                    $subscription = $this->subscribeStudentToService($student, $service, $currentAcademicYear, false);
                    if ($subscription) {
                        $servicesAssigned++;
                        
                        // Randomly apply discounts to some services for testing
                        if (rand(1, 4) == 1) { // 25% chance of discount
                            $this->applyRandomDiscount($subscription);
                            $this->command->line("  ✓ Optional: {$service->name} (with discount)");
                        } else {
                            $this->command->line("  ✓ Optional: {$service->name}");
                        }
                    }
                }

            } catch (\Exception $e) {
                $this->command->error("Error processing student {$student->id}: " . $e->getMessage());
            }
        }

        $this->command->info("👥 Service assignment completed!");
        $this->command->info("📊 Summary:");
        $this->command->line("   Students Processed: {$studentsProcessed}");
        $this->command->line("   Services Assigned: {$servicesAssigned}");
    }

    /**
     * Subscribe student to a service
     */
    private function subscribeStudentToService(Student $student, FeesType $service, int $academicYearId, bool $isMandatory): ?StudentService
    {
        // Check if already subscribed
        $existing = StudentService::where('student_id', $student->id)
            ->where('fee_type_id', $service->id)
            ->where('academic_year_id', $academicYearId)
            ->first();

        if ($existing) {
            return null; // Already subscribed
        }

        // Create subscription
        return StudentService::create([
            'student_id' => $student->id,
            'fee_type_id' => $service->id,
            'academic_year_id' => $academicYearId,
            'amount' => $service->amount,
            'due_date' => $this->calculateDueDate($service->due_date_offset),
            'discount_type' => 'none',
            'discount_value' => 0,
            'final_amount' => $service->amount,
            'subscription_date' => now(),
            'is_active' => true,
            'notes' => $isMandatory ? 'Auto-assigned mandatory service' : 'Test optional service',
            'created_by' => 1 // System user
        ]);
    }

    /**
     * Apply random discount for testing purposes
     */
    private function applyRandomDiscount(StudentService $subscription): void
    {
        $discountTypes = ['percentage', 'fixed'];
        $discountType = $discountTypes[array_rand($discountTypes)];

        switch ($discountType) {
            case 'percentage':
                $discountValue = rand(5, 25); // 5-25% discount
                $finalAmount = $subscription->amount * (1 - ($discountValue / 100));
                $notes = "Test discount: {$discountValue}% off";
                break;
            
            case 'fixed':
                $discountValue = rand(5, min(20, $subscription->amount * 0.5)); // Fixed discount up to 50% of amount
                $finalAmount = max(0, $subscription->amount - $discountValue);
                $notes = "Test discount: $" . number_format($discountValue, 2) . " off";
                break;
        }

        $subscription->update([
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'final_amount' => $finalAmount,
            'notes' => $notes
        ]);
    }

    /**
     * Determine academic level based on student's class
     */
    private function determineAcademicLevel(Student $student): string
    {
        if (!$student->classes) {
            return 'primary'; // Default fallback
        }

        $classNumber = $student->classes->numeric_name ?? 0;
        
        return match(true) {
            $classNumber >= 1 && $classNumber <= 5 => 'primary',
            $classNumber >= 6 && $classNumber <= 10 => 'secondary',
            $classNumber >= 11 && $classNumber <= 12 => 'high_school',
            $classNumber < 1 => 'kg',
            default => 'primary'
        };
    }

    /**
     * Calculate due date based on offset
     */
    private function calculateDueDate(int $offset): Carbon
    {
        return now()->addDays($offset);
    }
}
