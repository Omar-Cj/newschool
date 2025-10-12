# Report Display Components Documentation

Complete documentation for the Report Viewer, Data Table, and Export components.

## Overview

This module provides a comprehensive reporting system with:
- Interactive data table with sorting and filtering
- Multiple export formats (Excel, PDF, CSV)
- Print-optimized layouts
- Responsive design with Bootstrap 5
- Full accessibility support (WCAG 2.1 AA compliant)

## Components

### 1. ReportViewer

Main component for rendering report results with different visualization types.

#### Installation

```javascript
import ReportViewer from './components/ReportViewer.js';

const reportViewer = new ReportViewer('containerId', {
    showPagination: true,
    showExport: true,
    pageSize: 50,
    onPageChange: (page) => console.log('Page:', page)
});
```

#### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `showPagination` | Boolean | `true` | Display pagination controls |
| `showExport` | Boolean | `true` | Show export buttons |
| `pageSize` | Number | `50` | Records per page |
| `onPageChange` | Function | `null` | Callback when page changes |

#### Methods

##### render(data, reportType)
Main rendering function that displays report data.

```javascript
reportViewer.render({
    success: true,
    report: {
        id: 1,
        name: 'Student Fee Report',
        description: 'Outstanding fees by student',
        type: 'tabular'
    },
    results: [
        { student_id: 1, name: 'John Doe', amount_due: 5000 },
        { student_id: 2, name: 'Jane Smith', amount_due: 3000 }
    ],
    columns: [
        { key: 'student_id', label: 'ID', type: 'number' },
        { key: 'name', label: 'Student Name', type: 'string' },
        { key: 'amount_due', label: 'Amount Due', type: 'currency' }
    ],
    meta: {
        total_rows: 150,
        page: 1,
        per_page: 50
    }
}, 'tabular');
```

**Parameters:**
- `data` (Object): Report data from API
- `reportType` (String): Type of report - `'tabular'`, `'chart'`, `'summary'`

##### showLoading()
Display loading state while report is being generated.

```javascript
reportViewer.showLoading();
```

##### applyFormatting(value, type)
Format values based on data type.

```javascript
const formatted = reportViewer.applyFormatting(1234.56, 'currency');
// Returns: "$1,234.56"
```

**Supported Types:**
- `currency`: Formats as currency ($1,234.56)
- `date`: Formats as date (Jan 15, 2025)
- `datetime`: Formats as date and time (Jan 15, 2025, 2:30 PM)
- `number`: Formats with thousand separators (1,234)
- `percentage`: Formats as percentage (45.67%)
- `boolean`: Displays as Yes/No badges
- `status`: Displays colored status badges

##### destroy()
Clean up and remove the report viewer.

```javascript
reportViewer.destroy();
```

#### Events

The component triggers callbacks through options:

```javascript
const reportViewer = new ReportViewer('container', {
    onPageChange: (page) => {
        console.log('Navigated to page:', page);
        // Fetch new page data from server
    }
});
```

---

### 2. DataTable

Responsive, accessible data table with client-side sorting and filtering.

#### Installation

```javascript
import DataTable from './components/DataTable.js';

const dataTable = new DataTable({
    columns: [
        { key: 'id', label: 'ID', type: 'number', sortable: true },
        { key: 'name', label: 'Name', type: 'string' },
        { key: 'amount', label: 'Amount', type: 'currency' }
    ],
    data: [
        { id: 1, name: 'John', amount: 1000 },
        { id: 2, name: 'Jane', amount: 2000 }
    ],
    pageSize: 50,
    sortable: true,
    searchable: true
});

const container = document.getElementById('tableContainer');
dataTable.render(container);
```

#### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `columns` | Array | `[]` | Column definitions |
| `data` | Array | `[]` | Table data |
| `pageSize` | Number | `50` | Rows per page |
| `sortable` | Boolean | `true` | Enable sorting |
| `searchable` | Boolean | `true` | Show search box |
| `responsive` | Boolean | `true` | Responsive layout |
| `onSort` | Function | `null` | Callback after sort |
| `onFilter` | Function | `null` | Callback after filter |

#### Column Definition

