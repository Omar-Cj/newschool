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
                        <li class="breadcrumb-item">{{ ___('mainapp_common.Record Payment') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <div class="row">
            <div class="col-xl-12">
                <div class="card ot-card">
                    <div class="card-header">
                        <h4>{{ ___('mainapp_common.Record Payment') }}</h4>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('subscription-payments.store') }}" method="post" class="form-horizontal">
                            @csrf

                            <input type="hidden" name="school_id" value="{{ $data['school']->id }}">
                            <input type="hidden" name="subscription_id" value="{{ $data['subscription']->id ?? '' }}">

                            <!-- School Information (Read-only) -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="alert alert-info">
                                        <h5><i class="fa-solid fa-school"></i> {{ $data['school']->name }}</h5>
                                        <p class="mb-1"><strong>{{ ___('mainapp_common.Email') }}:</strong> {{ $data['school']->email }}</p>
                                        <p class="mb-1"><strong>{{ ___('mainapp_common.Phone') }}:</strong> {{ $data['school']->phone }}</p>
                                        @if($data['subscription'] && $data['subscription']->package)
                                            <p class="mb-1"><strong>{{ ___('mainapp_subscriptions.Package') }}:</strong> {{ $data['subscription']->package->name }}</p>
                                            <p class="mb-0"><strong>{{ ___('mainapp_common.Price') }}:</strong> {{ number_format($data['subscription']->package->price, 2) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Amount -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="amount" class="form-label">
                                            {{ ___('mainapp_common.Amount') }} <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" step="0.01" min="0" class="form-control ot-input @error('amount') is-invalid @enderror"
                                               id="amount" name="amount"
                                               value="{{ old('amount', $data['paymentAmount'] ?? '') }}"
                                               placeholder="0.00" required>
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_date" class="form-label">
                                            {{ ___('mainapp_common.Payment Date') }} <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" class="form-control ot-input @error('payment_date') is-invalid @enderror"
                                               id="payment_date" name="payment_date"
                                               value="{{ old('payment_date', date('Y-m-d')) }}"
                                               max="{{ date('Y-m-d') }}" required>
                                        @error('payment_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_method" class="form-label">
                                            {{ ___('mainapp_common.Payment Method') }} <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select ot-input @error('payment_method') is-invalid @enderror"
                                                id="payment_method" name="payment_method" required>
                                            <option value="">{{ ___('mainapp_common.Select') }}</option>
                                            @foreach($data['paymentMethods'] as $value => $label)
                                                <option value="{{ $value }}" {{ old('payment_method') == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('payment_method')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference_number" class="form-label">
                                            {{ ___('mainapp_common.Reference Number') }}
                                        </label>
                                        <input type="text" class="form-control ot-input @error('reference_number') is-invalid @enderror"
                                               id="reference_number" name="reference_number"
                                               value="{{ old('reference_number') }}"
                                               placeholder="e.g., Receipt #, Check #" maxlength="255">
                                        @error('reference_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Transaction ID -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="transaction_id" class="form-label">
                                            {{ ___('mainapp_common.Transaction ID') }}
                                        </label>
                                        <input type="text" class="form-control ot-input @error('transaction_id') is-invalid @enderror"
                                               id="transaction_id" name="transaction_id"
                                               value="{{ old('transaction_id') }}"
                                               placeholder="e.g., Gateway transaction ID" maxlength="255">
                                        <div class="form-text">
                                            {{ ___('mainapp_common.For online payments, enter the transaction ID from payment gateway') }}
                                        </div>
                                        @error('transaction_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <i class="fa-solid fa-info-circle"></i>
                                {{ ___('mainapp_common.This payment will be recorded as PENDING and requires admin approval before the subscription is extended.') }}
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-lg ot-btn-primary">
                                    <i class="fa-solid fa-save"></i> {{ ___('mainapp_common.Record Payment') }}
                                </button>
                                <a href="{{ route('subscription-payments.history', $data['school']->id) }}" class="btn btn-lg btn-secondary">
                                    <i class="fa-solid fa-times"></i> {{ ___('mainapp_common.Cancel') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
