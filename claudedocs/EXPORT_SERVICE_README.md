# Dynamic Report Export System

## Overview

A comprehensive multi-format export system for dynamically generated reports in the School Management System. Supports Excel, PDF, and CSV exports with advanced formatting, large dataset handling, and async processing.

## Quick Start

### 1. Installation

All required packages are already installed:
- `maatwebsite/excel` - Excel exports
- `barryvdh/laravel-dompdf` - PDF generation
- Native PHP - CSV handling

### 2. Register Routes

Add to `routes/web.php`:

```php
require __DIR__.'/reports.php';
```

### 3. Run Migrations

```bash
php artisan queue:table
php artisan migrate
```

### 4. Start Queue Worker

```bash
php artisan queue:work --queue=exports,exports-heavy
```

## Files Created

### Core Services
- `/app/Services/ExportService.php` - Main export service with format handlers
- `/app/Exports/DynamicReportExport.php` - Excel export class with formatting
- `/app/Jobs/GenerateReportExportJob.php` - Background job for heavy exports

### Controllers
- `/app/Http/Controllers/DynamicReportController.php` - HTTP endpoints for exports

### Views
- `/resources/views/reports/pdf/template.blade.php` - Professional PDF template

### Notifications
- `/app/Notifications/ExportReadyNotification.php` - Export ready notification
- `/app/Notifications/ExportFailedNotification.php` - Export failure notification

### Commands
- `/app/Console/Commands/CleanupReportExports.php` - Cleanup old exports

### Routes
- `/routes/reports.php` - Export route definitions

### Documentation
- `/docs/EXPORT_SERVICE_DOCUMENTATION.md` - Complete technical documentation
- `/docs/EXPORT_INTEGRATION_EXAMPLE.md` - Integration examples
- `/EXPORT_SERVICE_README.md` - This file

## Basic Usage

### From Controller

```php
use App\Services\ExportService;

public function export(Request $request)
{
    $exportService = app(ExportService::class);

    $results = [
        ['id' => 1, 'name' => 'John Doe', 'grade' => 85, 'fees' => 1500.50],
        ['id' => 2, 'name' => 'Jane Smith', 'grade' => 92, 'fees' => 2300.75],
    ];

    $columns = [
        ['key' => 'id', 'label' => 'Student ID', 'type' => 'number'],
        ['key' => 'name', 'label' => 'Name', 'type' => 'string'],
        ['key' => 'grade', 'label' => 'Grade', 'type' => 'percentage'],
        ['key' => 'fees', 'label' => 'Fees', 'type' => 'currency'],
    ];

    $metadata = [
        'name' => 'Student Report',
        'parameters' => ['academic_year' => '2024-2025']
    ];

    return $exportService->export(
        reportId: 1,
        format: $request->format, // 'excel', 'pdf', or 'csv'
        results: $results,
        columns: $columns,
        reportMetadata: $metadata
    );
}
```

### From Frontend (AJAX)

```javascript
// Export button click
async function exportReport(format) {
    const response = await fetch('/reports/1/export', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            format: format,
            results: reportData,
            columns: columnDefinitions,
            metadata: { name: 'My Report' }
        })
    });

    if (response.headers.get('content-type').includes('json')) {
        // Async export - queued
        const result = await response.json();
        alert(result.data.message);
    } else {
        // Sync export - download immediately
        const blob = await response.blob();
        downloadFile(blob, `report.${format}`);
    }
}
```

## Features

### Export Formats

| Format | Max Rows | Features | Best For |
|--------|----------|----------|----------|
| **Excel** | Unlimited | Advanced formatting, formulas, auto-width | Financial reports, data analysis |
| **PDF** | 2,000 | Professional layout, print-ready | Official documents, presentations |
| **CSV** | Unlimited | Lightweight, universal compatibility | Data import/export, large datasets |

### Column Types

Supported data types with automatic formatting:
- `string` - Text data
- `number` - Numeric values with thousand separators
- `currency` - Money values ($1,234.56)
- `percentage` - Percentage values (85.5%)
- `date` - Date values (YYYY-MM-DD)
- `datetime` - DateTime values (YYYY-MM-DD HH:MM:SS)
- `boolean` - Yes/No values

