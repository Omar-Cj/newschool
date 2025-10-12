# Dynamic Report Export System Documentation

## Overview

The Dynamic Report Export System provides comprehensive multi-format export capabilities for dynamically generated reports. It supports Excel, PDF, and CSV formats with advanced formatting, large dataset handling, and async processing.

## Features

- **Multiple Export Formats**: Excel (XLSX), PDF, CSV
- **Advanced Formatting**: Type-aware cell formatting (currency, dates, percentages, etc.)
- **Large Dataset Support**: Streaming and chunking for efficient memory usage
- **Async Processing**: Queue-based processing for exports >500 rows
- **Security**: Permission-based access, CSV injection prevention, multi-tenant awareness
- **Professional Output**: Styled headers, auto-width columns, metadata inclusion
- **User Notifications**: Email and database notifications when exports are ready

## Architecture

### Components

1. **ExportService** (`app/Services/ExportService.php`)
   - Core export logic for all formats
   - Format-specific handlers (Excel, PDF, CSV)
   - Data formatting and sanitization
   - Queue management for large exports

2. **DynamicReportExport** (`app/Exports/DynamicReportExport.php`)
   - Laravel Excel export class
   - Advanced Excel formatting with PHPSpreadsheet
   - Dynamic column widths and styling
   - Metadata header rows

3. **GenerateReportExportJob** (`app/Jobs/GenerateReportExportJob.php`)
   - Background job for heavy exports
   - File storage management
   - User notification handling
   - Automatic cleanup of old files

4. **DynamicReportController** (`app/Http/Controllers/DynamicReportController.php`)
   - HTTP endpoints for export operations
   - Request validation
   - Permission enforcement
   - Download management

5. **PDF Template** (`resources/views/reports/pdf/template.blade.php`)
   - Professional PDF layout
   - Responsive table design
   - Metadata display
   - Page numbering

## Installation & Setup

### 1. Register Routes

Add to `routes/web.php`:

```php
require __DIR__.'/reports.php';
```

### 2. Configure Queue

In `.env`:

```env
QUEUE_CONNECTION=database
```

Run migrations for queue tables:

```bash
php artisan queue:table
php artisan migrate
```

### 3. Start Queue Worker

```bash
php artisan queue:work --queue=exports,exports-heavy
```

### 4. Schedule Export Cleanup (Optional)

In `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Clean up exports older than 24 hours daily
    $schedule->call(function () {
        \App\Jobs\GenerateReportExportJob::cleanupOldExports(24);
    })->daily();
}
```

## Usage Examples

### Basic Export Request

```javascript
// Frontend JavaScript example
async function exportReport(reportId, format) {
    const response = await fetch(`/reports/${reportId}/export`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            format: format, // 'excel', 'pdf', or 'csv'
            results: [
                { id: 1, name: 'John Doe', grade: 85, amount: 1500.50 },
                { id: 2, name: 'Jane Smith', grade: 92, amount: 2300.75 }
            ],
            columns: [
                { key: 'id', label: 'ID', type: 'number' },
                { key: 'name', label: 'Student Name', type: 'string' },
                { key: 'grade', label: 'Grade', type: 'percentage' },
                { key: 'amount', label: 'Amount', type: 'currency' }
            ],
            metadata: {
                name: 'Student Performance Report',
                parameters: {
                    academic_year: '2024-2025',
                    class: '10th Grade',
                    section: 'A'
                }
            }
        })
    });

    const result = await response.json();

    if (result.success) {
        if (result.data && result.data.status === 'queued') {
            // Export is queued
            alert(result.data.message);
        } else {
            // Direct download (response is file)
            // Handle file download
        }
    }
}
```

### Backend PHP Example

```php
use App\Services\ExportService;

class MyReportController extends Controller
{
    public function exportStudentReport(Request $request)
    {
        $exportService = app(ExportService::class);

        // Get your report data
        $results = Student::with('class')
            ->where('status', 'active')
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->full_name,
                    'enrollment_date' => $student->enrollment_date,
                    'grade_percentage' => $student->average_grade,
                    'total_fees' => $student->total_fees,
                    'is_active' => $student->status === 'active'
                ];
            })
            ->toArray();

        // Define columns with types
        $columns = [
            ['key' => 'id', 'label' => 'Student ID', 'type' => 'number'],
            ['key' => 'name', 'label' => 'Full Name', 'type' => 'string'],
            ['key' => 'enrollment_date', 'label' => 'Enrolled', 'type' => 'date'],
            ['key' => 'grade_percentage', 'label' => 'Grade', 'type' => 'percentage'],
            ['key' => 'total_fees', 'label' => 'Fees', 'type' => 'currency'],
            ['key' => 'is_active', 'label' => 'Active', 'type' => 'boolean'],
        ];

        $metadata = [
            'name' => 'Student Report',
            'parameters' => [
                'academic_year' => $request->academic_year,
                'status' => 'Active Students Only'
            ]
        ];

        return $exportService->export(
            reportId: 123,
            format: $request->format,
            results: $results,
            columns: $columns,
            reportMetadata: $metadata
        );
    }
}
```

