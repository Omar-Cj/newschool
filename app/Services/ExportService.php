<?php

declare(strict_types=1);

namespace App\Services;

use App\Exports\DynamicReportExport;
use App\Jobs\GenerateReportExportJob;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Service for exporting dynamic reports to multiple formats (Excel, PDF, CSV)
 * Handles large datasets efficiently with streaming and chunking
 */
class ExportService
{
    private const CHUNK_SIZE = 1000;
    private const LARGE_DATASET_THRESHOLD = 500;
    private const MAX_MEMORY = '256M';

    /**
     * Main export method - routes to appropriate format handler
     *
     * @param int $reportId Report identifier
     * @param string $format Export format (excel, pdf, csv)
     * @param array $results Query results array
     * @param array $columns Column metadata with types and formatting
     * @param array $reportMetadata Report name, parameters, filters used
     * @param bool $forceAsync Force async processing even for small datasets
     * @return mixed Download response or job dispatch confirmation
     */
    public function export(
        int $reportId,
        string $format,
        array $results,
        array $columns,
        array $reportMetadata = [],
        bool $forceAsync = false
    ) {
        // Validate format
        if (!in_array($format, ['excel', 'pdf', 'csv'])) {
            throw new \InvalidArgumentException("Unsupported export format: {$format}");
        }

        // Log export operation for audit trail
        Log::info('Report export initiated', [
            'report_id' => $reportId,
            'format' => $format,
            'row_count' => count($results),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
        ]);

        // Queue large exports to avoid timeout
        if ($forceAsync || count($results) > self::LARGE_DATASET_THRESHOLD) {
            return $this->queueExport($reportId, $format, $results, $columns, $reportMetadata);
        }

        // Process synchronously for smaller datasets
        return match($format) {
            'excel' => $this->exportExcel($reportId, $results, $columns, $reportMetadata),
            'pdf' => $this->exportPdf($reportId, $results, $columns, $reportMetadata),
            'csv' => $this->exportCsv($reportId, $results, $columns, $reportMetadata),
        };
    }

