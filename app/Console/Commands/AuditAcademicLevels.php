<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Classes;
use App\Models\StudentInfo\Student;
use App\Models\StudentService;
use App\Models\Fees\FeesType;
use App\Models\AcademicLevelConfig;
use Illuminate\Support\Collection;

class AuditAcademicLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:audit-academic-levels
                            {--show-details : Show detailed information for each issue}
                            {--export=null : Export results to file (csv, json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit academic level assignments and identify inconsistencies in fee assignments';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔍 Academic Level Audit Report');
        $this->info('====================================');
        $this->newLine();

        $issues = [];
        $summary = [
            'total_classes' => 0,
            'classes_without_academic_level' => 0,
            'classes_with_mismatched_levels' => 0,
            'students_with_incorrect_fees' => 0,
            'conflicting_fee_assignments' => 0
        ];

        // 1. Audit Classes
        $this->info('📋 Auditing Class Academic Level Assignments...');
        $classIssues = $this->auditClassAcademicLevels();
        $issues['classes'] = $classIssues;
        $summary['total_classes'] = Classes::count();
        $summary['classes_without_academic_level'] = $classIssues->where('issue_type', 'missing_academic_level')->count();
        $summary['classes_with_mismatched_levels'] = $classIssues->where('issue_type', 'mismatched_academic_level')->count();

        // 2. Audit Student Fee Assignments
        $this->info('👥 Auditing Student Fee Assignments...');
        $studentIssues = $this->auditStudentFeeAssignments();
        $issues['students'] = $studentIssues;
        $summary['students_with_incorrect_fees'] = $studentIssues->where('issue_type', 'incorrect_academic_level_fees')->count();
        $summary['conflicting_fee_assignments'] = $studentIssues->where('issue_type', 'conflicting_fees')->count();

        // 3. Display Summary
        $this->displaySummary($summary);

        // 4. Display Details if requested
        if ($this->option('show-details')) {
            $this->displayDetailedIssues($issues);
        }

        // 5. Export if requested
        if ($this->option('export')) {
            $this->exportResults($issues, $summary);
        }

        // 6. Provide recommendations
        $this->displayRecommendations($issues);

        return Command::SUCCESS;
    }

    private function auditClassAcademicLevels(): Collection
    {
        $issues = collect();
        $classes = Classes::all();

        foreach ($classes as $class) {
            // Check if academic_level is missing
            if (empty($class->academic_level)) {
                $detectedLevel = AcademicLevelConfig::detectAcademicLevel($class->name);

                $issues->push([
                    'issue_type' => 'missing_academic_level',
                    'class_id' => $class->id,
                    'class_name' => $class->name,
                    'current_academic_level' => null,
                    'detected_academic_level' => $detectedLevel,
                    'severity' => 'high',
                    'description' => "Class '{$class->name}' has no academic level assigned"
                ]);
            } else {
                // Check if assigned level matches detected level
                $detectedLevel = AcademicLevelConfig::detectAcademicLevel($class->name);

                if ($detectedLevel && $detectedLevel !== $class->academic_level) {
                    $issues->push([
                        'issue_type' => 'mismatched_academic_level',
                        'class_id' => $class->id,
                        'class_name' => $class->name,
                        'current_academic_level' => $class->academic_level,
                        'detected_academic_level' => $detectedLevel,
                        'severity' => 'medium',
                        'description' => "Class '{$class->name}' assigned as '{$class->academic_level}' but detected as '{$detectedLevel}'"
                    ]);
                }
            }
        }

        return $issues;
    }

    private function auditStudentFeeAssignments(): Collection
    {
        $issues = collect();
        $currentSession = setting('session');

        // Get students with their academic levels and fee assignments
        $students = Student::with(['sessionStudentDetails.class', 'studentServices.feeType'])
            ->whereHas('sessionStudentDetails', function($query) use ($currentSession) {
                $query->where('session_id', $currentSession);
            })
            ->get();

        foreach ($students as $student) {
            $academicLevel = $student->getAcademicLevel();
            $activeServices = $student->studentServices->where('is_active', true);

            // Check for incorrect academic level fees
            foreach ($activeServices as $service) {
                $feeType = $service->feeType;

                if ($feeType && $feeType->academic_level !== 'all' && $feeType->academic_level !== $academicLevel) {
                    $issues->push([
                        'issue_type' => 'incorrect_academic_level_fees',
                        'student_id' => $student->id,
                        'student_name' => $student->full_name,
                        'class_name' => $student->sessionStudentDetails?->class?->name,
                        'student_academic_level' => $academicLevel,
                        'fee_type_name' => $feeType->name,
                        'fee_academic_level' => $feeType->academic_level,
                        'service_id' => $service->id,
                        'severity' => 'high',
                        'description' => "Student in {$academicLevel} level has {$feeType->academic_level} level fee '{$feeType->name}'"
                    ]);
                }
            }

            // Check for conflicting fees (multiple academic level fees for same category)
            $feesByCategory = $activeServices->groupBy(function($service) {
                return $service->feeType?->category ?? 'unknown';
            });

            foreach ($feesByCategory as $category => $categoryServices) {
                $academicLevels = $categoryServices->pluck('feeType.academic_level')->filter(function($level) {
                    return $level !== 'all';
                })->unique();

                if ($academicLevels->count() > 1) {
                    $issues->push([
                        'issue_type' => 'conflicting_fees',
                        'student_id' => $student->id,
                        'student_name' => $student->full_name,
                        'class_name' => $student->sessionStudentDetails?->class?->name,
                        'student_academic_level' => $academicLevel,
                        'category' => $category,
                        'conflicting_levels' => $academicLevels->values()->toArray(),
                        'fee_names' => $categoryServices->pluck('feeType.name')->filter()->toArray(),
                        'severity' => 'critical',
                        'description' => "Student has conflicting {$category} fees for multiple academic levels: " . $academicLevels->join(', ')
                    ]);
                }
            }
        }

        return $issues;
    }

    private function displaySummary(array $summary): void
    {
        $this->newLine();
        $this->info('📊 Summary Report');
        $this->info('=================');

        $this->table(
            ['Metric', 'Count', 'Status'],
            [
                ['Total Classes', $summary['total_classes'], '📈'],
                ['Classes Missing Academic Level', $summary['classes_without_academic_level'], $summary['classes_without_academic_level'] > 0 ? '⚠️' : '✅'],
                ['Classes with Mismatched Levels', $summary['classes_with_mismatched_levels'], $summary['classes_with_mismatched_levels'] > 0 ? '⚠️' : '✅'],
                ['Students with Incorrect Fees', $summary['students_with_incorrect_fees'], $summary['students_with_incorrect_fees'] > 0 ? '🚨' : '✅'],
                ['Students with Conflicting Fees', $summary['conflicting_fee_assignments'], $summary['conflicting_fee_assignments'] > 0 ? '🚨' : '✅'],
            ]
        );
    }

    private function displayDetailedIssues(array $issues): void
    {
        $this->newLine();
        $this->info('🔍 Detailed Issues');
        $this->info('==================');

        // Class Issues
        if ($issues['classes']->isNotEmpty()) {
            $this->warn('📋 Class Academic Level Issues:');
            foreach ($issues['classes'] as $issue) {
                $severity = $this->getSeverityIcon($issue['severity']);
                $this->line("  {$severity} {$issue['description']}");
                if ($issue['detected_academic_level']) {
                    $this->line("     → Suggested: {$issue['detected_academic_level']}");
                }
            }
            $this->newLine();
        }

        // Student Issues
        if ($issues['students']->isNotEmpty()) {
            $this->warn('👥 Student Fee Assignment Issues:');
            foreach ($issues['students']->take(20) as $issue) { // Limit to first 20 for readability
                $severity = $this->getSeverityIcon($issue['severity']);
                $this->line("  {$severity} {$issue['description']}");
                $this->line("     → Student: {$issue['student_name']} (Class: {$issue['class_name']})");
            }

            if ($issues['students']->count() > 20) {
                $remaining = $issues['students']->count() - 20;
                $this->line("  ... and {$remaining} more issues");
            }
        }
    }

    private function getSeverityIcon(string $severity): string
    {
        return match($severity) {
            'critical' => '🚨',
            'high' => '⚠️',
            'medium' => '⚡',
            'low' => 'ℹ️',
            default => '📍'
        };
    }

    private function exportResults(array $issues, array $summary): void
    {
        $format = $this->option('export');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "academic_level_audit_{$timestamp}.{$format}";

        $data = [
            'summary' => $summary,
            'class_issues' => $issues['classes']->toArray(),
            'student_issues' => $issues['students']->toArray(),
            'generated_at' => now()->toISOString()
        ];

        if ($format === 'json') {
            file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
        } elseif ($format === 'csv') {
            // Export as CSV with multiple sheets (simplified)
            $csv = fopen($filename, 'w');
            fputcsv($csv, ['Summary Report']);
            foreach ($summary as $key => $value) {
                fputcsv($csv, [$key, $value]);
            }
            fputcsv($csv, []);
            fputcsv($csv, ['Class Issues']);
            fputcsv($csv, ['Issue Type', 'Class Name', 'Current Level', 'Detected Level', 'Severity', 'Description']);
            foreach ($issues['classes'] as $issue) {
                fputcsv($csv, [
                    $issue['issue_type'],
                    $issue['class_name'],
                    $issue['current_academic_level'] ?? '',
                    $issue['detected_academic_level'] ?? '',
                    $issue['severity'],
                    $issue['description']
                ]);
            }
            fclose($csv);
        }

        $this->info("📄 Results exported to: {$filename}");
    }

    private function displayRecommendations(array $issues): void
    {
        $this->newLine();
        $this->info('💡 Recommendations');
        $this->info('==================');

        $recommendations = [];

        if ($issues['classes']->where('issue_type', 'missing_academic_level')->isNotEmpty()) {
            $recommendations[] = "Run 'php artisan fees:fix-academic-levels' to populate missing academic levels";
        }

        if ($issues['classes']->where('issue_type', 'mismatched_academic_level')->isNotEmpty()) {
            $recommendations[] = "Review and update class academic level assignments manually or run fix command";
        }

        if ($issues['students']->where('issue_type', 'incorrect_academic_level_fees')->isNotEmpty()) {
            $recommendations[] = "Run 'php artisan fees:fix-student-assignments' to correct student fee assignments";
        }

        if ($issues['students']->where('issue_type', 'conflicting_fees')->isNotEmpty()) {
            $recommendations[] = "Manually review conflicting fee assignments - requires administrative decision";
        }

        if (empty($recommendations)) {
            $this->info('✅ No issues found - academic level assignments appear to be correct!');
        } else {
            foreach ($recommendations as $recommendation) {
                $this->line("  • {$recommendation}");
            }
        }

        $this->newLine();
        $this->info('🚀 Next Steps:');
        $this->line('  1. Review the audit results above');
        $this->line('  2. Run fix commands for automated corrections');
        $this->line('  3. Manually review any critical issues');
        $this->line('  4. Re-run this audit to verify fixes');
    }
}