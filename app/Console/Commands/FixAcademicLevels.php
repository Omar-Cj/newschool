<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Classes;
use App\Models\StudentInfo\Student;
use App\Models\StudentService;
use App\Models\Fees\FeesType;
use App\Models\AcademicLevelConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixAcademicLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:fix-academic-levels
                            {--dry-run : Preview changes without applying them}
                            {--force : Skip confirmation prompts}
                            {--class-id=* : Fix specific class IDs only}
                            {--backup : Create backup before making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix academic level assignments for classes and update related fee assignments';

    /**
     * Execute the console command.
     *
     * @return int
     */
    protected $stats = [
        'classes_updated' => 0,
        'students_affected' => 0,
        'fee_assignments_corrected' => 0,
        'conflicts_resolved' => 0
    ];

    public function handle()
    {
        $this->info('🔧 Academic Level Fix Utility');
        $this->info('==============================');
        $this->newLine();

        $isDryRun = $this->option('dry-run');
        $isForce = $this->option('force');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be applied');
        }

        // 1. Create backup if requested
        if ($this->option('backup') && !$isDryRun) {
            $this->createBackup();
        }

        // 2. Get confirmation unless forced
        if (!$isDryRun && !$isForce) {
            if (!$this->confirm('This will update academic level assignments and fee data. Continue?')) {
                $this->info('Operation cancelled.');
                return Command::FAILURE;
            }
        }

        // 3. Fix class academic levels
        $classesToFix = $this->getClassesToFix();
        $this->info("Found {$classesToFix->count()} classes that need academic level fixes");

        if ($classesToFix->isEmpty()) {
            $this->info('✅ No classes need academic level fixes');
            return Command::SUCCESS;
        }

        // 4. Apply fixes
        DB::transaction(function () use ($classesToFix, $isDryRun) {
            $this->fixClassAcademicLevels($classesToFix, $isDryRun);

            if (!$isDryRun) {
                $this->fixStudentFeeAssignments($isDryRun);
            }
        });

        // 5. Display results
        $this->displayResults($isDryRun);

        return Command::SUCCESS;
    }

    private function getClassesToFix()
    {
        $query = Classes::query();

        // Filter by specific class IDs if provided
        if ($this->option('class-id')) {
            $query->whereIn('id', $this->option('class-id'));
        }

        return $query->get()->filter(function ($class) {
            // Include classes that have no academic level or mismatched level
            if (empty($class->academic_level)) {
                return true;
            }

            $detectedLevel = AcademicLevelConfig::detectAcademicLevel($class->name);
            return $detectedLevel && $detectedLevel !== $class->academic_level;
        });
    }

    private function fixClassAcademicLevels($classes, bool $isDryRun): void
    {
        $this->info('🏫 Fixing Class Academic Levels...');

        $progressBar = $this->output->createProgressBar($classes->count());

        foreach ($classes as $class) {
            $detectedLevel = AcademicLevelConfig::detectAcademicLevel($class->name);

            if ($detectedLevel) {
                $oldLevel = $class->academic_level ?? 'null';

                if ($isDryRun) {
                    $this->line("  Would update: '{$class->name}' from '{$oldLevel}' to '{$detectedLevel}'");
                } else {
                    $class->update([
                        'academic_level' => $detectedLevel,
                        'updated_at' => now()
                    ]);

                    Log::info('Academic level updated for class', [
                        'class_id' => $class->id,
                        'class_name' => $class->name,
                        'old_level' => $oldLevel,
                        'new_level' => $detectedLevel,
                        'updated_by' => 'fix_command'
                    ]);

                    $this->stats['classes_updated']++;
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    private function fixStudentFeeAssignments(bool $isDryRun): void
    {
        $this->info('👥 Fixing Student Fee Assignments...');

        $currentSession = setting('session');

        // Get students with potentially incorrect fee assignments
        $studentsWithIssues = Student::with(['sessionStudentDetails.class', 'studentServices.feeType'])
            ->whereHas('sessionStudentDetails', function($query) use ($currentSession) {
                $query->where('session_id', $currentSession);
            })
            ->whereHas('studentServices', function($query) {
                $query->where('is_active', true);
            })
            ->get()
            ->filter(function ($student) {
                return $this->studentHasIncorrectFees($student);
            });

        if ($studentsWithIssues->isEmpty()) {
            $this->info('✅ No students found with incorrect fee assignments');
            return;
        }

        $this->info("Found {$studentsWithIssues->count()} students with fee assignment issues");

        $progressBar = $this->output->createProgressBar($studentsWithIssues->count());

        foreach ($studentsWithIssues as $student) {
            $this->fixStudentFees($student, $isDryRun);
            $this->stats['students_affected']++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    private function studentHasIncorrectFees(Student $student): bool
    {
        $academicLevel = $student->getAcademicLevel();
        $activeServices = $student->studentServices->where('is_active', true);

        foreach ($activeServices as $service) {
            $feeType = $service->feeType;
            if ($feeType && $feeType->academic_level !== 'all' && $feeType->academic_level !== $academicLevel) {
                return true;
            }
        }

        return false;
    }

    private function fixStudentFees(Student $student, bool $isDryRun): void
    {
        $academicLevel = $student->getAcademicLevel();
        $activeServices = $student->studentServices->where('is_active', true);

        foreach ($activeServices as $service) {
            $feeType = $service->feeType;

            if (!$feeType || $feeType->academic_level === 'all' || $feeType->academic_level === $academicLevel) {
                continue;
            }

            // This is an incorrect fee assignment
            if ($isDryRun) {
                $this->line("  Would deactivate: {$student->full_name} - {$feeType->name} ({$feeType->academic_level} fee for {$academicLevel} student)");
            } else {
                $service->update([
                    'is_active' => false,
                    'notes' => ($service->notes ? $service->notes . ' | ' : '') .
                              "Deactivated by fix command - academic level mismatch. Student level: {$academicLevel}, Fee level: {$feeType->academic_level}",
                    'updated_by' => auth()->id() ?? 0,
                    'updated_at' => now()
                ]);

                Log::info('Deactivated incorrect fee assignment', [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'student_academic_level' => $academicLevel,
                    'fee_type' => $feeType->name,
                    'fee_academic_level' => $feeType->academic_level,
                    'service_id' => $service->id
                ]);

                $this->stats['fee_assignments_corrected']++;
            }

            // Try to find and assign correct fee for this academic level
            $correctFee = FeesType::active()
                ->where('category', $feeType->category)
                ->where(function($query) use ($academicLevel) {
                    $query->where('academic_level', $academicLevel)
                          ->orWhere('academic_level', 'all');
                })
                ->where('is_mandatory_for_level', true)
                ->first();

            if ($correctFee && !$isDryRun) {
                // Check if student already has this correct fee
                $existingCorrectService = $student->studentServices()
                    ->where('fee_type_id', $correctFee->id)
                    ->where('academic_year_id', $service->academic_year_id)
                    ->first();

                if (!$existingCorrectService) {
                    // Create correct fee assignment
                    StudentService::create([
                        'student_id' => $student->id,
                        'fee_type_id' => $correctFee->id,
                        'academic_year_id' => $service->academic_year_id,
                        'base_amount' => $correctFee->amount,
                        'final_amount' => $correctFee->amount,
                        'due_date' => $service->due_date,
                        'is_active' => true,
                        'notes' => "Auto-assigned by fix command to replace incorrect {$feeType->academic_level} level fee",
                        'created_by' => auth()->id() ?? 0
                    ]);

                    Log::info('Auto-assigned correct fee', [
                        'student_id' => $student->id,
                        'student_name' => $student->full_name,
                        'student_academic_level' => $academicLevel,
                        'new_fee_type' => $correctFee->name,
                        'new_fee_academic_level' => $correctFee->academic_level
                    ]);
                }
            }
        }
    }

    private function createBackup(): void
    {
        $this->info('💾 Creating backup...');

        $timestamp = now()->format('Y_m_d_H_i_s');
        $backupData = [
            'timestamp' => $timestamp,
            'classes' => Classes::all()->toArray(),
            'student_services' => StudentService::where('is_active', true)->get()->toArray()
        ];

        $backupFile = storage_path("backups/academic_level_fix_backup_{$timestamp}.json");

        // Ensure backup directory exists
        if (!file_exists(dirname($backupFile))) {
            mkdir(dirname($backupFile), 0755, true);
        }

        file_put_contents($backupFile, json_encode($backupData, JSON_PRETTY_PRINT));

        $this->info("✅ Backup created: {$backupFile}");
    }

    private function displayResults(bool $isDryRun): void
    {
        $this->newLine();
        $this->info($isDryRun ? '🔍 Dry Run Results' : '✅ Fix Results');
        $this->info('===================');

        $this->table(
            ['Metric', 'Count'],
            [
                ['Classes Updated', $this->stats['classes_updated']],
                ['Students Affected', $this->stats['students_affected']],
                ['Fee Assignments Corrected', $this->stats['fee_assignments_corrected']],
                ['Conflicts Resolved', $this->stats['conflicts_resolved']]
            ]
        );

        if ($isDryRun) {
            $this->newLine();
            $this->info('💡 To apply these changes, run the command without --dry-run');
        } else {
            $this->newLine();
            $this->info('🎉 Academic level fixes completed successfully!');
            $this->info('💡 Run "php artisan fees:audit-academic-levels" to verify the fixes');
        }
    }
}