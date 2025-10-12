# Export Service Integration Examples

## Complete Integration Example

This guide shows how to integrate the export service into your existing reports.

## Example 1: Student Fees Report with Export

### Backend Controller

```php
<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Services\ExportService;
use App\Models\StudentInfo\Student;
use App\Models\Fees\FeesInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentFeesReportController extends Controller
{
    protected ExportService $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
        $this->middleware('auth');
        $this->middleware('permission:reports.fees');
    }

    /**
     * Display fees report
     */
    public function index(Request $request)
    {
        return view('reports.fees.index');
    }

    /**
     * Generate fees report data
     */
    public function generate(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|integer',
            'class_id' => 'nullable|integer',
            'section_id' => 'nullable|integer',
            'status' => 'nullable|string|in:paid,unpaid,partial',
        ]);

        // Build query
        $query = DB::table('students')
            ->join('session_class_students', 'students.id', '=', 'session_class_students.student_id')
            ->join('classes', 'session_class_students.classes_id', '=', 'classes.id')
            ->join('sections', 'session_class_students.section_id', '=', 'sections.id')
            ->leftJoin('fees_invoices', function($join) use ($request) {
                $join->on('students.id', '=', 'fees_invoices.student_id')
                     ->where('fees_invoices.academic_year', $request->academic_year);
            })
            ->where('session_class_students.session_id', $request->academic_year)
            ->select([
                'students.id',
                'students.full_name',
                'students.enrollment_number',
                'classes.name as class_name',
                'sections.name as section_name',
                DB::raw('COALESCE(SUM(fees_invoices.total_amount), 0) as total_fees'),
                DB::raw('COALESCE(SUM(fees_invoices.paid_amount), 0) as paid_amount'),
                DB::raw('COALESCE(SUM(fees_invoices.total_amount - fees_invoices.paid_amount), 0) as outstanding'),
                DB::raw('CASE
                    WHEN SUM(fees_invoices.paid_amount) = 0 THEN "unpaid"
                    WHEN SUM(fees_invoices.paid_amount) >= SUM(fees_invoices.total_amount) THEN "paid"
                    ELSE "partial"
                END as payment_status')
            ])
            ->groupBy([
                'students.id',
                'students.full_name',
                'students.enrollment_number',
                'classes.name',
                'sections.name'
            ]);

        // Apply filters
        if ($request->class_id) {
            $query->where('session_class_students.classes_id', $request->class_id);
        }

        if ($request->section_id) {
            $query->where('session_class_students.section_id', $request->section_id);
        }

        $results = $query->get()->toArray();

        // Apply status filter
        if ($request->status) {
            $results = array_filter($results, function($row) use ($request) {
                return $row->payment_status === $request->status;
            });
        }

        return response()->json([
            'success' => true,
            'data' => array_values($results)
        ]);
    }

    /**
     * Export fees report
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|string|in:excel,pdf,csv',
            'academic_year' => 'required|integer',
            'class_id' => 'nullable|integer',
            'section_id' => 'nullable|integer',
            'status' => 'nullable|string|in:paid,unpaid,partial',
        ]);

        // Generate report data (reuse generate method logic)
        $reportData = $this->getReportData($request);

        // Define columns with appropriate types
        $columns = [
            ['key' => 'id', 'label' => 'Student ID', 'type' => 'number'],
            ['key' => 'enrollment_number', 'label' => 'Enrollment No.', 'type' => 'string'],
            ['key' => 'full_name', 'label' => 'Student Name', 'type' => 'string'],
            ['key' => 'class_name', 'label' => 'Class', 'type' => 'string'],
            ['key' => 'section_name', 'label' => 'Section', 'type' => 'string'],
            ['key' => 'total_fees', 'label' => 'Total Fees', 'type' => 'currency'],
            ['key' => 'paid_amount', 'label' => 'Paid Amount', 'type' => 'currency'],
            ['key' => 'outstanding', 'label' => 'Outstanding', 'type' => 'currency'],
            ['key' => 'payment_status', 'label' => 'Status', 'type' => 'string'],
        ];

        // Build metadata
        $metadata = [
            'name' => 'Student Fees Report',
            'parameters' => [
                'academic_year' => $this->getAcademicYearName($request->academic_year),
                'class' => $request->class_id ? $this->getClassName($request->class_id) : 'All Classes',
                'section' => $request->section_id ? $this->getSectionName($request->section_id) : 'All Sections',
                'status_filter' => $request->status ? ucfirst($request->status) : 'All Statuses',
            ]
        ];

        // Use export service
        return $this->exportService->export(
            reportId: 1001, // Unique report ID
            format: $request->format,
            results: $reportData,
            columns: $columns,
            reportMetadata: $metadata
        );
    }

    /**
     * Get report data (extracted for reuse)
     */
    protected function getReportData(Request $request): array
    {
        // Same logic as generate() method
        // Returns array of results
    }

    protected function getAcademicYearName($id): string
    {
        return \App\Models\Academic\Session::find($id)->name ?? "Year {$id}";
    }

    protected function getClassName($id): string
    {
        return \App\Models\Academic\Classes::find($id)->name ?? "Class {$id}";
    }

    protected function getSectionName($id): string
    {
        return \App\Models\Academic\Section::find($id)->name ?? "Section {$id}";
    }
}
```

