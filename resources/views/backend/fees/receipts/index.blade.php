@extends('backend.master')

@section('title')
    Receipts
@endsection

@php($currency = Setting('currency_symbol'))

@section('content')
<div class="page-content">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label" for="receipt-search">{{ ___('common.search') }}</label>
                    <input type="text" name="q" id="receipt-search" class="form-control" value="{{ request('q') }}" placeholder="Search by receipt, student, admission no.">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="from-date">{{ ___('fees.from_date') }}</label>
                    <input type="date" name="from_date" id="from-date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="to-date">{{ ___('fees.to_date') }}</label>
                    <input type="date" name="to_date" id="to-date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="payment-method-filter">{{ ___('fees.payment_method') }}</label>
                    <select class="form-select" id="payment-method-filter" name="payment_method">
                        <option value="">{{ ___('common.all') }}</option>
                        @foreach($availableMethods as $methodValue => $methodLabel)
                            <option value="{{ $methodValue }}" @selected((string)request('payment_method') === (string)$methodValue)>
                                {{ ___($methodLabel) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label" for="collector-filter">{{ ___('fees.collected_by') }}</label>
                    <select class="form-select" id="collector-filter" name="collector_id">
                        <option value="">{{ ___('common.all') }}</option>
                        @foreach($collectors as $collector)
                            <option value="{{ $collector->id }}" @selected((string)request('collector_id') === (string)$collector->id)>
                                {{ $collector->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn ot-btn-primary">{{ ___('common.filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ ___('fees.receipt_no') }}</th>
                            <th>{{ ___('student_info.student_name') }}</th>
                            <th>{{ ___('academic.class') }}</th>
                            <th>{{ ___('fees.amount_paid') }}</th>
                            <th>{{ ___('fees.payment_date') }}</th>
                            <th>{{ ___('fees.payment_method') }}</th>
                            <th>{{ ___('fees.collected_by') }}</th>
                            <th class="text-end">{{ ___('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receipts as $receipt)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $receipt->receipt_number }}</span>
                                    @if(($receipt->related_payment_count ?? 1) > 1)
                                        <span class="badge bg-primary ms-2">{{ $receipt->related_payment_count }} items</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $receipt->student->full_name ?? ($receipt->student->first_name . ' ' . $receipt->student->last_name) }}</div>
                                    <small class="text-muted">{{ ___('student_info.admission_no') }}: {{ $receipt->student->admission_no }}</small>
                                </td>
                                <td>
                                    {{ $receipt->student->sessionStudentDetails->class->name ?? '—' }}
                                    @if($receipt->student->sessionStudentDetails && $receipt->student->sessionStudentDetails->section)
                                        <span class="text-muted">/ {{ $receipt->student->sessionStudentDetails->section->name }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $currency }} {{ number_format($receipt->grand_total, 2) }}</div>
                                    @if(($receipt->total_fine ?? 0) > 0)
                                        <small class="text-muted">{{ ___('fees.fine') }}: {{ $currency }} {{ number_format($receipt->total_fine, 2) }}</small>
                                    @endif
                                </td>
                                <td>{{ dateFormat($receipt->date) }}</td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ ___($receipt->payment_method_label) }}</span>
                                </td>
                                <td>{{ $receipt->collectBy->name ?? '—' }}</td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <button type="button"
                                                class="btn btn-outline-primary btn-sm"
                                                data-action="view-receipt"
                                                data-payment-id="{{ $receipt->id }}"
                                                data-fallback="{{ route('fees.receipt.options', $receipt->id) }}">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-outline-success btn-sm"
                                                onclick="ReceiptActions.printReceipt({{ $receipt->id }})">
                                            <i class="fa-solid fa-print"></i>
                                        </button>
                                        <a class="btn btn-outline-secondary btn-sm" target="_blank"
                                           href="{{ route('fees.receipt.individual', $receipt->id) }}">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    {{ ___('common.no_data_available') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            {{ $receipts->links() }}
        </div>
    </div>
</div>
@endsection

@push('script')
    @include('backend.fees.receipts.partials.actions-script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-action="view-receipt"]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const paymentId = this.dataset.paymentId;
                    const fallback = this.dataset.fallback;

                    if (!window.ReceiptActions) {
                        window.location.href = fallback;
                        return;
                    }

                    const originalHtml = this.innerHTML;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                    this.disabled = true;

                    window.ReceiptActions.loadOptionsModal(paymentId).then(function (modal) {
                        button.innerHTML = originalHtml;

                        if (modal) {
                            modal.on('hidden.bs.modal', function () {
                                button.disabled = false;
                                button.innerHTML = originalHtml;
                            });
                        } else {
                            button.disabled = false;
                        }
                    }).catch(function () {
                        window.location.href = fallback;
                        button.disabled = false;
                        button.innerHTML = originalHtml;
                    });
                });
            });
        });
    </script>
@endpush
