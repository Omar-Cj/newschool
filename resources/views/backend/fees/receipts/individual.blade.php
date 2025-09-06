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
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        
        .receipt-container {
            max-width: 800px;
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
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #2c5aa0;
        }
        
        .receipt-number {
            font-size: 16px;
            font-weight: bold;
            color: #2c5aa0;
        }
        
        .receipt-date {
            font-size: 14px;
            color: #666;
        }
        
        .student-info {
            background: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            margin: 20px 0;
        }
        
        .student-info h3 {
            color: #2c5aa0;
            font-size: 16px;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #555;
            width: 30%;
        }
        
        .info-value {
            color: #333;
            width: 65%;
        }
        
        .payment-details {
            margin: 30px 0;
        }
        
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .payment-table th {
            background: #2c5aa0;
            color: white;
            padding: 12px 8px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #1a4480;
        }
        
        .payment-table td {
            padding: 12px 8px;
            border-bottom: 1px solid #ddd;
            background: #fff;
        }
        
        .payment-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .amount-cell {
            text-align: right;
            font-weight: bold;
            color: #2c5aa0;
        }
        
        .total-row {
            background: #e3f2fd !important;
            border-top: 2px solid #2c5aa0;
        }
        
        .total-row td {
            font-weight: bold;
            font-size: 16px;
            color: #2c5aa0;
        }
        
        .payment-method {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .payment-method-label {
            font-weight: bold;
            color: #856404;
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
            width: 200px;
        }
        
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 12px;
            color: #666;
        }
        
        .verification-code {
            background: #f0f0f0;
            padding: 10px;
            margin: 15px 0;
            text-align: center;
            font-family: monospace;
            border: 1px dashed #999;
        }
        
        .important-note {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px 15px;
            margin: 20px 0;
            font-size: 12px;
            color: #856404;
        }
        
        @media print {
            .receipt-container {
                border: none;
                box-shadow: none;
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
            
            <div class="receipt-title">{{ ___('fees.payment_receipt') }}</div>
        </div>
        
        {{-- Receipt Information --}}
        <div class="receipt-info">
            <div>
                <div class="receipt-number">{{ ___('fees.receipt_no') }}: {{ $data['receipt_number'] }}</div>
                <div class="receipt-date">{{ ___('fees.payment_date') }}: {{ dateFormat($data['payment']->date) }}</div>
            </div>
            <div>
                <div class="receipt-date">{{ ___('fees.generated_on') }}: {{ date('d M Y, h:i A') }}</div>
                <div class="receipt-date">{{ ___('fees.collected_by') }}: {{ $data['payment']->collectBy->name ?? 'N/A' }}</div>
            </div>
        </div>
        
        {{-- Student Information --}}
        <div class="student-info">
            <h3>{{ ___('student_info.student_information') }}</h3>
            
            <div class="info-row">
                <div class="info-label">{{ ___('student_info.student_name') }}:</div>
                <div class="info-value">{{ $data['payment']->student->first_name }} {{ $data['payment']->student->last_name }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">{{ ___('student_info.admission_no') }}:</div>
                <div class="info-value">{{ $data['payment']->student->admission_no }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">{{ ___('academic.class') }}:</div>
                <div class="info-value">{{ $data['payment']->student->currentClass->class->name ?? 'N/A' }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">{{ ___('academic.section') }}:</div>
                <div class="info-value">{{ $data['payment']->student->currentClass->section->name ?? 'N/A' }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">{{ ___('academic.session') }}:</div>
                <div class="info-value">{{ $data['payment']->student->currentClass->session->name ?? 'N/A' }}</div>
            </div>
        </div>
        
        {{-- Payment Details --}}
        <div class="payment-details">
            <h3 style="color: #2c5aa0; margin-bottom: 15px;">{{ ___('fees.payment_details') }}</h3>
            
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>{{ ___('fees.fee_type') }}</th>
                        <th>{{ ___('fees.fee_group') }}</th>
                        <th>{{ ___('fees.due_date') }}</th>
                        <th>{{ ___('fees.amount') }} ({{ $data['school_info']['currency'] }})</th>
                        <th>{{ ___('fees.fine') }} ({{ $data['school_info']['currency'] }})</th>
                        <th>{{ ___('fees.total') }} ({{ $data['school_info']['currency'] }})</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $data['payment']->feesAssignChildren->feesMaster->type->name ?? 'N/A' }}</td>
                        <td>{{ $data['payment']->feesAssignChildren->feesMaster->group->name ?? 'N/A' }}</td>
                        <td>{{ dateFormat($data['payment']->feesAssignChildren->feesMaster->date ?? '') }}</td>
                        <td class="amount-cell">{{ number_format($data['payment']->amount - $data['payment']->fine_amount, 2) }}</td>
                        <td class="amount-cell">{{ number_format($data['payment']->fine_amount, 2) }}</td>
                        <td class="amount-cell">{{ number_format($data['payment']->amount, 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="5">{{ ___('fees.total_paid') }}</td>
                        <td class="amount-cell">{{ $data['school_info']['currency'] }} {{ number_format($data['payment']->amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        {{-- Payment Method --}}
        <div class="payment-method">
            <span class="payment-method-label">{{ ___('fees.payment_method') }}:</span>
            {{ ___(\Config::get('site.payment_methods')[$data['payment']->payment_method] ?? 'Unknown') }}
            
            @if($data['payment']->transaction_id)
                <br><strong>{{ ___('fees.transaction_id') }}:</strong> {{ $data['payment']->transaction_id }}
            @endif
        </div>
        
        {{-- Amount in Words --}}
        <div style="background: #f8f9fa; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;">
            <strong>{{ ___('fees.amount_in_words') }}:</strong> 
            <em>{{ ucfirst(numberToWords($data['payment']->amount)) }} {{ ___('fees.only') }}</em>
        </div>
        
        {{-- Verification Code --}}
        <div class="verification-code">
            <strong>{{ ___('fees.verification_code') }}:</strong> {{ strtoupper(md5($data['payment']->id . $data['payment']->date)) }}
        </div>
        
        {{-- Important Note --}}
        <div class="important-note">
            <strong>{{ ___('common.note') }}:</strong> {{ ___('fees.receipt_note') }}
        </div>
        
        {{-- Footer with Signatures --}}
        <div class="footer">
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line">{{ ___('fees.student_signature') }}</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">{{ ___('fees.collector_signature') }}</div>
                </div>
                <div class="signature-box">
                    <div class="signature-line">{{ ___('fees.authorized_signature') }}</div>
                </div>
            </div>
            
            <div style="margin-top: 30px; font-size: 12px; color: #666;">
                {{ ___('fees.generated_by_system') }} | {{ date('d M Y, h:i A') }}
            </div>
        </div>
    </div>
</body>
</html>