### Quick Export Endpoint

For simple, one-off exports:

```javascript
fetch('/reports/quick-export', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
    },
    body: JSON.stringify({
        name: 'My Quick Report',
        format: 'excel',
        data: myDataArray,
        columns: myColumnsArray
    })
}).then(response => response.blob())
  .then(blob => {
      // Download file
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'report.xlsx';
      a.click();
  });
```

## Column Types and Formatting

### Supported Column Types

| Type | Description | Excel Format | PDF/CSV Format |
|------|-------------|--------------|----------------|
| `string` | Text data | Text | As-is |
| `number` | Numeric values | `#,##0.00` | `1,234.56` |
| `currency` | Money values | `$#,##0.00` | `$1,234.56` |
| `percentage` | Percentage values | `0.0%` | `85.5%` |
| `date` | Date values | `YYYY-MM-DD` | `2025-01-15` |
| `datetime` | DateTime values | `YYYY-MM-DD HH:MM:SS` | `2025-01-15 14:30:00` |
| `boolean` | True/False | Yes/No | Yes/No |

### Column Definition Structure

```php
[
    'key' => 'column_name',        // Data key in results array
    'label' => 'Display Name',      // Column header text
    'type' => 'currency'            // Column type (optional, defaults to 'string')
]
```

## Export Formats

### Excel (XLSX)

**Features:**
- Advanced cell formatting based on data type
- Auto-width columns
- Frozen header row
- Auto-filter enabled
- Professional styling with colored headers
- Metadata rows at top of sheet
- Page setup for printing

**Limits:**
- No row limit
- Recommended: <100,000 rows for performance

**Best For:**
- Financial reports
- Data analysis
- Large datasets
- Reports requiring calculations

### PDF

**Features:**
- Professional layout with logo support
- Landscape orientation
- Responsive table design
- Page numbers and footer
- Metadata display
- Automatic page breaks

**Limits:**
- Maximum 2,000 rows (memory constraint)
- Use Excel/CSV for larger datasets

**Best For:**
- Printable reports
- Official documents
- Executive summaries
- Presentations

### CSV

**Features:**
- UTF-8 BOM for Excel compatibility
- CSV injection prevention
- Streaming for large datasets
- Lightweight format

**Limits:**
- No row limit
- No formatting support

**Best For:**
- Data import/export
- Very large datasets (>100K rows)
- Integration with other systems
- Simple data transfer

## Async Processing

### When Exports are Queued

Exports are automatically queued when:
- Row count > 500
- `force_async` parameter is `true`

### Queue Configuration

Two queues are used:
- `exports` - Excel and CSV exports
- `exports-heavy` - PDF exports (more resource-intensive)

### Notification Flow

1. Export request received
2. Data cached with expiration
3. Job dispatched to queue
4. User receives confirmation
5. Job processes export
6. File stored temporarily
7. User notified via email and database
8. User downloads file via link
9. File auto-deleted after 24 hours

### Monitoring Queue

```bash
# View queue status
php artisan queue:work --queue=exports,exports-heavy --verbose

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

## Security Features

### Permission Enforcement

```php
// In controller
$this->middleware('permission:reports.export');
```

Customize permissions in controller:
```php
protected function canAccessReport(int $reportId): bool
{
    // Add your permission logic
    return auth()->user()->can('view-report', $reportId);
}
```

### Multi-Tenant Isolation

Reports automatically respect tenant boundaries when using the tenant-aware middleware.

### CSV Injection Prevention

Special characters (`=`, `+`, `-`, `@`) are automatically escaped in CSV exports.

### Data Sanitization

All exported data is sanitized and validated before processing.

## Performance Optimization

### Memory Management

```php
// Set in .env or php.ini
memory_limit=256M
max_execution_time=300
```

### Large Dataset Handling

For datasets >10,000 rows:
1. Use CSV format when possible
2. Enable async processing
3. Consider chunking data
4. Use database cursors

### Example: Chunked Export

```php
// Instead of loading all data at once
$results = Student::all()->toArray(); // BAD for large datasets