### Frontend Blade View

```blade
{{-- resources/views/reports/fees/index.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>Student Fees Report</h4>
                </div>
                <div class="card-body">
                    {{-- Filters --}}
                    <form id="reportFilters">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Academic Year</label>
                                <select name="academic_year" class="form-control" required>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Class</label>
                                <select name="class_id" class="form-control">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Section</label>
                                <select name="section_id" class="form-control">
                                    <option value="">All Sections</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>Payment Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Statuses</option>
                                    <option value="paid">Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                    <option value="partial">Partial</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> Generate Report
                                </button>
                                <div class="btn-group ml-2">
                                    <button type="button" class="btn btn-success dropdown-toggle"
                                            id="exportDropdown" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false"
                                            disabled>
                                        <i class="fa fa-download"></i> Export
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="exportDropdown">
                                        <a class="dropdown-item export-btn" data-format="excel" href="#">
                                            <i class="fa fa-file-excel"></i> Excel (.xlsx)
                                        </a>
                                        <a class="dropdown-item export-btn" data-format="pdf" href="#">
                                            <i class="fa fa-file-pdf"></i> PDF
                                        </a>
                                        <a class="dropdown-item export-btn" data-format="csv" href="#">
                                            <i class="fa fa-file-csv"></i> CSV
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Results Table --}}
                    <div id="reportResults" class="mt-4" style="display: none;">
                        <h5>Report Results</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="resultsTable">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Enrollment No.</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Total Fees</th>
                                        <th>Paid Amount</th>
                                        <th>Outstanding</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="resultsBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let reportData = null;

// Generate report
$('#reportFilters').on('submit', function(e) {
    e.preventDefault();

    const formData = $(this).serialize();

    $.ajax({
        url: '{{ route("reports.fees.generate") }}',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                reportData = response.data;
                displayResults(response.data);
                $('#exportDropdown').prop('disabled', false);
            }
        },
        error: function(xhr) {
            alert('Error generating report: ' + xhr.responseText);
        }
    });
});

// Display results in table
function displayResults(data) {
    const tbody = $('#resultsBody');
    tbody.empty();

    if (data.length === 0) {
        tbody.append('<tr><td colspan="9" class="text-center">No data found</td></tr>');
        $('#reportResults').show();
        return;
    }

    data.forEach(row => {
        tbody.append(`
            <tr>
                <td>${row.id}</td>
                <td>${row.enrollment_number}</td>
                <td>${row.full_name}</td>
                <td>${row.class_name}</td>
                <td>${row.section_name}</td>
                <td>$${parseFloat(row.total_fees).toFixed(2)}</td>
                <td>$${parseFloat(row.paid_amount).toFixed(2)}</td>
                <td>$${parseFloat(row.outstanding).toFixed(2)}</td>
                <td>
                    <span class="badge badge-${getStatusColor(row.payment_status)}">
                        ${row.payment_status}
                    </span>
                </td>
            </tr>
        `);
    });

    $('#reportResults').show();
}

// Export functionality
$('.export-btn').on('click', function(e) {
    e.preventDefault();

    if (!reportData || reportData.length === 0) {
        alert('Please generate the report first');
        return;
    }

    const format = $(this).data('format');
    const formData = $('#reportFilters').serializeArray();

    // Add format to form data
    formData.push({ name: 'format', value: format });

    // Create form and submit
    const form = $('<form>', {
        method: 'POST',
        action: '{{ route("reports.fees.export") }}'
    });

    // Add CSRF token
    form.append($('<input>', {
        type: 'hidden',
        name: '_token',
        value: '{{ csrf_token() }}'
    }));

    // Add form data
    formData.forEach(item => {
        form.append($('<input>', {
            type: 'hidden',
            name: item.name,
            value: item.value
        }));
    });

    // Submit form
    $('body').append(form);
    form.submit();
    form.remove();
});

function getStatusColor(status) {
    switch(status) {
        case 'paid': return 'success';
        case 'unpaid': return 'danger';
        case 'partial': return 'warning';
        default: return 'secondary';
    }
}
</script>
@endpush
@endsection
```

### Routes

```php
// routes/web.php or routes/reports.php

Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {
    Route::prefix('fees')->name('fees.')->group(function () {
        Route::get('/', [StudentFeesReportController::class, 'index'])->name('index');
        Route::post('/generate', [StudentFeesReportController::class, 'generate'])->name('generate');
        Route::post('/export', [StudentFeesReportController::class, 'export'])->name('export');
    });
});
```

