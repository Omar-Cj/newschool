<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\BatchIdService;
use App\Models\Fees\FeesGeneration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class BatchIdServiceTest extends TestCase
{
    use RefreshDatabase;

    private BatchIdService $batchIdService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->batchIdService = app(BatchIdService::class);
    }

    /** @test */
    public function it_generates_batch_id_with_correct_format()
    {
        $batchId = $this->batchIdService->generateBatchId();

        $this->assertMatchesRegularExpression('/^BATCH_ID_\d+$/', $batchId);
    }

    /** @test */
    public function it_generates_sequential_batch_ids()
    {
        // Create some fee generations first to test sequencing
        FeesGeneration::create([
            'batch_id' => 'OLD_BATCH_1',
            'status' => 'completed',
            'total_students' => 0,
            'filters' => [],
            'created_by' => 1,
        ]);

        FeesGeneration::create([
            'batch_id' => 'OLD_BATCH_2',
            'status' => 'completed',
            'total_students' => 0,
            'filters' => [],
            'created_by' => 1,
        ]);

        // Get the current max ID
        $maxId = FeesGeneration::max('id');

        // Generate new batch ID
        $batchId = $this->batchIdService->generateBatchId();

        // Should be BATCH_ID_3 (maxId + 1)
        $expectedBatchId = 'BATCH_ID_' . ($maxId + 1);
        $this->assertEquals($expectedBatchId, $batchId);
    }

    /** @test */
    public function it_starts_from_1_when_no_records_exist()
    {
        // Ensure no records exist
        $this->assertEquals(0, FeesGeneration::count());

        $batchId = $this->batchIdService->generateBatchId();

        $this->assertEquals('BATCH_ID_1', $batchId);
    }

    /** @test */
    public function it_validates_batch_id_format_correctly()
    {
        $this->assertTrue($this->batchIdService->isValidBatchIdFormat('BATCH_ID_1'));
        $this->assertTrue($this->batchIdService->isValidBatchIdFormat('BATCH_ID_999'));

        $this->assertFalse($this->batchIdService->isValidBatchIdFormat('BATCH_ID_'));
        $this->assertFalse($this->batchIdService->isValidBatchIdFormat('BATCH_ID_abc'));
        $this->assertFalse($this->batchIdService->isValidBatchIdFormat('FG_20250101_abc'));
        $this->assertFalse($this->batchIdService->isValidBatchIdFormat('MONTHLY_2025_01_123'));
    }

    /** @test */
    public function it_extracts_sequence_number_correctly()
    {
        $this->assertEquals(1, $this->batchIdService->extractSequenceNumber('BATCH_ID_1'));
        $this->assertEquals(999, $this->batchIdService->extractSequenceNumber('BATCH_ID_999'));

        $this->assertNull($this->batchIdService->extractSequenceNumber('INVALID_FORMAT'));
        $this->assertNull($this->batchIdService->extractSequenceNumber('BATCH_ID_abc'));
    }

    /** @test */
    public function it_checks_batch_id_existence_correctly()
    {
        FeesGeneration::create([
            'batch_id' => 'BATCH_ID_1',
            'status' => 'completed',
            'total_students' => 0,
            'filters' => [],
            'created_by' => 1,
        ]);

        $this->assertTrue($this->batchIdService->batchIdExists('BATCH_ID_1'));
        $this->assertFalse($this->batchIdService->batchIdExists('BATCH_ID_999'));
    }

    /** @test */
    public function it_gets_next_sequence_number_correctly()
    {
        // Start with no records
        $this->assertEquals(1, $this->batchIdService->getNextSequenceNumber());

        // Add a record
        FeesGeneration::create([
            'batch_id' => 'BATCH_ID_1',
            'status' => 'completed',
            'total_students' => 0,
            'filters' => [],
            'created_by' => 1,
        ]);

        // Should be 2 now
        $this->assertEquals(2, $this->batchIdService->getNextSequenceNumber());
    }

    /** @test */
    public function it_generates_custom_batch_id_with_prefix()
    {
        $customBatchId = $this->batchIdService->generateCustomBatchId('CUSTOM');

        $this->assertMatchesRegularExpression('/^CUSTOM_\d+$/', $customBatchId);
        $this->assertEquals('CUSTOM_1', $customBatchId);
    }

    /** @test */
    public function it_handles_concurrent_batch_id_generation()
    {
        // This test simulates concurrent access using database transactions
        $batchIds = [];

        // Simulate 5 concurrent requests
        for ($i = 0; $i < 5; $i++) {
            DB::transaction(function () use (&$batchIds) {
                $batchId = $this->batchIdService->generateBatchId();
                $batchIds[] = $batchId;

                // Create the generation record to increment the max ID
                FeesGeneration::create([
                    'batch_id' => $batchId,
                    'status' => 'processing',
                    'total_students' => 0,
                    'filters' => [],
                    'created_by' => 1,
                ]);
            });
        }

        // All batch IDs should be unique
        $this->assertEquals(5, count(array_unique($batchIds)));

        // Should be sequential
        $this->assertEquals(['BATCH_ID_1', 'BATCH_ID_2', 'BATCH_ID_3', 'BATCH_ID_4', 'BATCH_ID_5'], $batchIds);
    }
}