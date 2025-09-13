<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Fees\FeesType;
use App\Models\StudentService;
use App\Models\StudentInfo\Student;
use App\Services\StudentServiceManager;

class TestEnhancedFeeSystem extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'fees:test-enhanced 
                            {action=status : Action to perform (status|seed|clear|reset)}
                            {--students=10 : Number of students to process}
                            {--force : Force action without confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Test and manage the Enhanced Fee System';

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
        $action = $this->argument('action');

        match($action) {
            'status' => $this->showStatus(),
            'seed' => $this->seedTestData(),
            'clear' => $this->clearTestData(),
            'reset' => $this->resetSystem(),
            default => $this->error("Unknown action: {$action}")
        };
    }

    /**
     * Show system status
     */
    private function showStatus(): void
    {
        $this->info('📊 Enhanced Fee System Status');
        $this->line('================================');

        // System settings
        $enhancedEnabled = setting('use_enhanced_fee_system', false);
        $this->line("Enhanced System: " . ($enhancedEnabled ? '✅ ENABLED' : '❌ DISABLED'));

        // Fee types
        $totalFeeTypes = FeesType::count();
        $enhancedFeeTypes = FeesType::whereNotNull('academic_level')->count();
        $this->line("Fee Types: {$totalFeeTypes} total, {$enhancedFeeTypes} enhanced");

        // Students and services
        $totalStudents = Student::where('status', 1)->count();
        $studentsWithServices = StudentService::distinct('student_id')->count();
        $totalServices = StudentService::where('is_active', true)->count();
        
        $this->line("Students: {$totalStudents} active, {$studentsWithServices} with services");
        $this->line("Active Services: {$totalServices}");

        // Service breakdown by category
        $this->line("\n📋 Fee Types by Category:");
        $categories = FeesType::selectRaw('category, COUNT(*) as count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->get();

        foreach ($categories as $category) {
            $this->line("  {$category->category}: {$category->count}");
        }

        // Academic level breakdown
        $this->line("\n🎓 Fee Types by Academic Level:");
        $levels = FeesType::selectRaw('academic_level, COUNT(*) as count')
            ->whereNotNull('academic_level')
            ->groupBy('academic_level')
            ->get();

        foreach ($levels as $level) {
            $this->line("  {$level->academic_level}: {$level->count}");
        }

        // Recent activity
        $recentServices = StudentService::where('created_at', '>=', now()->subDays(7))->count();
        $this->line("\nRecent Activity (7 days): {$recentServices} new service subscriptions");
    }

    /**
     * Seed test data
     */
    private function seedTestData(): void
    {
        if (!$this->option('force') && !$this->confirm('This will create test fee types and assign services to students. Continue?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $this->info('🌱 Seeding Enhanced Fee System Test Data...');

        // Run the existing fee types seeder
        $this->call('db:seed', ['--class' => 'ExistingFeeTypesServiceSeeder']);

        $this->info('✅ Test data seeding completed!');
        $this->call('fees:test-enhanced', ['action' => 'status']);
    }

    /**
     * Clear test data
     */
    private function clearTestData(): void
    {
        if (!$this->option('force') && !$this->confirm('This will remove all student service subscriptions. Continue?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $this->info('🧹 Clearing test data...');

        $deletedServices = StudentService::count();
        StudentService::truncate();

        $this->info("✅ Cleared {$deletedServices} student service subscriptions");
    }

    /**
     * Reset entire system
     */
    private function resetSystem(): void
    {
        if (!$this->option('force') && !$this->confirm('This will reset the entire enhanced fee system. Continue?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $this->info('🔄 Resetting Enhanced Fee System...');

        // Clear services
        $servicesCount = StudentService::count();
        StudentService::truncate();

        // Reset fee types enhanced fields
        $feeTypesCount = FeesType::whereNotNull('academic_level')->count();
        FeesType::whereNotNull('academic_level')->update([
            'academic_level' => null,
            'category' => null,
            'amount' => 0,
            'due_date_offset' => 30,
            'is_mandatory_for_level' => false
        ]);

        $this->info("✅ Reset complete:");
        $this->line("  - Cleared {$servicesCount} service subscriptions");
        $this->line("  - Reset {$feeTypesCount} fee types");
    }
}
