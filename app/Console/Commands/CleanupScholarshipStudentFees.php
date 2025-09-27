<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StudentInfo\Student;
use App\Models\StudentService;
use App\Models\Fees\FeesCollect;
use App\Models\Fees\FeesAssignChildren;
use App\Services\FeeEligibilityService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupScholarshipStudentFees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:cleanup-scholarship-students
                            {--dry-run : Run without making changes to see what would be cleaned up}
                            {--force : Force cleanup without confirmation}
                            {--backup : Create backup tables before cleanup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up incorrect fee records for scholarship students (fee-exempt categories)';

    private FeeEligibilityService $eligibilityService;

    public function __construct(FeeEligibilityService $eligibilityService)
    {
        parent::__construct();
        $this->eligibilityService = $eligibilityService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Scholarship Student Fee Cleanup Tool');
        $this->info('=====================================');

        $isDryRun = $this->option('dry-run');
        $isForced = $this->option('force');
        $shouldBackup = $this->option('backup');

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE: No changes will be made to the database');
        }

        // Step 1: Get fee-exempt categories
        $exemptCategories = $this->eligibilityService->getFeeExemptCategories();

        if ($exemptCategories->isEmpty()) {
            $this->warn('⚠️  No fee-exempt student categories found.');
            $this->info('💡 To mark categories as fee-exempt, update the student_categories table:');
            $this->info('   UPDATE student_categories SET is_fee_exempt = 1 WHERE name IN (\'Scholarship\', \'Sponsored\')');
            return 0;
        }

        $this->info("📋 Found {$exemptCategories->count()} fee-exempt categories:");
        foreach ($exemptCategories as $category) {
            $this->line("   • {$category->name} (ID: {$category->id})");
        }

        // Step 2: Find fee-exempt students
        $exemptStudents = Student::whereIn('student_category_id', $exemptCategories->pluck('id'))
            ->where('status', \App\Enums\Status::ACTIVE)
            ->with('studentCategory')
            ->get();

        if ($exemptStudents->isEmpty()) {
            $this->info('✅ No active students found in fee-exempt categories. Nothing to clean up.');
            return 0;
        }

        $this->info("👥 Found {$exemptStudents->count()} students in fee-exempt categories:");

        // Step 3: Analyze what needs cleanup
        $cleanupData = $this->analyzeCleanupNeeds($exemptStudents);
        $this->displayCleanupSummary($cleanupData);

        if ($cleanupData['total_records'] === 0) {
            $this->info('✅ No cleanup needed. All fee records are already correct.');
            return 0;
        }

        // Step 4: Confirm cleanup (unless forced or dry-run)
        if (!$isDryRun && !$isForced) {
            if (!$this->confirm("⚠️  This will remove {$cleanupData['total_records']} fee-related records. Continue?")) {
                $this->info('🚫 Cleanup cancelled by user.');
                return 1;
            }
        }

        // Step 5: Create backups if requested
        if ($shouldBackup && !$isDryRun) {
            $this->createBackups($cleanupData);
        }

        // Step 6: Perform cleanup
        if (!$isDryRun) {
            $this->performCleanup($cleanupData);
            $this->info('✅ Cleanup completed successfully!');

            // Log the cleanup operation
            Log::info('Scholarship student fee cleanup completed', [
                'removed_records' => $cleanupData['total_records'],
                'affected_students' => $exemptStudents->count(),
                'executed_by' => 'console_command',
                'backup_created' => $shouldBackup
            ]);
        }

        return 0;
    }

    private function analyzeCleanupNeeds($exemptStudents): array
    {
        $studentIds = $exemptStudents->pluck('id');

        $data = [
            'students' => $exemptStudents,
            'student_services' => [],
            'fees_collects' => [],
            'fees_assign_children' => [],
            'total_records' => 0
        ];

        // Check StudentService records
        $studentServices = StudentService::whereIn('student_id', $studentIds)->get();
        if ($studentServices->isNotEmpty()) {
            $data['student_services'] = $studentServices;
            $this->warn("📦 Found {$studentServices->count()} service subscriptions for fee-exempt students");
        }

        // Check FeesCollect records
        $feesCollects = FeesCollect::whereIn('student_id', $studentIds)->get();
        if ($feesCollects->isNotEmpty()) {
            $data['fees_collects'] = $feesCollects;
            $this->warn("💰 Found {$feesCollects->count()} fee collection records for fee-exempt students");
        }

        // Check FeesAssignChildren records (legacy system)
        $feesAssignChildren = FeesAssignChildren::whereIn('student_id', $studentIds)->get();
        if ($feesAssignChildren->isNotEmpty()) {
            $data['fees_assign_children'] = $feesAssignChildren;
            $this->warn("📋 Found {$feesAssignChildren->count()} fee assignment records for fee-exempt students");
        }

        $data['total_records'] = count($data['student_services']) + count($data['fees_collects']) + count($data['fees_assign_children']);

        return $data;
    }

    private function displayCleanupSummary(array $cleanupData): void
    {
        $this->info('');
        $this->info('📊 Cleanup Summary:');
        $this->info('==================');

        foreach ($cleanupData['students'] as $student) {
            $this->line("👤 {$student->full_name} (Category: {$student->studentCategory->name})");

            $studentServices = collect($cleanupData['student_services'])->where('student_id', $student->id);
            $feesCollects = collect($cleanupData['fees_collects'])->where('student_id', $student->id);
            $feesAssigns = collect($cleanupData['fees_assign_children'])->where('student_id', $student->id);

            if ($studentServices->isNotEmpty()) {
                $this->line("   📦 {$studentServices->count()} service subscriptions to remove");
            }
            if ($feesCollects->isNotEmpty()) {
                $totalAmount = $feesCollects->sum('total_paid');
                $this->line("   💰 {$feesCollects->count()} fee collection records to remove (Total: $" . number_format($totalAmount, 2) . ")");
            }
            if ($feesAssigns->isNotEmpty()) {
                $this->line("   📋 {$feesAssigns->count()} legacy fee assignments to remove");
            }
        }

        $this->info('');
        $this->info("🔄 Total records to clean up: {$cleanupData['total_records']}");
    }

    private function createBackups(array $cleanupData): void
    {
        $this->info('💾 Creating backup tables...');

        $timestamp = now()->format('Y_m_d_H_i_s');

        try {
            if (!empty($cleanupData['student_services'])) {
                $backupTable = "student_services_backup_{$timestamp}";
                DB::statement("CREATE TABLE {$backupTable} AS SELECT * FROM student_services WHERE id IN (" .
                    collect($cleanupData['student_services'])->pluck('id')->implode(',') . ")");
                $this->info("   ✅ StudentService backup created: {$backupTable}");
            }

            if (!empty($cleanupData['fees_collects'])) {
                $backupTable = "fees_collects_backup_{$timestamp}";
                DB::statement("CREATE TABLE {$backupTable} AS SELECT * FROM fees_collects WHERE id IN (" .
                    collect($cleanupData['fees_collects'])->pluck('id')->implode(',') . ")");
                $this->info("   ✅ FeesCollect backup created: {$backupTable}");
            }

            if (!empty($cleanupData['fees_assign_children'])) {
                $backupTable = "fees_assign_childrens_backup_{$timestamp}";
                DB::statement("CREATE TABLE {$backupTable} AS SELECT * FROM fees_assign_childrens WHERE id IN (" .
                    collect($cleanupData['fees_assign_children'])->pluck('id')->implode(',') . ")");
                $this->info("   ✅ FeesAssignChildren backup created: {$backupTable}");
            }

        } catch (\Exception $e) {
            $this->error("❌ Backup creation failed: " . $e->getMessage());
            if (!$this->confirm('Continue without backup?')) {
                $this->info('🚫 Cleanup cancelled due to backup failure.');
                exit(1);
            }
        }
    }

    private function performCleanup(array $cleanupData): void
    {
        $this->info('🧹 Starting cleanup process...');

        DB::transaction(function () use ($cleanupData) {
            // Remove StudentService records
            if (!empty($cleanupData['student_services'])) {
                $serviceIds = collect($cleanupData['student_services'])->pluck('id');
                StudentService::whereIn('id', $serviceIds)->delete();
                $this->info("   ✅ Removed {$serviceIds->count()} service subscription records");
            }

            // Remove FeesCollect records
            if (!empty($cleanupData['fees_collects'])) {
                $collectIds = collect($cleanupData['fees_collects'])->pluck('id');
                FeesCollect::whereIn('id', $collectIds)->delete();
                $this->info("   ✅ Removed {$collectIds->count()} fee collection records");
            }

            // Remove FeesAssignChildren records (legacy system)
            if (!empty($cleanupData['fees_assign_children'])) {
                $assignIds = collect($cleanupData['fees_assign_children'])->pluck('id');
                FeesAssignChildren::whereIn('id', $assignIds)->delete();
                $this->info("   ✅ Removed {$assignIds->count()} legacy fee assignment records");
            }
        });
    }
}