## Example 2: Attendance Report with Export

### Controller Method

```php
public function exportAttendance(Request $request)
{
    $request->validate([
        'format' => 'required|in:excel,pdf,csv',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'class_id' => 'required|integer',
    ]);

    // Get attendance data
    $attendance = DB::table('attendance_records')
        ->join('students', 'attendance_records.student_id', '=', 'students.id')
        ->whereBetween('attendance_records.date', [$request->start_date, $request->end_date])
        ->where('attendance_records.class_id', $request->class_id)
        ->select([
            'students.id',
            'students.full_name',
            DB::raw('COUNT(*) as total_days'),
            DB::raw('SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present_days'),
            DB::raw('SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent_days'),
            DB::raw('ROUND((SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as attendance_percentage')
        ])
        ->groupBy('students.id', 'students.full_name')
        ->get()
        ->toArray();

    $columns = [
        ['key' => 'id', 'label' => 'Student ID', 'type' => 'number'],
        ['key' => 'full_name', 'label' => 'Student Name', 'type' => 'string'],
        ['key' => 'total_days', 'label' => 'Total Days', 'type' => 'number'],
        ['key' => 'present_days', 'label' => 'Present', 'type' => 'number'],
        ['key' => 'absent_days', 'label' => 'Absent', 'type' => 'number'],
        ['key' => 'attendance_percentage', 'label' => 'Attendance %', 'type' => 'percentage'],
    ];

    $metadata = [
        'name' => 'Student Attendance Report',
        'parameters' => [
            'period' => $request->start_date . ' to ' . $request->end_date,
            'class' => $this->getClassName($request->class_id),
        ]
    ];

    return app(ExportService::class)->export(
        1002,
        $request->format,
        $attendance,
        $columns,
        $metadata
    );
}
```

## Example 3: Custom Report Builder

For dynamic user-created reports:

```php
public function exportCustomReport(Request $request, $reportId)
{
    // Get saved report definition
    $report = Report::findOrFail($reportId);

    // Execute report query
    $results = $this->executeReportQuery($report->query_definition);

    // Get column definitions from report
    $columns = $report->columns;

    // Build metadata
    $metadata = [
        'name' => $report->name,
        'parameters' => $report->parameters,
        'description' => $report->description,
    ];

    return app(ExportService::class)->export(
        $reportId,
        $request->format,
        $results,
        $columns,
        $metadata,
        count($results) > 500 // Auto-queue if > 500 rows
    );
}
```

## Example 4: Vue.js Frontend Integration

```vue
<template>
    <div class="report-export">
        <button @click="exportReport('excel')" class="btn btn-success">
            <i class="fa fa-file-excel"></i> Export Excel
        </button>
        <button @click="exportReport('pdf')" class="btn btn-danger">
            <i class="fa fa-file-pdf"></i> Export PDF
        </button>
        <button @click="exportReport('csv')" class="btn btn-primary">
            <i class="fa fa-file-csv"></i> Export CSV
        </button>

        <div v-if="exporting" class="alert alert-info mt-3">
            <i class="fa fa-spinner fa-spin"></i> {{ exportMessage }}
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            exporting: false,
            exportMessage: ''
        }
    },
    methods: {
        async exportReport(format) {
            this.exporting = true;
            this.exportMessage = `Generating ${format.toUpperCase()} export...`;

            try {
                const response = await fetch(`/reports/${this.reportId}/export`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        format: format,
                        results: this.reportData,
                        columns: this.columnDefinitions,
                        metadata: this.reportMetadata
                    })
                });

                if (response.headers.get('content-type')?.includes('application/json')) {
                    // Async export
                    const result = await response.json();
                    this.exportMessage = result.data.message;

                    setTimeout(() => {
                        this.exporting = false;
                    }, 3000);
                } else {
                    // Sync download
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `report_${Date.now()}.${this.getExtension(format)}`;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);

                    this.exporting = false;
                    this.$toast.success('Export downloaded successfully');
                }
            } catch (error) {
                console.error('Export failed:', error);
                this.$toast.error('Export failed. Please try again.');
                this.exporting = false;
            }
        },

        getExtension(format) {
            return format === 'excel' ? 'xlsx' : format;
        }
    }
}
</script>
```

## Tips for Integration

1. **Always validate user input** before passing to export service
2. **Cache report data** if user might export multiple formats
3. **Show loading indicators** during export processing
4. **Handle async exports** with proper user notifications
5. **Implement proper permissions** for sensitive reports
6. **Test with large datasets** to verify performance
7. **Use meaningful report IDs** for tracking and debugging
8. **Include comprehensive metadata** for user context
9. **Log all export operations** for audit purposes
10. **Handle errors gracefully** with user-friendly messages