### Async Processing

- Exports >500 rows are automatically queued
- User notified via email and database when ready
- Download link expires after 24 hours
- Separate queues for different export types

### Security

- Permission-based access control
- Multi-tenant isolation
- CSV injection prevention
- Data sanitization
- Audit logging

## API Endpoints

### POST /reports/{reportId}/export
Export a report in specified format

### GET /reports/download-export
Download a previously generated export

### POST /reports/quick-export
Quick export without report ID

### GET /reports/export-options
Get available export formats and configuration

### POST /reports/cleanup-exports
Clean up old export files (admin only)

## Console Commands

### Clean Up Old Exports
```bash
# Delete exports older than 24 hours
php artisan reports:cleanup-exports

# Delete exports older than 48 hours
php artisan reports:cleanup-exports --hours=48

# Dry run - see what would be deleted
php artisan reports:cleanup-exports --dry-run
```

## Configuration

### Queue Configuration

In `.env`:
```env
QUEUE_CONNECTION=database
```

### Memory Limits

For large exports, increase PHP limits:
```ini
memory_limit=256M
max_execution_time=300
```

### Storage

Configure default storage disk in `config/filesystems.php`

## Scheduled Tasks

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Clean up exports daily
    $schedule->call(function () {
        \App\Jobs\GenerateReportExportJob::cleanupOldExports(24);
    })->daily();
}
```

## Performance Tips

1. **Use CSV for large datasets** (>100K rows)
2. **Enable async processing** for exports >500 rows
3. **Chunk database queries** to avoid memory issues
4. **Monitor queue** for failed jobs
5. **Clean up old exports** regularly

## Error Handling

All exports include comprehensive error handling:
- Input validation
- Permission checks
- Memory limit monitoring
- User-friendly error messages
- Detailed logging

## Testing

### Unit Test Example

```php
public function test_excel_export_generates_file()
{
    $service = app(ExportService::class);

    $results = [['name' => 'Test', 'amount' => 100.50]];
    $columns = [
        ['key' => 'name', 'label' => 'Name', 'type' => 'string'],
        ['key' => 'amount', 'label' => 'Amount', 'type' => 'currency']
    ];

    $response = $service->exportExcel(1, $results, $columns);

    $this->assertInstanceOf(BinaryFileResponse::class, $response);
}
```

## Troubleshooting

### Export times out
**Solution:** Enable async processing or increase PHP timeout

### Memory limit exceeded
**Solution:** Increase memory_limit in php.ini or use CSV format

### PDF export fails for large dataset
**Solution:** PDF is limited to 2,000 rows. Use Excel or CSV instead.

### Queue not processing
**Solution:**
```bash
php artisan queue:restart
php artisan queue:work
```

### Download link expired
**Solution:** Downloads expire after 24 hours. Re-export the report.

## Documentation

- **Technical Documentation:** See `/docs/EXPORT_SERVICE_DOCUMENTATION.md`
- **Integration Examples:** See `/docs/EXPORT_INTEGRATION_EXAMPLE.md`
- **API Reference:** See controller PHPDoc comments

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Review failed queue jobs: `php artisan queue:failed`
3. Verify permissions and middleware
4. Review documentation files

## Best Practices

1. Always specify column types for proper formatting
2. Use async processing for exports >500 rows
3. Implement proper permissions for sensitive data
4. Clean up old exports regularly
5. Monitor queue for failed jobs
6. Test with large datasets before production
7. Validate data before passing to export service
8. Use meaningful report names
9. Log all exports for audit trail
10. Handle errors gracefully with user-friendly messages

## Requirements

- PHP 8.2+
- Laravel 12.0+
- Required packages (already installed):
  - maatwebsite/excel ^3.1
  - barryvdh/laravel-dompdf ^3.1
  - phpoffice/phpspreadsheet ^1.29

## License

Part of the School Management System - follows project license terms.

---

**Created:** 2025-10-11
**Version:** 1.0.0
**Author:** Export Handler Agent
