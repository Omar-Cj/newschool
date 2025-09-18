@extends('backend.master')

@section('title')
    {{ ___('fees.payment_receipt') }}
@endsection

@section('content')
<div class="page-content">
    <style>
        .receipt-options-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .receipt-header {
            text-align: center;
            padding-bottom: 20px;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c5aa0;
        }
        
        .school-name {
            font-size: 22px;
            font-weight: 600;
            color: #2c5aa0;
            margin-bottom: 8px;
        }
        
        .payment-success {
            font-size: 18px;
            font-weight: 600;
            color: #28a745;
            margin-top: 15px;
            padding: 8px 16px;
            background: #f8f9fa;
            border-radius: 6px;
            display: inline-block;
        }
        
        .payment-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 25px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #2c5aa0;
        }
        
        .payment-number {
            font-weight: 600;
            color: #2c5aa0;
        }
        
        .payment-date {
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
        
        .amount-display {
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
        
        .download-actions {
            margin: 30px 0;
        }
        
        .download-btn {
            display: block;
            width: 100%;
            padding: 15px 20px;
            margin: 10px 0;
            background: #2c5aa0;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            transition: background-color 0.3s;
        }
        
        .download-btn:hover {
            background: #1a4480;
            color: white;
            text-decoration: none;
        }
        
        .print-btn {
            background: #28a745;
        }
        
        .print-btn:hover {
            background: #1e7e34;
        }
        
        .back-btn {
            background: #6c757d;
            font-size: 14px;
            padding: 10px 20px;
        }
        
        .back-btn:hover {
            background: #545b62;
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
    </style>

    <div class="receipt-options-container">
        {{-- Header Section --}}
        <div class="receipt-header">
            <div class="school-name">{{ setting('application_name') }}</div>
            <div class="payment-success">{{ ___('fees.payment_completed') }} ✓</div>
        </div>
        
        {{-- Payment Information --}}
        <div class="payment-info">
            <div>
            <div class="payment-number">{{ ___('fees.receipt_no') }}: {{ $payment->receipt_number ?? ('RCT-' . date('Y') . '-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT)) }}</div>
                <div class="payment-date">{{ dateFormat($payment->date) }}</div>
            </div>
        </div>
        
        {{-- Student Information --}}
        <div class="student-info">
            <div class="info-row">
                <div class="info-label">{{ ___('student_info.student_name') }}</div>
                <div class="info-value">{{ $payment->student->first_name }} {{ $payment->student->last_name }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">{{ ___('student_info.admission_no') }}</div>
                <div class="info-value">{{ $payment->student->admission_no }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">{{ ___('academic.class') }}</div>
                <div class="info-value">{{ $payment->student->sessionStudentDetails->class->name ?? 'N/A' }} - {{ $payment->student->sessionStudentDetails->section->name ?? 'N/A' }}</div>
            </div>
        </div>
        
        {{-- Payment Amount --}}
        <div class="amount-display">
            <div class="amount-paid">{{ Setting('currency_symbol') }} {{ number_format($payment->grand_total ?? (($payment->total_amount ?? $payment->amount) + ($payment->total_fine ?? $payment->fine_amount ?? 0)), 2) }}</div>
            <div>{{ ___('fees.amount_paid') }}</div>
        </div>
        
        {{-- Download Actions --}}
        <div class="download-actions">
            <a href="{{ route('fees.receipt.individual', $payment->id) }}" 
               class="download-btn" target="_blank">
                📄 {{ ___('fees.download_receipt') }}
            </a>

            <button type="button" class="download-btn print-btn" 
                    onclick="printReceipt({{ $payment->id }})">
                🖨️ {{ ___('fees.print_receipt') }}
            </button>
            
            <a href="{{ route('fees-collect.index') }}" class="download-btn back-btn">
                ← {{ ___('fees.back_to_collection') }}
            </a>
        </div>
        
        {{-- Footer --}}
        <div class="footer">
            <div>{{ ___('common.thank_you') }}</div>
            <div class="generated-info">
                {{ ___('fees.generated_on') }}: {{ date('d M Y, h:i A') }}
            </div>
        </div>
    </div>
</div>

@endsection

@push('script')
    @include('backend.fees.receipts.partials.actions-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.receipt-options-container');
            if (!container) {
                return;
            }

            container.style.opacity = '0';
            container.style.transform = 'translateY(20px)';
            container.style.transition = 'all 0.5s ease';

            setTimeout(() => {
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
@endpush
