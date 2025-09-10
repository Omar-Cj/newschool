<?php

namespace App\Services;

use App\Models\AcademicLevelConfig;
use App\Models\StudentInfo\Student;
use App\Models\Academic\Classes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AcademicLevelDetectionService
{
    private const CACHE_KEY = 'academic_level_configs';
    private const CACHE_DURATION = 60; // 60 minutes

    /**
     * Detect academic level for a student based on their class assignment
     */
    public function detectStudentAcademicLevel(Student $student): string
    {
        // Try to detect from student's current class
        if ($student->classes) {
            $level = $this->detectFromClass($student->classes);
            if ($level) {
                return $level;
            }
        }

        // Fallback to session-based class if available
        if ($student->sessionStudentDetails && $student->sessionStudentDetails->class) {
            $level = $this->detectFromClass($student->sessionStudentDetails->class);
            if ($level) {
                return $level;
            }
        }

        // Log the detection failure for analysis
        Log::warning("Failed to detect academic level for student", [
            'student_id' => $student->id,
            'student_name' => $student->full_name,
            'class_id' => $student->class_id,
            'class_name' => $student->classes->name ?? null
        ]);

        // Default fallback
        return $this->getDefaultAcademicLevel();
    }

    /**
     * Detect academic level from a class model
     */
    public function detectFromClass(Classes $class): ?string
    {
        // Try detection by class name
        $level = $this->detectByClassName($class->name);
        if ($level) {
            return $level;
        }

        // Try detection by numeric name if available
        if ($class->numeric_name) {
            $level = $this->detectByClassNumber($class->numeric_name);
            if ($level) {
                return $level;
            }
        }

        return null;
    }

    /**
     * Detect academic level by class name using configured mappings
     */
    public function detectByClassName(string $className): ?string
    {
        $configs = $this->getAcademicLevelConfigs();

        foreach ($configs as $config) {
            if ($config->matchesClassName($className)) {
                return $config->academic_level;
            }
        }

        // Try pattern-based detection as fallback
        return $this->patternBasedDetection($className);
    }

    /**
     * Detect academic level by class number using configured mappings
     */
    public function detectByClassNumber(int $classNumber): ?string
    {
        $configs = $this->getAcademicLevelConfigs();

        foreach ($configs as $config) {
            if ($config->matchesClassNumber($classNumber)) {
                return $config->academic_level;
            }
        }

        // Fallback to hardcoded numeric ranges
        return $this->numericFallbackDetection($classNumber);
    }

    /**
     * Batch detect academic levels for multiple students
     */
    public function batchDetectStudentsAcademicLevels(Collection $students): array
    {
        $results = [];
        $stats = [
            'total_students' => $students->count(),
            'detected_levels' => [],
            'failed_detections' => 0
        ];

        foreach ($students as $student) {
            try {
                $level = $this->detectStudentAcademicLevel($student);
                $results[$student->id] = $level;
                
                // Track statistics
                if (!isset($stats['detected_levels'][$level])) {
                    $stats['detected_levels'][$level] = 0;
                }
                $stats['detected_levels'][$level]++;
                
            } catch (\Exception $e) {
                $results[$student->id] = $this->getDefaultAcademicLevel();
                $stats['failed_detections']++;
                
                Log::error("Failed to detect academic level for student in batch", [
                    'student_id' => $student->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'results' => $results,
            'statistics' => $stats
        ];
    }

    /**
     * Analyze class structure and suggest academic level configurations
     */
    public function analyzeClassStructureAndSuggestConfigs(): array
    {
        $classes = Classes::all();
        $analysis = [
            'total_classes' => $classes->count(),
            'class_patterns' => [],
            'numeric_ranges' => [],
            'suggested_configs' => []
        ];

        // Analyze class names
        $classNames = $classes->pluck('name')->filter();
        $numericNames = $classes->pluck('numeric_name')->filter();

        // Pattern analysis
        $patterns = [];
        foreach ($classNames as $name) {
            // Extract patterns (Grade 1, Class 1, Form 1, etc.)
            if (preg_match('/^(grade|class|form|std|standard)\s*(\d+)$/i', $name, $matches)) {
                $pattern = strtolower($matches[1]);
                $number = (int) $matches[2];
                
                if (!isset($patterns[$pattern])) {
                    $patterns[$pattern] = [];
                }
                $patterns[$pattern][] = $number;
            }
        }

        $analysis['class_patterns'] = $patterns;

        // Numeric range analysis
        if ($numericNames->isNotEmpty()) {
            $analysis['numeric_ranges'] = [
                'min' => $numericNames->min(),
                'max' => $numericNames->max(),
                'distribution' => $numericNames->countBy()->toArray()
            ];
        }

        // Generate suggestions
        $analysis['suggested_configs'] = $this->generateConfigSuggestions($patterns, $numericNames);

        return $analysis;
    }

    /**
     * Validate and update academic level configurations
     */
    public function validateAndUpdateConfigs(array $configUpdates): array
    {
        $results = [
            'updated' => 0,
            'errors' => [],
            'warnings' => []
        ];

        DB::transaction(function () use ($configUpdates, &$results) {
            foreach ($configUpdates as $levelName => $configData) {
                try {
                    $config = AcademicLevelConfig::where('academic_level', $levelName)->first();
                    
                    if ($config) {
                        // Validate the configuration
                        $validationErrors = $config->isValidConfiguration();
                        if (!empty($validationErrors)) {
                            $results['errors'][$levelName] = $validationErrors;
                            continue;
                        }

                        $config->update($configData);
                        $results['updated']++;
                    } else {
                        $results['warnings'][] = "Academic level '{$levelName}' not found";
                    }
                } catch (\Exception $e) {
                    $results['errors'][$levelName] = [$e->getMessage()];
                }
            }
        });

        // Clear cache after updates
        $this->clearConfigCache();

        return $results;
    }

    /**
     * Get academic level distribution for current students
     */
    public function getAcademicLevelDistribution(): array
    {
        $students = Student::where('status', \App\Enums\Status::ACTIVE)
            ->with('classes')
            ->get();

        $distribution = [];
        $undetected = 0;

        foreach ($students as $student) {
            try {
                $level = $this->detectStudentAcademicLevel($student);
                if (!isset($distribution[$level])) {
                    $distribution[$level] = 0;
                }
                $distribution[$level]++;
            } catch (\Exception $e) {
                $undetected++;
            }
        }

        return [
            'total_students' => $students->count(),
            'distribution' => $distribution,
            'undetected' => $undetected,
            'detection_rate' => $students->count() > 0 
                ? (($students->count() - $undetected) / $students->count()) * 100 
                : 0
        ];
    }

    /**
     * Get cached academic level configurations
     */
    private function getAcademicLevelConfigs(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            return AcademicLevelConfig::active()->ordered()->get();
        });
    }

    /**
     * Clear configuration cache
     */
    public function clearConfigCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Pattern-based detection fallback
     */
    private function patternBasedDetection(string $className): ?string
    {
        $lowerName = strtolower(trim($className));

        // Kindergarten patterns
        if (preg_match('/^(kg|kindergarten|pre-?k|nursery|pre-?school|reception)/', $lowerName)) {
            return 'kg';
        }

        // Primary patterns
        if (preg_match('/^(grade|class|form|std|standard)?\s*([1-5])$/', $lowerName, $matches) ||
            preg_match('/^(primary|elementary)\s*([1-5]?)/', $lowerName)) {
            return 'primary';
        }

        // Secondary patterns
        if (preg_match('/^(grade|class|form|std|standard)?\s*([6-9]|10)$/', $lowerName, $matches) ||
            preg_match('/^(secondary|middle|junior)\s*([6-9]|10)?/', $lowerName)) {
            return 'secondary';
        }

        // High school patterns
        if (preg_match('/^(grade|class|form|std|standard)?\s*(11|12)$/', $lowerName, $matches) ||
            preg_match('/^(high|senior|college)\s*(11|12)?/', $lowerName)) {
            return 'high_school';
        }

        return null;
    }

    /**
     * Numeric fallback detection
     */
    private function numericFallbackDetection(int $classNumber): ?string
    {
        return match(true) {
            $classNumber >= 1 && $classNumber <= 5 => 'primary',
            $classNumber >= 6 && $classNumber <= 10 => 'secondary',
            $classNumber >= 11 && $classNumber <= 12 => 'high_school',
            $classNumber <= 0 => 'kg',
            default => null
        };
    }

    /**
     * Generate configuration suggestions based on analysis
     */
    private function generateConfigSuggestions(array $patterns, Collection $numericNames): array
    {
        $suggestions = [];

        // Analyze numeric distribution
        if ($numericNames->isNotEmpty()) {
            $min = $numericNames->min();
            $max = $numericNames->max();

            if ($min <= 5 && $max <= 5) {
                $suggestions['primary'] = [
                    'numeric_range' => ['min' => $min, 'max' => $max],
                    'confidence' => 'high'
                ];
            } elseif ($min >= 1 && $max <= 10) {
                $suggestions['primary'] = [
                    'numeric_range' => ['min' => $min, 'max' => min(5, $max)],
                    'confidence' => 'medium'
                ];
                if ($max > 5) {
                    $suggestions['secondary'] = [
                        'numeric_range' => ['min' => 6, 'max' => $max],
                        'confidence' => 'medium'
                    ];
                }
            }
        }

        // Analyze patterns
        foreach ($patterns as $pattern => $numbers) {
            $minNum = min($numbers);
            $maxNum = max($numbers);
            
            $suggestions["{$pattern}_based"] = [
                'pattern' => $pattern,
                'numeric_range' => ['min' => $minNum, 'max' => $maxNum],
                'class_count' => count($numbers),
                'confidence' => count($numbers) > 3 ? 'high' : 'medium'
            ];
        }

        return $suggestions;
    }

    /**
     * Get default academic level when detection fails
     */
    private function getDefaultAcademicLevel(): string
    {
        return 'primary'; // Conservative default
    }

    /**
     * Test detection accuracy against sample data
     */
    public function testDetectionAccuracy(array $testCases): array
    {
        $results = [
            'total_tests' => count($testCases),
            'correct' => 0,
            'incorrect' => 0,
            'failed' => 0,
            'accuracy_percentage' => 0,
            'details' => []
        ];

        foreach ($testCases as $testCase) {
            $className = $testCase['class_name'];
            $expectedLevel = $testCase['expected_level'];

            try {
                $detectedLevel = $this->detectByClassName($className);
                
                if ($detectedLevel === $expectedLevel) {
                    $results['correct']++;
                    $status = 'correct';
                } else {
                    $results['incorrect']++;
                    $status = 'incorrect';
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $detectedLevel = null;
                $status = 'failed';
            }

            $results['details'][] = [
                'class_name' => $className,
                'expected' => $expectedLevel,
                'detected' => $detectedLevel,
                'status' => $status
            ];
        }

        $results['accuracy_percentage'] = $results['total_tests'] > 0 
            ? ($results['correct'] / $results['total_tests']) * 100 
            : 0;

        return $results;
    }
}