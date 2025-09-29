@extends('backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection

@push('style')
    @include('backend.parent-deposits.statements.statement-style')
@endpush

@section('content')
    <div class="page-content">
        {{-- Breadcrumb Area --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">
                        <i class="fa-solid fa-file-lines me-2 text-primary"></i>
                        {{ $data['title'] }}
                    </h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('parent-statements.index') }}">{{ ___('common.statements') }}</a></li>
                        <li class="breadcrumb-item active">{{ $data['parent']->user->name }}</li>
                    </ol>
                </div>
                <div class="col-sm-6 text-end">
                    <div class="btn-group" role="group">
                        <button class="btn btn-lg ot-btn-success" onclick="exportPDF()">
                            <i class="fa-solid fa-file-pdf me-2"></i>Export PDF
                        </button>
                        <button class="btn btn-lg ot-btn-info" onclick="exportExcel()">
                            <i class="fa-solid fa-file-excel me-2"></i>Export Excel
                        </button>
                        <button class="btn btn-lg ot-btn-primary" onclick="window.print()">
                            <i class="fa-solid fa-print me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statement Content --}}
        <div class="row">
            {{-- Parent Information --}}
            <div class="col-12 mb-4">
                <div class="ot-card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fa-solid fa-user-tie me-2 text-primary"></i>
                                Parent Information
                            </h5>
                            <div>
                                <span class="badge bg-primary">
                                    {{ $data['statement']['period']['start_date']->format('M d, Y') }} - {{ $data['statement']['period']['end_date']->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fa-solid fa-user text-primary me-3" style="font-size: 18px;"></i>
                                            <div>
                                                <small class="text-muted d-block">Name</small>
                                                <strong>{{ $data['statement']['parent']->user->name }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fa-solid fa-envelope text-info me-3" style="font-size: 18px;"></i>
                                            <div>
                                                <small class="text-muted d-block">Email</small>
                                                <strong>{{ $data['statement']['parent']->user->email }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @if($data['statement']['parent']->user->phone)
                                    <div class="col-md-4">
                                        <div class="d-flex align-items-center">
                                            <i class="fa-solid fa-phone text-success me-3" style="font-size: 18px;"></i>
                                            <div>
                                                <small class="text-muted d-block">Phone</small>
                                                <strong>{{ $data['statement']['parent']->user->phone }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="mb-3">
                                        <i class="fa-solid fa-calendar-days text-warning me-2"></i>
                                        <small class="text-muted d-block">Duration</small>
                                        <strong>{{ $data['statement']['period']['duration_days'] }} days</strong>
                                    </div>
                                    <div>
                                        <i class="fa-solid fa-clock text-secondary me-2"></i>
                                        <small class="text-muted d-block">Generated</small>
                                        <strong>{{ $data['statement']['generated_at']->format('M d, Y H:i') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Balance Summary --}}
            <div class="col-md-8 mb-4">
                <div class="ot-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-chart-pie me-2 text-primary"></i>
                            Balance Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6 col-lg-3">
                                <div class="ot_crm_summeryBox2 d-flex align-items-center mb-24">
                                    <div class="icon style2">
                                        <i class="fa-solid fa-wallet text-success" style="font-size: 24px;"></i>
                                    </div>
                                    <div class="summeryContent">
                                        <h4>${{ number_format($data['statement']['balance_summary']['total_available'], 2) }}</h4>
                                        <h1>Available Balance</h1>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="ot_crm_summeryBox2 d-flex align-items-center mb-24">
                                    <div class="icon style3">
                                        <i class="fa-solid fa-lock text-warning" style="font-size: 24px;"></i>
                                    </div>
                                    <div class="summeryContent">
                                        <h4>${{ number_format($data['statement']['balance_summary']['total_reserved'], 2) }}</h4>
                                        <h1>Reserved Balance</h1>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="ot_crm_summeryBox2 d-flex align-items-center mb-24">
                                    <div class="icon">
                                        <i class="fa-solid fa-arrow-down text-info" style="font-size: 24px;"></i>
                                    </div>
                                    <div class="summeryContent">
                                        <h4>${{ number_format($data['statement']['balance_summary']['total_deposits'], 2) }}</h4>
                                        <h1>Total Deposits</h1>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="ot_crm_summeryBox2 d-flex align-items-center mb-24">
                                    <div class="icon style4">
                                        <i class="fa-solid fa-arrow-up text-danger" style="font-size: 24px;"></i>
                                    </div>
                                    <div class="summeryContent">
                                        <h4>${{ number_format($data['statement']['balance_summary']['total_withdrawals'], 2) }}</h4>
                                        <h1>Total Withdrawals</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Period Statistics --}}
            <div class="col-md-4 mb-4">
                <div class="ot-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-chart-line me-2"></i>
                            Period Statistics
                        </h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered role-table">
                            <tr>
                                <td>
                                    <i class="fa-solid fa-list text-primary me-2"></i>
                                    Total Transactions
                                </td>
                                <td class="text-end">
                                    <strong class="text-primary">{{ $data['statement']['statistics']['total_transactions'] }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fa-solid fa-arrow-down text-success me-2"></i>
                                    Deposits
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">${{ number_format($data['statement']['statistics']['total_deposits'], 2) }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fa-solid fa-arrow-up text-danger me-2"></i>
                                    Withdrawals
                                </td>
                                <td class="text-end">
                                    <strong class="text-danger">${{ number_format($data['statement']['statistics']['total_withdrawals'], 2) }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fa-solid fa-hand-holding-dollar text-info me-2"></i>
                                    Allocations
                                </td>
                                <td class="text-end">
                                    <strong class="text-info">${{ number_format($data['statement']['statistics']['total_allocations'], 2) }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fa-solid fa-undo text-warning me-2"></i>
                                    Refunds
                                </td>
                                <td class="text-end">
                                    <strong class="text-warning">${{ number_format($data['statement']['statistics']['total_refunds'], 2) }}</strong>
                                </td>
                            </tr>
                            <tr class="border-top">
                                <td>
                                    <i class="fa-solid fa-balance-scale text-dark me-2"></i>
                                    <strong>Net Change</strong>
                                </td>
                                <td class="text-end">
                                    <strong class="{{ $data['statement']['statistics']['net_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $data['statement']['statistics']['net_change'] >= 0 ? '+' : '' }}${{ number_format($data['statement']['statistics']['net_change'], 2) }}
                                    </strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Transaction History --}}
            <div class="col-12">
                <div class="ot-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-history me-2"></i>
                            Transaction History
                        </h5>
                        <span class="badge bg-primary">
                            <i class="fa-solid fa-list me-1"></i>
                            {{ $data['statement']['transactions']->count() }} transactions
                        </span>
                    </div>
                    <div class="card-body">
                        @if($data['statement']['transactions']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered role-table">
                                    <thead>
                                        <tr>
                                            <th>
                                                <i class="fa-solid fa-calendar me-1"></i>
                                                Date
                                            </th>
                                            <th>
                                                <i class="fa-solid fa-hashtag me-1"></i>
                                                Reference
                                            </th>
                                            <th>
                                                <i class="fa-solid fa-tag me-1"></i>
                                                Type
                                            </th>
                                            <th>
                                                <i class="fa-solid fa-comment me-1"></i>
                                                Description
                                            </th>
                                            <th>
                                                <i class="fa-solid fa-user-graduate me-1"></i>
                                                Student
                                            </th>
                                            <th class="text-end">
                                                <i class="fa-solid fa-dollar-sign me-1"></i>
                                                Amount
                                            </th>
                                            <th class="text-end">
                                                <i class="fa-solid fa-wallet me-1"></i>
                                                Balance
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['statement']['transactions'] as $transaction)
                                            <tr>
                                                <td>
                                                    <div>
                                                        <div><strong>{{ $transaction->transaction_date->format('M d, Y') }}</strong></div>
                                                        <div class="text-muted small">{{ $transaction->transaction_date->format('H:i') }}</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <code class="reference-code">{{ $transaction->reference_number }}</code>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $transaction->type_color }}">
                                                        <i class="fa {{ $transaction->type_icon }} me-1"></i>
                                                        {{ $transaction->getTransactionTypeLabel() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                        {{ $transaction->description }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="student-name">
                                                        <strong>{{ $transaction->getStudentName() }}</strong>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <span class="transaction-amount {{ $transaction->isPositive() ? 'positive' : 'negative' }}">
                                                        {{ $transaction->isPositive() ? '+' : '-' }}{{ $transaction->getFormattedAmount() }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <strong>{{ $transaction->getFormattedBalanceAfter() }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="empty-state">
                                <i class="fa-solid fa-inbox"></i>
                                <p>No transactions found for the selected period.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        function exportPDF() {
            var params = new URLSearchParams(window.location.search);
            params.set('format', 'pdf');
            params.set('parent_id', '{{ $data['parent']->id }}');

            window.location.href = "{{ route('parent-statements.export') }}?" + params.toString();
        }

        function exportExcel() {
            var params = new URLSearchParams(window.location.search);
            params.set('format', 'excel');
            params.set('parent_id', '{{ $data['parent']->id }}');

            window.location.href = "{{ route('parent-statements.export') }}?" + params.toString();
        }
    </script>
@endpush