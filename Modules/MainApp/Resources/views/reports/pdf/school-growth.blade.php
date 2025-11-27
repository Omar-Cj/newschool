<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Growth Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.3;
            padding: 0 25mm;
        }

        /* STEP 1: Updated Header Structure */
        .page-header {
            margin-bottom: 10px;
            border-bottom: 3px solid #00C48C;
            padding-bottom: 8px;
        }

        .page-header-content {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-container {
            flex-shrink: 0;
            width: 110px;
        }

        .logo {
            max-width: 100px;
            max-height: 70px;
            display: block;
        }

        .title-container {
            flex: 1;
            text-align: center;
        }

        h1 {
            font-size: 20pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 0 0 5px 0;
            letter-spacing: 0.5px;
        }

        .report-subtitle {
            font-size: 11pt;
            color: #7f8c8d;
            font-style: italic;
            margin: 0;
        }

        /* STEP 2: Metadata Section */
        .metadata-section {
            background-color: #ecf0f1;
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 10px;
            border: 1px solid #bdc3c7;
        }

        .metadata-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .metadata-item {
            display: flex;
            padding: 2px 0;
        }

        .metadata-label {
            font-weight: 600;
            color: #00C48C;
            min-width: 100px;
            flex-shrink: 0;
            font-size: 8pt;
        }

        .metadata-value {
            color: #34495e;
            font-weight: 500;
            font-size: 8pt;
        }

        .summary-section {
            margin: 5px 0;
            padding: 8px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 5px;
            page-break-inside: avoid;
        }

        .summary-grid {
            display: table;
            width: 100%;
            margin-top: 0;
        }

        .summary-row {
            display: table-row;
        }

        .summary-cell {
            display: table-cell;
            padding: 8px;
            width: 25%;
            text-align: center;
            border-right: 1px solid #dee2e6;
        }

        .summary-cell:last-child {
            border-right: none;
        }

        .summary-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 18pt;
            font-weight: bold;
            color: #333;
        }

        .summary-value.success {
            color: #00C48C;
        }

        .growth-indicator {
            font-size: 8pt;
            margin-top: 5px;
        }

        .growth-indicator.positive {
            color: #28a745;
        }

        .growth-indicator.negative {
            color: #dc3545;
        }

        .period-info {
            background: #fff;
            padding: 10px;
            border-left: 4px solid #00C48C;
            margin: 15px 0;
            font-size: 9pt;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 7pt;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        thead tr {
            background: #00C48C;
            color: white;
        }

        th {
            padding: 8px 8px;
            text-align: left;
            font-weight: 600;
            color: #ffffff;
            background-color: #00C48C;
            border: 1px solid #00B87A;
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        th.text-center {
            text-align: center;
        }

        td {
            padding: 7px;
            border: 1px solid #d5d8dc;
            vertical-align: middle;
            color: #2c3e50;
            font-size: 7pt;
        }

        td.text-center {
            text-align: center;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        tbody tr:hover {
            background: #e9ecef;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
        }

        .badge-primary {
            background: #5669FF;
            color: white;
        }

        .badge-up {
            background: #d4edda;
            color: #155724;
        }

        .badge-down {
            background: #f8d7da;
            color: #721c24;
        }

        .text-success {
            color: #28a745;
            font-weight: bold;
        }

        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        .text-muted {
            color: #6c757d;
            font-size: 8pt;
        }

        .period-label {
            font-weight: bold;
            color: #333;
        }

        .page-footer {
            margin-top: 20px;
            padding-top: 8px;
            border-top: 2px solid #bdc3c7;
            font-size: 8pt;
            color: #7f8c8d;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        @page {
            margin: 15mm;
        }

        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .page-header {
                page-break-after: avoid;
            }

            .metadata-section {
                page-break-inside: avoid;
            }

            table thead {
                display: table-header-group;
            }

            table tr {
                page-break-inside: avoid;
            }

            .summary-section {
                page-break-inside: avoid;
            }

            .no-print {
                display: none !important;
            }
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
            font-style: italic;
        }

        .total-row {
            background: #f8f9fa !important;
            font-weight: bold;
            border-top: 2px solid #00C48C;
        }

        .trend-arrow {
            font-size: 12pt;
        }
    </style>
</head>
<body>
    {{-- Page Header --}}
    <div class="page-header">
        <div class="page-header-content">
            @php
                $logoPath = setting('dark_logo') ? public_path(setting('dark_logo')) : null;
            @endphp

            @if($logoPath && file_exists($logoPath))
            <div class="logo-container">
                <img src="{{ $logoPath }}" alt="School Logo" class="logo">
            </div>
            @endif

            <div class="title-container">
                <h1>School Growth Report</h1>
            </div>
        </div>
    </div>

    {{-- Metadata Section --}}
    <div class="metadata-section">
        <div class="metadata-grid">
            <div class="metadata-item">
                <span class="metadata-label">Generated:</span>
                <span class="metadata-value">{{ \Carbon\Carbon::now()->format('F d, Y - h:i A') }}</span>
            </div>
            <div class="metadata-item">
                <span class="metadata-label">Generated By:</span>
                <span class="metadata-value">{{ auth()->user()->name ?? 'System' }}</span>
            </div>
            <div class="metadata-item">
                <span class="metadata-label">Period From:</span>
                <span class="metadata-value">{{ \Carbon\Carbon::parse($dateFrom ?? now()->subMonths(12))->format('F d, Y') }}</span>
            </div>
            <div class="metadata-item">
                <span class="metadata-label">Period To:</span>
                <span class="metadata-value">{{ \Carbon\Carbon::parse($dateTo ?? now())->format('F d, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Summary Section --}}
    <div class="summary-section">
        <div class="summary-label" style="text-align: center; margin-bottom: 10px;">
            GROWTH METRICS OVERVIEW
        </div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-label">Total Schools</div>
                    <div class="summary-value success">
                        {{ number_format($totalSchools ?? 0) }}
                    </div>
                    @if(isset($schoolsGrowthRate))
                        <div class="growth-indicator {{ $schoolsGrowthRate >= 0 ? 'positive' : 'negative' }}">
                            {{ $schoolsGrowthRate >= 0 ? '↑' : '↓' }}
                            {{ abs($schoolsGrowthRate) }}% growth
                        </div>
                    @endif
                </div>
                <div class="summary-cell">
                    <div class="summary-label">New This Month</div>
                    <div class="summary-value">
                        {{ number_format($newSchoolsThisMonth ?? 0) }}
                    </div>
                    <div class="text-muted">Current Month</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Total Branches</div>
                    <div class="summary-value">
                        {{ number_format($totalBranches ?? 0) }}
                    </div>
                    @if(isset($branchesGrowthRate))
                        <div class="growth-indicator {{ $branchesGrowthRate >= 0 ? 'positive' : 'negative' }}">
                            {{ $branchesGrowthRate >= 0 ? '↑' : '↓' }}
                            {{ abs($branchesGrowthRate) }}% growth
                        </div>
                    @endif
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Total Students</div>
                    <div class="summary-value">
                        {{ number_format($totalStudents ?? 0) }}
                    </div>
                    @if(isset($studentsGrowthRate))
                        <div class="growth-indicator {{ $studentsGrowthRate >= 0 ? 'positive' : 'negative' }}">
                            {{ $studentsGrowthRate >= 0 ? '↑' : '↓' }}
                            {{ abs($studentsGrowthRate) }}% growth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Growth Data Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Period</th>
                <th class="text-center" style="width: 18%;">New Schools</th>
                <th class="text-center" style="width: 18%;">New Branches</th>
                <th class="text-center" style="width: 18%;">New Students</th>
                <th class="text-center" style="width: 16%;">Growth Rate</th>
            </tr>
        </thead>
        <tbody>
            @forelse($growthData ?? [] as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <span class="period-label">{{ $row['period'] ?? 'N/A' }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-success">
                            {{ number_format($row['new_schools'] ?? 0) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-info">
                            {{ number_format($row['new_branches'] ?? 0) }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-primary">
                            {{ number_format($row['new_students'] ?? 0) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @php
                            $growthRate = $row['growth_rate'] ?? 0;
                            $isPositive = $growthRate >= 0;
                        @endphp
                        <span class="badge {{ $isPositive ? 'badge-up' : 'badge-down' }}">
                            <span class="trend-arrow">{{ $isPositive ? '↑' : '↓' }}</span>
                            {{ number_format(abs($growthRate), 2) }}%
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="no-data">
                        No growth data available for the selected period.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($growthData ?? []) > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="2" style="text-align: right; padding-right: 15px;">TOTALS:</td>
                <td class="text-center">
                    <strong>{{ number_format(array_sum(array_column($growthData, 'new_schools'))) }}</strong>
                </td>
                <td class="text-center">
                    <strong>{{ number_format(array_sum(array_column($growthData, 'new_branches'))) }}</strong>
                </td>
                <td class="text-center">
                    <strong>{{ number_format(array_sum(array_column($growthData, 'new_students'))) }}</strong>
                </td>
                <td class="text-center">
                    @php
                        $avgGrowth = count($growthData) > 0
                            ? array_sum(array_column($growthData, 'growth_rate')) / count($growthData)
                            : 0;
                    @endphp
                    <strong class="{{ $avgGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ number_format($avgGrowth, 2) }}% avg
                    </strong>
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- Page Footer --}}
    <div class="page-footer">
        <div>
            {{ config('app.name', 'School Management System') }} &copy; {{ date('Y') }}
        </div>
        <div>
            Total Periods: {{ count($growthData ?? []) }}
        </div>
    </div>
</body>
</html>
