<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['title'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        
        .receipt-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #2c5aa0;
            background: #fff;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2c5aa0;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .school-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 10px;
        }
        
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c5aa0;
            margin-bottom: 5px;
        }
        
        .school-details {
            font-size: 12px;
            color: #666;
            margin-bottom: 15px;
        }
        
        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            color: #d32f2f;
            background: #f8f9fa;
            padding: 10px;
            border: 2px solid #d32f2f;
            margin-top: 15px;
        }
        
        .batch-info {
            background: #e3f2fd;
            border: 1px solid #2c5aa0;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .batch-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            margin-top: 10px;
        }
        
        .batch-stat {
            text-align: center;
            background: white;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .batch-stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #2c5aa0;
        }
        
        .batch-stat-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }
        
        .summary-section {
            margin: 30px 0;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 4px;
        }
        
        .summary-card h4 {
            color: #2c5aa0;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 3px 0;
            border-bottom: 1px dotted #ccc;
        }
        
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 11px;
        }
        
        .payment-table th {
            background: #2c5aa0;
            color: white;
            padding: 8px 5px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #1a4480;
        }
        
        .payment-table td {
            padding: 8px 5px;
            border-bottom: 1px solid #ddd;
            background: #fff;
        }
        
        .payment-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .amount-cell {
            text-align: right;
            font-weight: bold;
        }
        
        .total-row {
            background: #e3f2fd !important;
            border-top: 2px solid #2c5aa0;
        }
        
        .total-row td {
            font-weight: bold;
            font-size: 12px;
            color: #2c5aa0;
        }
        
        .grand-total {
            background: #2c5aa0;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            border-top: 2px solid #2c5aa0;
            padding-top: 20px;
            text-align: center;
        }
        
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
        }
        
        .signature-box {
            text-align: center;
            width: 150px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 40px;
            padding-top: 5px;
            font-size: 11px;
            color: #666;
        }
        
        @media print {
            .receipt-container {
                border: none;
                box-shadow: none;
            }
            
            body {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        {{-- Header Section --}}
        <div class="header">
            @if($data['school_info']['logo'])
                <div class="school-logo">
                    <img src="{{ globalAsset($data['school_info']['logo']) }}" alt="School Logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
            @endif
            
            <div class="school-name">{{ $data['school_info']['name'] }}</div>
            
            <div class="school-details">
                @if($data['school_info']['address'])
                    {{ $data['school_info']['address'] }}<br>
                @endif
                @if($data['school_info']['phone'])
                    Phone: {{ $data['school_info']['phone'] }}
                @endif
                @if($data['school_info']['email'])
                    | Email: {{ $data['school_info']['email'] }}
                @endif
            </div>
            
            <div class="receipt-title">{{ ___('fees.group_payment_receipt') }}</div>
        </div>
        
        {{-- Batch Information --}}
        <div class="batch-info">
            <h3 style="color: #2c5aa0; margin-bottom: 10px;">{{ ___('fees.batch_information') }}</h3>
            <div><strong>{{ ___('fees.batch_id') }}:</strong> {{ $data['batch_info']['batch_id'] }}</div>
            <div><strong>{{ ___('fees.generated_on') }}:</strong> {{ date('d M Y, h:i A') }}</div>
            
            <div class="batch-info-grid">
                <div class="batch-stat">
                    <div class="batch-stat-value">{{ $data['batch_info']['payment_count'] }}</div>
                    <div class="batch-stat-label">{{ ___('fees.total_payments') }}</div>
                </div>
                <div class="batch-stat">
                    <div class="batch-stat-value">{{ $data['batch_info']['student_count'] }}</div>
                    <div class="batch-stat-label">{{ ___('fees.students_paid') }}</div>
                </div>
                <div class="batch-stat">
                    <div class="batch-stat-value">{{ $data['school_info']['currency'] }} {{ number_format($data['totals']['grand_total'], 2) }}</div>
                    <div class="batch-stat-label">{{ ___('fees.total_collected') }}</div>
                </div>
            </div>
        </div>
        
        {{-- Summary Section --}}
        <div class="summary-section">
            <div class="summary-grid">
                {{-- Fee Type Summary --}}
                <div class="summary-card">
                    <h4>{{ ___('fees.summary_by_fee_type') }}</h4>
                    @foreach($data['summary_by_type'] as $feeType => $summary)
                        <div class="summary-item">
                            <span>{{ $feeType }} ({{ $summary['count'] }})</span>
                            <span>{{ $data['school_info']['currency'] }} {{ number_format($summary['total'], 2) }}</span>
                        </div>
                    @endforeach
                </div>
                
                {{-- Collection Summary --}}
                <div class="summary-card">
                    <h4>{{ ___('fees.collection_summary') }}</h4>
                    <div class="summary-item">
                        <span>{{ ___('fees.total_amount') }}</span>
                        <span>{{ $data['school_info']['currency'] }} {{ number_format($data['totals']['total_amount'], 2) }}</span>
                    </div>
                    <div class="summary-item">
                        <span>{{ ___('fees.total_fine') }}</span>
                        <span>{{ $data['school_info']['currency'] }} {{ number_format($data['totals']['total_fine'], 2) }}</span>
                    </div>
                    <div class="summary-item" style="border-top: 2px solid #2c5aa0; font-weight: bold; color: #2c5aa0;">
                        <span>{{ ___('fees.grand_total') }}</span>
                        <span>{{ $data['school_info']['currency'] }} {{ number_format($data['totals']['grand_total'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Detailed Payment List --}}
        <div class="payment-details">
            <h3 style="color: #2c5aa0; margin-bottom: 15px;">{{ ___('fees.detailed_payment_list') }}</h3>
            
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ ___('student_info.student_name') }}</th>
                        <th>{{ ___('student_info.admission_no') }}</th>
                        <th>{{ ___('academic.class') }}</th>
                        <th>{{ ___('fees.fee_type') }}</th>
                        <th>{{ ___('fees.payment_date') }}</th>
                        <th>{{ ___('fees.amount') }}</th>
                        <th>{{ ___('fees.fine') }}</th>
                        <th>{{ ___('fees.total') }}</th>
                        <th>{{ ___('fees.method') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['payments'] as $index => $payment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $payment->student->first_name }} {{ $payment->student->last_name }}</td>
                            <td>{{ $payment->student->admission_no }}</td>
                            <td>{{ $payment->student->currentClass->class->name ?? 'N/A' }}</td>
                            <td>{{ $payment->feesAssignChildren->feesMaster->type->name ?? 'N/A' }}</td>
                            <td>{{ dateFormat($payment->date) }}</td>
                            <td class="amount-cell">{{ number_format($payment->amount - $payment->fine_amount, 2) }}</td>
                            <td class="amount-cell">{{ number_format($payment->fine_amount, 2) }}</td>
                            <td class="amount-cell">{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ ___(\Config::get('site.payment_methods')[$payment->payment_method] ?? 'Unknown') }}</td>
                        </tr>
                    @endforeach
                    
                    {{-- Total Row --}}
                    <tr class="grand-total">
                        <td colspan="6">{{ ___('fees.grand_total') }}</td>
                        <td class="amount-cell">{{ number_format($data['totals']['total_amount'], 2) }}</td>
                        <td class="amount-cell">{{ number_format($data['totals']['total_fine'], 2) }}</td>
                        <td class="amount-cell">{{ number_format($data['totals']['grand_total'], 2) }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        {{-- Amount in Words --}}
        <div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;">
            <strong>{{ ___('fees.total_amount_in_words') }}:</strong> 
            <em>{{ ucfirst(numberToWords($data['totals']['grand_total'])) }} {{ ___('fees.only') }}</em>
        </div>
        
        {{-- Important Notes --}}
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 10px 15px; margin: 20px 0; font-size: 11px; color: #856404;">
            <strong>{{ ___('common.notes') }}:</strong>
            <ul style="margin: 5px 0 0 20px;">
                <li>{{ ___('fees.group_receipt_note_1') }}</li>
                <li>{{ ___('fees.group_receipt_note_2') }}</li>
                <li>{{ ___('fees.group_receipt_note_3') }}</li>
            </ul>
        </div>
        
        {{-- Footer with Signatures --}}
        <div class="footer">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">{{ ___('fees.prepared_by') }}</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">{{ ___('fees.verified_by') }}</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">{{ ___('fees.authorized_by') }}</div>
                </div>
            </div>
            
            <div style="margin-top: 30px; font-size: 11px; color: #666;">
                {{ ___('fees.generated_by_system') }} | {{ date('d M Y, h:i A') }}<br>
                <strong>{{ ___('fees.verification_batch_code') }}:</strong> {{ strtoupper(md5($data['batch_info']['batch_id'] . date('Y-m-d'))) }}
            </div>
        </div>
    </div>
</body>
</html>
