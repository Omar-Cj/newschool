<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #392C7D;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            color: #392C7D;
            font-size: 24px;
            font-weight: bold;
        }

        .header .school-info {
            margin-top: 5px;
            font-size: 11px;
            color: #666;
        }

        .report-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .report-title h2 {
            margin: 0;
            color: #333;
            font-size: 18px;
            font-weight: 600;
        }

        .filters-section {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .filters-section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #392C7D;
            font-weight: 600;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .filter-item {
            font-size: 11px;
        }

        .filter-item strong {
            color: #333;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .results-table thead th {
            background-color: #392C7D;
            color: #fff;
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            border: 1px solid #392C7D;
        }

        .results-table tbody td {
            padding: 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }

        .results-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary-section {
            margin-top: 20px;
            float: right;
            width: 50%;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td,
        .summary-table th {
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        .summary-table th {
            background-color: #f5f5f5;
            text-align: right;
            font-weight: 600;
        }

        .summary-table .net-total {
            background-color: #d4edda;
            font-weight: bold;
        }

        .footer {
            clear: both;
            margin-top: 40px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ setting('application_name') }}</h1>
        <div class="school-info">
            {{ setting('address') }}<br>
            Generated on: {{ date('F d, Y h:i A') }}
        </div>
    </div>

    <!-- Report Title -->
    <div class="report-title">
        <h2>{{ $title }}</h2>
    </div>

    <!-- Filters Applied -->
    <div class="filters-section">
        <h3>Filters Applied</h3>
        <div class="filter-grid">
            <div class="filter-item">
                <strong>Date Range:</strong> {{ date('d/m/Y', strtotime($filters['start_date'])) }} - {{ date('d/m/Y', strtotime($filters['end_date'])) }}
            </div>
            <div class="filter-item">
                <strong>Grade:</strong> {{ $filters['grade'] }}
            </div>
            <div class="filter-item">
                <strong>Class:</strong> {{ $filters['class'] }}
            </div>
            <div class="filter-item">
                <strong>Section:</strong> {{ $filters['section'] }}
            </div>
            <div class="filter-item">
                <strong>Gender:</strong> {{ $filters['gender'] }}
            </div>
        </div>
    </div>

    <!-- Results Table -->
    @if($results->count() > 0)
        <table class="results-table">
            <thead>
                <tr>
                    <th>Payment Date</th>
                    <th>Journal</th>
                    <th>Student Name</th>
                    <th>Mobile</th>
                    <th class="text-end">Paid Amount</th>
                    <th class="text-end">Deposit</th>
                    <th class="text-end">Discount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($results as $row)
                    <tr>
                        <td>{{ $row->payment_date ? date('d/m/Y', strtotime($row->payment_date)) : '-' }}</td>
                        <td>{{ $row->journal ?? '-' }}</td>
                        <td>{{ $row->student_name ?? '-' }}</td>
                        <td>{{ $row->mobile ?? '-' }}</td>
                        <td class="text-end">{{ number_format($row->paid_amount ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($row->deposit_used ?? 0, 2) }}</td>
                        <td class="text-end">{{ number_format($row->discount ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <th>Total Paid Amount:</th>
                    <td class="text-end">{{ $summary['total_paid_amount'] }}</td>
                </tr>
                <tr>
                    <th>Total Deposit:</th>
                    <td class="text-end">{{ $summary['total_deposit'] }}</td>
                </tr>
                <tr>
                    <th>Total Discount:</th>
                    <td class="text-end">{{ $summary['total_discount'] }}</td>
                </tr>
                <tr class="net-total">
                    <th>Net Total:</th>
                    <td class="text-end"><strong>{{ $summary['net_total'] }}</strong></td>
                </tr>
            </table>
        </div>
    @else
        <div class="no-data">
            No records found for the selected filters.
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        {{ setting('footer_text') ?? 'Powered by School Management System' }}<br>
        This is a computer-generated document. No signature is required.
    </div>
</body>
</html>
