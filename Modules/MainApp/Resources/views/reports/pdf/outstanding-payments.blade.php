<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outstanding Payments Report</title>
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
            background: #f8f9fa;
            border-radius: 5px;
            page-break-inside: avoid;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-row {
            display: table-row;
        }

        .summary-cell {
            display: table-cell;
            padding: 8px;
            width: 33.33%;
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
            margin-bottom: 3px;
        }

        .summary-value {
            font-size: 18pt;
            font-weight: bold;
            color: #333;
        }

        .summary-value.danger {
            color: #dc3545;
        }

        .summary-value.warning {
            color: #ffc107;
        }

        .summary-value.info {
            color: #17a2b8;
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

        td {
            padding: 7px;
            border: 1px solid #d5d8dc;
            vertical-align: top;
            color: #2c3e50;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        tbody tr.critical {
            background: #ffe5e5 !important;
        }

        tbody tr.grace {
            background: #fff4e5 !important;
        }

        tbody tr.expiring {
            background: #fff9e5 !important;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
        }

        .badge-danger {
            background: #dc3545;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: #000;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
        }

        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        .text-muted {
            color: #6c757d;
            font-size: 7pt;
        }

        .text-small {
            font-size: 7pt;
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

        .school-name {
            font-weight: bold;
            color: #333;
        }

        .amount-cell {
            text-align: right;
            font-weight: bold;
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
                <h1>Outstanding Payments Report</h1>
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
                <span class="metadata-label">Total Schools:</span>
                <span class="metadata-value">{{ count($schools ?? []) }}</span>
            </div>
            <div class="metadata-item">
                <span class="metadata-label">Status:</span>
                <span class="metadata-value">Outstanding Payments</span>
            </div>
        </div>
    </div>

    {{-- Summary Section --}}
    <div class="summary-section">
        <div class="summary-label" style="text-align: center; margin-bottom: 10px;">
            EXECUTIVE SUMMARY
        </div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-label">Total Outstanding</div>
                    <div class="summary-value danger">
                        ${{ number_format($totalOutstanding ?? 0, 2) }}
                    </div>
                    <div class="text-muted">{{ count($schools ?? []) }} Schools</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Critical (Overdue)</div>
                    <div class="summary-value danger">
                        {{ $overdueCount ?? 0 }}
                    </div>
                    <div class="text-muted">Immediate Action Required</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">In Grace Period</div>
                    <div class="summary-value warning">
                        {{ $graceCount ?? 0 }}
                    </div>
                    <div class="text-muted">Action Needed Soon</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 15%;">School Name</th>
                <th style="width: 12%;">Package</th>
                <th style="width: 10%;">Contact</th>
                <th style="width: 10%;">Expiry Date</th>
                <th style="width: 10%;">Grace End</th>
                <th style="width: 8%;">Days Overdue</th>
                <th style="width: 10%;">Amount</th>
                <th style="width: 10%;">Urgency</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schools ?? [] as $index => $school)
                @php
                    $rowClass = '';
                    $badgeClass = 'badge-info';

                    if ($school->urgency_level == 'Critical') {
                        $rowClass = 'critical';
                        $badgeClass = 'badge-danger';
                    } elseif ($school->urgency_level == 'In Grace Period') {
                        $rowClass = 'grace';
                        $badgeClass = 'badge-warning';
                    } elseif ($school->urgency_level == 'Expiring Soon') {
                        $rowClass = 'expiring';
                        $badgeClass = 'badge-info';
                    }
                @endphp
                <tr class="{{ $rowClass }}">
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <span class="school-name">{{ $school->school_name ?? 'N/A' }}</span>
                        @if(!empty($school->sub_domain_key))
                            <br><span class="text-muted">{{ $school->sub_domain_key }}</span>
                        @endif
                    </td>
                    <td>{{ $school->package_name ?? 'N/A' }}</td>
                    <td class="text-small">
                        {{ $school->school_phone ?? 'N/A' }}
                        @if(!empty($school->school_email))
                            <br><span class="text-muted">{{ Str::limit($school->school_email, 20) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($school->expiry_date)
                            {{ \Carbon\Carbon::parse($school->expiry_date)->format('M d, Y') }}
                            <br><span class="text-muted">{{ \Carbon\Carbon::parse($school->expiry_date)->diffForHumans() }}</span>
                        @else
                            N/A
                        @endif
                    </td>
                    <td>
                        @if($school->grace_expiry_date)
                            {{ \Carbon\Carbon::parse($school->grace_expiry_date)->format('M d, Y') }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if($school->days_overdue > 0)
                            <span class="badge badge-danger">{{ $school->days_overdue }} days</span>
                        @elseif($school->days_overdue < 0)
                            <span class="badge badge-warning">{{ abs($school->days_overdue) }} days left</span>
                        @else
                            <span class="badge badge-info">Today</span>
                        @endif
                    </td>
                    <td class="amount-cell text-danger">
                        ${{ number_format($school->outstanding_amount ?? 0, 2) }}
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }}">{{ $school->urgency_level }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="no-data">
                        No outstanding payments found for the selected criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($schools ?? []) > 0)
        <tfoot>
            <tr style="background: #f8f9fa; font-weight: bold;">
                <td colspan="7" style="text-align: right; padding-right: 10px;">TOTAL OUTSTANDING:</td>
                <td class="amount-cell text-danger" style="font-size: 10pt;">
                    ${{ number_format($totalOutstanding ?? 0, 2) }}
                </td>
                <td></td>
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
            Total Records: {{ count($schools ?? []) }}
        </div>
    </div>
</body>
</html>
