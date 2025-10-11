<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        .report-header {
            background: #392C7D;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            color: #FFFFFF;
        }

        .report-header h2 {
            font-size: 24px;
            margin: 0;
            color: #FFFFFF;
        }

        .report-header p {
            font-size: 14px;
            margin: 5px 0 0 0;
            color: #D6D6D6;
        }

        .report-filters {
            background: #F5F5F5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .report-filters h4 {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 10px 0;
            color: #424242;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 8px;
        }

        .filter-item {
            flex: 1;
            min-width: 150px;
        }

        .filter-item strong {
            font-weight: 600;
            color: #1A1A21;
        }

        .filter-item span {
            color: #424242;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background-color: #E6E6E6;
        }

        table thead th {
            padding: 10px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            color: #1A1A21;
            border: 1px solid #D0D0D0;
        }

        table tbody td {
            padding: 8px 10px;
            border: 1px solid #E0E0E0;
            font-size: 11px;
            color: #424242;
        }

        table tbody tr:nth-child(odd) {
            background: #F8F8F8;
        }

        table tbody tr:nth-child(even) {
            background: #EFEFEF;
        }

        .text-end {
            text-align: right;
        }

        .summary-section {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .summary-table {
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .summary-table td,
        .summary-table th {
            padding: 10px;
            border: 1px solid #D0D0D0;
            text-align: right;
        }

        .summary-table th {
            background-color: #FFE5E5;
            font-weight: 600;
            color: #DC3545;
        }

        .summary-table td {
            background-color: #FFF5F5;
            font-weight: bold;
            color: #DC3545;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #818181;
            page-break-inside: avoid;
        }

        .footer p {
            margin: 5px 0;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Report Header -->
    <div class="report-header">
        <div>
            <h2>{{ setting('application_name') }}</h2>
            <p>{{ setting('address') }}</p>
            <p style="margin-top: 10px; font-size: 16px; font-weight: 600;">{{ $title }}</p>
        </div>
    </div>

    <!-- Applied Filters -->
    <div class="report-filters">
        <h4>Applied Filters:</h4>
        <div class="filter-row">
            <div class="filter-item">
                <strong>Date Range:</strong> <span>{{ $filters['start_date'] }} to {{ $filters['end_date'] }}</span>
            </div>
            <div class="filter-item">
                <strong>Grade:</strong> <span>{{ $filters['grade'] }}</span>
            </div>
        </div>
        <div class="filter-row">
            <div class="filter-item">
                <strong>Class:</strong> <span>{{ $filters['class'] }}</span>
            </div>
            <div class="filter-item">
                <strong>Section:</strong> <span>{{ $filters['section'] }}</span>
            </div>
        </div>
        <div class="filter-row">
            <div class="filter-item">
                <strong>Status:</strong> <span>{{ $filters['status'] }}</span>
            </div>
            <div class="filter-item">
                <strong>Shift:</strong> <span>{{ $filters['shift'] }}</span>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Student Name</th>
                <th>Mobile</th>
                <th>Grade</th>
                <th>Class</th>
                <th>Section</th>
                <th class="text-end">Outstanding Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $row)
                <tr>
                    <td>{{ $row->date ?? '-' }}</td>
                    <td>{{ $row->name ?? '-' }}</td>
                    <td>{{ $row->mobile ?? '-' }}</td>
                    <td>{{ $row->grade ?? '-' }}</td>
                    <td>{{ $row->class ?? '-' }}</td>
                    <td>{{ $row->section ?? '-' }}</td>
                    <td class="text-end">{{ number_format($row->total_amount ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No records found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <table class="summary-table">
            <tr>
                <th>Total Outstanding Amount:</th>
                <td>{{ $summary['total_outstanding'] }}</td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>{{ setting('footer_text') ?? 'School Management System' }}</p>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
        <p>Total Records: {{ $count }}</p>
    </div>
</body>
</html>
