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

        .page-header-content {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .logo-container {
            display: table-cell;
            vertical-align: middle;
            width: 110px;
            padding-right: 10px;
        }

        .logo {
            max-width: 100px;
            max-height: 70px;
            display: block;
        }

        .title-container {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .report-title {
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 0 0 8px 0;
            letter-spacing: 0.5px;
        }

        .report-subtitle {
            text-align: center;
            font-size: 11pt;
            color: #7f8c8d;
            margin: 0;
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

        .metadata-table {
            width: 100%;
            border-collapse: collapse;
        }

        .metadata-table td {
            padding: 6px 10px;
            vertical-align: top;
            width: 50%;
            color: #34495e;
            font-weight: 500;
        }

        .metadata-table strong {
            font-weight: 600;
            color: #2980b9;
            margin-right: 5px;
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
        <div class="page-header-content">
            @php
                $logoSetting = \App\Models\Setting::where('name', 'light_logo')->first();
                $logoSrc = null;

                if ($logoSetting) {
                    $logoPath = public_path($logoSetting->value);

                    if (file_exists($logoPath)) {
                        // Base64 encode for DomPDF compatibility
                        $imageData = file_get_contents($logoPath);
                        $imageType = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $logoSrc = 'data:image/' . $imageType . ';base64,' . base64_encode($imageData);
                    }
                }

                // Fallback to default logo if needed
                if (!$logoSrc) {
                    $defaultLogoPath = public_path('backend/assets/images/default-logo.png');
                    if (file_exists($defaultLogoPath)) {
                        $imageData = file_get_contents($defaultLogoPath);
                        $logoSrc = 'data:image/png;base64,' . base64_encode($imageData);
                    }
                }
            @endphp

            @if($logoSrc)
            <div class="logo-container">
                <img src="{{ $logoSrc }}" alt="School Logo" class="logo">
            </div>
            @endif

            <div class="title-container">
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
        </div>
    </div>

    {{-- Metadata Section - Table-based for DomPDF compatibility --}}
    @if(!empty($parameters) || $generatedAt)
    <div class="metadata-section">
        @php
            // Collect all metadata items into array
            $metadataItems = [];

            if($generatedAt) {
                $metadataItems[] = ['label' => 'Generated', 'value' => $generatedAt];
            }

            if(auth()->check()) {
                $metadataItems[] = ['label' => 'Generated By', 'value' => auth()->user()->name];
            }

            // Add all resolved parameters
            foreach($parameters as $label => $value) {
                $metadataItems[] = ['label' => $label, 'value' => $value];
            }

            // Chunk into rows of 2 columns each
            $metadataRows = array_chunk($metadataItems, 2);
        @endphp

        <table class="metadata-table">
            @foreach($metadataRows as $row)
            <tr>
                @foreach($row as $item)
                <td>
                    <strong>{{ $item['label'] }}:</strong> {{ $item['value'] }}
                </td>
                @endforeach
                {{-- Fill empty cell if odd number of items --}}
                @if(count($row) === 1)
                <td></td>
                @endif
            </tr>
            @endforeach
        </table>
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

    {{-- Financial Summary Table - For Financial Reports --}}
    @if(isset($summaryData) && !empty($summaryData) && isset($summaryData['type']) && $summaryData['type'] === 'financial' && isset($summaryData['rows']) && in_array($procedureName, ['GetPaidStudentsReport', 'GetFeeGenerationReport', 'GetUnpaidStudentsReport', 'GetDiscountReport', 'GetExpensesReport']))
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

    {{-- Count Summary Table - For Student Count Reports --}}
    @if(isset($summaryData) && !empty($summaryData) && isset($summaryData['type']) && $summaryData['type'] === 'count' && isset($summaryData['rows']))
    <div class="summary-section">
        <h5 style="margin-top: 30px; margin-bottom: 15px; font-weight: bold;">Summary</h5>
        <table class="summary-table" style="width: 50%; margin-left: auto; border-collapse: collapse;">
            <tbody>
                @foreach($summaryData['rows'] as $row)
                    <tr class="exam-row" style="background-color: #e9ecef;">
                        <td style="padding: 8px 12px; text-align: right; width: 50%; font-weight: bold; border: 1px solid #dee2e6;">{{ $row['metric'] }}</td>
                        <td style="padding: 8px 12px; text-align: right; width: 50%; font-weight: bold; border: 1px solid #dee2e6;">{{ number_format($row['value']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Fee Generation & Collection Summary - Three Column Layout --}}
    @if(isset($summaryData) && !empty($summaryData) && isset($summaryData['type']) && $summaryData['type'] === 'fee_generation_collection' && isset($summaryData['sections']) && $procedureName === 'GetFeeGenerationCollectionReport')
    <div class="summary-section">
        <h5 style="text-align: center; margin-bottom: 20px;">Summary Report</h5>
        <div style="overflow: hidden; width: 100%;">
            @foreach($summaryData['sections'] as $section)
                <div style="float: left; width: 33.33%; padding-right: 10px; box-sizing: border-box;">
                    <div style="border: 2px solid #2c3e50; border-radius: 6px; overflow: hidden;">
                        <div style="background-color: #2c3e50; color: white; padding: 10px; text-align: center; font-weight: bold; font-size: 11pt;">
                            {{ $section['title'] }}
                        </div>
                        <table style="width: 100%; border-collapse: collapse; margin: 0;">
                            <tbody>
                                @foreach($section['rows'] as $row)
                                    <tr style="{{ $row['is_total'] ? 'background-color: #2980b9; color: white; font-weight: bold;' : 'background-color: white;' }}">
                                        <td style="padding: 10px; border: 1px solid #7f8c8d; font-size: 9pt; width: 60%;">
                                            {{ $row['label'] }}
                                        </td>
                                        <td style="padding: 10px; border: 1px solid #7f8c8d; text-align: right; font-size: 9pt; width: 40%;">
                                            ${{ number_format($row['value'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
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
