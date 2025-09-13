<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentInfo\Student;
use App\Models\StudentService;
use App\Models\Fees\FeesType;
use App\Services\StudentServiceManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixStudentAcademicLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:fix-academic-levels {--dry-run : Show what would be changed without making actual changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix student academic level assignments after configuration update';

    /**
     * Service manager for handling student services
     */
    private StudentServiceManager $serviceManager;

    /**
     * Create a new command instance.
     */
    public function __construct(StudentServiceManager $serviceManager)
    {
        parent::__construct();
        $this->serviceManager = $serviceManager;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('Starting academic level fix process...');
        $this->info('Mode: ' . ($isDryRun ? 'DRY RUN (no changes will be made)' : 'LIVE RUN'));

        try {
            DB::beginTransaction();
            
            // Get all active students with their class information
            $students = Student::active()
                ->with(['sessionStudentDetails.class'])
                ->get();

            $this->info("Found {$students->count()} active students to process");

            $stats = [
                'total_processed' => 0,
                'level_changes' => 0,
                'service_updates' => 0,
                'errors' => 0
            ];

            foreach ($students as $student) {
                try {
                    $result = $this->processStudent($student, $isDryRun);
                    
                    $stats['total_processed']++;
                    if ($result['level_changed']) {
                        $stats['level_changes']++;
                    }
                    if ($result['services_updated']) {
                        $stats['service_updates']++;
                    }
                    
                } catch (\Exception $e) {
                    $stats['errors']++;
                    $this->error("Error processing student {$student->id}: " . $e->getMessage());
                    Log::error('Error processing student academic level', [
                        'student_id' => $student->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            if (!$isDryRun) {
                DB::commit();
                $this->info('✅ Changes committed to database');
            } else {
                DB::rollback();
                $this->info('🔍 Dry run completed - no changes made');
            }

            $this->displaySummary($stats);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ Migration failed: ' . $e->getMessage());
            Log::error('Academic level fix command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Process a single student
     */
    private function processStudent(Student $student, bool $isDryRun): array
    {
        $result = [
            'level_changed' => false,
            'services_updated' => false
        ];

        // Determine the correct academic level using updated configuration
        $oldLevel = $this->getOldAcademicLevel($student);
        $newLevel = $this->serviceManager->determineAcademicLevel($student);

        if ($oldLevel !== $newLevel) {
            $this->line("Student {$student->id} ({$student->full_name}): {$oldLevel} → {$newLevel}");
            $result['level_changed'] = true;

            if (!$isDryRun) {
                // Update student services that were assigned with wrong academic level
                $this->updateStudentServices($student, $oldLevel, $newLevel);
                $result['services_updated'] = true;
            }
        }

        return $result;
    }

    /**
     * Get the old academic level that would have been assigned with previous config
     */
    private function getOldAcademicLevel(Student $student): string
    {
        $className = $student->sessionStudentDetails?->class?->name ?? '';
        
        // Extract numeric value from class name for old logic
        $classNumber = 0;
        if (preg_match('/(\d+)/', $className, $matches)) {
            $classNumber = (int) $matches[1];
        }
        
        // Old logic before the fix (grades 6-10 were considered secondary)
        return match(true) {
            $classNumber >= 1 && $classNumber <= 5 => 'primary',
            $classNumber >= 6 && $classNumber <= 10 => 'secondary', 
            $classNumber >= 11 && $classNumber <= 12 => 'high_school',
            $classNumber < 1 => 'kg',
            default => 'primary'
        };
    }

    /**
     * Update student services that were assigned with wrong academic level
     */
    private function updateStudentServices(Student $student, string $oldLevel, string $newLevel): void
    {
        $academicYearId = session('academic_year_id') ?? 
            \App\Models\Session::active()->value('id');

        if (!$academicYearId) {
            $this->warn("No active academic year found for student {$student->id}");
            return;
        }

        // Find student services that were assigned based on wrong academic level
        $wrongServices = StudentService::where('student_id', $student->id)
            ->where('academic_year_id', $academicYearId)
            ->where('is_active', true)
            ->whereHas('feeType', function($q) use ($oldLevel) {
                $q->where('academic_level', $oldLevel);
            })
            ->get();

        foreach ($wrongServices as $service) {
            // Check if this service type is applicable for the new level
            $correctFeeType = FeesType::where('name', $service->feeType->name)
                ->where('category', $service->feeType->category)
                ->where(function($q) use ($newLevel) {
                    $q->where('academic_level', $newLevel)
                      ->orWhere('academic_level', 'all');
                })
                ->first();

            if ($correctFeeType) {
                // Update the service to use the correct fee type
                $service->update([
                    'fee_type_id' => $correctFeeType->id,
                    'amount' => $correctFeeType->amount,
                    'final_amount' => $correctFeeType->amount, // Reset final amount
                    'notes' => ($service->notes ? $service->notes . ' | ' : '') . 
                              "Academic level corrected from {$oldLevel} to {$newLevel}",
                    'updated_by' => 1 // System user
                ]);

                $this->line("  ↳ Updated service: {$service->feeType->name} → {$correctFeeType->name}");
            } else {
                // Deactivate services that are not applicable for the new level
                $service->update([
                    'is_active' => false,
                    'notes' => ($service->notes ? $service->notes . ' | ' : '') . 
                              "Deactivated: not applicable for {$newLevel} level",
                    'updated_by' => 1 // System user
                ]);

                $this->line("  ↳ Deactivated service: {$service->feeType->name} (not applicable for {$newLevel})");
            }
        }

        // Auto-assign any missing mandatory services for the new level
        try {
            $mandatoryServices = $this->serviceManager->getMandatoryServices($student);
            $existingServiceTypes = $student->activeServices($academicYearId)
                ->pluck('fee_type_id')
                ->toArray();

            foreach ($mandatoryServices as $mandatoryService) {
                if (!in_array($mandatoryService->id, $existingServiceTypes)) {
                    $this->serviceManager->subscribeToService($student, $mandatoryService, [
                        'academic_year_id' => $academicYearId,
                        'notes' => 'Auto-assigned after academic level correction'
                    ]);

                    $this->line("  ↳ Added mandatory service: {$mandatoryService->name}");
                }
            }
            
        } catch (\Exception $e) {
            $this->warn("Failed to auto-assign mandatory services for student {$student->id}: " . $e->getMessage());
        }
    }

    /**
     * Display summary of the migration
     */
    private function displaySummary(array $stats): void
    {
        $this->info('');
        $this->info('=== SUMMARY ===');
        $this->info("Total students processed: {$stats['total_processed']}");
        $this->info("Students with level changes: {$stats['level_changes']}");
        $this->info("Students with service updates: {$stats['service_updates']}");
        $this->info("Errors encountered: {$stats['errors']}");
        
        if ($stats['errors'] > 0) {
            $this->warn('⚠️  Some errors occurred. Check the logs for details.');
        }
        
        if ($stats['level_changes'] > 0) {
            $this->info('📝 Students with academic level changes have been logged and updated.');
        }
    }
}