    /**
     * Generate Excel export with advanced formatting
     *
     * @param int $reportId Report identifier
     * @param array $results Query results
     * @param array $columns Column definitions with types
     * @param array $metadata Report metadata
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportExcel(
        int $reportId,
        array $results,
        array $columns,
        array $metadata = []
    ) {
        try {
            // Increase memory limit for large exports
            ini_set('memory_limit', self::MAX_MEMORY);

            $filename = $this->generateFilename($metadata['name'] ?? 'Report', 'xlsx');

            // Use custom export class with formatting
            return Excel::download(
                new DynamicReportExport($results, $columns, $metadata),
                $filename
            );
        } catch (\Exception $e) {
            Log::error('Excel export failed', [
                'report_id' => $reportId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new \RuntimeException("Excel export failed: " . $e->getMessage());
        }
    }

    /**
     * Generate PDF export with professional layout
     *
     * @param int $reportId Report identifier
     * @param array $results Query results
     * @param array $columns Column definitions
     * @param array $metadata Report metadata
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(
        int $reportId,
        array $results,
        array $columns,
        array $metadata = []
    ) {
        try {
            // Limit PDF exports to reasonable size to prevent memory issues
            if (count($results) > 2000) {
                throw new \RuntimeException(
                    'PDF export limited to 2000 rows. Please use Excel or CSV for larger datasets.'
                );
            }

            // Format data for PDF display
            $formattedResults = $this->formatDataForDisplay($results, $columns);

            // Generate PDF using view template
            $pdf = Pdf::loadView('reports.pdf.template', [
                'reportName' => $metadata['name'] ?? 'Dynamic Report',
                'generatedAt' => now()->format('Y-m-d H:i:s'),
                'parameters' => $metadata['parameters'] ?? [],
                'columns' => $columns,
                'results' => $formattedResults,
                'totalRows' => count($formattedResults),
            ]);

            // Configure PDF settings
            $pdf->setPaper('a4', 'landscape')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => false,
                ]);

            $filename = $this->generateFilename($metadata['name'] ?? 'Report', 'pdf');

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF export failed', [
                'report_id' => $reportId,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException("PDF export failed: " . $e->getMessage());
        }
    }

    /**
     * Generate CSV export with streaming for large datasets
     *
     * @param int $reportId Report identifier
     * @param array $results Query results
     * @param array $columns Column definitions
     * @param array $metadata Report metadata
     * @return StreamedResponse
     */
    public function exportCsv(
        int $reportId,
        array $results,
        array $columns,
        array $metadata = []
    ): StreamedResponse {
        try {
            $filename = $this->generateFilename($metadata['name'] ?? 'Report', 'csv');

            // Stream CSV to avoid memory issues with large datasets
            return response()->streamDownload(function () use ($results, $columns) {
                $handle = fopen('php://output', 'w');

                // Write UTF-8 BOM for Excel compatibility
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                // Write headers
                $headers = array_column($columns, 'label');
                fputcsv($handle, $headers);

                // Write data rows with formatting
                foreach ($results as $row) {
                    $formattedRow = $this->formatRowForCsv($row, $columns);
                    fputcsv($handle, $formattedRow);
                }

                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        } catch (\Exception $e) {
            Log::error('CSV export failed', [
                'report_id' => $reportId,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException("CSV export failed: " . $e->getMessage());
        }
    }

    /**
     * Queue export job for async processing
     *
     * @param int $reportId Report identifier
     * @param string $format Export format
     * @param array $results Query results
     * @param array $columns Column definitions
     * @param array $metadata Report metadata
     * @return array Job dispatch confirmation
     */
    protected function queueExport(
        int $reportId,
        string $format,
        array $results,
        array $columns,
        array $metadata
    ): array {
        // Cache results for job to process
        $cacheKey = "export_data_{$reportId}_" . uniqid();
        Cache::put($cacheKey, [
            'results' => $results,
            'columns' => $columns,
            'metadata' => $metadata,
        ], now()->addHours(2));

        // Dispatch export job
        GenerateReportExportJob::dispatch(
            $reportId,
            $format,
            $cacheKey,
            auth()->id()
        );

        return [
            'status' => 'queued',
            'message' => 'Export is being processed. You will be notified when it\'s ready.',
            'estimated_time' => $this->estimateProcessingTime(count($results)),
        ];
    }

    /**
     * Format data for display (PDF/view)
     *
     * @param array $results Raw query results
     * @param array $columns Column metadata
     * @return array Formatted results
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
     * Format single row for CSV export
     *
     * @param array $row Data row
     * @param array $columns Column definitions
     * @return array Formatted row values
     */
    protected function formatRowForCsv(array $row, array $columns): array
    {
        $formatted = [];
        foreach ($columns as $column) {
            $key = $column['key'];
            $value = $row[$key] ?? null;

            // Sanitize for CSV injection prevention
            $formattedValue = $this->formatValue($value, $column);
            $formatted[] = $this->sanitizeForCsv($formattedValue);
        }
        return $formatted;
    }

    /**
     * Format value based on column type
     *
     * @param mixed $value Raw value
     * @param array $column Column metadata
     * @return string Formatted value
     */
    protected function formatValue($value, array $column): string
    {
        if ($value === null) {
            return '';
        }

        $type = $column['type'] ?? 'string';

        return match($type) {
            'currency' => $this->formatCurrency($value),
            'number' => $this->formatNumber($value),
            'percentage' => $this->formatPercentage($value),
            'date' => $this->formatDate($value),
            'datetime' => $this->formatDateTime($value),
            'boolean' => $value ? 'Yes' : 'No',
            default => (string) $value,
        };
    }

    /**
     * Format currency value
     *
     * @param mixed $value Numeric value
     * @return string Formatted currency
     */
    protected function formatCurrency($value): string
    {
        return '$' . number_format((float) $value, 2, '.', ',');
    }

    /**
     * Format numeric value with thousand separators
     *
     * @param mixed $value Numeric value
     * @return string Formatted number
     */
    protected function formatNumber($value): string
    {
        return number_format((float) $value, 2, '.', ',');
    }

    /**
     * Format percentage value
     *
     * @param mixed $value Numeric value
     * @return string Formatted percentage
     */
    protected function formatPercentage($value): string
    {
        return number_format((float) $value, 1, '.', '') . '%';
    }

    /**
     * Format date value
     *
     * @param mixed $value Date string or object
     * @return string Formatted date
     */
    protected function formatDate($value): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return (string) $value;
        }
    }

    /**
     * Format datetime value
     *
     * @param mixed $value DateTime string or object
     * @return string Formatted datetime
     */
    protected function formatDateTime($value): string
    {
        if (empty($value)) {
            return '';
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return (string) $value;
        }
    }

    /**
     * Sanitize value for CSV to prevent formula injection
     *
     * @param string $value Input value
     * @return string Sanitized value
     */
    protected function sanitizeForCsv(string $value): string
    {
        // Prevent CSV formula injection
        if (in_array(substr($value, 0, 1), ['=', '+', '-', '@', "\t", "\r"])) {
            return "'" . $value;
        }
        return $value;
    }

    /**
     * Generate standardized filename for export
     *
     * @param string $reportName Base report name
     * @param string $extension File extension
     * @return string Generated filename
     */
    protected function generateFilename(string $reportName, string $extension): string
    {
        // Sanitize report name for filename
        $sanitized = preg_replace('/[^A-Za-z0-9_\-]/', '_', $reportName);
        $timestamp = now()->format('Y-m-d_His');

        return "{$sanitized}_{$timestamp}.{$extension}";
    }

    /**
     * Estimate processing time for async jobs
     *
     * @param int $rowCount Number of rows
     * @return string Estimated time description
     */
    protected function estimateProcessingTime(int $rowCount): string
    {
        $seconds = ceil($rowCount / 100); // Rough estimate: 100 rows per second

        if ($seconds < 60) {
            return "less than 1 minute";
        } elseif ($seconds < 300) {
            return "1-5 minutes";
        } else {
            return "5-10 minutes";
        }
    }
}
