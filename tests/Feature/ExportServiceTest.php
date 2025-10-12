<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\ExportService;
use App\Exports\DynamicReportExport;
use App\Jobs\GenerateReportExportJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Feature tests for Export Service
 */
class ExportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ExportService $exportService;
    protected array $sampleResults;
    protected array $sampleColumns;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exportService = app(ExportService::class);

        // Sample test data
        $this->sampleResults = [
            ['id' => 1, 'name' => 'John Doe', 'grade' => 85.5, 'fees' => 1500.50, 'enrolled' => '2024-01-15', 'active' => true],
            ['id' => 2, 'name' => 'Jane Smith', 'grade' => 92.0, 'fees' => 2300.75, 'enrolled' => '2024-01-20', 'active' => true],
            ['id' => 3, 'name' => 'Bob Johnson', 'grade' => 78.3, 'fees' => 1800.00, 'enrolled' => '2024-02-01', 'active' => false],
        ];

        $this->sampleColumns = [
            ['key' => 'id', 'label' => 'Student ID', 'type' => 'number'],
            ['key' => 'name', 'label' => 'Student Name', 'type' => 'string'],
            ['key' => 'grade', 'label' => 'Grade', 'type' => 'percentage'],
            ['key' => 'fees', 'label' => 'Total Fees', 'type' => 'currency'],
            ['key' => 'enrolled', 'label' => 'Enrollment Date', 'type' => 'date'],
            ['key' => 'active', 'label' => 'Active Status', 'type' => 'boolean'],
        ];
    }

    /** @test */
    public function it_can_export_to_excel()
    {
        Excel::fake();

        $response = $this->exportService->exportExcel(
            1,
            $this->sampleResults,
            $this->sampleColumns,
            ['name' => 'Test Report']
        );

        Excel::assertDownloaded('Test_Report_*.xlsx', function(DynamicReportExport $export) {
            return $export->collection()->count() === 3;
        });
    }

    /** @test */
    public function it_can_export_to_csv()
    {
        $response = $this->exportService->exportCsv(
            1,
            $this->sampleResults,
            $this->sampleColumns,
            ['name' => 'Test Report']
        );

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\StreamedResponse::class, $response);
        $this->assertEquals('text/csv; charset=UTF-8', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function it_can_export_to_pdf()
    {
        $response = $this->exportService->exportPdf(
            1,
            $this->sampleResults,
            $this->sampleColumns,
            ['name' => 'Test Report']
        );

        $this->assertInstanceOf(\Illuminate\Http\Response::class, $response);
    }

    /** @test */
    public function it_queues_large_exports()
    {
        Queue::fake();

        // Create large dataset (>500 rows)
        $largeResults = array_fill(0, 600, $this->sampleResults[0]);

        $result = $this->exportService->export(
            1,
            'excel',
            $largeResults,
            $this->sampleColumns
        );

        Queue::assertPushed(GenerateReportExportJob::class);

        $this->assertIsArray($result);
        $this->assertEquals('queued', $result['status']);
    }

    /** @test */
    public function it_processes_small_exports_synchronously()
    {
        Queue::fake();

        Excel::fake();

        $result = $this->exportService->export(
            1,
            'excel',
            $this->sampleResults,
            $this->sampleColumns
        );

        Queue::assertNotPushed(GenerateReportExportJob::class);

        Excel::assertDownloaded('*.xlsx');
    }

    /** @test */
    public function it_validates_export_format()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->exportService->export(
            1,
            'invalid_format',
            $this->sampleResults,
            $this->sampleColumns
        );
    }

    /** @test */
    public function it_formats_currency_correctly()
    {
        $reflection = new \ReflectionClass($this->exportService);
        $method = $reflection->getMethod('formatCurrency');
        $method->setAccessible(true);

        $result = $method->invoke($this->exportService, 1234.56);

        $this->assertEquals('$1,234.56', $result);
    }

    /** @test */
    public function it_formats_percentage_correctly()
    {
        $reflection = new \ReflectionClass($this->exportService);
        $method = $reflection->getMethod('formatPercentage');
        $method->setAccessible(true);

        $result = $method->invoke($this->exportService, 85.567);

        $this->assertEquals('85.6%', $result);
    }

    /** @test */
    public function it_formats_date_correctly()
    {
        $reflection = new \ReflectionClass($this->exportService);
        $method = $reflection->getMethod('formatDate');
        $method->setAccessible(true);

        $result = $method->invoke($this->exportService, '2024-01-15 14:30:00');

        $this->assertEquals('2024-01-15', $result);
    }

    /** @test */
    public function it_sanitizes_csv_for_injection_prevention()
    {
        $reflection = new \ReflectionClass($this->exportService);
        $method = $reflection->getMethod('sanitizeForCsv');
        $method->setAccessible(true);

        // Test formula injection prevention
        $this->assertEquals("'=1+1", $method->invoke($this->exportService, '=1+1'));
        $this->assertEquals("'+cmd", $method->invoke($this->exportService, '+cmd'));
        $this->assertEquals("'-cmd", $method->invoke($this->exportService, '-cmd'));
        $this->assertEquals("'@SUM", $method->invoke($this->exportService, '@SUM'));

        // Normal values should not be modified
        $this->assertEquals('normal text', $method->invoke($this->exportService, 'normal text'));
    }

    /** @test */
    public function it_generates_proper_filename()
    {
        $reflection = new \ReflectionClass($this->exportService);
        $method = $reflection->getMethod('generateFilename');
        $method->setAccessible(true);

        $filename = $method->invoke($this->exportService, 'Test Report Name', 'xlsx');

        $this->assertStringContainsString('Test_Report_Name', $filename);
        $this->assertStringEndsWith('.xlsx', $filename);
    }

    /** @test */
    public function it_handles_empty_results()
    {
        Excel::fake();

        $response = $this->exportService->exportExcel(
            1,
            [],
            $this->sampleColumns,
            ['name' => 'Empty Report']
        );

        Excel::assertDownloaded('Empty_Report_*.xlsx', function(DynamicReportExport $export) {
            return $export->collection()->count() === 0;
        });
    }

    /** @test */
    public function it_handles_null_values_in_results()
    {
        $resultsWithNull = [
            ['id' => 1, 'name' => null, 'grade' => null, 'fees' => 0, 'enrolled' => null, 'active' => null],
        ];

        Excel::fake();

        $response = $this->exportService->exportExcel(
            1,
            $resultsWithNull,
            $this->sampleColumns
        );

        Excel::assertDownloaded('*.xlsx');
    }

    /** @test */
    public function it_enforces_pdf_row_limit()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('PDF export limited to 2000 rows');

        $largeResults = array_fill(0, 2500, $this->sampleResults[0]);

        $this->exportService->exportPdf(
            1,
            $largeResults,
            $this->sampleColumns
        );
    }

    /** @test */
    public function it_includes_metadata_in_export()
    {
        Excel::fake();

        $metadata = [
            'name' => 'Student Report',
            'parameters' => [
                'academic_year' => '2024-2025',
                'class' => '10th Grade',
            ],
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];

        $this->exportService->exportExcel(
            1,
            $this->sampleResults,
            $this->sampleColumns,
            $metadata
        );

        Excel::assertDownloaded('Student_Report_*.xlsx');
    }

    /** @test */
    public function export_job_stores_file_correctly()
    {
        Storage::fake('local');

        $cacheKey = 'test_cache_key';
        \Illuminate\Support\Facades\Cache::put($cacheKey, [
            'results' => $this->sampleResults,
            'columns' => $this->sampleColumns,
            'metadata' => ['name' => 'Test Report'],
        ]);

        $user = \App\Models\User::factory()->create();

        $job = new GenerateReportExportJob(1, 'csv', $cacheKey, $user->id);
        $job->handle($this->exportService);

        Storage::disk('local')->assertExists('exports/reports/Test_Report_*.csv');
    }

    /** @test */
    public function it_cleans_up_old_exports()
    {
        Storage::fake('local');

        // Create test files with different timestamps
        Storage::disk('local')->put('exports/reports/old_file.xlsx', 'content');
        Storage::disk('local')->put('exports/reports/new_file.xlsx', 'content');

        // Manually set old file timestamp (25 hours ago)
        $oldPath = Storage::disk('local')->path('exports/reports/old_file.xlsx');
        touch($oldPath, time() - (25 * 3600));

        $deletedCount = GenerateReportExportJob::cleanupOldExports(24);

        $this->assertEquals(1, $deletedCount);
        Storage::disk('local')->assertMissing('exports/reports/old_file.xlsx');
        Storage::disk('local')->assertExists('exports/reports/new_file.xlsx');
    }

    /** @test */
    public function it_estimates_processing_time_correctly()
    {
        $reflection = new \ReflectionClass($this->exportService);
        $method = $reflection->getMethod('estimateProcessingTime');
        $method->setAccessible(true);

        $this->assertEquals('less than 1 minute', $method->invoke($this->exportService, 50));
        $this->assertEquals('1-5 minutes', $method->invoke($this->exportService, 10000));
        $this->assertEquals('5-10 minutes', $method->invoke($this->exportService, 50000));
    }

    /** @test */
    public function it_logs_export_operations()
    {
        \Illuminate\Support\Facades\Log::spy();

        $this->exportService->export(
            1,
            'excel',
            $this->sampleResults,
            $this->sampleColumns
        );

        \Illuminate\Support\Facades\Log::shouldHaveReceived('info')
            ->with('Report export initiated', \Mockery::any())
            ->once();
    }
}
