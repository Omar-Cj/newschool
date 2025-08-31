<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Database\Seeders\StudentInfo\SomalilandStudentSeeder;
use App\Models\Academic\Classes;
use App\Models\Gender;
use App\Models\Religion;
use App\Models\BloodGroup;
use App\Models\Session;

class SeedSomalilandStudentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:seed-somaliland 
                            {--count=50 : Number of students to generate}
                            {--classes=all : Specific classes or "all" for distribution}
                            {--with-parents=true : Generate parent/guardian records}
                            {--branch=1 : Branch ID to assign students}
                            {--dry-run : Preview what will be created without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate sample student registration data with authentic Somaliland demographics';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        $classes = $this->option('classes');
        $withParents = $this->option('with-parents') !== 'false';
        $branchId = (int) $this->option('branch');
        $isDryRun = $this->option('dry-run');

        // Validation phase
        $this->info('🔍 Validating system prerequisites...');
        
        if (!$this->validatePrerequisites($branchId)) {
            return Command::FAILURE;
        }

        $this->info('✅ All prerequisites validated successfully');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No data will be created');
        }

        // Display generation plan
        $this->displayGenerationPlan($count, $classes, $withParents, $branchId);

        if (!$this->confirm('Do you want to proceed with generating Somaliland student data?', true)) {
            $this->info('Operation cancelled.');
            return Command::SUCCESS;
        }

        // Execute data generation
        try {
            $this->info('🚀 Starting Somaliland student data generation...');
            
            $seeder = new SomalilandStudentSeeder();
            $seeder->setCommand($this);
            $seeder->setOptions([
                'count' => $count,
                'classes' => $classes,
                'with_parents' => $withParents,
                'branch_id' => $branchId,
                'dry_run' => $isDryRun
            ]);

            if (!$isDryRun) {
                DB::transaction(function () use ($seeder) {
                    $seeder->run();
                });
            } else {
                $seeder->run();
            }

            $this->newLine();
            $this->info('🎉 Somaliland student data generation completed successfully!');
            
            if (!$isDryRun) {
                $this->displaySuccessSummary($count, $withParents);
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error during data generation: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    /**
     * Validate system prerequisites
     */
    private function validatePrerequisites(int $branchId): bool
    {
        // Check if classes exist
        $classCount = Classes::where('branch_id', $branchId)->count();
        if ($classCount === 0) {
            $this->error("❌ No classes found for branch ID: {$branchId}");
            return false;
        }
        $this->line("   ✓ Found {$classCount} classes for branch {$branchId}");

        // Check if reference data exists
        if (Gender::count() === 0) {
            $this->error('❌ No gender records found. Please run gender seeder first.');
            return false;
        }
        $this->line('   ✓ Gender reference data available');

        if (Religion::count() === 0) {
            $this->error('❌ No religion records found. Please run religion seeder first.');
            return false;
        }
        $this->line('   ✓ Religion reference data available');

        if (BloodGroup::count() === 0) {
            $this->error('❌ No blood group records found. Please run blood group seeder first.');
            return false;
        }
        $this->line('   ✓ Blood group reference data available');

        // Check if current session exists
        $currentSession = Session::first();
        if (!$currentSession) {
            $this->error('❌ No session found. Please create an academic session first.');
            return false;
        }
        $this->line("   ✓ Current session available: {$currentSession->name}");

        return true;
    }

    /**
     * Display generation plan
     */
    private function displayGenerationPlan(int $count, string $classes, bool $withParents, int $branchId): void
    {
        $this->newLine();
        $this->info('📋 Generation Plan:');
        $this->line("   👥 Students to generate: {$count}");
        $this->line("   🏫 Classes: {$classes}");
        $this->line("   👨‍👩‍👧‍👦 Include parents/guardians: " . ($withParents ? 'Yes' : 'No'));
        $this->line("   🏢 Branch ID: {$branchId}");
        $this->line("   🇸🇴 Demographics: Authentic Somaliland data");
        $this->newLine();
    }

    /**
     * Display success summary
     */
    private function displaySuccessSummary(int $count, bool $withParents): void
    {
        $this->info('📊 Generation Summary:');
        $this->line("   ✅ Students created: {$count}");
        $this->line("   ✅ User accounts created: {$count}");
        
        if ($withParents) {
            $parentCount = ceil($count / 2); // Approximate, some siblings share parents
            $this->line("   ✅ Parent/Guardian records created: ~{$parentCount}");
        }
        
        $this->line("   ✅ Session enrollments created: {$count}");
        $this->line("   🇸🇴 All data uses authentic Somaliland demographics");
        $this->newLine();
        $this->info('💡 You can now use these students for testing and demonstration purposes.');
    }
}