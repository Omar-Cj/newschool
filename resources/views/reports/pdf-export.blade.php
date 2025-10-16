<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $report->name }} - Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .meta-info {
            margin-bottom: 15px;
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        table td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 10px;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .summary-box {
            background-color: #f5f5f5;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .summary-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }
        .summary-label {
            font-weight: bold;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $report->name }}</h1>
        @if($report->description)
            <p>{{ $report->description }}</p>
        @endif
    </div>

    <div class="meta-info">
        <strong>Generated:</strong> {{ $generated_at }}<br>
        <strong>Report Type:</strong> {{ ucfirst($report->report_type) }}<br>
        @if(isset($result['meta']['total_records']))
            <strong>Total Records:</strong> {{ $result['meta']['total_records'] }}<br>
        @endif
        @if(isset($result['meta']['execution_time_ms']))
            <strong>Execution Time:</strong> {{ $result['meta']['execution_time_ms'] }}ms
        @endif
    </div>

    @if($report->report_type === 'summary' && isset($result['data']))
        <div class="summary-box">
            <h3 style="margin-top: 0;">Summary</h3>
            @foreach($result['data'] as $item)
                <div class="summary-item">
                    <span class="summary-label">{{ $item['metric'] ?? 'N/A' }}:</span>
                    <span>{{ $item['formatted'] ?? $item['value'] ?? 'N/A' }}</span>
                </div>
            @endforeach
        </div>
    @endif

    @if($report->report_type === 'tabular' && isset($result['data']['rows']))
        <table>
            <thead>
                <tr>
                    @foreach($result['data']['columns'] as $column)
                        <th>{{ $column['label'] ?? $column['field'] ?? 'N/A' }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($result['data']['rows'] as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell ?? '-' }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($result['data']['columns']) }}" style="text-align: center; color: #999;">
                            No data available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if($report->report_type === 'custom' && isset($result['data']))
        <table>
            <thead>
                <tr>
                    @php
                        $firstRow = is_array($result['data']) && !empty($result['data']) ? reset($result['data']) : [];
                        $headers = is_array($firstRow) ? array_keys($firstRow) : [];
                    @endphp
                    @foreach($headers as $header)
                        <th>{{ ucwords(str_replace('_', ' ', $header)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($result['data'] as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td>{{ $cell ?? '-' }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" style="text-align: center; color: #999;">
                            No data available
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    @endif

    {{-- Include Summary Tables Partial --}}
    @if(isset($result['data']['summary']))
        @include('reports.partials.summary-tables', ['summary' => $result['data']['summary']])
    @endif

    <div class="footer">
        <p>This report was generated automatically by the School Management System</p>
        <p>&copy; {{ date('Y') }} - Confidential</p>
    </div>
</body>
</html>
