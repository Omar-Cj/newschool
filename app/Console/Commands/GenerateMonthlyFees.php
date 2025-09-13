<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EnhancedFeesGenerationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyFees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:generate-monthly
                            {--month= : Month to generate fees for (YYYY-MM format, defaults to current month)}
                            {--academic-year= : Academic year ID (defaults to current session)}
                            {--school-id= : School ID for multi-tenant installations}
                            {--class-ids=* : Specific class IDs to generate fees for}
                            {--section-ids=* : Specific section IDs to generate fees for}
                            {--service-categories=* : Specific service categories (academic, transport, meal, etc.)}
                            {--fee-type-ids=* : Specific fee type IDs to generate}
                            {--include-one-time : Include one-time fees in addition to monthly fees}
                            {--prorated : Use pro-rated calculations for mid-month subscriptions}
                            {--dry-run : Preview fees without actually generating them}
                            {--force : Skip confirmation prompts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate monthly fees for students based on their service subscriptions';

    private EnhancedFeesGenerationService $feeService;

    public function __construct(EnhancedFeesGenerationService $feeService)
    {
        parent::__construct();
        $this->feeService = $feeService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🏫 Monthly Fee Generation Tool');
        $this->info('===============================');
        $this->newLine();

        try {
            // Parse and validate month
            $monthInput = $this->option('month') ?? now()->format('Y-m');
            $month = Carbon::createFromFormat('Y-m', $monthInput);

            if (!$month) {
                $this->error("Invalid month format. Use YYYY-MM format (e.g., 2024-09)");
                return Command::FAILURE;
            }

            // Build criteria from options
            $criteria = $this->buildCriteria($month);

            // Validate criteria
            if (!$this->validateCriteria($criteria)) {
                return Command::FAILURE;
            }

            // Display generation info
            $this->displayGenerationInfo($month, $criteria);

            // Handle dry run
            if ($this->option('dry-run')) {
                return $this->handleDryRun($month, $criteria);
            }

            // Get confirmation unless forced
            if (!$this->option('force') && !$this->confirm('Proceed with fee generation?')) {
                $this->info('Fee generation cancelled.');
                return Command::SUCCESS;
            }

            // Generate fees
            $this->info('🔄 Generating monthly fees...');
            $startTime = microtime(true);

            if ($this->option('prorated')) {
                $result = $this->feeService->generateProRatedMonthlyFees($month, $criteria);
            } else {
                $result = $this->feeService->generateMonthlyFees($month, $criteria);
            }

            $duration = round(microtime(true) - $startTime, 2);

            // Display results
            $this->displayResults($result, $duration);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Fee generation failed: ' . $e->getMessage());
            Log::error('Monthly fee generation command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'options' => $this->options()
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Build criteria array from command options
     */
    private function buildCriteria(Carbon $month): array
    {
        $criteria = [
            'generation_month' => $month,
            'academic_year_id' => $this->option('academic-year') ?? session('academic_year_id'),
            'include_one_time_fees' => $this->option('include-one-time')
        ];

        // Add optional filters
        if ($this->option('school-id')) {
            $criteria['school_id'] = $this->option('school-id');
        }

        if ($this->option('class-ids')) {
            $criteria['class_ids'] = $this->option('class-ids');
        }

        if ($this->option('section-ids')) {
            $criteria['section_ids'] = $this->option('section-ids');
        }

        if ($this->option('service-categories')) {
            $criteria['service_categories'] = $this->option('service-categories');
        }

        if ($this->option('fee-type-ids')) {
            $criteria['fee_type_ids'] = $this->option('fee-type-ids');
        }

        return $criteria;
    }

    /**
     * Validate criteria
     */
    private function validateCriteria(array $criteria): bool
    {
        // Check if academic year exists
        if (!$criteria['academic_year_id']) {
            $this->error('❌ Academic year ID is required. Please provide --academic-year option or ensure session is set.');
            return false;
        }

        // Validate service categories
        if (!empty($criteria['service_categories'])) {
            $validCategories = ['academic', 'transport', 'meal', 'accommodation', 'activity', 'other'];
            $invalidCategories = array_diff($criteria['service_categories'], $validCategories);

            if (!empty($invalidCategories)) {
                $this->error('❌ Invalid service categories: ' . implode(', ', $invalidCategories));
                $this->line('Valid categories: ' . implode(', ', $validCategories));
                return false;
            }
        }

        return true;
    }

    /**
     * Display generation information
     */
    private function displayGenerationInfo(Carbon $month, array $criteria): void
    {
        $this->info('📅 Generation Details:');
        $this->line('Month: ' . $month->format('F Y'));
        $this->line('Academic Year ID: ' . $criteria['academic_year_id']);

        if (!empty($criteria['school_id'])) {
            $this->line('School ID: ' . $criteria['school_id']);
        }

        if (!empty($criteria['class_ids'])) {
            $this->line('Class IDs: ' . implode(', ', $criteria['class_ids']));
        }

        if (!empty($criteria['section_ids'])) {
            $this->line('Section IDs: ' . implode(', ', $criteria['section_ids']));
        }

        if (!empty($criteria['service_categories'])) {
            $this->line('Service Categories: ' . implode(', ', $criteria['service_categories']));
        }

        if (!empty($criteria['fee_type_ids'])) {
            $this->line('Fee Type IDs: ' . implode(', ', $criteria['fee_type_ids']));
        }

        $this->line('Include One-time Fees: ' . ($criteria['include_one_time_fees'] ? 'Yes' : 'No'));
        $this->line('Pro-rated Calculation: ' . ($this->option('prorated') ? 'Yes' : 'No'));

        $this->newLine();
    }

    /**
     * Handle dry run (preview only)
     */
    private function handleDryRun(Carbon $month, array $criteria): int
    {
        $this->info('🔍 Dry Run - Previewing fees to be generated...');
        $this->newLine();

        try {
            $preview = $this->feeService->previewMonthlyFees($month, $criteria);

            $this->displayPreview($preview);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Preview failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Display preview results
     */
    private function displayPreview(array $preview): void
    {
        $this->info('📊 Preview Results for ' . $preview['month']);
        $this->info('===================================');

        $this->line('Total Students: ' . $preview['total_students']);
        $this->line('Total Amount: $' . number_format($preview['total_amount'], 2));

        // Display duplicate warning if any
        if ($preview['duplicate_info']['has_duplicates']) {
            $this->warn('⚠️  ' . $preview['duplicate_info']['message']);
        }

        $this->newLine();

        // Display service summary
        if (!empty($preview['service_summary'])) {
            $this->info('📋 Service Summary:');
            $this->table(
                ['Service', 'Category', 'Students', 'Total Amount'],
                collect($preview['service_summary'])->map(function($service) {
                    return [
                        $service['service_name'],
                        ucfirst($service['category']),
                        $service['student_count'],
                        '$' . number_format($service['total_amount'], 2)
                    ];
                })->toArray()
            );
        }

        $this->newLine();

        // Display student preview
        if (!empty($preview['students_preview'])) {
            $this->info('👥 Student Preview (first 10):');
            $this->table(
                ['Student', 'Class', 'Services', 'Amount'],
                collect($preview['students_preview'])->map(function($student) {
                    return [
                        $student['student_name'],
                        $student['class'],
                        count($student['services']),
                        '$' . number_format($student['total_amount'], 2)
                    ];
                })->toArray()
            );
        }

        $this->newLine();
        $this->info('💡 To actually generate these fees, run the command without --dry-run');
    }

    /**
     * Display generation results
     */
    private function displayResults(array $result, float $duration): void
    {
        $this->newLine();
        $this->info('✅ Monthly fee generation completed!');
        $this->info('=====================================');

        $this->line('Generation ID: ' . $result['generation_id']);

        if (isset($result['month'])) {
            $this->line('Month: ' . $result['month']);
        }

        $this->line('Total Students: ' . $result['total_students']);
        $this->line('Successfully Processed: ' . $result['success_count']);

        if ($result['error_count'] > 0) {
            $this->warn('Errors: ' . $result['error_count']);
        }

        $this->line('Total Amount Generated: $' . number_format($result['total_amount'], 2));
        $this->line('Processing Time: ' . $duration . ' seconds');

        // Display errors if any
        if (!empty($result['errors'])) {
            $this->newLine();
            $this->warn('❌ Errors encountered:');

            foreach (array_slice($result['errors'], 0, 10) as $error) {
                $this->line('  • ' . $error['student_name'] . ': ' . $error['error']);
            }

            if (count($result['errors']) > 10) {
                $this->line('  ... and ' . (count($result['errors']) - 10) . ' more errors');
            }
        }

        $this->newLine();
        $this->info('📄 Check the fees_generation_logs table for detailed information.');
    }
}