```javascript
{
    key: 'column_name',        // Data key
    label: 'Display Name',     // Header label
    type: 'currency',          // Data type for formatting
    sortable: true,            // Enable sorting (default: true)
    className: 'text-end',     // Additional CSS classes
    cellClassName: 'fw-bold',  // Cell CSS classes
    format: (value) => {       // Custom formatter function
        return `Custom: ${value}`;
    }
}
```

#### Column Types

- `string`: Plain text
- `number`: Numeric values with thousand separators
- `currency`: Formatted currency ($1,234.56)
- `date`: Date formatting (Jan 15, 2025)
- `datetime`: Date and time
- `percentage`: Percentage with badge
- `boolean`: Yes/No badges
- `status`: Colored status badges
- `badge`: Generic badge

#### Methods

##### render(container)
Render the table in the specified container.

```javascript
const container = document.getElementById('myTable');
dataTable.render(container);
```

##### updateData(newData)
Update table with new data.

```javascript
dataTable.updateData([
    { id: 3, name: 'Bob', amount: 3000 }
]);
```

##### getData()
Get current filtered/sorted data.

```javascript
const currentData = dataTable.getData();
console.log('Showing', currentData.length, 'rows');
```

##### destroy()
Clean up the table.

```javascript
dataTable.destroy();
```

#### Accessibility Features

- Proper ARIA roles and labels
- Keyboard navigation (Tab, Enter, Space)
- Screen reader support
- Sort indicators with aria-sort
- Accessible search input

---

### 3. ExportButtons

Handle data export to Excel, PDF, and CSV formats.

#### Installation

```javascript
import ExportButtons from './components/ExportButtons.js';

const exportButtons = new ExportButtons('exportContainer', {
    reportId: 123,
    formats: ['excel', 'pdf', 'csv'],
    apiEndpoint: '/api/reports/export',
    onExportStart: (format) => console.log('Exporting:', format),
    onExportComplete: (format, filename) => console.log('Done:', filename),
    onExportError: (error, format) => console.error('Failed:', error)
});
```

#### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `reportId` | Number | `null` | Report ID for export |
| `formats` | Array | `['excel', 'pdf', 'csv']` | Available export formats |
| `apiEndpoint` | String | `'/api/reports/export'` | Export API endpoint |
| `onExportStart` | Function | `null` | Called when export starts |
| `onExportComplete` | Function | `null` | Called on success |
| `onExportError` | Function | `null` | Called on error |

#### Methods

##### setExportData(data)
Set additional data to include in export request.

```javascript
exportButtons.setExportData({
    report_id: 123,
    filters: {
        start_date: '2025-01-01',
        end_date: '2025-01-31',
        status: 'active'
    }
});
```

##### setReportId(reportId)
Update the report ID.

```javascript
exportButtons.setReportId(456);
```

##### setEnabled(enabled)
Enable or disable export buttons.

```javascript
exportButtons.setEnabled(false); // Disable
exportButtons.setEnabled(true);  // Enable
```

##### destroy()
Clean up the component.

```javascript
exportButtons.destroy();
```

#### Export API Response

The API endpoint should return file blob with proper headers:

```php
// Laravel Controller Example
public function export(Request $request)
{
    $format = $request->get('format'); // excel, pdf, csv
    $reportId = $request->get('report_id');

    // Generate export file
    $filename = "report_{$reportId}." . $this->getExtension($format);

    return response()->download($filepath, $filename, [
        'Content-Type' => $this->getContentType($format),
        'Content-Disposition' => 'attachment; filename="' . $filename . '"'
    ]);
}
```

---

## Complete Usage Example

### Blade Template

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

### JavaScript Integration

```javascript
import ReportViewer from './components/ReportViewer.js';
import ExportButtons from './components/ExportButtons.js';

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {

    // Initialize Report Viewer
    const reportViewer = new ReportViewer('reportTableContainer', {
        showPagination: true,
        pageSize: 50,
        onPageChange: (page) => {
            // Load new page from server
            fetch(`/api/reports/${reportId}?page=${page}`)
                .then(res => res.json())
                .then(data => reportViewer.render(data));
        }
    });

    // Initialize Export Buttons
    const exportButtons = new ExportButtons('exportButtons', {
        reportId: reportId,
        apiEndpoint: '/api/reports/export',
        onExportStart: (format) => {
            console.log('Starting export:', format);
        },
        onExportComplete: (format, filename) => {
            console.log('Export completed:', filename);
        }
    });

    // Load initial data
    fetch(`/api/reports/${reportId}`)
        .then(res => res.json())
        .then(data => {
            reportViewer.render(data, 'tabular');
            exportButtons.setExportData({
                report_id: data.report.id,
                filters: data.report.filters
            });
        })
        .catch(error => {
            console.error('Error loading report:', error);
            reportViewer.renderError(error.message);
        });
});
```

