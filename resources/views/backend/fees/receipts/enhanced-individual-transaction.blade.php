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
            max-width: 850px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .receipt-header {
            text-align: center;
            padding-bottom: 30px;
            border-bottom: 3px solid #007bff;
            margin-bottom: 30px;
            position: relative;
        }

        .receipt-number {
            position: absolute;
            top: 0;
            right: 0;
            background: #007bff;
            color: white;
            padding: 8px 15px;
            border-radius: 0 0 0 15px;
            font-weight: bold;
            font-size: 14px;
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
            text-align: center;
            padding: 20px;
            margin: -30px -30px 30px -30px;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .payment-type-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 10px;
        }

        .payment-type-badge.partial {
            background: #fd7e14;
        }

        .receipt-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .info-group {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #007bff;
        }

        .info-group h4 {
            color: #007bff;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .info-label {
            font-weight: 500;
            color: #6c757d;
            font-size: 14px;
        }

        .info-value {
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .payment-summary {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }

        .amount-paid {
            font-size: 48px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .amount-label {
            font-size: 16px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .payment-method-info {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.3);
            font-size: 14px;
        }

        .allocation-section {
            margin-bottom: 30px;
        }

        .allocation-header {
            background: linear-gradient(135deg, #6f42c1, #5a2d91);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .allocation-header h3 {
            font-size: 18px;
            margin: 0;
            font-weight: bold;
        }

        .allocation-count {
            background: rgba(255,255,255,0.2);
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }

        .allocation-list {
            border: 2px solid #6f42c1;
            border-top: none;
            border-radius: 0 0 10px 10px;
            overflow: hidden;
        }

        .allocation-item {
            display: grid;
            grid-template-columns: 1fr auto auto auto;
            align-items: center;
            padding: 20px;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }

        .allocation-item:hover {
            background: #f8f9fa;
        }

        .allocation-item:last-child {
            border-bottom: none;
        }

        .fee-details {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .fee-name {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }

        .fee-category {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .fee-progress {
            margin-top: 8px;
        }

        .progress-bar {
            width: 200px;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            border-radius: 4px;
            transition: width 0.3s ease;
        }

        .progress-fill.partial {
            background: linear-gradient(90deg, #fd7e14, #fd7e14);
        }

        .progress-text {
            font-size: 11px;
            color: #6c757d;
            margin-top: 4px;
        }

        .amount-allocated {
            text-align: center;
            padding: 10px 15px;
            background: #e3f2fd;
            border-radius: 8px;
            border: 2px solid #2196f3;
        }

        .amount-allocated .value {
            font-size: 18px;
            font-weight: bold;
            color: #1976d2;
            display: block;
        }

        .amount-allocated .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }

        .remaining-balance {
            text-align: center;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .remaining-balance.has-balance {
            background: #fff3cd;
            border: 2px solid #ffc107;
        }

        .remaining-balance.fully-paid {
            background: #d4edda;
            border: 2px solid #28a745;
        }

        .remaining-balance .value {
            font-size: 16px;
            font-weight: bold;
            display: block;
        }

        .remaining-balance.has-balance .value {
            color: #856404;
        }

        .remaining-balance.fully-paid .value {
            color: #155724;
        }

        .remaining-balance .label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
        }

        .payment-status {
            text-align: center;
        }

        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.fully-paid {
            background: #d4edda;
            color: #155724;
            border: 2px solid #28a745;
        }

        .status-badge.partial-paid {
            background: #fff3cd;
            color: #856404;
            border: 2px solid #ffc107;
        }

        .allocation-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            border: 2px solid #dee2e6;
        }

        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            text-align: center;
        }

        .summary-stat {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .summary-stat .number {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            display: block;
        }

        .summary-stat .label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .payment-sequence {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }

        .sequence-info {
            font-size: 14px;
            color: #0056b3;
            font-weight: 500;
        }

        .receipt-footer {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
        }

        .qr-code {
            margin: 20px 0;
        }

        .verification-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 12px;
            line-height: 1.4;
        }

        /* Print Styles */
        @media print {
            .receipt-container {
                box-shadow: none;
                padding: 20px;
            }

            .receipt-title {
                margin: -20px -20px 20px -20px;
            }

            .allocation-item:hover {
                background: #fff !important;
            }
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .receipt-container {
                padding: 20px;
                margin: 10px;
            }

            .receipt-info {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .allocation-item {
                grid-template-columns: 1fr;
                gap: 15px;
                text-align: left;
            }

            .amount-allocated,
            .remaining-balance,
            .payment-status {
                text-align: left;
            }

            .summary-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="receipt-number">{{ $data['receipt']->receipt_number ?? 'N/A' }}</div>

            @if(isset($data['school_info']['logo']) && $data['school_info']['logo'])
                <img src="{{ $data['school_info']['logo'] }}" alt="School Logo" class="school-logo">
            @endif

            <div class="school-name">{{ $data['school_info']['name'] ?? 'School Name' }}</div>
            <div class="school-info">
                @if(isset($data['school_info']['address']))
                    {{ $data['school_info']['address'] }}<br>
                @endif
                @if(isset($data['school_info']['phone']))
                    Phone: {{ $data['school_info']['phone'] }}
                @endif
                @if(isset($data['school_info']['email']))
                    | Email: {{ $data['school_info']['email'] }}
                @endif
            </div>
        </div>

        <div class="receipt-title">
            Payment Receipt
            <span class="payment-type-badge {{ $data['receipt']->payment_status === 'partial' ? 'partial' : '' }}">
                {{ $data['receipt']->payment_status === 'partial' ? 'Partial Payment' : 'Full Payment' }}
            </span>
        </div>

        <div class="receipt-info">
            <div class="info-group">
                <h4>Student Information</h4>
                <div class="info-item">
                    <span class="info-label">Student Name:</span>
                    <span class="info-value">{{ $data['receipt']->student->first_name ?? '' }} {{ $data['receipt']->student->last_name ?? '' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Admission No:</span>
                    <span class="info-value">{{ $data['receipt']->student->admission_no ?? 'N/A' }}</span>
                </div>
                @if(isset($data['receipt']->student->sessionStudentDetails))
                    <div class="info-item">
                        <span class="info-label">Class:</span>
                        <span class="info-value">{{ $data['receipt']->student->sessionStudentDetails->class->class_name ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Section:</span>
                        <span class="info-value">{{ $data['receipt']->student->sessionStudentDetails->section->section_name ?? 'N/A' }}</span>
                    </div>
                @endif
            </div>

            <div class="info-group">
                <h4>Payment Information</h4>
                <div class="info-item">
                    <span class="info-label">Payment Date:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($data['receipt']->payment_date)->format('M d, Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Collected By:</span>
                    <span class="info-value">{{ $data['receipt']->collected_by->name ?? 'N/A' }}</span>
                </div>
                @if(isset($data['receipt']->transaction_reference))
                    <div class="info-item">
                        <span class="info-label">Reference:</span>
                        <span class="info-value">{{ $data['receipt']->transaction_reference }}</span>
                    </div>
                @endif
                <div class="info-item">
                    <span class="info-label">Receipt Date:</span>
                    <span class="info-value">{{ now()->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="payment-summary">
            <div class="amount-paid">{{ $data['school_info']['currency'] ?? '$' }}{{ number_format($data['receipt']->amount_paid, 2) }}</div>
            <div class="amount-label">Total Amount Paid</div>
            <div class="payment-method-info">
                <strong>Payment Method:</strong> {{ $data['receipt']->payment_method ?? 'N/A' }}
                @if(isset($data['receipt']->transaction_reference))
                    <br><strong>Transaction ID:</strong> {{ $data['receipt']->transaction_reference }}
                @endif
            </div>
        </div>

        {{-- Payment Sequence Information --}}
        @if(isset($data['receipt']->allocation_summary) && $data['receipt']->has_partial_payments)
            <div class="payment-sequence">
                <div class="sequence-info">
                    💡 <strong>Payment Progress:</strong> {{ $data['receipt']->allocation_summary }}
                    @if($data['receipt']->payment_status === 'partial')
                        <br>📝 <strong>Note:</strong> This is a partial payment. Additional payments may be required to complete all fees.
                    @endif
                </div>
            </div>
        @endif

        {{-- Enhanced Payment Allocation Details --}}
        @if(count($data['receipt']->fees_affected) > 0)
            <div class="allocation-section">
                <div class="allocation-header">
                    <h3>💰 Payment Allocation Breakdown</h3>
                    <div class="allocation-count">{{ count($data['receipt']->fees_affected) }} Fee(s) Affected</div>
                </div>
                <div class="allocation-list">
                    @foreach($data['receipt']->fees_affected as $index => $fee)
                        @php
                            $feeAmount = $fee['amount'] ?? 0;
                            $remainingBalance = $fee['remaining_balance'] ?? 0;
                            $totalFeeAmount = $feeAmount + $remainingBalance;
                            $isFullyPaid = $remainingBalance <= 0;
                            $paymentProgress = $totalFeeAmount > 0 ? (($totalFeeAmount - $remainingBalance) / $totalFeeAmount) * 100 : 100;
                        @endphp

                        <div class="allocation-item">
                            <div class="fee-details">
                                <div class="fee-name">{{ $fee['name'] ?? 'Fee Payment' }}</div>
                                <div class="fee-category">Fee #{{ $index + 1 }}</div>

                                <div class="fee-progress">
                                    <div class="progress-bar">
                                        <div class="progress-fill {{ $isFullyPaid ? '' : 'partial' }}" style="width: {{ $paymentProgress }}%"></div>
                                    </div>
                                    <div class="progress-text">
                                        {{ number_format($paymentProgress, 1) }}% Complete
                                        ({{ $data['school_info']['currency'] ?? '$' }}{{ number_format($totalFeeAmount - $remainingBalance, 2) }} of {{ $data['school_info']['currency'] ?? '$' }}{{ number_format($totalFeeAmount, 2) }})
                                    </div>
                                </div>
                            </div>

                            <div class="amount-allocated">
                                <span class="value">{{ $data['school_info']['currency'] ?? '$' }}{{ number_format($feeAmount, 2) }}</span>
                                <span class="label">Allocated</span>
                            </div>

                            <div class="remaining-balance {{ $isFullyPaid ? 'fully-paid' : 'has-balance' }}">
                                <span class="value">{{ $data['school_info']['currency'] ?? '$' }}{{ number_format($remainingBalance, 2) }}</span>
                                <span class="label">{{ $isFullyPaid ? 'Paid In Full' : 'Remaining' }}</span>
                            </div>

                            <div class="payment-status">
                                <span class="status-badge {{ $isFullyPaid ? 'fully-paid' : 'partial-paid' }}">
                                    {{ $isFullyPaid ? '✅ Complete' : '⏳ Partial' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Allocation Summary Statistics --}}
                <div class="allocation-summary">
                    <div class="summary-stats">
                        <div class="summary-stat">
                            <span class="number">{{ count($data['receipt']->fees_affected) }}</span>
                            <span class="label">Fees Affected</span>
                        </div>
                        <div class="summary-stat">
                            <span class="number">{{ $data['school_info']['currency'] ?? '$' }}{{ number_format($data['receipt']->amount_paid, 2) }}</span>
                            <span class="label">Total Allocated</span>
                        </div>
                        @php
                            $totalRemaining = array_sum(array_column($data['receipt']->fees_affected, 'remaining_balance'));
                            $fullyPaidCount = count(array_filter($data['receipt']->fees_affected, fn($fee) => ($fee['remaining_balance'] ?? 0) <= 0));
                        @endphp
                        <div class="summary-stat">
                            <span class="number">{{ $data['school_info']['currency'] ?? '$' }}{{ number_format($totalRemaining, 2) }}</span>
                            <span class="label">Total Remaining</span>
                        </div>
                        <div class="summary-stat">
                            <span class="number">{{ $fullyPaidCount }}</span>
                            <span class="label">Fully Paid</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="verification-info">
            <strong>🔒 Receipt Verification:</strong> This receipt can be verified using receipt number <strong>{{ $data['receipt']->receipt_number ?? 'N/A' }}</strong>
            through the school's payment verification system. Keep this receipt for your records.

            @if(isset($data['receipt']->payment_notes) && $data['receipt']->payment_notes)
                <br><br><strong>📝 Payment Notes:</strong> {{ $data['receipt']->payment_notes }}
            @endif
        </div>

        <div class="receipt-footer">
            <p><strong>{{ $data['school_info']['name'] ?? 'School Name' }}</strong></p>
            <p>This is a computer-generated receipt and does not require a signature.</p>
            <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>

            @if(config('app.env') !== 'production')
                <p style="color: #dc3545; font-weight: bold;">⚠️ DEVELOPMENT RECEIPT - NOT FOR OFFICIAL USE</p>
            @endif
        </div>
    </div>

    <script>
        // Auto-print when accessed with print parameter
        if (new URLSearchParams(window.location.search).get('print') === '1') {
            window.onload = function() {
                setTimeout(() => {
                    window.print();
                }, 500);
            };
        }
    </script>
</body>
</html>