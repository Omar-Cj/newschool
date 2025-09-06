@extends('backend.master')

@section('title')
    {{ ___('fees.receipt_options') }}
@endsection

@section('content')
<div class="page-content">
    {{-- breadcrumb Area S t a r t --}}
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h4 class="bradecrumb-title mb-1">{{ ___('fees.receipt_options') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('fees-collect.index') }}">{{ ___('fees.fees_collect') }}</a></li>
                    <li class="breadcrumb-item">{{ ___('fees.receipt_options') }}</li>
                </ol>
            </div>
        </div>
    </div>
    {{-- breadcrumb Area E n d --}}

    <div class="container-fluid">
        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i>
                <strong>{{ ___('fees.payment_successful') }}!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Payment Summary Card --}}
        <div class="card border-success mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fa-solid fa-check-circle me-2"></i>{{ ___('fees.payment_completed') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>{{ ___('student_info.student_information') }}</h6>
                        <p><strong>{{ ___('student_info.student_name') }}:</strong> {{ $payment->student->first_name }} {{ $payment->student->last_name }}</p>
                        <p><strong>{{ ___('student_info.admission_no') }}:</strong> {{ $payment->student->admission_no }}</p>
                        <p><strong>{{ ___('academic.class') }}:</strong> {{ $payment->feesAssignChildren->feesMaster->group->name ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>{{ ___('fees.payment_details') }}</h6>
                        <p><strong>{{ ___('fees.payment_date') }}:</strong> {{ dateFormat($payment->date) }}</p>
                        <p><strong>{{ ___('fees.amount_paid') }}:</strong> 
                           <span class="h5 text-success">{{ Setting('currency_symbol') }} {{ number_format($payment->amount, 2) }}</span>
                        </p>
                        <p><strong>{{ ___('fees.payment_method') }}:</strong> {{ ___(\Config::get('site.payment_methods')[$payment->payment_method] ?? 'Unknown') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Receipt Options --}}
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-download me-2"></i>{{ ___('fees.download_receipts') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            {{-- Individual Receipt --}}
                            <div class="col-md-6">
                                <div class="card h-100 border-primary">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fa-solid fa-file-pdf fa-4x text-primary"></i>
                                        </div>
                                        <h6 class="card-title">{{ ___('fees.individual_receipt') }}</h6>
                                        <p class="card-text">{{ ___('fees.individual_receipt_description') }}</p>
                                        <a href="{{ route('fees.receipt.individual', $payment->id) }}" 
                                           class="btn btn-primary w-100" target="_blank">
                                            <i class="fa-solid fa-download me-2"></i>{{ ___('fees.download_pdf') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Student Summary Receipt --}}
                            <div class="col-md-6">
                                <div class="card h-100 border-info">
                                    <div class="card-body text-center">
                                        <div class="mb-3">
                                            <i class="fa-solid fa-file-lines fa-4x text-info"></i>
                                        </div>
                                        <h6 class="card-title">{{ ___('fees.student_summary') }}</h6>
                                        <p class="card-text">{{ ___('fees.student_summary_description') }}</p>
                                        <a href="{{ route('fees.receipt.student-summary', $payment->student_id) }}" 
                                           class="btn btn-info w-100" target="_blank">
                                            <i class="fa-solid fa-download me-2"></i>{{ ___('fees.download_summary') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Actions --}}
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="alert alert-light">
                                    <h6 class="mb-3">
                                        <i class="fa-solid fa-tools me-2"></i>{{ ___('fees.additional_actions') }}
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-outline-primary w-100" 
                                                    onclick="printReceipt({{ $payment->id }})">
                                                <i class="fa-solid fa-print me-2"></i>{{ ___('fees.print_receipt') }}
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-outline-success w-100" 
                                                    onclick="emailReceipt({{ $payment->id }})">
                                                <i class="fa-solid fa-envelope me-2"></i>{{ ___('fees.email_receipt') }}
                                            </button>
                                        </div>
                                        <div class="col-md-4">
                                            <a href="{{ route('fees-collect.index') }}" class="btn btn-outline-secondary w-100">
                                                <i class="fa-solid fa-arrow-left me-2"></i>{{ ___('fees.back_to_collection') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions Sidebar --}}
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-bolt me-2"></i>{{ ___('fees.quick_actions') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('fees-collect.collect', $payment->student_id) }}" 
                               class="btn btn-success">
                                <i class="fa-solid fa-plus me-2"></i>{{ ___('fees.collect_more_fees') }}
                            </a>
                            
                            <a href="{{ route('fees-collect.index') }}" 
                               class="btn btn-primary">
                                <i class="fa-solid fa-users me-2"></i>{{ ___('fees.collect_for_another_student') }}
                            </a>
                            
                            <button type="button" class="btn btn-info" onclick="generateGroupReceipt()">
                                <i class="fa-solid fa-file-invoice me-2"></i>{{ ___('fees.generate_daily_report') }}
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Payment Verification --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-shield-check me-2"></i>{{ ___('fees.payment_verification') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted">{{ ___('fees.verification_code_info') }}</p>
                        <div class="bg-light p-2 rounded text-center">
                            <code>{{ strtoupper(md5($payment->id . $payment->date)) }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printReceipt(paymentId) {
    const printWindow = window.open(
        '{{ route("fees.receipt.individual", ":id") }}'.replace(':id', paymentId),
        '_blank',
        'width=800,height=600'
    );
    
    printWindow.onload = function() {
        printWindow.print();
    };
}

function emailReceipt(paymentId) {
    alert('{{ ___("fees.email_feature_coming_soon") }}');
}

function generateGroupReceipt() {
    window.open('{{ route("fees.receipt.daily-collection") }}?date={{ date("Y-m-d") }}&collector_id={{ auth()->id() }}', '_blank');
}
</script>
@endsection
