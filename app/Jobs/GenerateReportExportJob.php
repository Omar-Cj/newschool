<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\ExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

/**
 * Async job for generating large report exports
 * Processes heavy exports in background queue
 */
class GenerateReportExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600; // 10 minutes
    public int $tries = 3;
    public array $backoff = [60, 300, 600]; // Retry delays in seconds

    protected int $reportId;
    protected string $format;
    protected string $cacheKey;
    protected int $userId;

    /**
     * Create a new job instance
     *
     * @param int $reportId Report identifier
     * @param string $format Export format (excel, pdf, csv)
     * @param string $cacheKey Cache key for stored data
     * @param int $userId User requesting export
     */
    public function __construct(
        int $reportId,
        string $format,
        string $cacheKey,
        int $userId
    ) {
        $this->reportId = $reportId;
        $this->format = $format;
        $this->cacheKey = $cacheKey;
        $this->userId = $userId;

        // Set queue based on format and priority
        $this->onQueue($this->determineQueue($format));
    }

    /**
     * Execute the job
     *
     * @param ExportService $exportService
     * @return void
     */
    public function handle(ExportService $exportService): void
    {
        try {
            Log::info('Export job started', [
                'report_id' => $this->reportId,
                'format' => $this->format,
                'user_id' => $this->userId,
            ]);

            // Retrieve cached data
            $cachedData = Cache::get($this->cacheKey);

            if (!$cachedData) {
                throw new \RuntimeException('Export data not found in cache');
            }

            $results = $cachedData['results'];
            $columns = $cachedData['columns'];
            $metadata = $cachedData['metadata'];

            // Generate filename
            $filename = $this->generateFilename($metadata['name'] ?? 'Report');

            // Generate export file
            $filePath = $this->generateExportFile(
                $exportService,
                $results,
                $columns,
                $metadata,
                $filename
            );

            // Store file path for download
            $downloadKey = "export_download_{$this->userId}_{$this->reportId}_" . uniqid();
            Cache::put($downloadKey, [
                'path' => $filePath,
                'filename' => $filename,
                'format' => $this->format,
            ], now()->addHours(24));

            // Notify user
            $this->notifyUser($downloadKey, $filename);

            // Cleanup cache
            Cache::forget($this->cacheKey);

            Log::info('Export job completed', [
                'report_id' => $this->reportId,
                'format' => $this->format,
                'file_path' => $filePath,
            ]);

        } catch (\Exception $e) {
            Log::error('Export job failed', [
                'report_id' => $this->reportId,
                'format' => $this->format,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Notify user of failure
            $this->notifyUserOfFailure($e->getMessage());

            throw $e;
        }
    }

    /**
     * Generate export file and store it
     *
     * @param ExportService $exportService
     * @param array $results
     * @param array $columns
     * @param array $metadata
     * @param string $filename
     * @return string File path
     */
    protected function generateExportFile(
        ExportService $exportService,
        array $results,
        array $columns,
        array $metadata,
        string $filename
    ): string {
        $disk = config('filesystems.default');
        $directory = 'exports/reports';

        // Create directory if it doesn't exist
        Storage::disk($disk)->makeDirectory($directory);

        $filePath = "{$directory}/{$filename}";

        // Generate file based on format
        switch ($this->format) {
            case 'excel':
                $export = new \App\Exports\DynamicReportExport($results, $columns, $metadata);
                $content = \Maatwebsite\Excel\Facades\Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);
                Storage::disk($disk)->put($filePath, $content);
                break;

            case 'pdf':
                $formattedResults = $this->formatDataForDisplay($results, $columns);
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pdf.template', [
                    'reportName' => $metadata['name'] ?? 'Dynamic Report',
                    'generatedAt' => now()->format('Y-m-d H:i:s'),
                    'parameters' => $metadata['parameters'] ?? [],
                    'columns' => $columns,
                    'results' => $formattedResults,
                    'totalRows' => count($formattedResults),
                ]);
                $pdf->setPaper('a4', 'landscape');
                Storage::disk($disk)->put($filePath, $pdf->output());
                break;

            case 'csv':
                $csvContent = $this->generateCsvContent($results, $columns);
                Storage::disk($disk)->put($filePath, $csvContent);
                break;

            default:
                throw new \InvalidArgumentException("Unsupported format: {$this->format}");
        }

        return $filePath;
    }

    /**
     * Generate CSV content
     *
     * @param array $results
     * @param array $columns
     * @return string CSV content
     */
    protected function generateCsvContent(array $results, array $columns): string
    {
        $handle = fopen('php://temp', 'r+');

        // Write UTF-8 BOM
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write headers
        $headers = array_column($columns, 'label');
        fputcsv($handle, $headers);

        // Write data
        foreach ($results as $row) {
            $formattedRow = [];
            foreach ($columns as $column) {
                $key = $column['key'];
                $value = $row[$key] ?? null;
                $formattedRow[] = $this->formatValue($value, $column);
            }
            fputcsv($handle, $formattedRow);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }

    /**
     * Format data for display
     *
     * @param array $results
     * @param array $columns
     * @return array
     */
    protected function formatDataForDisplay(array $results, array $columns): array
    {
        return array_map(function ($row) use ($columns) {
            $formatted = [];
            foreach ($columns as $column) {
                $key = $column['key'];
                $value = $row[$key] ?? null;
                $formatted[$key] = $this->formatValue($value, $column);
            }
            return $formatted;
        }, $results);
    }

    /**
     * Format value based on column type
     *
     * @param mixed $value
     * @param array $column
     * @return string
     */
    protected function formatValue($value, array $column): string
    {
        if ($value === null) {
            return '';
        }

        $type = $column['type'] ?? 'string';

        return match($type) {
            'currency' => '$' . number_format((float) $value, 2, '.', ','),
            'number' => number_format((float) $value, 2, '.', ','),
            'percentage' => number_format((float) $value, 1, '.', '') . '%',
            'date' => \Carbon\Carbon::parse($value)->format('Y-m-d'),
            'datetime' => \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s'),
            'boolean' => $value ? 'Yes' : 'No',
            default => (string) $value,
        };
    }

    /**
     * Notify user that export is ready
     *
     * @param string $downloadKey
     * @param string $filename
     * @return void
     */
    protected function notifyUser(string $downloadKey, string $filename): void
    {
        $user = User::find($this->userId);

        if ($user) {
            // Create download URL
            $downloadUrl = route('reports.download-export', ['key' => $downloadKey]);

            // Send notification (assuming notification class exists)
            // You can customize this based on your notification system
            try {
                $user->notify(new \App\Notifications\ExportReadyNotification(
                    $filename,
                    $downloadUrl,
                    $this->format
                ));
            } catch (\Exception $e) {
                Log::warning('Failed to send export notification', [
                    'user_id' => $this->userId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Notify user of export failure
     *
     * @param string $errorMessage
     * @return void
     */
    protected function notifyUserOfFailure(string $errorMessage): void
    {
        $user = User::find($this->userId);

        if ($user) {
            try {
                $user->notify(new \App\Notifications\ExportFailedNotification(
                    $this->reportId,
                    $this->format,
                    $errorMessage
                ));
            } catch (\Exception $e) {
                Log::warning('Failed to send failure notification', [
                    'user_id' => $this->userId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Generate filename for export
     *
     * @param string $reportName
     * @return string
     */
    protected function generateFilename(string $reportName): string
    {
        $sanitized = preg_replace('/[^A-Za-z0-9_\-]/', '_', $reportName);
        $timestamp = now()->format('Y-m-d_His');

        $extension = match($this->format) {
            'excel' => 'xlsx',
            'pdf' => 'pdf',
            'csv' => 'csv',
            default => 'txt',
        };

        return "{$sanitized}_{$timestamp}.{$extension}";
    }

    /**
     * Determine queue based on format
     *
     * @param string $format
     * @return string
     */
    protected function determineQueue(string $format): string
    {
        // PDFs are more resource-intensive, use separate queue
        return $format === 'pdf' ? 'exports-heavy' : 'exports';
    }

    /**
     * Handle job failure
     *
     * @param \Throwable $exception
     * @return void
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Export job failed permanently', [
            'report_id' => $this->reportId,
            'format' => $this->format,
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $this->notifyUserOfFailure($exception->getMessage());

        // Cleanup cache
        Cache::forget($this->cacheKey);
    }

    /**
     * Cleanup old export files (can be called separately via scheduled task)
     *
     * @param int $hoursOld Files older than this will be deleted
     * @return int Number of files deleted
     */
    public static function cleanupOldExports(int $hoursOld = 24): int
    {
        $disk = config('filesystems.default');
        $directory = 'exports/reports';
        $cutoffTime = now()->subHours($hoursOld);

        $files = Storage::disk($disk)->files($directory);
        $deletedCount = 0;

        foreach ($files as $file) {
            $lastModified = Storage::disk($disk)->lastModified($file);

            if ($lastModified < $cutoffTime->timestamp) {
                Storage::disk($disk)->delete($file);
                $deletedCount++;
            }
        }

        Log::info('Old export files cleaned up', [
            'files_deleted' => $deletedCount,
            'hours_old' => $hoursOld,
        ]);

        return $deletedCount;
    }
}
