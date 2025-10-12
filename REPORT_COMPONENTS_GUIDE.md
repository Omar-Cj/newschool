# Report Display Components - Implementation Guide

Complete guide for integrating the Report Viewer, Data Table, and Export components into the School Management System.

## Table of Contents

1. [Quick Start](#quick-start)
2. [Components Overview](#components-overview)
3. [Backend Integration](#backend-integration)
4. [Frontend Integration](#frontend-integration)
5. [Customization](#customization)
6. [Accessibility](#accessibility)
7. [Testing](#testing)
8. [Troubleshooting](#troubleshooting)

---

## Quick Start

### 1. Build Assets

```bash
npm run dev    # Development mode
npm run build  # Production build
```

### 2. Include in Blade Template

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            @include('reports.partials.results', [
                'reportId' => $report->id,
                'reportData' => $reportData
            ])
        </div>
    </div>
</div>
@endsection
```

### 3. Setup API Route

```php
// routes/api.php
Route::post('/reports/export', [ReportController::class, 'export'])
    ->name('reports.export');
```

---

## Components Overview

### File Structure

```
resources/
├── js/
│   ├── components/
│   │   ├── ReportViewer.js      # Main report viewer
│   │   ├── DataTable.js          # Table with sort/filter
│   │   ├── ExportButtons.js      # Export functionality
│   │   ├── README.md             # Component documentation
│   │   └── example.html          # Live demo
│   └── app.js                    # Main entry point
├── css/
│   └── reports-print.css         # Print stylesheet
└── views/
    └── reports/
        ├── show.blade.php        # Report display page
        └── partials/
            └── results.blade.php # Results partial
```

### Component Responsibilities

| Component | Purpose | Key Features |
|-----------|---------|--------------|
| **ReportViewer** | Main orchestrator | Loading states, error handling, pagination |
| **DataTable** | Data display | Sorting, filtering, formatting, accessibility |
| **ExportButtons** | Export handling | Excel, PDF, CSV downloads with progress |

---

## Backend Integration

### Laravel Controller Setup

#### 1. Report Display Controller

```php
<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    /**
     * Display report results
     */
    public function show(Request $request, int $id)
    {
        $report = Report::findOrFail($id);

        // Generate report data
        $reportData = $this->reportService->generate($report, $request->all());

        return view('reports.show', [
            'report' => $report,
            'reportData' => $reportData,
            'filters' => $request->all()
        ]);
    }

    /**
     * Export report
     */
    public function export(Request $request)
    {
        $validated = $request->validate([
            'format' => 'required|in:excel,pdf,csv',
            'report_id' => 'required|integer|exists:reports,id',
            'filters' => 'sometimes|array'
        ]);

        $report = Report::findOrFail($validated['report_id']);
        $format = $validated['format'];

        // Generate export
        $file = $this->reportService->export(
            $report,
            $format,
            $validated['filters'] ?? []
        );

        $filename = $this->generateFilename($report, $format);

        return response()->download($file, $filename, [
            'Content-Type' => $this->getContentType($format),
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ])->deleteFileAfterSend(true);
    }

    private function generateFilename(Report $report, string $format): string
    {
        $slug = str_slug($report->name);
        $date = now()->format('Y-m-d');
        $extension = $this->getExtension($format);

        return "{$slug}_{$date}.{$extension}";
    }

    private function getExtension(string $format): string
    {
        return match($format) {
            'excel' => 'xlsx',
            'pdf' => 'pdf',
            'csv' => 'csv',
            default => 'dat'
        };
    }

    private function getContentType(string $format): string
    {
        return match($format) {
            'excel' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pdf' => 'application/pdf',
            'csv' => 'text/csv',
            default => 'application/octet-stream'
        };
    }
}
```

#### 2. Report Service

```php
<?php

namespace App\Services;

use App\Models\Report;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    /**
     * Generate report data
     */
    public function generate(Report $report, array $filters = []): array
    {
        // Execute report query
        $results = $this->executeReportQuery($report, $filters);

        // Build column definitions
        $columns = $this->buildColumnDefinitions($report);

        // Calculate metadata
        $meta = [
            'total_rows' => $results->total(),
            'page' => $results->currentPage(),
            'per_page' => $results->perPage()
        ];

        return [
            'success' => true,
            'report' => [
                'id' => $report->id,
                'name' => $report->name,
                'description' => $report->description,
                'type' => $report->type ?? 'tabular',
                'filters' => $filters
            ],
            'results' => $results->items(),
            'columns' => $columns,
            'meta' => $meta
        ];
    }

    /**
     * Export report to specified format
     */
    public function export(Report $report, string $format, array $filters = []): string
    {
        $data = $this->generate($report, $filters);

        return match($format) {
            'excel' => $this->exportToExcel($data),
            'pdf' => $this->exportToPdf($data),
            'csv' => $this->exportToCsv($data),
            default => throw new \InvalidArgumentException("Unsupported format: {$format}")
        };
    }

    private function executeReportQuery(Report $report, array $filters)
    {
        // This is report-specific logic
        // Example for student fee report:
        $query = DB::table('students')
            ->join('fee_records', 'students.id', '=', 'fee_records.student_id')
            ->select([
                'students.id as student_id',
                'students.name',
                'students.grade',
                'fee_records.amount_due',
                'fee_records.amount_paid',
                'fee_records.due_date',
                'fee_records.status'
            ]);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('fee_records.status', $filters['status']);
        }

        if (isset($filters['start_date'])) {
            $query->where('fee_records.due_date', '>=', $filters['start_date']);
        }

        return $query->paginate(50);
    }

    private function buildColumnDefinitions(Report $report): array
    {
        // This would typically be stored in database or config
        return [
            ['key' => 'student_id', 'label' => 'Student ID', 'type' => 'number'],
            ['key' => 'name', 'label' => 'Student Name', 'type' => 'string'],
            ['key' => 'grade', 'label' => 'Grade', 'type' => 'string'],
            ['key' => 'amount_due', 'label' => 'Amount Due', 'type' => 'currency'],
            ['key' => 'amount_paid', 'label' => 'Amount Paid', 'type' => 'currency'],
            ['key' => 'due_date', 'label' => 'Due Date', 'type' => 'date'],
            ['key' => 'status', 'label' => 'Status', 'type' => 'status']
        ];
    }

    private function exportToExcel(array $data): string
    {
        return Excel::store(
            new ReportExport($data),
            'exports/report_' . time() . '.xlsx',
            'local'
        );
    }

    private function exportToPdf(array $data): string
    {
        $pdf = Pdf::loadView('reports.pdf', $data);
        $filepath = storage_path('app/exports/report_' . time() . '.pdf');
        $pdf->save($filepath);
        return $filepath;
    }

    private function exportToCsv(array $data): string
    {
        $filepath = storage_path('app/exports/report_' . time() . '.csv');
        $file = fopen($filepath, 'w');

        // Write headers
        $headers = array_column($data['columns'], 'label');
        fputcsv($file, $headers);

        // Write data rows
        foreach ($data['results'] as $row) {
            $values = [];
            foreach ($data['columns'] as $column) {
                $values[] = $row[$column['key']] ?? '';
            }
            fputcsv($file, $values);
        }

        fclose($file);
        return $filepath;
    }
}
```

#### 3. Excel Export Class

```php
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromArray, WithHeadings, WithStyles
{
    public function __construct(
        private array $data
    ) {}

    public function array(): array
    {
        return array_map(function($row) {
            $values = [];
            foreach ($this->data['columns'] as $column) {
                $values[] = $row[$column['key']] ?? '';
            }
            return $values;
        }, $this->data['results']);
    }

    public function headings(): array
    {
        return array_column($this->data['columns'], 'label');
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
```

---

## Frontend Integration

### Option 1: Via Blade Partial (Recommended)

```blade
<!-- resources/views/reports/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-body">
            @include('reports.partials.results', [
                'reportId' => $report->id,
                'reportData' => $reportData
            ])
        </div>
    </div>
</div>
@endsection
```

### Option 2: Direct JavaScript Initialization

```blade
@section('content')
<div class="container">
    <div id="reportContainer"></div>
    <div id="exportContainer"></div>
</div>
@endsection

@push('scripts')
<script type="module">
    import ReportViewer from '@/components/ReportViewer.js';
    import ExportButtons from '@/components/ExportButtons.js';

    const reportViewer = new ReportViewer('reportContainer');
    const exportButtons = new ExportButtons('exportContainer', {
        reportId: {{ $report->id }},
        apiEndpoint: '{{ route('reports.export') }}'
    });

    // Load data
    fetch('/api/reports/{{ $report->id }}')
        .then(res => res.json())
        .then(data => {
            reportViewer.render(data);
            exportButtons.setExportData({
                report_id: data.report.id,
                filters: data.report.filters
            });
        });
</script>
@endpush
```

### Option 3: AJAX Dynamic Loading

```javascript
// Load report dynamically
function loadReport(reportId, filters = {}) {
    const reportViewer = new ReportViewer('reportContainer');

    reportViewer.showLoading();

    fetch(`/api/reports/${reportId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ filters })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            reportViewer.render(data, data.report.type);
        } else {
            reportViewer.renderError(data.message);
        }
    })
    .catch(error => {
        reportViewer.renderError('Failed to load report: ' + error.message);
    });
}

// Usage
loadReport(123, { status: 'pending', academic_year: '2024-2025' });
```

---

## Customization

### Custom Column Formatting

```javascript
const dataTable = new DataTable({
    columns: [
        {
            key: 'custom_field',
            label: 'Custom Field',
            type: 'string',
            format: (value) => {
                // Custom formatting logic
                return `<strong>${value}</strong>`;
            }
        }
    ],
    data: myData
});
```

### Custom Export Endpoint

```javascript
const exportButtons = new ExportButtons('exportContainer', {
    apiEndpoint: '/custom/export/endpoint',
    onExportStart: (format) => {
        console.log('Starting export:', format);
        // Show custom loading modal
    },
    onExportComplete: (format, filename) => {
        console.log('Export complete:', filename);
        // Show success notification
    }
});
```

### Custom Styling

```css
/* Custom table styling */
.data-table-wrapper .table {
    font-size: 0.9rem;
}

.data-table-wrapper .table th {
    background-color: #007bff;
    color: white;
}

/* Custom badge colors */
.badge.bg-custom-status {
    background-color: #6f42c1 !important;
}
```

---

## Accessibility

### WCAG 2.1 AA Compliance Features

#### Keyboard Navigation
- All interactive elements are keyboard accessible
- Logical tab order
- Visible focus indicators
- Enter/Space to activate buttons

#### Screen Reader Support
```html
<!-- Proper ARIA labels -->
<table role="table" aria-label="Student fee report">
  <thead>
    <th scope="col" aria-sort="ascending">Student Name</th>
  </thead>
</table>

<!-- Live regions for dynamic updates -->
<div role="status" aria-live="polite" aria-atomic="true">
  Loading report results...
</div>
```

#### Visual Accessibility
- High contrast mode support
- Minimum 4.5:1 contrast ratio
- No information conveyed by color alone
- Resizable text up to 200%

### Testing Accessibility

```bash
# Install accessibility testing tools
npm install --save-dev axe-core pa11y

# Run accessibility audit
npx pa11y http://localhost:8000/reports/1
```

---

## Testing

### Unit Testing (JavaScript)

```javascript
// tests/components/ReportViewer.test.js
import ReportViewer from '@/components/ReportViewer.js';

describe('ReportViewer', () => {
    let container;
    let reportViewer;

    beforeEach(() => {
        container = document.createElement('div');
        container.id = 'test-container';
        document.body.appendChild(container);
        reportViewer = new ReportViewer('test-container');
    });

    afterEach(() => {
        reportViewer.destroy();
        document.body.removeChild(container);
    });

    test('renders loading state', () => {
        reportViewer.showLoading();
        expect(container.querySelector('.loading-state')).toBeTruthy();
    });

    test('renders data correctly', () => {
        const testData = {
            success: true,
            report: { name: 'Test Report' },
            results: [{ id: 1, name: 'Test' }],
            columns: [
                { key: 'id', label: 'ID', type: 'number' },
                { key: 'name', label: 'Name', type: 'string' }
            ]
        };

        reportViewer.render(testData);
        expect(container.querySelector('table')).toBeTruthy();
    });
});
```

### Integration Testing (Laravel)

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Report;

class ReportControllerTest extends TestCase
{
    /** @test */
    public function it_displays_report_page()
    {
        $report = Report::factory()->create();

        $response = $this->get(route('reports.show', $report));

        $response->assertOk();
        $response->assertViewIs('reports.show');
        $response->assertViewHas('report');
    }

    /** @test */
    public function it_exports_report_to_excel()
    {
        $report = Report::factory()->create();

        $response = $this->post(route('reports.export'), [
            'format' => 'excel',
            'report_id' => $report->id
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
```

---

## Troubleshooting

### Common Issues

#### 1. Components not loading

**Problem:** JavaScript components don't initialize

**Solution:**
```bash
# Rebuild assets
npm run dev

# Check browser console for errors
# Ensure Bootstrap 5 is loaded before components
```

#### 2. Export downloads fail

**Problem:** Export button triggers but no download

**Solution:**
```php
// Ensure CSRF token is present
<meta name="csrf-token" content="{{ csrf_token() }}">

// Check export route is defined
Route::post('/api/reports/export', [ReportController::class, 'export'])
    ->middleware('auth')
    ->name('reports.export');

// Verify file permissions
chmod -R 775 storage/app/exports
```

#### 3. Print layout issues

**Problem:** Report doesn't print correctly

**Solution:**
```html
<!-- Ensure print CSS is loaded with media="print" -->
<link rel="stylesheet" href="{{ asset('css/reports-print.css') }}" media="print">

<!-- Check browser print settings:
- Margins: Default
- Scale: 100%
- Background graphics: On
-->
```

#### 4. Table not sorting

**Problem:** Click on headers doesn't sort

**Solution:**
```javascript
// Ensure sortable is enabled
const dataTable = new DataTable({
    sortable: true,  // Must be true
    columns: [
        { key: 'name', label: 'Name', sortable: true }  // Column-level override
    ]
});
```

---

## Performance Optimization

### Large Datasets

For reports with >1,000 rows:

1. **Server-side pagination**
```php
// Load only 50-100 rows per page
$results = $query->paginate(50);
```

2. **Lazy loading**
```javascript
// Load more data as user scrolls
const reportViewer = new ReportViewer('container', {
    pageSize: 50,
    onPageChange: (page) => {
        loadMoreData(page);
    }
});
```

3. **Debounced search**
```javascript
// Already implemented in DataTable component
// Search triggers after 300ms delay
```

---

## Security Considerations

### 1. Authorization

```php
// Ensure user can view report
public function show(Request $request, int $id)
{
    $report = Report::findOrFail($id);

    $this->authorize('view', $report);

    // ... rest of code
}
```

### 2. Input Validation

```php
// Validate export requests
public function export(Request $request)
{
    $validated = $request->validate([
        'format' => 'required|in:excel,pdf,csv',
        'report_id' => 'required|integer|exists:reports,id',
        'filters' => 'sometimes|array'
    ]);

    // Sanitize filters
    $filters = array_map('strip_tags', $validated['filters'] ?? []);
}
```

### 3. Rate Limiting

```php
// routes/api.php
Route::middleware(['throttle:exports'])->group(function () {
    Route::post('/reports/export', [ReportController::class, 'export']);
});

// app/Providers/RouteServiceProvider.php
RateLimiter::for('exports', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()?->id);
});
```

---

## Support

For questions or issues:
1. Check this guide and component README
2. Review example.html for working demos
3. Check browser console for JavaScript errors
4. Review Laravel logs for backend errors

---

## License

Internal use only - School Management System
