@extends('mainapp::layouts.backend.master')
@section('title')
    {{ @$data['title'] }}
@endsection

@push('style')
<style>
    /* Print styles */
    @media print {
        /* Hide non-essential elements */
        .sidebar, .header, .footer, .page-header, .breadcrumb,
        form, .btn, .card-header .d-flex.gap-2, .no-print,
        .ot_crm_summeryBox, .card-header {
            display: none !important;
        }

        /* Page setup */
        @page {
            size: landscape;
            margin: 15mm 15mm 0 15mm;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            font-size: 9pt !important;
            color: #333;
            line-height: 1.3;
            padding-bottom: 15mm;
        }

        /* SOLUTION FOR ISSUE #2: Add container padding */
        .page-content {
            padding: 0 20mm !important; /* Add horizontal padding */
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }

        .card-body {
            padding: 0 !important;
            overflow: visible !important;
        }

        /* Print Header - Professional Design */
        .print-header {
            display: flex !important;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 3px solid #00C48C;
        }

        .print-header img {
            max-height: 70px;
            max-width: 100px;
        }

        .print-header h2 {
            flex: 1;
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .print-header p {
            font-size: 8pt;
            color: #7f8c8d;
            margin: 0;
        }

        /* Metadata Section - NEW */
        .print-metadata {
            background-color: #ecf0f1;
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 10px;
            border: 1px solid #bdc3c7;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .print-metadata-item {
            display: flex;
            padding: 2px 0;
            border-bottom: none;
        }

        .print-metadata-label {
            font-weight: 600;
            color: #00C48C;
            min-width: 100px;
            flex-shrink: 0;
            font-size: 8pt;
        }

        .print-metadata-value {
            color: #34495e;
            font-weight: 500;
            font-size: 8pt;
        }

        /* Table Styling - Refined */
        .table-responsive {
            overflow: visible !important;
            page-break-before: avoid !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 7pt !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            table-layout: fixed !important;
            page-break-before: avoid !important;
        }

        thead {
            display: table-header-group !important;
            background-color: #00C48C !important;
        }

        th {
            padding: 8px 8px !important;
            text-align: left;
            font-weight: 600 !important;
            color: #ffffff !important;
            background-color: #00C48C !important;
            border: 1px solid #00B87A !important;
            font-size: 7pt !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        td {
            padding: 7px !important;
            border: 1px solid #d5d8dc !important;
            font-size: 7pt !important;
            color: #2c3e50;
            vertical-align: top !important;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* Prevent table rows from breaking across pages */
        tr {
            page-break-inside: avoid !important;
        }

        tfoot {
            display: table-footer-group !important;
        }

        /* Summary Section - Enhanced */
        .summary-section {
            display: block !important;
            margin: 5px 0;
            padding: 5px 0;
            background: #f8f9fa;
            border-radius: 5px;
            page-break-inside: avoid;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-cell {
            display: table-cell;
            padding: 8px;
            text-align: center;
            border-right: 1px solid #dee2e6;
        }

        .summary-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
        }

        .summary-value {
            font-size: 18pt;
            font-weight: bold;
            color: #333;
        }

        /* Badge styling for print */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            border: 1px solid #000;
        }

        .badge-success {
            background: #28a745 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
        }

        .badge-danger {
            background: #dc3545 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
        }

        .badge-warning {
            background: #ffc107 !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact !important;
        }

        .badge-info {
            background: #17a2b8 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        /* Print Summary Section - Matches PDF design */
        .print-summary-section {
            display: block !important;
            margin: 5px 0 !important;
            padding: 8px !important;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 5px;
            page-break-inside: avoid;
            page-break-after: avoid !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .print-summary-title {
            font-size: 10pt;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px !important;
        }

        .print-summary-grid {
            display: table;
            width: 100%;
        }

        .print-summary-row {
            display: table-row;
        }

        .print-summary-cell {
            display: table-cell;
            padding: 8px;
            width: 25%;
            text-align: center;
            border-right: 1px solid #dee2e6;
        }

        .print-summary-cell:last-child {
            border-right: none;
        }

        .print-summary-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .print-summary-value {
            font-size: 18pt;
            font-weight: bold;
            color: #333;
        }

        .print-summary-value.success {
            color: #00C48C;
        }

        .print-summary-value.primary {
            color: #007bff;
        }

        .print-summary-value.danger {
            color: #dc3545;
        }

        .print-growth-indicator {
            font-size: 8pt;
            margin-top: 5px;
            color: #666;
        }
    }
    @media screen {
        .print-header { display: none !important; }
        .print-metadata { display: none !important; }
        .print-summary-section { display: none !important; }
    }
</style>
@endpush

@section('content')
    <div class="page-content">

        {{-- Print Header (visible only when printing) --}}
        <div class="print-header">
            @if(setting('dark_logo'))
            <img src="{{ @globalAsset(setting('dark_logo'), '154X38.webp') }}" alt="Logo">
            @endif
            <h2>{{ $data['title'] }}</h2>
        </div>

        {{-- Print Metadata Section (visible only when printing) --}}
        <div class="print-metadata">
            <div class="print-metadata-item">
                <span class="print-metadata-label">{{ ___('mainapp_common.Generated') }}:</span>
                <span class="print-metadata-value">{{ now()->format('F d, Y - h:i A') }}</span>
            </div>
            <div class="print-metadata-item">
                <span class="print-metadata-label">{{ ___('mainapp_common.Generated By') }}:</span>
                <span class="print-metadata-value">{{ auth()->user()->name ?? 'System' }}</span>
            </div>
            <div class="print-metadata-item">
                <span class="print-metadata-label">{{ ___('mainapp_common.Period From') }}:</span>
                <span class="print-metadata-value">{{ request('date_from', $data['dateFrom'] ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}</span>
            </div>
            <div class="print-metadata-item">
                <span class="print-metadata-label">{{ ___('mainapp_common.Period To') }}:</span>
                <span class="print-metadata-value">{{ request('date_to', $data['dateTo'] ?? \Carbon\Carbon::now()->format('Y-m-d')) }}</span>
            </div>
        </div>

        {{-- Print Summary Section (visible only when printing) --}}
        <div class="print-summary-section">
            <div class="print-summary-title">PAYMENT SUMMARY</div>
            <div class="print-summary-grid">
                <div class="print-summary-row">
                    <div class="print-summary-cell">
                        <div class="print-summary-label">Total Payments</div>
                        <div class="print-summary-value success">{{ number_format($data['summary']['total_payments'] ?? 0) }}</div>
                        <div class="print-growth-indicator">All Records</div>
                    </div>
                    <div class="print-summary-cell">
                        <div class="print-summary-label">Approved</div>
                        <div class="print-summary-value primary">{{ number_format($data['summary']['approved_count'] ?? 0) }}</div>
                        <div class="print-growth-indicator">
                            {{ $data['summary']['total_payments'] > 0 ? number_format(($data['summary']['approved_count'] / $data['summary']['total_payments']) * 100, 1) : 0 }}% of total
                        </div>
                    </div>
                    <div class="print-summary-cell">
                        <div class="print-summary-label">Pending</div>
                        <div class="print-summary-value">{{ number_format($data['summary']['pending_count'] ?? 0) }}</div>
                        <div class="print-growth-indicator">
                            {{ $data['summary']['total_payments'] > 0 ? number_format(($data['summary']['pending_count'] / $data['summary']['total_payments']) * 100, 1) : 0 }}% of total
                        </div>
                    </div>
                    <div class="print-summary-cell">
                        <div class="print-summary-label">Rejected</div>
                        <div class="print-summary-value danger">{{ number_format($data['summary']['rejected_count'] ?? 0) }}</div>
                        <div class="print-growth-indicator">
                            @php
                                $total = $data['summary']['total_payments'] ?? 1;
                                $rejected = $data['summary']['rejected_count'] ?? 0;
                                $percent = $total > 0 ? round(($rejected / $total) * 100, 1) : 0;
                            @endphp
                            {{ $percent }}% of total
                        </div>
                    </div>
                </div>
            </div>
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
                                    <input type="date" name="date_from" id="date_from" class="form-control ot-input" value="{{ request('date_from', $data['dateFrom'] ?? \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}" placeholder="{{ ___('mainapp_common.Start Date') }}">
                                </div>

                                {{-- End Date --}}
                                <div class="single_large_selectBox">
                                    <input type="date" name="date_to" id="date_to" class="form-control ot-input" value="{{ request('date_to', $data['dateTo'] ?? \Carbon\Carbon::now()->format('Y-m-d')) }}" placeholder="{{ ___('mainapp_common.End Date') }}">
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
                                            <option value="{{ $value }}" {{ request('status') !== null && request('status') !== '' && request('status') == $value ? 'selected' : '' }}>
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
                        @php
                            // Ensure dates are always present for export
                            $exportParams = array_merge([
                                'date_from' => request('date_from', now()->startOfMonth()->format('Y-m-d')),
                                'date_to' => request('date_to', now()->format('Y-m-d')),
                            ], request()->only(['school_id', 'status', 'payment_method']), ['format' => 'pdf']);

                            $excelParams = array_merge([
                                'date_from' => request('date_from', now()->startOfMonth()->format('Y-m-d')),
                                'date_to' => request('date_to', now()->format('Y-m-d')),
                            ], request()->only(['school_id', 'status', 'payment_method']), ['format' => 'excel']);
                        @endphp

                        <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()">
                            <i class="fa-solid fa-print me-1"></i> {{ ___('mainapp_common.Print') }}
                        </button>
                        <a href="{{ route('reports.payment-collection.export', $excelParams) }}"
                           class="btn btn-sm btn-success">
                            <i class="fa-solid fa-file-excel me-1"></i> {{ ___('mainapp_common.Export Excel') }}
                        </a>
                        <a href="{{ route('reports.payment-collection.export', $exportParams) }}"
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
