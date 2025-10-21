<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['title'] ?? 'Payment Receipt' }} - {{ $data['receipt']->receipt_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 30px;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }

        .receipt-header h1 {
            font-size: 28px;
            margin-bottom: 10px;
            text-transform: uppercase;
            color: #2c3e50;
        }

        .school-info {
            margin-bottom: 15px;
        }

        .school-info h2 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .school-info p {
            margin: 3px 0;
            font-size: 13px;
        }

        .receipt-number {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin: 20px 0;
            border: 1px dashed #333;
        }

        .receipt-number strong {
            font-size: 18px;
            color: #2c3e50;
        }

        .section {
            margin: 25px 0;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
            color: #2c3e50;
        }

        .info-grid {
            display: table;
            width: 100%;
            margin: 10px 0;
        }

        .info-row {
            display: table-row;
        }

        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 8px;
            width: 30%;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }

        .info-value {
            display: table-cell;
            padding: 8px;
            width: 70%;
            border: 1px solid #ddd;
        }

        .fees-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .fees-table th,
        .fees-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .fees-table th {
            background: #2c3e50;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
        }

        .fees-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .fees-table tfoot td {
            font-weight: bold;
            background: #e9ecef;
            padding: 15px 12px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totals-summary {
            background: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border: 2px solid #2c3e50;
            border-radius: 5px;
        }

        .totals-summary .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .totals-summary .total-row:last-child {
            border-bottom: none;
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            padding-top: 15px;
            border-top: 2px solid #2c3e50;
            margin-top: 10px;
        }

        .payment-details {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .payment-details p {
            margin: 5px 0;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px dashed #333;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .signature-box {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            border-top: 2px solid #333;
            margin-top: 60px;
            padding-top: 10px;
            font-weight: bold;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 10px;
        }

        .badge-family {
            background: #17a2b8;
            color: white;
        }

        @media print {
            body {
                padding: 0;
            }

            .receipt-container {
                border: none;
                max-width: 100%;
            }

            @page {
                margin: 20mm;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        {{-- Receipt Header with School Info --}}
        <div class="receipt-header">
            <div class="school-info">
                @if(!empty($data['school_info']['logo']))
                    <img src="{{ asset($data['school_info']['logo']) }}" alt="School Logo" style="height: 60px; margin-bottom: 10px;">
                @endif
                <h2>{{ $data['school_info']['name'] ?? 'School Name' }}</h2>
                <p>{{ $data['school_info']['address'] ?? '' }}</p>
                <p>
                    @if(!empty($data['school_info']['phone']))
                        {{ ___('common.phone') }}: {{ $data['school_info']['phone'] }}
                    @endif
                    @if(!empty($data['school_info']['email']))
                        | {{ ___('common.email') }}: {{ $data['school_info']['email'] }}
                    @endif
                </p>
            </div>
            <h1>{{ ___('fees.payment_receipt') ?? 'Payment Receipt' }}</h1>
        </div>

        {{-- Receipt Number --}}
        <div class="receipt-number">
            <strong>{{ ___('fees.receipt_no') ?? 'Receipt No' }}:</strong> {{ $data['receipt']->receipt_number }}
            @if($data['receipt']->isPartOfFamilyPayment())
                <span class="badge badge-family">
                    <i class="fas fa-users"></i> {{ ___('fees.family_payment') ?? 'Family Payment' }} ({{ $data['receipt']->getFamilyReceiptCount() }})
                </span>
            @endif
        </div>

        {{-- Student Information --}}
        <div class="section">
            <div class="section-title">{{ ___('student_info.student_information') ?? 'Student Information' }}</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">{{ ___('student_info.student_name') ?? 'Student Name' }}</div>
                    <div class="info-value">{{ $data['receipt']->student_name }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">{{ ___('academic.class') ?? 'Class' }} / {{ ___('academic.section') ?? 'Section' }}</div>
                    <div class="info-value">{{ $data['receipt']->class }} @if($data['receipt']->section) - {{ $data['receipt']->section }}@endif</div>
                </div>
                @if($data['receipt']->guardian_name)
                <div class="info-row">
                    <div class="info-label">{{ ___('student_info.guardian') ?? 'Guardian' }}</div>
                    <div class="info-value">{{ $data['receipt']->guardian_name }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Payment Details --}}
        <div class="section">
            <div class="section-title">{{ ___('fees.payment_details') ?? 'Payment Details' }}</div>
            <div class="payment-details">
                <p><strong>{{ ___('fees.payment_date') ?? 'Payment Date' }}:</strong> {{ dateFormat($data['receipt']->payment_date) }}</p>
                <p><strong>{{ ___('fees.payment_method') ?? 'Payment Method' }}:</strong> {{ $data['receipt']->getPaymentMethodName() }}</p>
                @if($data['receipt']->collector)
                <p><strong>{{ ___('fees.collected_by') ?? 'Collected By' }}:</strong> {{ $data['receipt']->collector->name }}</p>
                @endif
            </div>
        </div>

        {{-- Payment Summary --}}
        <div class="totals-summary">
            <div class="total-row">
                <span>{{ ___('fees.total_amount') ?? 'Total Amount' }}:</span>
                <span>{{ $data['receipt']->getFormattedAmount() }}</span>
            </div>
            <div class="total-row">
                <span>{{ ___('fees.discount') ?? 'Discount' }}:</span>
                <span>{{ $data['receipt']->getFormattedDiscount() }}</span>
            </div>
        </div>

        {{-- Payment Status --}}
        @if($data['receipt']->payment_status)
        <div class="section">
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-label">{{ ___('common.status') ?? 'Status' }}</div>
                    <div class="info-value">
                        @if($data['receipt']->payment_status === 'partial')
                            <span style="color: #ffc107; font-weight: bold;">{{ ___('fees.partial_payment') ?? 'Partial Payment' }}</span>
                        @else
                            <span style="color: #28a745; font-weight: bold;">{{ ___('fees.paid') ?? 'Paid' }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Signature Section --}}
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    {{ ___('fees.collector_signature') ?? 'Collector Signature' }}
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    {{ ___('fees.parent_signature') ?? 'Parent/Guardian Signature' }}
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>{{ ___('fees.receipt_footer_text') ?? 'This is a computer-generated receipt and does not require a signature.' }}</p>
            <p>{{ ___('fees.printed_on') ?? 'Printed on' }}: {{ date('F d, Y \a\t h:i A') }}</p>
            @if($data['receipt']->created_at)
            <p><small>{{ ___('fees.generated_on') ?? 'Generated on' }}: {{ $data['receipt']->created_at->format('F d, Y \a\t h:i A') }}</small></p>
            @endif
        </div>
    </div>

    {{-- Auto-print for browser --}}
    @if(request()->has('print') && request()->get('print') == '1')
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
    @endif
</body>
</html>