---

## API Response Format

### Expected Data Structure

```json
{
  "success": true,
  "report": {
    "id": 123,
    "name": "Student Fee Report",
    "description": "Outstanding fees by student",
    "type": "tabular",
    "filters": {
      "academic_year": "2024-2025",
      "status": "pending"
    }
  },
  "results": [
    {
      "student_id": 1,
      "name": "John Doe",
      "grade": "10th",
      "amount_due": 5000,
      "due_date": "2025-01-31",
      "status": "pending"
    }
  ],
  "columns": [
    {
      "key": "student_id",
      "label": "Student ID",
      "type": "number",
      "sortable": true
    },
    {
      "key": "name",
      "label": "Student Name",
      "type": "string"
    },
    {
      "key": "amount_due",
      "label": "Amount Due",
      "type": "currency"
    },
    {
      "key": "due_date",
      "label": "Due Date",
      "type": "date"
    },
    {
      "key": "status",
      "label": "Status",
      "type": "status"
    }
  ],
  "meta": {
    "total_rows": 150,
    "page": 1,
    "per_page": 50
  }
}
```

---

## Print Styling

Reports are print-optimized using `reports-print.css`:

### Features
- A4 page sizing with proper margins
- Page breaks at appropriate locations
- Header/footer with page numbers
- Optimized table layout
- Badge and status color printing
- Removes UI controls (buttons, search, etc.)

### Usage

```html
<!-- Include in Blade template -->
<link rel="stylesheet" href="{{ asset('css/reports-print.css') }}" media="print">
```

### Print-Specific Classes

```html
<!-- Hide from print -->
<div class="d-print-none">Won't appear in print</div>

<!-- Show only in print -->
<div class="print-only">Only in print</div>

<!-- Force page break -->
<div class="print-page-break"></div>

<!-- Prevent page break inside -->
<div class="print-no-break">Keep together</div>

<!-- Landscape orientation -->
<div class="print-landscape">Wide table</div>
```

---

## Accessibility Compliance

All components meet WCAG 2.1 AA standards:

### Keyboard Navigation
- **Tab**: Navigate through interactive elements
- **Enter/Space**: Activate buttons and sort columns
- **Arrow Keys**: Navigate table cells (native browser behavior)

### Screen Reader Support
- Proper ARIA roles and labels
- Table headers with scope attributes
- Sort state announcements
- Loading state notifications
- Error announcements

### Visual Accessibility
- High contrast text and borders
- Clear focus indicators
- Sufficient color contrast ratios
- Resizable text support
- No information conveyed by color alone

---

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

### Required Features
- ES6 modules
- Fetch API
- Intl.NumberFormat
- Intl.DateTimeFormat

---

## Performance Optimization

### Large Datasets
For reports with >1000 rows:

1. **Server-side pagination**: Only load 50-100 rows at a time
2. **Virtual scrolling**: Render only visible rows (future enhancement)
3. **Lazy export**: Generate exports asynchronously

### Client-side Optimization
- Efficient DOM manipulation
- Debounced search input
- Cached formatted values

---

## Troubleshooting

### Common Issues

**Export button not working:**
- Check CSRF token is present in meta tag
- Verify API endpoint is correct
- Check browser console for errors

**Table not sorting:**
- Ensure columns have `sortable: true` (or not set, defaults to true)
- Check data types match column type

**Print layout issues:**
- Ensure `reports-print.css` is loaded with `media="print"`
- Check browser print settings (margins, scaling)

**Data not displaying:**
- Verify API response structure matches expected format
- Check browser console for JavaScript errors
- Ensure container element exists with correct ID

---

## License

Part of the School Management System.
For internal use only.