// Use chunking
$results = [];
Student::chunk(1000, function($students) use (&$results) {
    foreach ($students as $student) {
        $results[] = $student->toArray();
    }
});
```

## API Endpoints

### POST /reports/{reportId}/export

Export a report in specified format.

**Request Body:**
```json
{
    "format": "excel",
    "results": [...],
    "columns": [...],
    "metadata": {...},
    "force_async": false
}
```

**Response (Sync):**
File download

**Response (Async):**
```json
{
    "success": true,
    "data": {
        "status": "queued",
        "message": "Export is being processed...",
        "estimated_time": "1-5 minutes"
    }
}
```

### GET /reports/download-export

Download a previously generated export.

**Query Parameters:**
- `key` - Download key from notification

**Response:**
File download

### POST /reports/quick-export

Quick export without report ID.

**Request Body:**
```json
{
    "name": "Report Name",
    "format": "excel",
    "data": [...],
    "columns": [...]
}
```

### GET /reports/export-options

Get available export formats and configuration.

**Response:**
```json
{
    "success": true,
    "data": {
        "formats": [...],
        "async_threshold": 500,
        "column_types": [...]
    }
}
```

## Error Handling

### Common Errors

| Error | Status | Solution |
|-------|--------|----------|
| Invalid format | 400 | Use excel, pdf, or csv |
| Validation failed | 422 | Check request structure |
| Unauthorized | 403 | Verify permissions |
| Export too large (PDF) | 500 | Use Excel or CSV |
| Download expired | 404 | Re-export report |

### Error Response Format

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field": ["validation error"]
    }
}
```

## Testing

### Unit Test Example

```php
use Tests\TestCase;
use App\Services\ExportService;

class ExportServiceTest extends TestCase
{
    public function test_excel_export_generates_file()
    {
        $service = app(ExportService::class);

        $results = [
            ['name' => 'Test', 'amount' => 100.50]
        ];

        $columns = [
            ['key' => 'name', 'label' => 'Name', 'type' => 'string'],
            ['key' => 'amount', 'label' => 'Amount', 'type' => 'currency']
        ];

        $response = $service->exportExcel(1, $results, $columns);

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $response);
    }
}
```

## Troubleshooting

### Issue: Export times out

**Solution:** Enable async processing or increase PHP timeout

### Issue: Memory limit exceeded

**Solution:**
- Increase `memory_limit` in php.ini
- Use CSV format for large exports
- Implement chunking

### Issue: PDF export fails for large dataset

**Solution:** PDF is limited to 2,000 rows. Use Excel or CSV instead.

### Issue: Queue not processing

**Solution:**
```bash
# Restart queue worker
php artisan queue:restart
php artisan queue:work
```

### Issue: Download link expired

**Solution:** Downloads expire after 24 hours. Re-export the report.

## Advanced Customization

### Custom PDF Template

Edit `resources/views/reports/pdf/template.blade.php` to customize PDF layout.

### Custom Excel Styling

Modify `DynamicReportExport::styles()` method for custom styling.

### Custom Formatters

Add custom formatters in `ExportService::formatValue()`:

```php
protected function formatValue($value, array $column): string
{
    $type = $column['type'] ?? 'string';

    return match($type) {
        'custom_type' => $this->formatCustomType($value),
        // ... existing types
    };
}

protected function formatCustomType($value): string
{
    // Your custom formatting logic
    return strtoupper($value);
}
```

## Best Practices

1. **Always specify column types** for proper formatting
2. **Use async processing** for exports >500 rows
3. **Implement proper permissions** for sensitive data
4. **Clean up old exports** regularly via scheduled task
5. **Monitor queue** for failed jobs
6. **Test with large datasets** before production
7. **Validate data** before passing to export service
8. **Use meaningful report names** for better file organization
9. **Log all exports** for audit trail (already implemented)
10. **Handle errors gracefully** with user-friendly messages

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Review failed queue jobs: `php artisan queue:failed`
3. Verify permissions and middleware
4. Check database configuration
5. Review this documentation

## License

This export system is part of the School Management System and follows the project's license terms.
