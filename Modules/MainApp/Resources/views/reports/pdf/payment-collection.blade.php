<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Collection Report</title>
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

        .period-info {
            background: #f8f9fa;
            padding: 12px;
            border-left: 4px solid #00C48C;
            margin: 15px 0;
            font-size: 9pt;
        }

        .summary-section {
            margin: 5px 0;
            padding: 8px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 5px;
            page-break-inside: avoid;
        }

        .summary-grid {
            display: table;
            width: 100%;
            margin-top: 10px;
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

        .summary-value.primary {
            color: #00C48C;
        }

        .summary-value.success {
            color: #28a745;
        }

        .summary-value.warning {
            color: #ffc107;
        }

        .summary-value.danger {
            color: #dc3545;
        }

        .summary-subtext {
            font-size: 7pt;
            color: #6c757d;
            margin-top: 3px;
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
            font-size: 7pt;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            text-align: center;
        }

        .badge-success {
            background: #28a745;
            color: white;
        }

        .badge-warning {
            background: #ffc107;
            color: #000;
        }

        .badge-danger {
            background: #dc3545;
            color: white;
        }

        .badge-info {
            background: #17a2b8;
            color: white;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-warning {
            color: #ffc107;
        }

        .text-muted {
            color: #6c757d;
            font-size: 7pt;
        }

        .text-small {
            font-size: 7pt;
        }

        .school-name {
            font-weight: bold;
            color: #333;
        }

        .amount-cell {
            text-align: right;
            font-weight: bold;
            font-size: 9pt;
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
            background: #e9ecef !important;
            font-weight: bold;
            border-top: 2px solid #00C48C;
        }

        .payment-method {
            text-transform: capitalize;
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
                <h1>Payment Collection Report</h1>
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
                <span class="metadata-value">{{ \Carbon\Carbon::parse($dateFrom ?? now()->startOfMonth())->format('F d, Y') }}</span>
            </div>
            <div class="metadata-item">
                <span class="metadata-label">Period To:</span>
                <span class="metadata-value">{{ \Carbon\Carbon::parse($dateTo ?? now())->format('F d, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Summary Section --}}
    @if(isset($summary) && $summary['total_payments'] > 0)
    <div class="summary-section">
        <div class="summary-label" style="text-align: center; margin-bottom: 10px;">
            PAYMENT SUMMARY
        </div>
        <div class="summary-grid">
            <div class="summary-row">
                <div class="summary-cell">
                    <div class="summary-label">Total Payments</div>
                    <div class="summary-value primary">
                        {{ number_format($summary['total_payments']) }}
                    </div>
                    <div class="summary-subtext">All Records</div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Approved</div>
                    <div class="summary-value success">
                        {{ number_format($summary['approved_count']) }}
                    </div>
                    <div class="summary-subtext">
                        {{ $summary['total_payments'] > 0 ? number_format(($summary['approved_count'] / $summary['total_payments']) * 100, 1) : 0 }}% of total
                    </div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Pending</div>
                    <div class="summary-value warning">
                        {{ number_format($summary['pending_count']) }}
                    </div>
                    <div class="summary-subtext">
                        {{ $summary['total_payments'] > 0 ? number_format(($summary['pending_count'] / $summary['total_payments']) * 100, 1) : 0 }}% of total
                    </div>
                </div>
                <div class="summary-cell">
                    <div class="summary-label">Rejected</div>
                    <div class="summary-value danger">
                        {{ number_format($summary['rejected_count']) }}
                    </div>
                    <div class="summary-subtext">
                        {{ $summary['total_payments'] > 0 ? number_format(($summary['rejected_count'] / $summary['total_payments']) * 100, 1) : 0 }}% of total
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Payments Table --}}
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 15%;">School Name</th>
                <th style="width: 12%;">Package</th>
                <th style="width: 10%;">Amount</th>
                <th style="width: 10%;">Payment Date</th>
                <th style="width: 10%;">Method</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 12%;">Approved By</th>
                <th style="width: 12%;">Invoice/Ref</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments ?? [] as $index => $payment)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        <span class="school-name">{{ $payment->school_name ?? 'N/A' }}</span>
                        @if(!empty($payment->school_phone))
                            <br><span class="text-muted">{{ $payment->school_phone }}</span>
                        @endif
                    </td>
                    <td class="text-small">{{ $payment->package_name ?? 'N/A' }}</td>
                    <td class="amount-cell">
                        ${{ number_format($payment->amount ?? 0, 2) }}
                    </td>
                    <td class="text-small">
                        {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                    </td>
                    <td class="payment-method text-small">
                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? 'N/A')) }}
                    </td>
                    <td style="text-align: center;">
                        @if($payment->status_code == 0)
                            <span class="badge badge-warning">Pending</span>
                        @elseif($payment->status_code == 1)
                            <span class="badge badge-success">Approved</span>
                        @elseif($payment->status_code == 2)
                            <span class="badge badge-danger">Rejected</span>
                        @else
                            <span class="badge badge-info">Unknown</span>
                        @endif
                    </td>
                    <td class="text-small">
                        @if($payment->status_code == 1 && !empty($payment->approver_name))
                            {{ $payment->approver_name }}
                            @if(!empty($payment->approved_at))
                                <br><span class="text-muted">{{ \Carbon\Carbon::parse($payment->approved_at)->format('M d, Y') }}</span>
                            @endif
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                    <td class="text-small">
                        @if(!empty($payment->invoice_number))
                            {{ $payment->invoice_number }}
                        @elseif(!empty($payment->reference_number))
                            {{ $payment->reference_number }}
                        @elseif(!empty($payment->transaction_id))
                            {{ $payment->transaction_id }}
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="no-data">
                        No payment records found for the selected period and filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($payments ?? []) > 0)
        <tfoot>
            <tr class="total-row">
                <td colspan="3" style="text-align: right; padding-right: 10px;">GRAND TOTAL:</td>
                <td class="amount-cell" style="font-size: 11pt; color: #00C48C;">
                    ${{ number_format($summary['total_amount'] ?? 0, 2) }}
                </td>
                <td colspan="5"></td>
            </tr>
            <tr style="background: #f8f9fa;">
                <td colspan="3" style="text-align: right; padding-right: 10px;">
                    <span class="text-small text-success">Approved Amount:</span>
                </td>
                <td class="amount-cell text-success">
                    @php
                        $approvedAmount = array_sum(array_map(function($p) {
                            return $p->status_code == 1 ? $p->amount : 0;
                        }, $payments));
                    @endphp
                    ${{ number_format($approvedAmount, 2) }}
                </td>
                <td colspan="2"></td>
                <td colspan="3" style="text-align: right;">
                    <span class="badge badge-success">{{ $summary['approved_count'] }} payments</span>
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
            Total Records: {{ count($payments ?? []) }}
        </div>
    </div>
</body>
</html>
