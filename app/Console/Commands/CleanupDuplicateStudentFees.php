<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentService;
use App\Models\StudentInfo\Student;
use App\Services\StudentServiceManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupDuplicateStudentFees extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'students:cleanup-duplicate-fees 
                            {--dry-run : Show what would be cleaned up without making changes}
                            {--auto-fix : Automatically fix conflicts without prompting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up students with duplicate or conflicting fee assignments (e.g., both primary and secondary fees)';

    private StudentServiceManager $serviceManager;

    public function __construct(StudentServiceManager $serviceManager)
    {
        parent::__construct();
        $this->serviceManager = $serviceManager;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $autoFix = $this->option('auto-fix');

        $this->info('🔍 Starting duplicate fee cleanup for students...');
        $this->info('Mode: ' . ($isDryRun ? 'DRY RUN (no changes will be made)' : 'LIVE RUN'));
        $this->newLine();

        // Find students with conflicting fee assignments
        $conflictingStudents = $this->findStudentsWithConflictingFees();

        if ($conflictingStudents->isEmpty()) {
            $this->info('✅ No students found with conflicting fee assignments!');
            return;
        }

        $this->warn("Found {$conflictingStudents->count()} students with conflicting fee assignments:");
        $this->newLine();

        $fixedCount = 0;
        $errorCount = 0;

        foreach ($conflictingStudents as $studentData) {
            $student = $studentData['student'];
            $conflicts = $studentData['conflicts'];

            $this->info("Student: {$student->full_name} (ID: {$student->id})");
            $this->info("Class: {$student->sessionStudentDetails?->class?->name}");
            $this->info("Academic Level: {$student->getAcademicLevel()}");
            
            $this->warn("Conflicting Fees:");
            foreach ($conflicts as $conflict) {
                $this->line("  - {$conflict['fee_name']} ({$conflict['academic_level']}) - Amount: {$conflict['amount']}");
            }

            if (!$isDryRun) {
                if ($autoFix || $this->confirm('Fix conflicts for this student?', true)) {
                    try {
                        $this->fixStudentFeeConflicts($student, $conflicts);
                        $fixedCount++;
                        $this->info("✅ Fixed conflicts for {$student->full_name}");
                    } catch (\Exception $e) {
                        $errorCount++;
                        $this->error("❌ Failed to fix conflicts for {$student->full_name}: " . $e->getMessage());
                    }
                }
            }
            
            $this->newLine();
        }

        // Summary
        $this->info('📊 Cleanup Summary:');
        $this->info("Students with conflicts: {$conflictingStudents->count()}");
        
        if (!$isDryRun) {
            $this->info("Successfully fixed: {$fixedCount}");
            $this->info("Errors encountered: {$errorCount}");
        }

        if ($isDryRun) {
            $this->info('💡 Run without --dry-run to apply fixes');
        }
    }

    /**
     * Find students with conflicting fee assignments
     */
    private function findStudentsWithConflictingFees()
    {
        $results = collect();
        $currentAcademicYear = session('academic_year_id');

        // Get all students with active services
        $studentsWithServices = StudentService::where('is_active', true)
            ->where('academic_year_id', $currentAcademicYear)
            ->with(['student.sessionStudentDetails.class', 'feeType'])
            ->get()
            ->groupBy('student_id');

        foreach ($studentsWithServices as $studentId => $services) {
            $student = $services->first()->student;
            if (!$student) continue;

            $correctAcademicLevel = $student->getAcademicLevel();
            
            // Find mandatory academic fees (tuition fees)
            $mandatoryAcademicFees = $services->filter(function ($service) {
                return $service->feeType->is_mandatory_for_level && 
                       $service->feeType->category === 'academic';
            });

            if ($mandatoryAcademicFees->count() <= 1) {
                continue; // No conflicts
            }

            // Check for conflicting academic levels
            $academicLevels = $mandatoryAcademicFees->pluck('feeType.academic_level')->unique();
            
            if ($academicLevels->count() > 1 && !$academicLevels->contains('all')) {
                // Found conflicts
                $conflicts = $mandatoryAcademicFees->map(function ($service) {
                    return [
                        'service_id' => $service->id,
                        'fee_name' => $service->feeType->name,
                        'academic_level' => $service->feeType->academic_level,
                        'amount' => $service->final_amount,
                        'is_correct' => $service->feeType->academic_level === $service->student->getAcademicLevel()
                    ];
                })->toArray();

                $results->push([
                    'student' => $student,
                    'correct_academic_level' => $correctAcademicLevel,
                    'conflicts' => $conflicts
                ]);
            }
        }

        return $results;
    }

    /**
     * Fix fee conflicts for a specific student
     */
    private function fixStudentFeeConflicts(Student $student, array $conflicts)
    {
        $correctAcademicLevel = $student->getAcademicLevel();
        
        DB::transaction(function () use ($student, $conflicts, $correctAcademicLevel) {
            foreach ($conflicts as $conflict) {
                $service = StudentService::find($conflict['service_id']);
                
                if (!$service) continue;

                if ($conflict['academic_level'] !== $correctAcademicLevel && $conflict['academic_level'] !== 'all') {
                    // Deactivate incorrect fee
                    $service->update([
                        'is_active' => false,
                        'notes' => ($service->notes ? $service->notes . ' | ' : '') . 
                                  "Deactivated by cleanup command. Student academic level: {$correctAcademicLevel}",
                        'updated_by' => 1 // System user
                    ]);

                    Log::info('Deactivated conflicting fee during cleanup', [
                        'student_id' => $student->id,
                        'service_id' => $service->id,
                        'fee_name' => $conflict['fee_name'],
                        'fee_level' => $conflict['academic_level'],
                        'correct_level' => $correctAcademicLevel
                    ]);
                }
            }

            // Ensure correct fee is assigned
            $mandatoryServices = $this->serviceManager->getMandatoryServices($student);
            foreach ($mandatoryServices as $mandatoryService) {
                try {
                    $this->serviceManager->subscribeToService($student, $mandatoryService, [
                        'notes' => 'Auto-assigned by cleanup command'
                    ]);
                } catch (\InvalidArgumentException $e) {
                    // Service already exists, which is fine
                }
            }
        });
    }
}