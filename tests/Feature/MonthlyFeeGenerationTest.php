<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\StudentService;
use App\Models\Fees\FeesType;
use App\Models\StudentInfo\Student;
use App\Services\EnhancedFeesGenerationService;
use App\Services\StudentServiceManager;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class MonthlyFeeGenerationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private EnhancedFeesGenerationService $feeService;
    private StudentServiceManager $serviceManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->feeService = app(EnhancedFeesGenerationService::class);
        $this->serviceManager = app(StudentServiceManager::class);
    }

    /** @test */
    public function it_can_preview_monthly_fees_for_students_with_services()
    {
        // Create test data
        $student = Student::factory()->create();
        $feeType = FeesType::factory()->create([
            'name' => 'Monthly Tuition',
            'academic_level' => 'primary',
            'amount' => 100.00,
            'fee_frequency' => 'monthly',
            'is_mandatory_for_level' => true,
            'category' => 'academic'
        ]);

        // Create service subscription
        StudentService::factory()->create([
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'academic_year_id' => 1,
            'amount' => 100.00,
            'final_amount' => 100.00,
            'is_active' => true
        ]);

        $month = Carbon::now();
        $filters = ['academic_year_id' => 1];

        $preview = $this->feeService->previewMonthlyFees($month, $filters);

        $this->assertIsArray($preview);
        $this->assertArrayHasKey('total_students', $preview);
        $this->assertArrayHasKey('total_amount', $preview);
        $this->assertEquals(1, $preview['total_students']);
        $this->assertEquals(100.00, $preview['total_amount']);
    }

    /** @test */
    public function it_can_generate_monthly_fees_based_on_student_services()
    {
        // Create test data
        $student = Student::factory()->create();
        $feeType = FeesType::factory()->create([
            'name' => 'Monthly Transport',
            'academic_level' => 'secondary',
            'amount' => 50.00,
            'fee_frequency' => 'monthly',
            'category' => 'transport'
        ]);

        // Create service subscription
        StudentService::factory()->create([
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'academic_year_id' => 1,
            'amount' => 50.00,
            'final_amount' => 45.00, // With discount
            'discount_type' => 'fixed',
            'discount_value' => 5.00,
            'is_active' => true
        ]);

        $month = Carbon::now();
        $filters = ['academic_year_id' => 1];

        $result = $this->feeService->generateMonthlyFees($month, $filters);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('success_count', $result);
        $this->assertArrayHasKey('total_amount', $result);
        $this->assertEquals(1, $result['success_count']);
        $this->assertEquals(45.00, $result['total_amount']);

        // Verify FeesCollect record was created
        $this->assertDatabaseHas('fees_collects', [
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'amount' => 45.00,
            'generation_method' => 'service_based'
        ]);
    }

    /** @test */
    public function it_can_generate_prorated_fees_for_mid_month_subscriptions()
    {
        // Create test data
        $student = Student::factory()->create();
        $feeType = FeesType::factory()->create([
            'name' => 'Prorated Monthly Fee',
            'academic_level' => 'primary',
            'amount' => 120.00, // $4 per day for 30-day month
            'fee_frequency' => 'monthly',
            'is_prorated' => true,
            'category' => 'academic'
        ]);

        $month = Carbon::create(2024, 9, 1); // September 2024
        $subscriptionDate = Carbon::create(2024, 9, 16); // Mid-month subscription

        // Create service subscription mid-month
        StudentService::factory()->create([
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'academic_year_id' => 1,
            'amount' => 120.00,
            'final_amount' => 120.00,
            'subscription_date' => $subscriptionDate,
            'is_active' => true
        ]);

        $filters = ['academic_year_id' => 1];

        $result = $this->feeService->generateProRatedMonthlyFees($month, $filters);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['success_count']);

        // Should be prorated for 15 days (16th to 30th = 15 days)
        $expectedAmount = (120.00 / 30) * 15; // $60.00

        $this->assertEquals($expectedAmount, $result['total_amount']);
    }

    /** @test */
    public function it_prevents_duplicate_fee_generation_for_same_month()
    {
        // Create test data
        $student = Student::factory()->create();
        $feeType = FeesType::factory()->create([
            'name' => 'Monthly Library Fee',
            'academic_level' => 'primary',
            'amount' => 25.00,
            'fee_frequency' => 'monthly',
            'category' => 'academic'
        ]);

        // Create service subscription
        StudentService::factory()->create([
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'academic_year_id' => 1,
            'amount' => 25.00,
            'final_amount' => 25.00,
            'is_active' => true
        ]);

        $month = Carbon::now();
        $filters = ['academic_year_id' => 1];

        // Generate fees first time
        $result1 = $this->feeService->generateMonthlyFees($month, $filters);
        $this->assertEquals(1, $result1['success_count']);

        // Try to generate fees again for same month
        $result2 = $this->feeService->generateMonthlyFees($month, $filters);
        $this->assertEquals(0, $result2['success_count']); // No new fees generated

        // Verify only one FeesCollect record exists
        $feeCount = \App\Models\Fees\FeesCollect::where('student_id', $student->id)
            ->where('fee_type_id', $feeType->id)
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month)
            ->count();

        $this->assertEquals(1, $feeCount);
    }

    /** @test */
    public function it_filters_fees_by_service_categories()
    {
        // Create test data
        $student = Student::factory()->create();

        $academicFee = FeesType::factory()->create([
            'name' => 'Academic Fee',
            'category' => 'academic',
            'amount' => 100.00,
            'fee_frequency' => 'monthly'
        ]);

        $transportFee = FeesType::factory()->create([
            'name' => 'Transport Fee',
            'category' => 'transport',
            'amount' => 50.00,
            'fee_frequency' => 'monthly'
        ]);

        // Create service subscriptions
        StudentService::factory()->create([
            'student_id' => $student->id,
            'fee_type_id' => $academicFee->id,
            'academic_year_id' => 1,
            'amount' => 100.00,
            'final_amount' => 100.00,
            'is_active' => true
        ]);

        StudentService::factory()->create([
            'student_id' => $student->id,
            'fee_type_id' => $transportFee->id,
            'academic_year_id' => 1,
            'amount' => 50.00,
            'final_amount' => 50.00,
            'is_active' => true
        ]);

        $month = Carbon::now();

        // Generate only academic fees
        $filters = [
            'academic_year_id' => 1,
            'service_categories' => ['academic']
        ];

        $result = $this->feeService->generateMonthlyFees($month, $filters);

        $this->assertEquals(1, $result['success_count']);
        $this->assertEquals(100.00, $result['total_amount']);

        // Verify only academic fee was generated
        $this->assertDatabaseHas('fees_collects', [
            'student_id' => $student->id,
            'fee_type_id' => $academicFee->id
        ]);

        $this->assertDatabaseMissing('fees_collects', [
            'student_id' => $student->id,
            'fee_type_id' => $transportFee->id
        ]);
    }

    /** @test */
    public function it_calculates_correct_due_dates_for_monthly_fees()
    {
        // Create test data
        $student = Student::factory()->create();
        $feeType = FeesType::factory()->create([
            'name' => 'Monthly Fee with Due Date',
            'academic_level' => 'primary',
            'amount' => 75.00,
            'fee_frequency' => 'monthly',
            'due_date_offset' => 15, // Due 15 days after end of month
            'category' => 'academic'
        ]);

        $month = Carbon::create(2024, 9, 1); // September 2024
        $expectedDueDate = Carbon::create(2024, 9, 30)->addDays(15); // October 15, 2024

        // Create service subscription
        StudentService::factory()->create([
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'academic_year_id' => 1,
            'amount' => 75.00,
            'final_amount' => 75.00,
            'is_active' => true
        ]);

        $filters = ['academic_year_id' => 1];

        $result = $this->feeService->generateProRatedMonthlyFees($month, $filters);

        $this->assertEquals(1, $result['success_count']);

        // Verify due date is calculated correctly
        $this->assertDatabaseHas('fees_collects', [
            'student_id' => $student->id,
            'fee_type_id' => $feeType->id,
            'due_date' => $expectedDueDate->toDateString()
        ]);
    }
}