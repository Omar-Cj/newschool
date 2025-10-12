<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\GenerateReportExportJob;
use Illuminate\Console\Command;

/**
 * Console command to clean up old report export files
 * Can be run manually or scheduled
 */
class CleanupReportExports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:cleanup-exports
                            {--hours=24 : Delete exports older than specified hours}
                            {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old report export files to free storage space';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $hours = (int) $this->option('hours');
        $dryRun = $this->option('dry-run');

        $this->info("Cleaning up report exports older than {$hours} hours...");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No files will be deleted');
        }

        try {
            if (!$dryRun) {
                $deletedCount = GenerateReportExportJob::cleanupOldExports($hours);

                $this->info("Successfully deleted {$deletedCount} old export file(s)");

                // Additional statistics
                $disk = config('filesystems.default');
                $directory = 'exports/reports';

                if (\Illuminate\Support\Facades\Storage::disk($disk)->exists($directory)) {
                    $remainingFiles = count(\Illuminate\Support\Facades\Storage::disk($disk)->files($directory));
                    $this->line("Remaining export files: {$remainingFiles}");
                }
            } else {
                // Dry run - show what would be deleted
                $this->showDryRunResults($hours);
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Cleanup failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Show dry run results without deleting
     *
     * @param int $hours
     * @return void
     */
    protected function showDryRunResults(int $hours): void
    {
        $disk = config('filesystems.default');
        $directory = 'exports/reports';
        $cutoffTime = now()->subHours($hours);

        if (!\Illuminate\Support\Facades\Storage::disk($disk)->exists($directory)) {
            $this->warn('Export directory does not exist');
            return;
        }

        $files = \Illuminate\Support\Facades\Storage::disk($disk)->files($directory);
        $toDelete = [];

        foreach ($files as $file) {
            $lastModified = \Illuminate\Support\Facades\Storage::disk($disk)->lastModified($file);

            if ($lastModified < $cutoffTime->timestamp) {
                $size = \Illuminate\Support\Facades\Storage::disk($disk)->size($file);
                $toDelete[] = [
                    'file' => basename($file),
                    'size' => $this->formatBytes($size),
                    'age' => $cutoffTime->diffInHours(\Carbon\Carbon::createFromTimestamp($lastModified)) . ' hours',
                ];
            }
        }

        if (empty($toDelete)) {
            $this->info('No files would be deleted');
            return;
        }

        $this->table(
            ['File', 'Size', 'Age'],
            $toDelete
        );

        $totalSize = array_sum(array_map(function($file) use ($disk, $directory) {
            return \Illuminate\Support\Facades\Storage::disk($disk)->size("{$directory}/" . $file['file']);
        }, $toDelete));

        $this->info('Total files to delete: ' . count($toDelete));
        $this->info('Total space to free: ' . $this->formatBytes($totalSize));
    }

    /**
     * Format bytes to human-readable size
     *
     * @param int $bytes
     * @return string
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2) . ' ' . $units[$unitIndex];
    }
}
