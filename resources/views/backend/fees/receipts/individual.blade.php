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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #333;
            background: #fff;
        }
        
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
        }
        
        .header {
            text-align: center;
            padding-bottom: 20px;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c5aa0;
        }
        
        .school-logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .school-name {
            font-size: 22px;
            font-weight: 600;
            color: #2c5aa0;
            margin-bottom: 8px;
        }
        
        .receipt-title {
            font-size: 18px;
            font-weight: 600;
            color: #28a745;
            margin-top: 15px;
            padding: 8px 16px;
            background: #f8f9fa;
            border-radius: 6px;
            display: inline-block;
        }
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 25px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #2c5aa0;
        }
        
        .receipt-number {
            font-weight: 600;
            color: #2c5aa0;
        }
        
        .receipt-date {
            font-size: 13px;
            color: #6c757d;
        }
        
        .student-info {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 4px 0;
        }
        
        .info-label {
            font-weight: 500;
            color: #495057;
            min-width: 120px;
        }
        
        .info-value {
            color: #212529;
            font-weight: 400;
        }
        
        .payment-summary {
            background: #e8f4fd;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        
        .amount-paid {
            font-size: 28px;
            font-weight: 700;
            color: #2c5aa0;
            margin-bottom: 8px;
        }
        
        .payment-method {
            font-size: 13px;
            color: #6c757d;
            margin-top: 10px;
        }
        
        .fee-details {
            margin: 25px 0;
        }
        
        .fee-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .fee-item:last-child {
            border-bottom: none;
            padding-top: 12px;
            border-top: 2px solid #2c5aa0;
            font-weight: 600;
            color: #2c5aa0;
        }
        
        .fee-name {
            font-weight: 500;
        }
        
        .fee-amount {
            font-weight: 600;
            color: #495057;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            text-align: center;
        }
        
        .generated-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 15px;
        }
        
        @media print {
            @page {
                size: A4;
                margin: 15mm;
            }
            
            body {
                font-size: 12px;
                line-height: 1.4;
                color: #000;
                background: white !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .receipt-container {
                margin: 0;
                padding: 0;
                max-width: none;
                border: none;
                box-shadow: none;
            }
            
            .header {
                border-bottom: 2px solid #2c5aa0 !important;
                margin-bottom: 20px;
            }
            
            .school-name {
                color: #2c5aa0 !important;
            }
            
            .receipt-title {
                color: #28a745 !important;
                background: #f8f9fa !important;
            }
            
            .receipt-info {
                background: #f8f9fa !important;
                border-left: 4px solid #2c5aa0 !important;
            }
            
            .receipt-number {
                color: #2c5aa0 !important;
            }
            
            .payment-summary {
                background: #e8f4fd !important;
                border: 1px solid #2c5aa0 !important;
            }
            
            .amount-paid {
                font-size: 22px;
                color: #2c5aa0 !important;
            }
            
            .fee-item:last-child {
                border-top: 2px solid #2c5aa0 !important;
                color: #2c5aa0 !important;
            }
            
            .footer {
                border-top: 1px solid #dee2e6 !important;
            }
            
            /* Hide any elements that shouldn't print */
            .no-print {
                display: none !important;
            }
            
            /* Ensure proper page breaks */
            .receipt-container {
                page-break-inside: avoid;
            }
            
            /* Optimize text for printing */
            .info-label {
                font-weight: 600 !important;
            }
            
            .fee-name {
                font-weight: 500 !important;
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
                    <img src="{{ globalAsset($data['school_info']['logo']) }}" alt="School Logo" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            @endif
            
            <div class="school-name">{{ $data['school_info']['name'] }}</div>
            <div class="receipt-title">{{ ___('fees.payment_receipt') }}</div>
        </div>
        
        {{-- Receipt Information --}}
        <div class="receipt-info">
            <div>
                <div class="receipt-number">{{ ___('fees.receipt_no') }}: {{ $data['receipt_number'] }}</div>
                <div class="receipt-date">{{ dateFormat($data['payment']->date) }}</div>
            </div>
            <div>
                <div class="receipt-date">{{ ___('fees.collected_by') }}: {{ $data['payment']->collectBy->name ?? 'System' }}</div>
            </div>
        </div>
        
        {{-- Student Information --}}
        <div class="student-info">
            <div class="info-row">
                <div class="info-label">{{ ___('student_info.student_name') }}</div>
                <div class="info-value">{{ $data['payment']->student->first_name }} {{ $data['payment']->student->last_name }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">{{ ___('student_info.admission_no') }}</div>
                <div class="info-value">{{ $data['payment']->student->admission_no }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">{{ ___('academic.class') }}</div>
                <div class="info-value">{{ $data['payment']->student->sessionStudentDetails->class->name ?? 'N/A' }} - {{ $data['payment']->student->sessionStudentDetails->section->name ?? 'N/A' }}</div>
            </div>
        </div>
        
        {{-- Payment Summary --}}
        <div class="payment-summary">
            <div class="amount-paid">{{ $data['school_info']['currency'] }} {{ number_format($data['total_amount'] + $data['total_fine'], 2) }}</div>
            <div>{{ ___('fees.amount_paid') }}</div>
            <div class="payment-method">
                {{ ___('fees.payment_method') }}: {{ ___(\Config::get('site.payment_methods')[$data['payment']->payment_method] ?? 'Cash') }}
            </div>
        </div>
        
        {{-- Fee Details --}}
        <div class="fee-details">
            @foreach($data['all_payments'] as $payment)
                <div class="fee-item">
                    <div class="fee-name">{{ $payment->feesAssignChildren->feesMaster->type->name ?? 'Fee Payment' }}</div>
                    <div class="fee-amount">{{ $data['school_info']['currency'] }} {{ number_format($payment->amount, 2) }}</div>
                </div>
                
                @if(($payment->fine_amount ?? 0) > 0)
                <div class="fee-item">
                    <div class="fee-name">{{ $payment->feesAssignChildren->feesMaster->type->name ?? 'Fee' }} - {{ ___('fees.late_fee') }}</div>
                    <div class="fee-amount">{{ $data['school_info']['currency'] }} {{ number_format($payment->fine_amount, 2) }}</div>
                </div>
                @endif
            @endforeach
            
            <div class="fee-item">
                <div class="fee-name">{{ ___('fees.total_paid') }}</div>
                <div class="fee-amount">{{ $data['school_info']['currency'] }} {{ number_format($data['total_amount'] + $data['total_fine'], 2) }}</div>
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="footer">
            <div>{{ ___('common.thank_you') }}</div>
            <div class="generated-info">
                {{ ___('fees.generated_on') }}: {{ date('d M Y, h:i A') }}
            </div>
        </div>
    </div>
    
    <!-- Print Functionality -->
    <script>
        let printTriggered = false;
        
        // Enhanced auto-trigger print dialog with better timing
        function triggerPrint() {
            if (printTriggered) return; // Prevent multiple triggers
            printTriggered = true;
            
            console.log('Triggering print dialog...');
            
            try {
                // Multiple fallback attempts for better reliability
                setTimeout(() => {
                    if (document.readyState === 'complete') {
                        window.print();
                    } else {
                        // Wait a bit more if document not fully loaded
                        setTimeout(() => window.print(), 200);
                    }
                }, 300);
            } catch (error) {
                console.error('Error triggering print:', error);
                // Manual print button will still be available as fallback
            }
        }
        
        // Enhanced load handling with multiple triggers
        window.addEventListener('load', function() {
            console.log('Window loaded, checking for print parameter...');
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.get('print') === '1') {
                console.log('Print parameter detected, preparing to trigger print...');
                
                // Ensure all resources are loaded before printing
                if (document.readyState === 'complete') {
                    triggerPrint();
                } else {
                    // Wait for complete document ready
                    document.addEventListener('readystatechange', function() {
                        if (document.readyState === 'complete') {
                            triggerPrint();
                        }
                    });
                }
                
                // Backup trigger after longer delay
                setTimeout(() => {
                    if (!printTriggered) {
                        console.log('Backup print trigger activated');
                        triggerPrint();
                    }
                }, 1500);
            }
        });
        
        // Also trigger on DOMContentLoaded as additional fallback
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('print') === '1' && !printTriggered) {
                console.log('DOMContentLoaded print trigger');
                setTimeout(triggerPrint, 800);
            }
        });
        
        // Enhanced print functionality
        function printReceipt() {
            console.log('Manual print triggered');
            try {
                window.print();
            } catch (error) {
                console.error('Manual print failed:', error);
                alert('Print function is not available in this browser.');
            }
        }
        
        // Enhanced print button creation
        document.addEventListener('DOMContentLoaded', function() {
            // Only add print button for screen view (not print mode)
            if (!window.matchMedia('print').matches) {
                const printButton = document.createElement('button');
                printButton.innerHTML = '🖨️ Print Receipt';
                printButton.className = 'print-btn no-print';
                printButton.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: #2c5aa0;
                    color: white;
                    border: none;
                    padding: 12px 20px;
                    border-radius: 6px;
                    font-weight: 600;
                    cursor: pointer;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    z-index: 1000;
                    transition: background-color 0.3s;
                `;
                printButton.onmouseover = () => printButton.style.background = '#1a4480';
                printButton.onmouseout = () => printButton.style.background = '#2c5aa0';
                printButton.onclick = printReceipt;
                document.body.appendChild(printButton);
                console.log('Print button added to page');
            }
        });
        
        // Enhanced print event handling
        window.addEventListener('beforeprint', function() {
            console.log('Print preview opening...');
            // Ensure proper styling is applied
            document.body.classList.add('printing');
        });
        
        window.addEventListener('afterprint', function() {
            console.log('Print dialog closed');
            document.body.classList.remove('printing');
            
            // Optional: Focus back to main window if it came from print window
            if (window.opener && !window.opener.closed) {
                try {
                    window.opener.focus();
                } catch (e) {
                    console.log('Cannot focus opener window');
                }
            }
        });
        
        // Enhanced keyboard shortcut for printing
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                printReceipt();
            }
        });
        
        // Debug information
        console.log('Print functionality initialized');
        console.log('Current URL:', window.location.href);
        console.log('Document ready state:', document.readyState);
    </script>
</body>
</html>
