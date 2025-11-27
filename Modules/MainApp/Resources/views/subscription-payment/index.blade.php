@extends('mainapp::layouts.backend.master')
@section('title')
    {{ @$data['title'] }}
@endsection
@section('content')
    <div class="page-content">

        {{-- bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('mainapp_common.home') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('mainapp_subscriptions.Subscription Payments') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <!--  Filter Section Start -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('subscription-payments.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="status" class="form-label">{{ ___('mainapp_common.status') }}</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">{{ ___('mainapp_common.All') }}</option>
                                @foreach($data['statusOptions'] as $value => $label)
                                    <option value="{{ $value }}" {{ request()->filled('status') && request('status') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="school_id" class="form-label">{{ ___('mainapp_schools.School') }}</label>
                            <select name="school_id" id="school_id" class="form-select">
                                <option value="">{{ ___('mainapp_common.All') }}</option>
                                @foreach($data['schools'] as $school)
                                    <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                        {{ $school->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="start_date" class="form-label">{{ ___('mainapp_common.Start Date') }}</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date', \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-2">
                            <label for="end_date" class="form-label">{{ ___('mainapp_common.End Date') }}</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">{{ ___('mainapp_common.Filter') }}</button>
                            <a href="{{ route('subscription-payments.index') }}" class="btn btn-secondary">{{ ___('mainapp_common.Reset') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--  Filter Section End -->

        <!--  table content start -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('mainapp_subscriptions.Subscription Payments') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered payment-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('mainapp_common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('mainapp_schools.School') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Package') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Amount') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Payment Date') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Payment Method') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Reference') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.status') }}</th>
                                    <th class="action">{{ ___('mainapp_common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @forelse ($data['payments'] as $key => $payment)
                                <tr id="row_{{ $payment->id }}">
                                    <td class="serial">{{ $data['payments']->firstItem() + $key }}</td>
                                    <td title="{{ @$payment->school->email }}">
                                        {{ Str::limit(@$payment->school->name, 30) ?? ___('mainapp_common.N/A') }}
                                        <br><small class="text-muted">{{ @$payment->school->phone }}</small>
                                    </td>
                                    <td>{{ $payment->subscription->package->name ?? ___('mainapp_common.N/A') }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ dateFormat($payment->payment_date) }}</td>
                                    <td>{{ $payment->getPaymentMethodLabel() }}</td>
                                    <td>
                                        @if($payment->reference_number)
                                            <strong>Ref:</strong> {{ $payment->reference_number }}<br>
                                        @endif
                                        @if($payment->transaction_id)
                                            <strong>Txn:</strong> {{ $payment->transaction_id }}
                                        @endif
                                        @if(!$payment->reference_number && !$payment->transaction_id)
                                            {{ ___('mainapp_common.N/A') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->status == 0)
                                            <span class="badge-basic-warning-text">{{ ___('mainapp_subscriptions.Pending') }}</span>
                                        @elseif ($payment->status == 1)
                                            <span class="badge-basic-success-text">{{ ___('mainapp_subscriptions.Approved') }}</span>
                                            @if($payment->approved_at)
                                                <br><small class="text-muted">{{ dateFormat($payment->approved_at) }}</small>
                                                <br><small class="text-muted">{{ ___('mainapp_common.by') }} {{ $payment->approver->name ?? 'Admin' }}</small>
                                            @endif
                                        @elseif ($payment->status == 2)
                                            <span class="badge-basic-danger-text">{{ ___('mainapp_subscriptions.Rejected') }}</span>
                                            @if($payment->rejection_reason)
                                                <br><small class="text-muted" title="{{ $payment->rejection_reason }}">
                                                    {{ Str::limit($payment->rejection_reason, 30) }}
                                                </small>
                                            @endif
                                        @endif
                                    </td>

                                    <td class="action">
                                        <div class="dropdown dropdown-action">
                                            <button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa-solid fa-ellipsis"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end">
                                                @if ($payment->isPending())
                                                    <li>
                                                        <a class="dropdown-item approve-payment" href="javascript:void(0)"
                                                           data-id="{{ $payment->id }}"
                                                           data-school="{{ $payment->school->name }}"
                                                           data-amount="{{ number_format($payment->amount, 2) }}">
                                                            <span class="icon mr-8"><i class="fa-solid fa-check text-success"></i></span>
                                                            {{ ___('mainapp_common.Approve') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item reject-payment" href="javascript:void(0)"
                                                           data-id="{{ $payment->id }}"
                                                           data-school="{{ $payment->school->name }}">
                                                            <span class="icon mr-8"><i class="fa-solid fa-times text-danger"></i></span>
                                                            {{ ___('mainapp_common.Reject') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($payment->isApproved())
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('subscription-payments.receipt', $payment->id) }}" target="_blank">
                                                            <span class="icon mr-8"><i class="fa-solid fa-file-pdf"></i></span>
                                                            {{ ___('mainapp_common.Download Receipt') }}
                                                        </a>
                                                    </li>
                                                @endif

                                                <li>
                                                    <a class="dropdown-item" href="{{ route('subscription-payments.history', $payment->school_id) }}">
                                                        <span class="icon mr-8"><i class="fa-solid fa-history"></i></span>
                                                        {{ ___('mainapp_common.View History') }}
                                                    </a>
                                                </li>

                                                @if ($payment->isPending())
                                                    <li>
                                                        <a class="dropdown-item delete_data" href="javascript:void(0);"
                                                           data-href="{{ route('subscription-payments.delete', $payment->id) }}">
                                                            <span class="icon mr-8"><i class="fa-solid fa-trash text-danger"></i></span>
                                                            {{ ___('mainapp_common.delete') }}
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">{{ ___('mainapp_common.No data available') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $data['payments']->links() }}
                    </div>
                </div>
            </div>
        </div>
        <!--  table content end -->
    </div>

    @include('mainapp::subscription-payment._approve_modal')
    @include('mainapp::subscription-payment._reject_modal')
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Approve payment modal
        $('.approve-payment').on('click', function() {
            const paymentId = $(this).data('id');
            const schoolName = $(this).data('school');
            const amount = $(this).data('amount');

            $('#approveSchoolName').text(schoolName);
            $('#approveAmount').text(amount);
            $('#approveForm').attr('action', `{{ url('subscription-payments') }}/${paymentId}/approve`);
            $('#approveModal').modal('show');
        });

        // Reject payment modal
        $('.reject-payment').on('click', function() {
            const paymentId = $(this).data('id');
            const schoolName = $(this).data('school');

            $('#rejectSchoolName').text(schoolName);
            $('#rejectForm').attr('action', `{{ url('subscription-payments') }}/${paymentId}/reject`);
            $('#rejectModal').modal('show');
        });

        // Confirm approve
        $('#confirmApprove').on('click', function() {
            $('#approveForm').submit();
        });

        // Confirm reject
        $('#confirmReject').on('click', function() {
            const reason = $('#rejection_reason').val().trim();
            if (reason.length < 10) {
                alert('Please provide a rejection reason (minimum 10 characters)');
                return;
            }
            $('#rejectForm').submit();
        });
    });
</script>
@endpush
