<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['title'] ?? 'Payment Receipt' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #fff;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        .receipt-header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 30px;
        }

        .school-logo {
            max-height: 80px;
            margin-bottom: 15px;
        }

        .school-name {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .school-info {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.4;
        }

        .receipt-title {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 30px;
        }

        .receipt-title h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .receipt-number {
            font-size: 16px;
            opacity: 0.9;
        }

        .receipt-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .info-section h3 {
            color: #495057;
            font-size: 16px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #dee2e6;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #dee2e6;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
        }

        .info-value {
            color: #333;
            font-weight: 500;
        }

        .payment-summary {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .payment-amount {
            text-align: center;
            margin-bottom: 20px;
        }

        .amount-paid {
            font-size: 36px;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 5px;
        }

        .amount-label {
            color: #6c757d;
            font-size: 14px;
        }

        .payment-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .allocation-section {
            margin-bottom: 30px;
        }

        .allocation-header {
            background: #e9ecef;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            border: 1px solid #dee2e6;
            border-bottom: none;
        }

        .allocation-header h3 {
            color: #495057;
            font-size: 18px;
            margin: 0;
        }

        .allocation-list {
            border: 1px solid #dee2e6;
            border-radius: 0 0 8px 8px;
        }

        .allocation-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .allocation-item:last-child {
            border-bottom: none;
        }

        .fee-details {
            flex: 1;
        }

        .fee-name {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .fee-status {
            font-size: 12px;
            color: #6c757d;
        }

        .fee-amount {
            font-weight: bold;
            color: #28a745;
            font-size: 16px;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            margin-left: 10px;
        }

        .status-partial {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-full {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .receipt-footer {
            text-align: center;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
            color: #6c757d;
            font-size: 12px;
        }

        .footer-note {
            margin-bottom: 10px;
            font-style: italic;
        }

        .generation-info {
            font-size: 11px;
            color: #adb5bd;
        }

        /* Print-specific styles */
        @media print {
            body {
                font-size: 12px;
            }

            .receipt-container {
                margin: 0;
                padding: 15px;
                box-shadow: none;
            }

            .receipt-title {
                background: #007bff !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Responsive design */
        @media (max-width: 600px) {
            .receipt-info {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .payment-details {
                grid-template-columns: 1fr;
            }

            .allocation-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        {{-- School Header --}}
        <div class="receipt-header">
            @if(!empty($data['school_info']['logo']))
                <img src="{{ asset($data['school_info']['logo']) }}" alt="School Logo" class="school-logo">
            @endif
            <div class="school-name">{{ $data['school_info']['name'] ?? 'School Name' }}</div>
            <div class="school-info">
                @if(!empty($data['school_info']['address']))
                    {{ $data['school_info']['address'] }}<br>
                @endif
                @if(!empty($data['school_info']['phone']) || !empty($data['school_info']['email']))
                    {{ $data['school_info']['phone'] }}
                    @if(!empty($data['school_info']['phone']) && !empty($data['school_info']['email'])) | @endif
                    {{ $data['school_info']['email'] }}
                @endif
            </div>
        </div>

        {{-- Receipt Title --}}
        <div class="receipt-title">
            <h2>{{ $data['title'] ?? 'Payment Receipt' }}</h2>
            <div class="receipt-number">Receipt No: {{ $data['receipt']->receipt_number }}</div>
        </div>

        {{-- Receipt Information --}}
        <div class="receipt-info">
            {{-- Student Information --}}
            <div class="info-section">
                <h3>Student Information</h3>
                <div class="info-item">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $data['receipt']->student->first_name }} {{ $data['receipt']->student->last_name }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Admission No:</span>
                    <span class="info-value">{{ $data['receipt']->student->admission_no }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Class:</span>
                    <span class="info-value">
                        {{ $data['receipt']->student->sessionStudentDetails->class->name ?? 'N/A' }} -
                        {{ $data['receipt']->student->sessionStudentDetails->section->name ?? 'N/A' }}
                    </span>
                </div>
            </div>

            {{-- Payment Information --}}
            <div class="info-section">
                <h3>Payment Information</h3>
                <div class="info-item">
                    <span class="info-label">Payment Date:</span>
                    <span class="info-value">{{ dateFormat($data['receipt']->payment_date) }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Payment Method:</span>
                    <span class="info-value">{{ $data['receipt']->payment_method }}</span>
                </div>
                @if($data['receipt']->transaction_reference)
                    <div class="info-item">
                        <span class="info-label">Reference:</span>
                        <span class="info-value">{{ $data['receipt']->transaction_reference }}</span>
                    </div>
                @endif
                <div class="info-item">
                    <span class="info-label">Collected By:</span>
                    <span class="info-value">{{ $data['receipt']->collected_by->name ?? 'System' }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Summary --}}
        <div class="payment-summary">
            <div class="payment-amount">
                <div class="amount-paid">{{ $data['school_info']['currency'] ?? '$' }}{{ number_format($data['receipt']->amount_paid, 2) }}</div>
                <div class="amount-label">Amount Paid</div>
            </div>

            <div class="payment-details">
                <div class="info-item">
                    <span class="info-label">Payment Status:</span>
                    <span class="info-value">
                        <span class="status-badge {{ $data['receipt']->payment_status === 'partial' ? 'status-partial' : 'status-full' }}">
                            {{ $data['receipt']->payment_status === 'partial' ? 'Partial Payment' : 'Full Payment' }}
                        </span>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Receipt Type:</span>
                    <span class="info-value">{{ ucfirst(str_replace('_', ' ', $data['receipt']->type)) }}</span>
                </div>
            </div>
        </div>

        {{-- Payment Allocation --}}
        @if(count($data['receipt']->fees_affected) > 0)
            <div class="allocation-section">
                <div class="allocation-header">
                    <h3>Payment Allocation Details</h3>
                </div>
                <div class="allocation-list">
                    @foreach($data['receipt']->fees_affected as $fee)
                        <div class="allocation-item">
                            <div class="fee-details">
                                <div class="fee-name">{{ $fee['name'] ?? 'Fee Payment' }}</div>
                                <div class="fee-status">
                                    @if(isset($fee['remaining_balance']))
                                        @if($fee['remaining_balance'] > 0)
                                            Remaining Balance: {{ $data['school_info']['currency'] ?? '$' }}{{ number_format($fee['remaining_balance'], 2) }}
                                        @else
                                            Fully Paid
                                        @endif
                                    @else
                                        Payment Applied
                                    @endif
                                </div>
                            </div>
                            <div class="fee-amount">
                                {{ $data['school_info']['currency'] ?? '$' }}{{ number_format($fee['amount'] ?? 0, 2) }}
                                @if(isset($fee['is_fully_paid']))
                                    <span class="status-badge {{ $fee['is_fully_paid'] ? 'status-full' : 'status-partial' }}">
                                        {{ $fee['is_fully_paid'] ? 'Complete' : 'Partial' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Receipt Footer --}}
        <div class="receipt-footer">
            <div class="footer-note">
                This receipt represents payment received for the above-mentioned fees.
                @if($data['receipt']->payment_status === 'partial')
                    Please note that this is a partial payment and balances may remain on some fees.
                @endif
            </div>
            <div class="generation-info">
                Generated on {{ now()->format('F j, Y \a\t g:i A') }} |
                Receipt System v2.0
            </div>
        </div>
    </div>

    {{-- Print functionality --}}
    <script>
        // Auto-print when opened in print mode
        if (window.location.search.includes('print=1')) {
            window.onload = function() {
                window.print();
            };
        }
    </script>
</body>
</html>