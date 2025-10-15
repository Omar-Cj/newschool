<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportName }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #2c3e50;
            line-height: 1.5;
            padding: 15px;
        }

        /* Page Header */
        .page-header {
            margin-bottom: 25px;
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 12px;
        }

        .logo {
            max-width: 140px;
            max-height: 70px;
        }

        .report-title {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .report-subtitle {
            text-align: center;
            font-size: 11pt;
            color: #7f8c8d;
            margin-bottom: 10px;
            font-style: italic;
        }

        /* Metadata Section */
        .metadata-section {
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #bdc3c7;
        }

        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .metadata-item {
            display: flex;
            padding: 6px 0;
        }

        .metadata-label {
            font-weight: 600;
            color: #2980b9;
            min-width: 140px;
            flex-shrink: 0;
        }

        .metadata-value {
            color: #34495e;
            font-weight: 500;
        }

        /* Data Table - Optimized Width */
        .table-container {
            margin-top: 15px;
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .data-table thead {
            background-color: #2980b9;
            color: #ffffff;
        }

        .data-table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            font-size: 10pt;
            color: #ffffff;
            background-color: #2980b9;
            border: 1px solid #2573a7;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .data-table td {
            padding: 10px 10px;
            border: 1px solid #d5d8dc;
            font-size: 9pt;
            color: #2c3e50;
        }

        .data-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .data-table tbody tr:hover {
            background-color: #e8f4f8;
        }

        /* Column type-specific alignment */
        .col-number,
        .col-currency,
        .col-percentage {
            text-align: right;
            font-weight: 500;
        }

        .col-date,
        .col-datetime,
        .col-boolean {
            text-align: center;
        }

        .col-string {
            text-align: left;
        }

        /* Student Name Header - For Gradebook */
        .student-name-header {
            text-align: center;
            font-size: 14pt;
            font-weight: 600;
            color: #2c3e50;
            margin: 15px 0;
            padding: 12px;
            background-color: #d5d8dc;
            border-radius: 6px;
            border: 1px solid #bdc3c7;
        }

        /* Summary Section - For Gradebook */
        .summary-section {
            margin-top: 25px;
            padding: 0;
        }

        .summary-section h5 {
            font-size: 12pt;
            color: #2980b9;
            font-weight: 600;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #3498db;
        }

        /* Summary Table */
        .summary-table {
            width: 60%;
            margin: 0 auto;
            border-collapse: collapse;
            border: 2px solid #2c3e50;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        .summary-table th {
            padding: 12px 15px;
            border: 1px solid #2c3e50;
            font-size: 10pt;
            background-color: #2c3e50;
            color: #ffffff;
            font-weight: 600;
            text-align: left;
        }

        .summary-table th:last-child {
            text-align: right;
        }

        .summary-table tbody tr.exam-row td {
            padding: 10px 15px;
            border: 1px solid #7f8c8d;
            font-size: 10pt;
            background-color: #ffffff;
        }

        .summary-table tbody tr.exam-row:nth-child(even) td {
            background-color: #ecf0f1;
        }

        /* Total All Exams row - special styling */
        .summary-table .total-all-exams-row td {
            background-color: #2980b9;
            color: #ffffff;
            font-weight: bold;
            padding: 12px 15px;
            border: 2px solid #2c3e50;
            border-top: 3px solid #000;
            font-size: 11pt;
        }

        .summary-table td:last-child {
            text-align: right;
        }

        /* Page footer */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 35px;
            border-top: 2px solid #bdc3c7;
            background-color: #f8f9fa;
            padding: 8px 20px;
            font-size: 8pt;
            color: #7f8c8d;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-number:after {
            content: "Page " counter(page);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #95a5a6;
            font-style: italic;
            font-size: 12pt;
            background-color: #f8f9fa;
            border-radius: 6px;
            border: 2px dashed #bdc3c7;
        }

        /* Print optimization */
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }

            .page-header {
                page-break-after: avoid;
            }

            .data-table thead {
                display: table-header-group;
            }

            .data-table tr {
                page-break-inside: avoid;
            }

            .summary-section {
                page-break-inside: avoid;
            }
        }

        /* Page break control */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    {{-- Page Header --}}
    <div class="page-header">
        @if(config('app.logo_path'))
        <div class="logo-section">
            <img src="{{ public_path(config('app.logo_path')) }}" alt="Logo" class="logo">
        </div>
        @endif

        <h1 class="report-title">
            @if(isset($studentName) && $studentName && $procedureName === 'GetStudentGradebook')
                {{ $studentName }} Gradebook
            @else
                {{ $reportName }}
            @endif
        </h1>

        {{-- Conditional Subtitle: Only show for Gradebook reports --}}
        @if(isset($procedureName) && $procedureName === 'GetStudentGradebook')
        <div class="report-subtitle">Complete gradebook showing all marks and grades for this student</div>
        @endif
    </div>

    {{-- Metadata Section - Smart Display with Resolved Parameters --}}
    @if(!empty($parameters) || $generatedAt)
    <div class="metadata-section">
        <div class="metadata-grid">
            @if($generatedAt)
            <div class="metadata-item">
                <span class="metadata-label">Generated:</span>
                <span class="metadata-value">{{ $generatedAt }}</span>
            </div>
            @endif

            @if(auth()->check())
            <div class="metadata-item">
                <span class="metadata-label">Generated By:</span>
                <span class="metadata-value">{{ auth()->user()->name }}</span>
            </div>
            @endif

            {{-- Display resolved parameters (now with human-readable labels and values) --}}
            @foreach($parameters as $label => $value)
            <div class="metadata-item">
                <span class="metadata-label">{{ $label }}:</span>
                <span class="metadata-value">{{ $value }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    @if(!empty($results))
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    @foreach($columns as $column)
                    <th class="col-{{ $column['type'] ?? 'string' }}">
                        {{ $column['label'] }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($results as $row)
                <tr>
                    @foreach($columns as $column)
                    <td class="col-{{ $column['type'] ?? 'string' }}">
                        {{ $row[$column['field']] ?? '' }}
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Summary Table - Only for Gradebook Reports --}}
    @if(isset($summaryData) && !empty($summaryData) && isset($summaryData['rows']) && $procedureName === 'GetStudentGradebook')
    <div class="summary-section">
        <h5>Exam Summary</h5>
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Exam Name</th>
                    <th>Total Mark</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summaryData['rows'] as $index => $row)
                    @php
                        $isTotal = $index === count($summaryData['rows']) - 1;
                        $rowClass = $isTotal ? 'total-all-exams-row' : 'exam-row';
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $row['exam_name'] ?? 'Unknown Exam' }}</td>
                        <td>{{ number_format($row['total_marks'] ?? 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Financial Summary Table - For Paid Students Report and Fee Generation Report --}}
    @if(isset($summaryData) && !empty($summaryData) && isset($summaryData['type']) && $summaryData['type'] === 'financial' && isset($summaryData['rows']) && in_array($procedureName, ['GetPaidStudentsReport', 'GetFeeGenerationReport']))
    <div class="summary-section">
        <h5>Financial Summary</h5>
        <table class="summary-table">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($summaryData['rows'] as $index => $row)
                    @php
                        // Grand Total row gets special styling
                        $isGrandTotal = ($row['metric'] === 'Grand Total');
                        $rowClass = $isGrandTotal ? 'total-all-exams-row' : 'exam-row';
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td>{{ $row['metric'] }}</td>
                        <td>${{ number_format($row['value'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @else
    <div class="empty-state">
        <p>No data available for the selected criteria.</p>
    </div>
    @endif

    {{-- Page Footer --}}
    <div class="page-footer">
        <div>
            {{ config('app.name', 'School Management System') }} &copy; {{ date('Y') }}
        </div>
        <div class="page-number"></div>
    </div>
</body>
</html>
