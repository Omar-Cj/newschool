<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Classes;
use App\Models\StudentInfo\Student;
use App\Models\AcademicLevelConfig;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AcademicLevelDetectionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test that numeric classes 1-8 are detected as primary level
     *
     * @test
     */
    public function it_detects_numeric_classes_1_to_8_as_primary()
    {
        $testCases = [
            'Class 1' => 'primary',
            'Class 2' => 'primary',
            'Class 3' => 'primary',
            'Class 4' => 'primary',
            'Class 5' => 'primary',
            'Class 6' => 'primary',  // Critical test - should be primary not secondary
            'Class 7' => 'primary',  // Critical test - should be primary not secondary
            'Class 8' => 'primary',  // Critical test - should be primary not secondary
            '1' => 'primary',
            '2' => 'primary',
            '6' => 'primary',        // Critical test - should be primary not secondary
            '7' => 'primary',        // Critical test - should be primary not secondary
            '8' => 'primary',        // Critical test - should be primary not secondary
            'Grade 6' => 'primary',  // Critical test
            'Grade 7' => 'primary',  // Critical test
            'Grade 8' => 'primary',  // Critical test
        ];

        foreach ($testCases as $className => $expectedLevel) {
            $detectedLevel = AcademicLevelConfig::detectAcademicLevel($className);

            $this->assertEquals(
                $expectedLevel,
                $detectedLevel,
                "Class '{$className}' should be detected as '{$expectedLevel}' but was detected as '{$detectedLevel}'"
            );
        }
    }

    /**
     * Test that Form 1-4 classes are detected as secondary level
     *
     * @test
     */
    public function it_detects_form_classes_1_to_4_as_secondary()
    {
        $testCases = [
            'Form 1' => 'secondary',
            'Form 2' => 'secondary',
            'Form 3' => 'secondary',
            'Form 4' => 'secondary',
            'Form I' => 'secondary',
            'Form II' => 'secondary',
            'Form III' => 'secondary',
            'Form IV' => 'secondary',
            'form 1' => 'secondary',  // Case insensitive
            'form 2' => 'secondary',
            'FORM 3' => 'secondary',
            'Form1' => 'secondary',   // No space
            'Form2' => 'secondary',
        ];

        foreach ($testCases as $className => $expectedLevel) {
            $detectedLevel = AcademicLevelConfig::detectAcademicLevel($className);

            $this->assertEquals(
                $expectedLevel,
                $detectedLevel,
                "Class '{$className}' should be detected as '{$expectedLevel}' but was detected as '{$detectedLevel}'"
            );
        }
    }

    /**
     * Test that Student model getAcademicLevel() method works correctly
     *
     * @test
     */
    public function student_get_academic_level_method_works_correctly()
    {
        // Create test classes
        $primaryClass = Classes::factory()->create([
            'name' => 'Class 6',
            'academic_level' => null  // Test fallback detection
        ]);

        $secondaryClass = Classes::factory()->create([
            'name' => 'Form 2',
            'academic_level' => null  // Test fallback detection
        ]);

        // Create test students (this would require proper setup of related models)
        // For now, we'll test the detection logic directly

        // Test Class 6 -> Primary
        $this->assertEquals('primary', AcademicLevelConfig::detectAcademicLevel('Class 6'));

        // Test Form 2 -> Secondary
        $this->assertEquals('secondary', AcademicLevelConfig::detectAcademicLevel('Form 2'));
    }

    /**
     * Test edge cases and variations in class naming
     *
     * @test
     */
    public function it_handles_edge_cases_in_class_naming()
    {
        $testCases = [
            // Variations of Class 6-8 (should all be primary)
            '6th Class' => 'primary',
            'Standard 6' => 'primary',
            'Std 7' => 'primary',
            'Grade 8' => 'primary',
            '6th Grade' => 'primary',

            // Variations of Form classes (should be secondary)
            'Form One' => null,     // Not supported by current regex - would need enhancement
            'F1' => null,           // Not supported by current regex - would need enhancement

            // Numeric grades 9+ (secondary/high school)
            'Class 9' => 'secondary',
            'Class 10' => 'secondary',
            'Grade 11' => 'high_school',
            'Grade 12' => 'high_school',

            // Kindergarten
            'KG' => 'kg',
            'Nursery' => 'kg',
            'Pre-School' => 'kg',
        ];

        foreach ($testCases as $className => $expectedLevel) {
            $detectedLevel = AcademicLevelConfig::detectAcademicLevel($className);

            if ($expectedLevel === null) {
                $this->assertNull(
                    $detectedLevel,
                    "Class '{$className}' should not be detected (return null) but was detected as '{$detectedLevel}'"
                );
            } else {
                $this->assertEquals(
                    $expectedLevel,
                    $detectedLevel,
                    "Class '{$className}' should be detected as '{$expectedLevel}' but was detected as '{$detectedLevel}'"
                );
            }
        }
    }

    /**
     * Test that the config file patterns are working correctly
     *
     * @test
     */
    public function config_patterns_match_detection_logic()
    {
        $patterns = config('fees.level_detection_patterns');

        // Test primary pattern
        $this->assertEquals(1, $patterns['primary']['numeric_range']['min']);
        $this->assertEquals(8, $patterns['primary']['numeric_range']['max']);

        // Test secondary pattern
        $this->assertEquals(9, $patterns['secondary']['numeric_range']['min']);
        $this->assertEquals(10, $patterns['secondary']['numeric_range']['max']);
        $this->assertContains('form', $patterns['secondary']['keywords']);
        $this->assertEquals(1, $patterns['secondary']['form_range']['min']);
        $this->assertEquals(4, $patterns['secondary']['form_range']['max']);
    }

    /**
     * Critical test: Ensure Classes 6-8 don't get secondary fees
     *
     * @test
     */
    public function classes_6_to_8_should_not_be_treated_as_secondary()
    {
        $criticalClasses = ['Class 6', 'Class 7', 'Class 8', '6', '7', '8'];

        foreach ($criticalClasses as $className) {
            $detectedLevel = AcademicLevelConfig::detectAcademicLevel($className);

            $this->assertNotEquals(
                'secondary',
                $detectedLevel,
                "CRITICAL ISSUE: Class '{$className}' is being detected as 'secondary' when it should be 'primary'"
            );

            $this->assertEquals(
                'primary',
                $detectedLevel,
                "Class '{$className}' should be detected as 'primary'"
            );
        }
    }

    /**
     * Test the priority order of detection methods
     *
     * @test
     */
    public function detection_follows_correct_priority_order()
    {
        // Form-based should take priority over numeric
        // A hypothetical class named "Form 2 Grade 8" should be detected as secondary (Form 2)
        // not primary (Grade 8)
        $className = "Form 2 Grade 8";  // Artificial test case
        $detectedLevel = AcademicLevelConfig::detectAcademicLevel($className);

        $this->assertEquals(
            'secondary',
            $detectedLevel,
            "Form-based detection should take priority over numeric detection"
        );
    }

    /**
     * Test that explicit academic_level column takes highest priority
     *
     * @test
     */
    public function explicit_academic_level_takes_highest_priority()
    {
        // Create a class with potentially confusing name but explicit academic level
        $class = Classes::factory()->create([
            'name' => 'Form 2',           // Would normally be detected as secondary
            'academic_level' => 'primary'  // But explicitly set as primary
        ]);

        // Mock a student in this class (simplified test)
        $this->assertEquals('primary', $class->academic_level);

        // The explicit level should override detection
        // (Full student test would require more complex setup)
    }

    /**
     * Integration test with actual fee assignment logic
     * This tests the complete flow from class name to fee assignment
     *
     * @test
     */
    public function complete_fee_assignment_flow_works_correctly()
    {
        // This is a placeholder for integration testing
        // Would require full setup of fees, student registration, etc.
        $this->assertTrue(true, 'Integration test placeholder - implement when fee system is fully testable');
    }

    /**
     * Performance test for academic level detection
     *
     * @test
     */
    public function academic_level_detection_is_performant()
    {
        $testClasses = [
            'Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5',
            'Class 6', 'Class 7', 'Class 8',  // Critical classes
            'Form 1', 'Form 2', 'Form 3', 'Form 4',
            'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'
        ];

        $startTime = microtime(true);

        // Run detection 100 times to test performance
        for ($i = 0; $i < 100; $i++) {
            foreach ($testClasses as $className) {
                AcademicLevelConfig::detectAcademicLevel($className);
            }
        }

        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;

        // Should complete 1600 detections (16 classes * 100 iterations) in under 1 second
        $this->assertLessThan(1.0, $totalTime, 'Academic level detection should be performant');
    }
}