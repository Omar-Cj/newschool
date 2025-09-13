<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Academic\Classes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AssignAcademicLevelsToClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classes:assign-academic-levels {--dry-run : Show what would be changed without making actual changes} {--auto : Automatically assign suggested levels without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign academic levels to existing classes that don\'t have them assigned';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $isAuto = $this->option('auto');
        
        $this->info('🏫 Starting Academic Level Assignment for Classes...');
        $this->info('Mode: ' . ($isDryRun ? 'DRY RUN (no changes will be made)' : 'LIVE RUN'));

        try {
            // Get all classes without academic levels
            $classesWithoutLevels = Classes::withoutAcademicLevel()->get();
            $allClasses = Classes::all();
            
            $this->info("📊 Found {$classesWithoutLevels->count()} classes without academic levels out of {$allClasses->count()} total classes");

            if ($classesWithoutLevels->isEmpty()) {
                $this->info('✅ All classes already have academic levels assigned!');
                return Command::SUCCESS;
            }

            // Show current statistics
            $this->displayStatistics();

            $stats = [
                'total_processed' => 0,
                'auto_assigned' => 0,
                'manual_assigned' => 0,
                'skipped' => 0,
                'errors' => 0
            ];

            if (!$isDryRun) {
                DB::beginTransaction();
            }

            foreach ($classesWithoutLevels as $class) {
                try {
                    $result = $this->processClass($class, $isDryRun, $isAuto);
                    
                    $stats['total_processed']++;
                    $stats[$result['type']]++;
                    
                } catch (\Exception $e) {
                    $stats['errors']++;
                    $this->error("❌ Error processing class {$class->name} (ID: {$class->id}): " . $e->getMessage());
                    Log::error('Error assigning academic level to class', [
                        'class_id' => $class->id,
                        'class_name' => $class->name,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if (!$isDryRun) {
                if ($stats['errors'] == 0) {
                    DB::commit();
                    $this->info('✅ All changes committed successfully!');
                } else {
                    DB::rollback();
                    $this->error('❌ Some errors occurred. All changes have been rolled back.');
                }
            }

            $this->displaySummary($stats);
            $this->displayUpdatedStatistics();

            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            if (!$isDryRun) {
                DB::rollback();
            }
            $this->error('❌ Command failed: ' . $e->getMessage());
            Log::error('Academic level assignment command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Process a single class
     */
    private function processClass(Classes $class, bool $isDryRun, bool $isAuto): array
    {
        $suggestion = $class->suggestAcademicLevel();
        
        $this->line("📝 Class: \"{$class->name}\" (ID: {$class->id})");
        
        if ($suggestion) {
            $levelName = $this->getAcademicLevelName($suggestion);
            $this->line("   💡 Suggested Level: {$levelName}");
            
            if ($isAuto) {
                // Auto-assign the suggestion
                if (!$isDryRun) {
                    $class->update(['academic_level' => $suggestion]);
                }
                $this->line("   ✅ Auto-assigned: {$levelName}");
                return ['type' => 'auto_assigned'];
            } else {
                // Ask for confirmation
                $confirmed = $this->confirm("   ❓ Assign \"{$levelName}\" to \"{$class->name}\"?", true);
                
                if ($confirmed) {
                    if (!$isDryRun) {
                        $class->update(['academic_level' => $suggestion]);
                    }
                    $this->line("   ✅ Assigned: {$levelName}");
                    return ['type' => 'manual_assigned'];
                } else {
                    $this->line("   ⏭️  Skipped");
                    return ['type' => 'skipped'];
                }
            }
        } else {
            $this->line("   ❓ No suggestion available for this class name");
            
            if (!$isAuto) {
                $this->line("   Available levels: KG, Primary (1-8), Secondary (Form 1-4), High School (11-12)");
                $manualLevel = $this->ask("   Enter academic level (kg/primary/secondary/high_school) or press Enter to skip");
                
                if ($manualLevel && in_array($manualLevel, ['kg', 'primary', 'secondary', 'high_school'])) {
                    if (!$isDryRun) {
                        $class->update(['academic_level' => $manualLevel]);
                    }
                    $levelName = $this->getAcademicLevelName($manualLevel);
                    $this->line("   ✅ Manually assigned: {$levelName}");
                    return ['type' => 'manual_assigned'];
                }
            }
            
            $this->line("   ⏭️  Skipped");
            return ['type' => 'skipped'];
        }
    }

    /**
     * Get human-readable academic level name
     */
    private function getAcademicLevelName(string $level): string
    {
        return match($level) {
            'kg' => 'Kindergarten',
            'primary' => 'Primary School', 
            'secondary' => 'Secondary School',
            'high_school' => 'High School',
            default => ucfirst($level)
        };
    }

    /**
     * Display current statistics
     */
    private function displayStatistics(): void
    {
        $counts = Classes::getAcademicLevelCounts();
        
        $this->info('');
        $this->info('📊 CURRENT ACADEMIC LEVEL DISTRIBUTION:');
        $this->table(['Academic Level', 'Count'], [
            ['Kindergarten', $counts['kg'] ?? 0],
            ['Primary School', $counts['primary'] ?? 0], 
            ['Secondary School', $counts['secondary'] ?? 0],
            ['High School', $counts['high_school'] ?? 0],
            ['⚠️ Unassigned', $counts['unassigned'] ?? 0]
        ]);
        $this->info('');
    }

    /**
     * Display updated statistics
     */
    private function displayUpdatedStatistics(): void
    {
        if ($this->option('dry-run')) {
            $this->info('📊 Statistics will be updated after running without --dry-run');
            return;
        }
        
        $this->info('');
        $this->info('📊 UPDATED ACADEMIC LEVEL DISTRIBUTION:');
        $this->displayStatistics();
    }

    /**
     * Display summary of the assignment process
     */
    private function displaySummary(array $stats): void
    {
        $this->info('');
        $this->info('📋 ASSIGNMENT SUMMARY:');
        $this->info("Total classes processed: {$stats['total_processed']}");
        $this->info("Auto-assigned: {$stats['auto_assigned']}");
        $this->info("Manually assigned: {$stats['manual_assigned']}");
        $this->info("Skipped: {$stats['skipped']}");
        $this->info("Errors: {$stats['errors']}");
        
        if ($stats['errors'] > 0) {
            $this->warn('⚠️  Some errors occurred. Check the logs for details.');
        }
        
        $totalAssigned = $stats['auto_assigned'] + $stats['manual_assigned'];
        if ($totalAssigned > 0) {
            $this->info("✅ Successfully processed {$totalAssigned} classes");
            
            if (!$this->option('dry-run')) {
                $this->info('');
                $this->info('🎯 Next Steps:');
                $this->info('1. Run student registration tests to verify fee assignment works correctly');
                $this->info('2. Check that students in updated classes get the right academic level fees');
                $this->info('3. Consider running: php artisan students:fix-academic-levels');
            }
        }
    }
}