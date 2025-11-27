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
                        <li class="breadcrumb-item"><a href="{{ route('subscription-payments.index') }}">{{ ___('mainapp_subscriptions.Subscription Payments') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('mainapp_common.Payment History') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <!-- School Info Card -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <h5><i class="fa-solid fa-school"></i> {{ $data['school']->name }}</h5>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-1"><strong>{{ ___('mainapp_common.Email') }}:</strong> {{ $data['school']->email }}</p>
                        <p class="mb-1"><strong>{{ ___('mainapp_common.Phone') }}:</strong> {{ $data['school']->phone }}</p>
                    </div>
                    <div class="col-md-3">
                        @if($data['school']->package)
                            <p class="mb-1"><strong>{{ ___('mainapp_subscriptions.Current Package') }}:</strong> {{ $data['school']->package->name }}</p>
                        @endif
                    </div>
                    <div class="col-md-3">
                        @if($data['school']->subscriptions->first())
                            <p class="mb-1">
                                <strong>{{ ___('mainapp_subscriptions.Expiry Date') }}:</strong>
                                {{ dateFormat($data['school']->subscriptions->first()->expiry_date) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!--  table content start -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('mainapp_common.Payment History') }}</h4>
                    <a href="{{ route('subscription-payments.create', $data['school']->id) }}" class="btn btn-lg ot-btn-primary">
                        <span><i class="fa-solid fa-plus"></i> </span>
                        <span class="">{{ ___('mainapp_common.Record Payment') }}</span>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('mainapp_common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Package') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Amount') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Payment Date') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Payment Method') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Invoice Number') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.status') }}</th>
                                    <th class="action">{{ ___('mainapp_common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @forelse ($data['payments'] as $key => $payment)
                                <tr>
                                    <td class="serial">{{ $data['payments']->firstItem() + $key }}</td>
                                    <td>{{ $payment->subscription->package->name ?? ___('mainapp_common.N/A') }}</td>
                                    <td>{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ dateFormat($payment->payment_date) }}</td>
                                    <td>{{ $payment->getPaymentMethodLabel() }}</td>
                                    <td>{{ $payment->invoice_number ?? ___('mainapp_common.N/A') }}</td>
                                    <td>
                                        @if ($payment->status == 0)
                                            <span class="badge-basic-warning-text">{{ ___('mainapp_subscriptions.Pending') }}</span>
                                        @elseif ($payment->status == 1)
                                            <span class="badge-basic-success-text">{{ ___('mainapp_subscriptions.Approved') }}</span>
                                            @if($payment->approved_at)
                                                <br><small class="text-muted">{{ dateFormat($payment->approved_at) }}</small>
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
                                        @if ($payment->isApproved())
                                            <a href="{{ route('subscription-payments.receipt', $payment->id) }}"
                                               class="btn btn-sm btn-primary" target="_blank">
                                                <i class="fa-solid fa-file-pdf"></i> {{ ___('mainapp_common.Receipt') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">{{ ___('mainapp_common.No payments found') }}</td>
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
@endsection
