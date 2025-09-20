<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentInfo\Student;
use Illuminate\Support\Facades\DB;

class ValidateStudentGrades extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'students:validate-grades
                           {--fix : Automatically fix issues found}
                           {--verbose : Show detailed information}';

    /**
     * The console command description.
     */
    protected $description = 'Validate that all students have valid grades assigned';

    /**
     * Valid grades
     */
    private array $validGrades = [
        'KG-1', 'KG-2',
        'Grade1', 'Grade2', 'Grade3', 'Grade4', 'Grade5', 'Grade6', 'Grade7', 'Grade8',
        'Form1', 'Form2', 'Form3', 'Form4'
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Student Grade Validation Tool');
        $this->info('=================================');

        $fix = $this->option('fix');
        $verbose = $this->option('verbose');

        $issues = $this->validateGrades($verbose);

        if (empty($issues)) {
            $this->info('✅ All students have valid grades assigned!');
            return 0;
        }

        $this->displayIssues($issues);

        if ($fix) {
            $this->info('🔧 Attempting to fix issues...');
            $fixed = $this->fixIssues($issues);
            $this->displayFixResults($fixed);
        } else {
            $this->warn('💡 Use --fix option to automatically resolve fixable issues.');
        }

        return empty($issues) ? 0 : 1;
    }

    /**
     * Validate student grades
     */
    private function validateGrades(bool $verbose): array
    {
        $issues = [];

        // Check for students without grades
        $studentsWithoutGrade = Student::whereNull('grade')
            ->where('status', 1)
            ->with(['sessionClassStudent.class'])
            ->get();

        if ($studentsWithoutGrade->isNotEmpty()) {
            $issues['no_grade'] = $studentsWithoutGrade;
        }

        // Check for students with invalid grades
        $studentsWithInvalidGrade = Student::whereNotNull('grade')
            ->whereNotIn('grade', $this->validGrades)
            ->where('status', 1)
            ->get();

        if ($studentsWithInvalidGrade->isNotEmpty()) {
            $issues['invalid_grade'] = $studentsWithInvalidGrade;
        }

        // Check for data consistency
        $inconsistentData = $this->checkDataConsistency();
        if (!empty($inconsistentData)) {
            $issues['inconsistent_data'] = $inconsistentData;
        }

        if ($verbose) {
            $this->displayVerboseStats();
        }

        return $issues;
    }

    /**
     * Check for data consistency issues
     */
    private function checkDataConsistency(): array
    {
        $issues = [];

        // Check students with conflicting class/grade assignments
        $conflictingAssignments = DB::table('students as s')
            ->join('session_class_students as scs', 's.id', '=', 'scs.student_id')
            ->join('classes as c', 'scs.classes_id', '=', 'c.id')
            ->whereNotNull('s.grade')
            ->select('s.id', 's.first_name', 's.last_name', 's.grade', 'c.name as class_name')
            ->get();

        foreach ($conflictingAssignments as $assignment) {
            $expectedGrade = $this->predictGradeFromClassName($assignment->class_name);
            if ($expectedGrade && $expectedGrade !== $assignment->grade) {
                $issues[] = [
                    'type' => 'class_grade_mismatch',
                    'student_id' => $assignment->id,
                    'student_name' => "{$assignment->first_name} {$assignment->last_name}",
                    'current_grade' => $assignment->grade,
                    'class_name' => $assignment->class_name,
                    'expected_grade' => $expectedGrade
                ];
            }
        }

        return $issues;
    }

    /**
     * Display validation issues
     */
    private function displayIssues(array $issues): void
    {
        $this->error('❌ Validation Issues Found:');

        if (isset($issues['no_grade'])) {
            $count = $issues['no_grade']->count();
            $this->warn("🔴 {$count} students without grade:");

            foreach ($issues['no_grade']->take(10) as $student) {
                $className = $student->sessionClassStudent?->class?->name ?? 'No class';
                $this->line("  • ID: {$student->id} - {$student->first_name} {$student->last_name} (Class: {$className})");
            }

            if ($count > 10) {
                $this->line("  ... and " . ($count - 10) . " more");
            }
        }

        if (isset($issues['invalid_grade'])) {
            $count = $issues['invalid_grade']->count();
            $this->warn("🟡 {$count} students with invalid grades:");

            foreach ($issues['invalid_grade']->take(10) as $student) {
                $this->line("  • ID: {$student->id} - {$student->first_name} {$student->last_name} (Grade: {$student->grade})");
            }

            if ($count > 10) {
                $this->line("  ... and " . ($count - 10) . " more");
            }
        }

        if (isset($issues['inconsistent_data'])) {
            $count = count($issues['inconsistent_data']);
            $this->warn("🟠 {$count} data consistency issues:");

            foreach (array_slice($issues['inconsistent_data'], 0, 10) as $issue) {
                $this->line("  • {$issue['student_name']}: Grade '{$issue['current_grade']}' but in class '{$issue['class_name']}'");
            }

            if ($count > 10) {
                $this->line("  ... and " . ($count - 10) . " more");
            }
        }
    }

    /**
     * Display verbose statistics
     */
    private function displayVerboseStats(): void
    {
        $this->info('📊 Detailed Statistics:');

        // Grade distribution
        $gradeStats = Student::whereNotNull('grade')
            ->where('status', 1)
            ->selectRaw('grade, COUNT(*) as count')
            ->groupBy('grade')
            ->orderBy('grade')
            ->get();

        if ($gradeStats->isNotEmpty()) {
            $this->info('📈 Grade Distribution:');
            $gradeData = [];
            foreach ($gradeStats as $stat) {
                $academicLevel = $this->getAcademicLevel($stat->grade);
                $gradeData[] = [$stat->grade, $stat->count, $academicLevel];
            }
            $this->table(['Grade', 'Students', 'Academic Level'], $gradeData);
        }

        // Academic level summary
        $academicLevelStats = [];
        foreach ($gradeStats as $stat) {
            $level = $this->getAcademicLevel($stat->grade);
            $academicLevelStats[$level] = ($academicLevelStats[$level] ?? 0) + $stat->count;
        }

        if (!empty($academicLevelStats)) {
            $this->info('🎓 Academic Level Summary:');
            $levelData = [];
            foreach ($academicLevelStats as $level => $count) {
                $levelData[] = [ucfirst($level), $count];
            }
            $this->table(['Academic Level', 'Students'], $levelData);
        }
    }

    /**
     * Fix identified issues
     */
    private function fixIssues(array $issues): array
    {
        $fixed = ['no_grade' => 0, 'invalid_grade' => 0, 'inconsistent_data' => 0];

        // Fix students without grades
        if (isset($issues['no_grade'])) {
            foreach ($issues['no_grade'] as $student) {
                $grade = $this->determineGradeForStudent($student);
                if ($grade) {
                    $student->update(['grade' => $grade]);
                    $fixed['no_grade']++;
                }
            }
        }

        // Fix invalid grades
        if (isset($issues['invalid_grade'])) {
            foreach ($issues['invalid_grade'] as $student) {
                $correctedGrade = $this->correctInvalidGrade($student->grade);
                if ($correctedGrade) {
                    $student->update(['grade' => $correctedGrade]);
                    $fixed['invalid_grade']++;
                }
            }
        }

        // Fix inconsistent data
        if (isset($issues['inconsistent_data'])) {
            foreach ($issues['inconsistent_data'] as $issue) {
                if ($this->confirm("Fix student {$issue['student_name']} grade from '{$issue['current_grade']}' to '{$issue['expected_grade']}'?")) {
                    Student::where('id', $issue['student_id'])->update(['grade' => $issue['expected_grade']]);
                    $fixed['inconsistent_data']++;
                }
            }
        }

        return $fixed;
    }

    /**
     * Display fix results
     */
    private function displayFixResults(array $fixed): void
    {
        $this->info('🔧 Fix Results:');
        $this->table(
            ['Issue Type', 'Fixed'],
            [
                ['Students without grade', $fixed['no_grade']],
                ['Students with invalid grade', $fixed['invalid_grade']],
                ['Data consistency issues', $fixed['inconsistent_data']],
            ]
        );

        $totalFixed = array_sum($fixed);
        if ($totalFixed > 0) {
            $this->info("✅ Successfully fixed {$totalFixed} issues!");
        } else {
            $this->warn('⚠️  No issues could be automatically fixed.');
        }
    }

    /**
     * Determine grade for student
     */
    private function determineGradeForStudent(Student $student): ?string
    {
        // Try class name first
        $className = $student->sessionClassStudent?->class?->name;
        if ($className) {
            $grade = $this->predictGradeFromClassName($className);
            if ($grade) {
                return $grade;
            }
        }

        // Try age-based prediction
        if ($student->dob) {
            return $this->predictGradeFromAge($student->dob);
        }

        return null;
    }

    /**
     * Predict grade from class name
     */
    private function predictGradeFromClassName(string $className): ?string
    {
        $className = strtolower(trim($className));

        // Simple mappings
        $mappings = [
            'kg-1' => 'KG-1', 'kg1' => 'KG-1', 'kindergarten 1' => 'KG-1',
            'kg-2' => 'KG-2', 'kg2' => 'KG-2', 'kindergarten 2' => 'KG-2',
            'grade 1' => 'Grade1', 'class 1' => 'Grade1', 'year 1' => 'Grade1',
            'grade 2' => 'Grade2', 'class 2' => 'Grade2', 'year 2' => 'Grade2',
            'grade 3' => 'Grade3', 'class 3' => 'Grade3', 'year 3' => 'Grade3',
            'grade 4' => 'Grade4', 'class 4' => 'Grade4', 'year 4' => 'Grade4',
            'grade 5' => 'Grade5', 'class 5' => 'Grade5', 'year 5' => 'Grade5',
            'grade 6' => 'Grade6', 'class 6' => 'Grade6', 'year 6' => 'Grade6',
            'grade 7' => 'Grade7', 'class 7' => 'Grade7', 'year 7' => 'Grade7',
            'grade 8' => 'Grade8', 'class 8' => 'Grade8', 'year 8' => 'Grade8',
            'form 1' => 'Form1', 'year 9' => 'Form1', 'grade 9' => 'Form1',
            'form 2' => 'Form2', 'year 10' => 'Form2', 'grade 10' => 'Form2',
            'form 3' => 'Form3', 'year 11' => 'Form3', 'grade 11' => 'Form3',
            'form 4' => 'Form4', 'year 12' => 'Form4', 'grade 12' => 'Form4',
        ];

        return $mappings[$className] ?? null;
    }

    /**
     * Predict grade from age
     */
    private function predictGradeFromAge(string $dob): ?string
    {
        try {
            $age = now()->diffInYears($dob);
            $ageGradeMap = [
                3 => 'KG-1', 4 => 'KG-1', 5 => 'KG-2', 6 => 'Grade1',
                7 => 'Grade2', 8 => 'Grade3', 9 => 'Grade4', 10 => 'Grade5',
                11 => 'Grade6', 12 => 'Grade7', 13 => 'Grade8', 14 => 'Form1',
                15 => 'Form2', 16 => 'Form3', 17 => 'Form4', 18 => 'Form4'
            ];
            return $ageGradeMap[$age] ?? null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Correct invalid grade
     */
    private function correctInvalidGrade(string $invalidGrade): ?string
    {
        $corrections = [
            'kg1' => 'KG-1', 'kg-i' => 'KG-1', 'kgi' => 'KG-1',
            'kg2' => 'KG-2', 'kg-ii' => 'KG-2', 'kgii' => 'KG-2',
            'grade1' => 'Grade1', 'gradeone' => 'Grade1',
            'grade2' => 'Grade2', 'gradetwo' => 'Grade2',
            'grade3' => 'Grade3', 'gradethree' => 'Grade3',
            'grade4' => 'Grade4', 'gradefour' => 'Grade4',
            'grade5' => 'Grade5', 'gradefive' => 'Grade5',
            'grade6' => 'Grade6', 'gradesix' => 'Grade6',
            'grade7' => 'Grade7', 'gradeseven' => 'Grade7',
            'grade8' => 'Grade8', 'gradeeight' => 'Grade8',
            'form1' => 'Form1', 'formone' => 'Form1',
            'form2' => 'Form2', 'formtwo' => 'Form2',
            'form3' => 'Form3', 'formthree' => 'Form3',
            'form4' => 'Form4', 'formfour' => 'Form4',
        ];

        $normalized = strtolower(trim($invalidGrade));
        return $corrections[$normalized] ?? null;
    }

    /**
     * Get academic level for grade
     */
    private function getAcademicLevel(string $grade): string
    {
        if (in_array($grade, ['KG-1', 'KG-2'])) {
            return 'kindergarten';
        } elseif (in_array($grade, ['Grade1', 'Grade2', 'Grade3', 'Grade4', 'Grade5', 'Grade6', 'Grade7', 'Grade8'])) {
            return 'primary';
        } elseif (in_array($grade, ['Form1', 'Form2', 'Form3', 'Form4'])) {
            return 'secondary';
        }
        return 'unknown';
    }
}