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
            font-size: 9pt;
            color: #333;
            line-height: 1.4;
        }

        .page-header {
            margin-bottom: 20px;
            border-bottom: 2px solid #5764c6;
            padding-bottom: 10px;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo {
            max-width: 120px;
            max-height: 60px;
        }

        .report-title {
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
            color: #5764c6;
            margin-bottom: 5px;
        }

        .report-subtitle {
            text-align: center;
            font-size: 10pt;
            color: #666;
            margin-bottom: 15px;
        }

        .metadata-section {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .metadata-item {
            display: flex;
        }

        .metadata-label {
            font-weight: bold;
            color: #5764c6;
            width: 120px;
            flex-shrink: 0;
        }

        .metadata-value {
            color: #333;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table thead {
            background-color: #5764c6;
            color: white;
        }

        .data-table th {
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 9pt;
            border: 1px solid #4a56b0;
        }

        .data-table td {
            padding: 6px 6px;
            border: 1px solid #ddd;
            font-size: 8pt;
        }

        .data-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .data-table tbody tr:hover {
            background-color: #e9ecef;
        }

        /* Column type-specific alignment */
        .col-number,
        .col-currency,
        .col-percentage {
            text-align: right;
        }

        .col-date,
        .col-datetime,
        .col-boolean {
            text-align: center;
        }

        .col-string {
            text-align: left;
        }

        /* Page footer */
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            border-top: 1px solid #ddd;
            padding: 5px 20px;
            font-size: 8pt;
            color: #666;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-number:after {
            content: "Page " counter(page);
        }

        /* Summary section */
        .summary-section {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .summary-title {
            font-weight: bold;
            color: #5764c6;
            margin-bottom: 5px;
        }

        /* Handle page breaks */
        .page-break {
            page-break-after: always;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }

        /* Responsive table for better fit */
        @media print {
            body {
                margin: 0;
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

        <h1 class="report-title">{{ $reportName }}</h1>
        <div class="report-subtitle">Dynamic Report Export</div>
    </div>

    {{-- Metadata Section --}}
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

            @if($totalRows)
            <div class="metadata-item">
                <span class="metadata-label">Total Records:</span>
                <span class="metadata-value">{{ number_format($totalRows) }}</span>
            </div>
            @endif

            @foreach($parameters as $paramName => $paramValue)
            <div class="metadata-item">
                <span class="metadata-label">{{ ucfirst(str_replace('_', ' ', $paramName)) }}:</span>
                <span class="metadata-value">{{ $paramValue }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Data Table --}}
    @if(!empty($results))
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
                    {{ $row[$column['key']] ?? '' }}
                </td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="empty-state">
        No data available for the selected criteria.
    </div>
    @endif

    {{-- Summary Section (Optional) --}}
    @if(!empty($results) && $totalRows > 0)
    <div class="summary-section">
        <div class="summary-title">Summary</div>
        <p>This report contains {{ number_format($totalRows) }} record(s) based on the specified criteria.</p>
    </div>
    @endif

    {{-- Page Footer --}}
    <div class="page-footer">
        <div>
            {{ config('app.name', 'School Management System') }} - Generated on {{ $generatedAt }}
        </div>
        <div class="page-number"></div>
    </div>
</body>
</html>
