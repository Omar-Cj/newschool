@extends('mainapp::layouts.backend.master')
@section('title')
    {{ @$data['title'] }}
@endsection

@push('style')
<style>
    /* Print styles */
    @media print {
        .sidebar, .header, .footer, .page-header, .breadcrumb,
        form, .btn, .card-header .d-flex.gap-2, .no-print,
        .ot_crm_summeryBox, .card-header {
            display: none !important;
        }
        .print-header { display: block !important; }
        .page-content { padding: 0 !important; margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .card-body { padding: 0 !important; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000 !important; padding: 8px !important; }
        .badge { border: 1px solid #000; }
    }
    @media screen {
        .print-header { display: none !important; }
    }
</style>
@endpush

@section('content')
    <div class="page-content">

        {{-- Print Header (visible only when printing) --}}
        <div class="print-header text-center mb-4">
            <img src="{{ @globalAsset(setting('dark_logo'), '154X38.webp') }}" alt="Logo" style="max-height: 60px;">
            <h2>{{ $data['title'] }}</h2>
            <p class="text-muted">{{ ___('mainapp_common.Generated') }}: {{ now()->format('Y-m-d H:i') }}</p>
        </div>

        {{-- bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('mainapp_common.home') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('mainapp_common.Reports') }}</li>
                        <li class="breadcrumb-item active">{{ ___('mainapp_common.Payment Collection Report') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        {{-- Filter Section Start --}}
        <div class="row">
            <div class="col-12">
                <form method="GET" action="{{ route('reports.payment-collection') }}">
                    <div class="card ot-card mb-24 position-relative z_1">
                        <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                            <h3 class="mb-0">{{ ___('mainapp_common.Filtering') }}</h3>

                            <div class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">

                                {{-- Start Date --}}
                                <div class="single_large_selectBox">
                                    <input type="date" name="date_from" id="date_from" class="form-control ot-input" value="{{ request('date_from') }}" placeholder="{{ ___('mainapp_common.Start Date') }}">
                                </div>

                                {{-- End Date --}}
                                <div class="single_large_selectBox">
                                    <input type="date" name="date_to" id="date_to" class="form-control ot-input" value="{{ request('date_to') }}" placeholder="{{ ___('mainapp_common.End Date') }}">
                                </div>

                                {{-- School Selector --}}
                                <div class="single_large_selectBox">
                                    <select name="school_id" id="school_id" class="form-select ot-input">
                                        <option value="">{{ ___('mainapp_common.All') }} {{ ___('mainapp_schools.School') }}</option>
                                        @foreach($data['schools'] as $school)
                                            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                                {{ $school->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Status Selector --}}
                                <div class="single_large_selectBox">
                                    <select name="status" id="status" class="form-select ot-input">
                                        <option value="">{{ ___('mainapp_common.All') }} {{ ___('mainapp_common.status') }}</option>
                                        @foreach($data['statusOptions'] as $value => $label)
                                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Payment Method Selector --}}
                                <div class="single_large_selectBox">
                                    <select name="payment_method" id="payment_method" class="form-select ot-input">
                                        <option value="">{{ ___('mainapp_common.All') }} {{ ___('mainapp_common.Payment Method') }}</option>
                                        @foreach($data['paymentMethods'] as $method)
                                            <option value="{{ $method }}" {{ request('payment_method') == $method ? 'selected' : '' }}>
                                                {{ ucfirst(str_replace('_', ' ', $method)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Search Button --}}
                                <button class="btn btn-lg ot-btn-primary" type="submit">
                                    <i class="fa-solid fa-search me-2"></i>
                                    {{ ___('mainapp_common.Filter') }}
                                </button>

                                {{-- Reset Button --}}
                                <a href="{{ route('reports.payment-collection') }}" class="btn btn-lg btn-secondary">
                                    <i class="fa-solid fa-rotate-right me-2"></i>
                                    {{ ___('mainapp_common.Reset') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        {{-- Filter Section End --}}

        {{-- Summary Cards Start --}}
        @if(count($data['payments']) > 0)
        <div class="row">
            {{-- Total Payments Card --}}
            <div class="col-xl-3 col-lg-3 col-md-6">
                <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                    <div class="icon">
                        <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/school.svg') }}" alt="total">
                    </div>
                    <div class="summeryContent">
                        <h4>{{ ___('mainapp_common.Total Payments') }}</h4>
                        <h1>{{ $data['summary']['total_payments'] }}</h1>
                        <small class="text-muted">{{ ___('mainapp_common.Records') }}</small>
                    </div>
                </div>
            </div>

            {{-- Approved Payments Card --}}
            <div class="col-xl-3 col-lg-3 col-md-6">
                <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                    <div class="icon">
                        <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/package.svg') }}" alt="approved">
                    </div>
                    <div class="summeryContent">
                        <h4>{{ ___('mainapp_subscriptions.Approved') }}</h4>
                        <h1>{{ $data['summary']['approved_count'] }}</h1>
                        <small class="text-success">
                            <i class="las la-check-circle"></i>
                            {{ ___('mainapp_common.Confirmed') }}
                        </small>
                    </div>
                </div>
            </div>

            {{-- Pending Payments Card --}}
            <div class="col-xl-3 col-lg-3 col-md-6">
                <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                    <div class="icon">
                        <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/feature.svg') }}" alt="pending">
                    </div>
                    <div class="summeryContent">
                        <h4>{{ ___('mainapp_subscriptions.Pending') }}</h4>
                        <h1>{{ $data['summary']['pending_count'] }}</h1>
                        <small class="text-warning">
                            <i class="las la-clock"></i>
                            {{ ___('mainapp_common.Awaiting') }}
                        </small>
                    </div>
                </div>
            </div>

            {{-- Rejected Payments Card --}}
            <div class="col-xl-3 col-lg-3 col-md-6">
                <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                    <div class="icon">
                        <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/faq.svg') }}" alt="rejected">
                    </div>
                    <div class="summeryContent">
                        <h4>{{ ___('mainapp_subscriptions.Rejected') }}</h4>
                        <h1>{{ $data['summary']['rejected_count'] }}</h1>
                        <small class="text-danger">
                            <i class="las la-times-circle"></i>
                            {{ ___('mainapp_common.Declined') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        @endif
        {{-- Summary Cards End --}}

        {{-- Table Content Start --}}
        <div class="table-content table-basic">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('mainapp_common.Payment Collection Report') }}</h4>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()">
                            <i class="fa-solid fa-print me-1"></i> {{ ___('mainapp_common.Print') }}
                        </button>
                        <a href="{{ route('reports.payment-collection.export', array_merge(request()->all(), ['format' => 'excel'])) }}"
                           class="btn btn-sm btn-success">
                            <i class="fa-solid fa-file-excel me-1"></i> {{ ___('mainapp_common.Export Excel') }}
                        </a>
                        <a href="{{ route('reports.payment-collection.export', array_merge(request()->all(), ['format' => 'pdf'])) }}"
                           class="btn btn-sm btn-danger">
                            <i class="fa-solid fa-file-pdf me-1"></i> {{ ___('mainapp_common.Export PDF') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered payment-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('mainapp_common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('mainapp_schools.School Name') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Package Name') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Amount') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Payment Date') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Payment Method') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.status') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Approved By') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Invoice Number') }}</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @forelse ($data['payments'] as $key => $payment)
                                <tr id="row_{{ $payment->id }}">
                                    <td class="serial">{{ $key + 1 }}</td>
                                    <td title="{{ @$payment->school_email }}">
                                        {{ Str::limit(@$payment->school_name, 30) ?? ___('mainapp_common.N/A') }}
                                        <br><small class="text-muted">{{ @$payment->school_phone }}</small>
                                    </td>
                                    <td>{{ $payment->package_name ?? ___('mainapp_common.N/A') }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ dateFormat($payment->payment_date) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method ?? ___('mainapp_common.N/A'))) }}</td>
                                    <td>
                                        @if ($payment->status_code == 0)
                                            <span class="badge-basic-warning-text">{{ ___('mainapp_subscriptions.Pending') }}</span>
                                        @elseif ($payment->status_code == 1)
                                            <span class="badge-basic-success-text">{{ ___('mainapp_subscriptions.Approved') }}</span>
                                        @elseif ($payment->status_code == 2)
                                            <span class="badge-basic-danger-text">{{ ___('mainapp_subscriptions.Rejected') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->status_code == 1 && !empty($payment->approver_name))
                                            {{ $payment->approver_name }}
                                            @if($payment->approved_at)
                                                <br><small class="text-muted">{{ dateFormat($payment->approved_at) }}</small>
                                            @endif
                                        @else
                                            {{ ___('mainapp_common.N/A') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($payment->invoice_number)
                                            {{ $payment->invoice_number }}
                                        @elseif($payment->reference_number)
                                            {{ $payment->reference_number }}
                                        @elseif($payment->transaction_id)
                                            {{ $payment->transaction_id }}
                                        @else
                                            {{ ___('mainapp_common.N/A') }}
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center gray-color">
                                        <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary" width="100">
                                        <p class="mb-0 text-center">{{ ___('mainapp_common.no_data_available') }}</p>
                                        <p class="mb-0 text-center text-secondary font-size-90">
                                            {{ ___('mainapp_common.No payment records found for the selected filters') }}
                                        </p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                            @if(count($data['payments']) > 0)
                            <tfoot>
                                <tr class="table-secondary fw-bold">
                                    <td colspan="3" class="text-end">{{ ___('mainapp_common.Total') }}:</td>
                                    <td>{{ number_format($data['summary']['total_amount'], 2) }}</td>
                                    <td colspan="5"></td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Table Content End --}}

    </div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Optional: Add date range validation
        $('#date_to').on('change', function() {
            const startDate = $('#date_from').val();
            const endDate = $(this).val();

            if (startDate && endDate && startDate > endDate) {
                alert('{{ ___('mainapp_common.End date must be after start date') }}');
                $(this).val('');
            }
        });
    });
</script>
@endpush
