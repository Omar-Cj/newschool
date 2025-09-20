<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentInfo\Student;
use App\Models\Academic\ClassSetup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateStudentGrades extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'students:migrate-grades
                           {--dry-run : Run the command without making changes}
                           {--force : Skip confirmation prompts}
                           {--batch-size=100 : Number of students to process per batch}';

    /**
     * The console command description.
     */
    protected $description = 'Migrate existing students to have grade field populated based on class mappings';

    /**
     * Grade mapping based on common class naming patterns
     */
    private array $classToGradeMapping = [
        // Kindergarten patterns
        'kg-1' => 'KG-1',
        'kg1' => 'KG-1',
        'kindergarten 1' => 'KG-1',
        'nursery 1' => 'KG-1',
        'pre-k' => 'KG-1',

        'kg-2' => 'KG-2',
        'kg2' => 'KG-2',
        'kindergarten 2' => 'KG-2',
        'nursery 2' => 'KG-2',
        'reception' => 'KG-2',

        // Primary grades patterns
        'grade 1' => 'Grade1',
        'class 1' => 'Grade1',
        'year 1' => 'Grade1',
        'standard 1' => 'Grade1',
        '1st grade' => 'Grade1',
        'primary 1' => 'Grade1',

        'grade 2' => 'Grade2',
        'class 2' => 'Grade2',
        'year 2' => 'Grade2',
        'standard 2' => 'Grade2',
        '2nd grade' => 'Grade2',
        'primary 2' => 'Grade2',

        'grade 3' => 'Grade3',
        'class 3' => 'Grade3',
        'year 3' => 'Grade3',
        'standard 3' => 'Grade3',
        '3rd grade' => 'Grade3',
        'primary 3' => 'Grade3',

        'grade 4' => 'Grade4',
        'class 4' => 'Grade4',
        'year 4' => 'Grade4',
        'standard 4' => 'Grade4',
        '4th grade' => 'Grade4',
        'primary 4' => 'Grade4',

        'grade 5' => 'Grade5',
        'class 5' => 'Grade5',
        'year 5' => 'Grade5',
        'standard 5' => 'Grade5',
        '5th grade' => 'Grade5',
        'primary 5' => 'Grade5',

        'grade 6' => 'Grade6',
        'class 6' => 'Grade6',
        'year 6' => 'Grade6',
        'standard 6' => 'Grade6',
        '6th grade' => 'Grade6',
        'primary 6' => 'Grade6',

        'grade 7' => 'Grade7',
        'class 7' => 'Grade7',
        'year 7' => 'Grade7',
        'standard 7' => 'Grade7',
        '7th grade' => 'Grade7',
        'primary 7' => 'Grade7',

        'grade 8' => 'Grade8',
        'class 8' => 'Grade8',
        'year 8' => 'Grade8',
        'standard 8' => 'Grade8',
        '8th grade' => 'Grade8',
        'primary 8' => 'Grade8',

        // Secondary forms patterns
        'form 1' => 'Form1',
        'year 9' => 'Form1',
        'grade 9' => 'Form1',
        '9th grade' => 'Form1',
        'secondary 1' => 'Form1',

        'form 2' => 'Form2',
        'year 10' => 'Form2',
        'grade 10' => 'Form2',
        '10th grade' => 'Form2',
        'secondary 2' => 'Form2',

        'form 3' => 'Form3',
        'year 11' => 'Form3',
        'grade 11' => 'Form3',
        '11th grade' => 'Form3',
        'secondary 3' => 'Form3',

        'form 4' => 'Form4',
        'year 12' => 'Form4',
        'grade 12' => 'Form4',
        '12th grade' => 'Form4',
        'secondary 4' => 'Form4',
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🎓 Student Grade Migration Tool');
        $this->info('=====================================');

        $dryRun = $this->option('dry-run');
        $force = $this->option('force');
        $batchSize = (int) $this->option('batch-size');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }

        // Get migration statistics
        $stats = $this->getMigrationStats();
        $this->displayStats($stats);

        if (!$force && !$dryRun) {
            if (!$this->confirm('Do you want to proceed with the migration?')) {
                $this->info('Migration cancelled.');
                return 0;
            }
        }

        if ($stats['students_without_grade'] === 0 && $stats['students_needing_correction'] === 0) {
            $this->info('✅ All students already have correct grades assigned.');
            return 0;
        }

        $this->info('🚀 Starting migration...');

        try {
            $result = $this->performMigration($dryRun, $batchSize);
            $this->displayResults($result, $dryRun);

            if (!$dryRun) {
                $this->info('✅ Migration completed successfully!');
            } else {
                $this->info('🔍 Dry run completed. Use --force to execute the migration.');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            Log::error('Student grade migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Get migration statistics
     */
    private function getMigrationStats(): array
    {
        $totalStudents = Student::count();
        $studentsWithGrade = Student::whereNotNull('grade')->count();
        $studentsWithoutGrade = Student::whereNull('grade')->count();

        // Get students who need grade correction (have grades but they're incorrect based on class)
        $studentsNeedingCorrection = DB::table('students as s')
            ->leftJoin('session_class_students as scs', 's.id', '=', 'scs.student_id')
            ->leftJoin('classes as c', 'scs.classes_id', '=', 'c.id')
            ->whereNotNull('s.grade')
            ->where('s.status', 1)
            ->whereNotNull('c.name')
            ->get()
            ->filter(function ($student) {
                $expectedGrade = $this->predictGradeFromClass($student->name);
                return $expectedGrade && $expectedGrade !== $student->grade;
            })
            ->count();

        // Get class distribution for students without grades OR needing correction
        $classDistribution = DB::table('students as s')
            ->leftJoin('session_class_students as scs', 's.id', '=', 'scs.student_id')
            ->leftJoin('classes as c', 'scs.classes_id', '=', 'c.id')
            ->where(function ($query) {
                $query->whereNull('s.grade')
                      ->orWhere(function ($subQuery) {
                          $subQuery->whereNotNull('s.grade');
                          // We'll check for incorrect grades in the display method
                      });
            })
            ->where('s.status', 1)
            ->whereNotNull('c.name')
            ->select('c.name as class_name', DB::raw('COUNT(*) as student_count'), 's.grade as current_grade')
            ->groupBy('c.name', 's.grade')
            ->get()
            ->groupBy('class_name')
            ->map(function ($group) {
                return $group->sum('student_count');
            })
            ->toArray();

        return [
            'total_students' => $totalStudents,
            'students_with_grade' => $studentsWithGrade,
            'students_without_grade' => $studentsWithoutGrade,
            'students_needing_correction' => $studentsNeedingCorrection,
            'class_distribution' => $classDistribution
        ];
    }

    /**
     * Display statistics
     */
    private function displayStats(array $stats): void
    {
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Students', number_format($stats['total_students'])],
                ['Students with Grade', number_format($stats['students_with_grade'])],
                ['Students without Grade', number_format($stats['students_without_grade'])],
                ['Students needing Correction', number_format($stats['students_needing_correction'])],
            ]
        );

        if (!empty($stats['class_distribution'])) {
            $this->info('📊 Class Distribution (Students without grade):');
            $classData = [];
            foreach ($stats['class_distribution'] as $className => $count) {
                $predictedGrade = $this->predictGradeFromClass($className);
                $classData[] = [$className, $count, $predictedGrade ?: 'Unknown'];
            }

            $this->table(['Class Name', 'Students', 'Predicted Grade'], $classData);
        }
    }

    /**
     * Perform the actual migration
     */
    private function performMigration(bool $dryRun, int $batchSize): array
    {
        $result = [
            'processed' => 0,
            'successful' => 0,
            'failed' => 0,
            'skipped' => 0,
            'grade_distribution' => [],
            'errors' => []
        ];

        // Get all active students to check and correct their grades
        Student::where('status', 1)
            ->with(['session_class_student.class'])
            ->chunk($batchSize, function ($students) use (&$result, $dryRun) {
                foreach ($students as $student) {
                    $result['processed']++;

                    try {
                        $expectedGrade = $this->determineStudentGrade($student);

                        if (!$expectedGrade) {
                            $result['skipped']++;
                            $result['errors'][] = "Student ID {$student->id}: Could not determine grade";
                            continue;
                        }

                        // Check if student needs grade update
                        if ($student->grade === $expectedGrade) {
                            // Grade is already correct, skip
                            $result['skipped']++;
                            continue;
                        }

                        if (!$dryRun) {
                            $student->update(['grade' => $expectedGrade]);
                        }

                        $result['successful']++;
                        $result['grade_distribution'][$expectedGrade] =
                            ($result['grade_distribution'][$expectedGrade] ?? 0) + 1;

                        // Progress indicator
                        if ($result['processed'] % 50 === 0) {
                            $this->info("Processed {$result['processed']} students...");
                        }

                    } catch (\Exception $e) {
                        $result['failed']++;
                        $result['errors'][] = "Student ID {$student->id}: {$e->getMessage()}";
                        Log::error('Failed to migrate student grade', [
                            'student_id' => $student->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            });

        return $result;
    }

    /**
     * Determine grade for a student based on their class
     */
    private function determineStudentGrade(Student $student): ?string
    {
        // Try to get grade from current class enrollment
        $className = $student->session_class_student?->class?->name;

        if ($className) {
            $grade = $this->predictGradeFromClass($className);
            if ($grade) {
                return $grade;
            }
        }

        // Fallback: Try to determine from date of birth
        if ($student->dob) {
            return $this->predictGradeFromAge($student->dob);
        }

        return null;
    }

    /**
     * Predict grade from class name
     */
    private function predictGradeFromClass(string $className): ?string
    {
        $className = strtolower(trim($className));

        // Direct mapping
        if (isset($this->classToGradeMapping[$className])) {
            return $this->classToGradeMapping[$className];
        }

        // Enhanced pattern matching for section-based class names
        // Handle Form{number}{letter} patterns (e.g., Form4A, Form1B)
        if (preg_match('/form\s*(\d+)[a-z]?/i', $className, $matches)) {
            $number = (int) $matches[1];
            if ($number >= 1 && $number <= 4) {
                return "Form{$number}";
            }
        }

        // Handle Grade{number}{letter} patterns (e.g., Grade8A, Grade1B)
        if (preg_match('/grade\s*(\d+)[a-z]?/i', $className, $matches)) {
            $number = (int) $matches[1];
            if ($number >= 1 && $number <= 8) {
                return "Grade{$number}";
            }
        }

        // Handle KG-{number}{letter} patterns (e.g., KG-1A, KG-2B)
        if (preg_match('/kg-?(\d+)[a-z]?/i', $className, $matches)) {
            $number = (int) $matches[1];
            if ($number >= 1 && $number <= 2) {
                return "KG-{$number}";
            }
        }

        // Fallback: Pattern matching for numbered classes (original logic)
        if (preg_match('/(\d+)/', $className, $matches)) {
            $number = (int) $matches[1];

            // Handle different numbering systems
            if ($number >= 1 && $number <= 8) {
                return "Grade{$number}";
            } elseif ($number >= 9 && $number <= 12) {
                $formNumber = $number - 8;
                return "Form{$formNumber}";
            }
        }

        // Check for kindergarten patterns
        if (preg_match('/k.*g|nursery|pre/', $className)) {
            return 'KG-1'; // Default to KG-1 for unspecified kindergarten
        }

        return null;
    }

    /**
     * Predict grade from student age (rough estimate)
     */
    private function predictGradeFromAge(string $dob): ?string
    {
        try {
            $birthDate = new \DateTime($dob);
            $now = new \DateTime();
            $age = $birthDate->diff($now)->y;

            // Age-to-grade mapping (approximate)
            $ageGradeMap = [
                3 => 'KG-1', 4 => 'KG-1',
                5 => 'KG-2', 6 => 'Grade1',
                7 => 'Grade2', 8 => 'Grade3',
                9 => 'Grade4', 10 => 'Grade5',
                11 => 'Grade6', 12 => 'Grade7',
                13 => 'Grade8', 14 => 'Form1',
                15 => 'Form2', 16 => 'Form3',
                17 => 'Form4', 18 => 'Form4'
            ];

            return $ageGradeMap[$age] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Display migration results
     */
    private function displayResults(array $result, bool $dryRun): void
    {
        $this->info('📈 Migration Results:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Processed', number_format($result['processed'])],
                ['Successful', number_format($result['successful'])],
                ['Failed', number_format($result['failed'])],
                ['Skipped', number_format($result['skipped'])],
            ]
        );

        if (!empty($result['grade_distribution'])) {
            $this->info('📊 Grade Distribution:');
            $gradeData = [];
            foreach ($result['grade_distribution'] as $grade => $count) {
                $gradeData[] = [$grade, number_format($count)];
            }
            $this->table(['Grade', 'Students'], $gradeData);
        }

        if (!empty($result['errors']) && count($result['errors']) <= 10) {
            $this->warn('⚠️  Errors encountered:');
            foreach ($result['errors'] as $error) {
                $this->line("  • {$error}");
            }
        } elseif (count($result['errors']) > 10) {
            $this->warn("⚠️  " . count($result['errors']) . " errors encountered (showing first 10):");
            foreach (array_slice($result['errors'], 0, 10) as $error) {
                $this->line("  • {$error}");
            }
        }
    }
}