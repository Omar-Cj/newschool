@extends('backend.master')

@section('title')
    {{ ___('fees.receipts') ?? 'Receipts' }}
@endsection

{{--
    Receipt listing with data consistency:
    - Table data now matches exactly what appears in receipt templates
    - Uses same data preparation logic as generateIndividualReceipt()
    - Ensures "what you see is what you get" between listing and printed receipts
--}}
@php($currency = $currency ?? Setting('currency_symbol'))

@section('content')
<div class="page-content">
    {{-- breadcrumb Area Start --}}
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h4 class="bradecrumb-title mb-1">{{ ___('fees.receipts') ?? 'Receipts' }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                    <li class="breadcrumb-item">{{ ___('fees.fees') ?? 'Fees' }}</li>
                    <li class="breadcrumb-item">{{ ___('fees.receipts') ?? 'Receipts' }}</li>
                </ol>
            </div>
        </div>
    </div>
    {{-- breadcrumb Area End --}}

    <div class="row">
        <div class="col-12">
            <div class="card ot-card mb-24 position-relative z_1">
                <form method="GET" enctype="multipart/form-data" id="receipts-filter">
                    <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                        <h3 class="mb-0">{{ ___('common.filtering') ?? 'Filtering' }}</h3>

                        <div class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">
                            <!-- Search Input -->
                            <div class="input-group table_searchBox">
                                <input name="q" type="text" class="form-control" value="{{ request('q') }}"
                                       placeholder="{{ ___('common.search') }} {{ ___('fees.receipt_no') }}, {{ ___('student_info.student_name') }}, {{ ___('student_info.admission_no') }}"
                                       aria-label="Search" aria-describedby="searchIcon">
                                <span class="input-group-text" id="searchIcon">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                            </div>

                            <!-- Date Range -->
                            <div class="single_selectBox">
                                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}"
                                       placeholder="{{ ___('fees.from_date') }}">
                            </div>
                            <div class="single_selectBox">
                                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}"
                                       placeholder="{{ ___('fees.to_date') }}">
                            </div>

                            <!-- Payment Method Filter -->
                            <div class="single_selectBox">
                                <select class="nice-select niceSelect bordered_style wide" name="payment_method">
                                    <option value="">{{ ___('fees.payment_method') }}</option>
                                    @foreach($availableMethods as $methodValue => $methodLabel)
                                        <option value="{{ $methodValue }}" @selected((string)request('payment_method') === (string)$methodValue)>
                                            {{ ___($methodLabel) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Collector Filter -->
                            <div class="single_selectBox">
                                <select class="nice-select niceSelect bordered_style wide" name="collector_id">
                                    <option value="">{{ ___('fees.collected_by') }}</option>
                                    @foreach($collectors as $collector)
                                        <option value="{{ $collector->id }}" @selected((string)request('collector_id') === (string)$collector->id)>
                                            {{ $collector->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button class="btn btn-lg ot-btn-primary">
                                {{ ___('common.filter') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--  table content start -->
    <div class="table-content table-basic mt-20">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ ___('fees.receipts') ?? 'Receipts' }}</h4>
                <div class="card_header_right">
                    <span class="badge badge-basic-info-text">{{ $receipts->total() }} {{ ___('common.total') }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered role-table">
                        <thead class="thead">
                            <tr>
                                <th class="serial">{{ ___('common.sr_no') }}</th>
                                <th class="purchase">{{ ___('fees.receipt_no') }}</th>
                                <th class="purchase">{{ ___('student_info.student_name') }}</th>
                                <th class="purchase">{{ ___('academic.class') }} ({{ ___('academic.section') }})</th>
                                <th class="purchase">{{ ___('fees.amount_paid') }}</th>
                                <th class="purchase">{{ ___('fees.payment_date') }}</th>
                                <th class="purchase">{{ ___('fees.payment_method') }}</th>
                                <th class="purchase">{{ ___('fees.collected_by') }}</th>
                                <th class="purchase">{{ ___('common.status') }}</th>
                                <th class="action">{{ ___('common.action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                            @forelse($receipts as $key => $receipt)
                                <tr id="row_{{ $receipt->id }}">
                                    <td class="serial">{{ $receipts->firstItem() + $key }}</td>
                                    <td>
                                        <div class="fw-semibold text-primary">{{ $receipt->receipt_number }}</div>
                                        <small class="badge
                                            @if($receipt->type === 'payment_transaction')
                                                badge-basic-info-text
                                            @else
                                                badge-basic-secondary-text
                                            @endif
                                        ">
                                            {{ $receipt->type === 'payment_transaction' ? ___('fees.payment_transaction') ?? 'Payment' : ___('fees.legacy_payment') ?? 'Legacy' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $receipt->student->first_name }} {{ $receipt->student->last_name }}</div>
                                        <small class="text-muted">{{ ___('student_info.admission_no') }}: {{ $receipt->student->admission_no }}</small>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $receipt->student->sessionStudentDetails->class->name ?? 'N/A' }} - {{ $receipt->student->sessionStudentDetails->section->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-success">{{ $currency }} {{ number_format($receipt->amount_paid, 2) }}</div>
                                        @if(count($receipt->fees_affected) > 1)
                                            <small class="text-info">{{ count($receipt->fees_affected) }} {{ ___('fees.fees_affected') ?? 'fees affected' }}</small>
                                        @elseif(count($receipt->fees_affected) === 1)
                                            <small class="text-muted">{{ $receipt->fees_affected[0]['name'] ?? 'Fee Payment' }}</small>
                                        @endif
                                    </td>
                                    <td>{{ dateFormat($receipt->payment_date) }}</td>
                                    <td>
                                        <span class="badge badge-basic-info-text">{{ $receipt->payment_method }}</span>
                                        @if($receipt->transaction_reference)
                                            <small class="d-block text-muted" title="{{ ___('fees.transaction_reference') }}">
                                                {{ Str::limit($receipt->transaction_reference, 15) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $receipt->collected_by->name ?? '—' }}</td>
                                    <td>
                                        @if($receipt->payment_status === 'partial')
                                            <span class="badge-basic-warning-text">{{ ___('fees.partial_payment') ?? 'Partial' }}</span>
                                        @else
                                            <span class="badge-basic-success-text">{{ ___('fees.full_payment') ?? 'Full' }}</span>
                                        @endif
                                    </td>
                                    <td class="action">
                                        <div class="dropdown dropdown-action">
                                            <button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa-solid fa-ellipsis"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0);"
                                                       onclick="ReceiptActions.printReceipt({{ $receipt->id }})">
                                                        <span class="icon mr-8"><i class="fa-solid fa-print"></i></span>
                                                        {{ ___('common.print') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" target="_blank"
                                                       href="{{ route('fees.receipt.individual', $receipt->id) }}">
                                                        <span class="icon mr-8"><i class="fa-solid fa-download"></i></span>
                                                        {{ ___('common.download') }}
                                                    </a>
                                                </li>
                                                @if(count($receipt->fees_affected) > 0)
                                                    <li>
                                                        <a class="dropdown-item" href="javascript:void(0);"
                                                           onclick="ReceiptActions.showAllocationDetails({{ json_encode($receipt->fees_affected) }})">
                                                            <span class="icon mr-8"><i class="fa-solid fa-list"></i></span>
                                                            {{ ___('fees.view_allocation') ?? 'View Allocation' }}
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center gray-color">
                                        <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary" width="100">
                                        <p class="mb-0 text-center">{{ ___('common.no_data_available') }}</p>
                                        <p class="mb-0 text-center text-secondary font-size-90">
                                            {{ ___('fees.no_receipts_found') ?? 'No receipts found. Try adjusting your search or filter criteria.' }}
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!--  table end -->
                <!--  pagination start -->
                <div class="ot-pagination pagination-content d-flex justify-content-end align-content-center py-3">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-between">
                            {!! $receipts->links() !!}
                        </ul>
                    </nav>
                </div>
                <!--  pagination end -->
            </div>
        </div>
    </div>
    <!--  table content end -->
</div>
@endsection

@push('script')
    @include('backend.fees.receipts.partials.actions-script')
@endpush
