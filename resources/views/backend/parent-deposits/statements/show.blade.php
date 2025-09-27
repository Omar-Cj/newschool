@extends('backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection

@section('content')
    <div class="page-content">
        {{-- Breadcrumb Area --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('parent-statements.index') }}">{{ ___('common.statements') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['parent']->user->name }}</li>
                    </ol>
                </div>
                <div class="col-sm-6 text-end">
                    <button class="btn btn-success" onclick="exportPDF()">
                        <i class="fa-solid fa-file-pdf me-2"></i>Export PDF
                    </button>
                    <button class="btn btn-info" onclick="exportExcel()">
                        <i class="fa-solid fa-file-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>

        {{-- Statement Content --}}
        <div class="row">
            {{-- Parent Information --}}
            <div class="col-12 mb-4">
                <div class="card border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa-solid fa-user me-2"></i>Parent Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ $data['statement']['parent']->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $data['statement']['parent']->user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Phone:</strong></td>
                                        <td>{{ $data['statement']['parent']->user->phone }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>Statement Period:</strong></td>
                                        <td>{{ $data['statement']['period']['start_date']->format('M d, Y') }} - {{ $data['statement']['period']['end_date']->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Duration:</strong></td>
                                        <td>{{ $data['statement']['period']['duration_days'] }} days</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Generated:</strong></td>
                                        <td>{{ $data['statement']['generated_at']->format('M d, Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Balance Summary --}}
            <div class="col-md-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa-solid fa-chart-bar me-2"></i>Balance Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <div class="card border-success">
                                    <div class="card-body text-center py-3">
                                        <h6 class="text-success mb-1">Available Balance</h6>
                                        <h4 class="text-success mb-0">${{ number_format($data['statement']['balance_summary']['total_available'], 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <div class="card border-warning">
                                    <div class="card-body text-center py-3">
                                        <h6 class="text-warning mb-1">Reserved Balance</h6>
                                        <h4 class="text-warning mb-0">${{ number_format($data['statement']['balance_summary']['total_reserved'], 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <div class="card border-info">
                                    <div class="card-body text-center py-3">
                                        <h6 class="text-info mb-1">Total Deposits</h6>
                                        <h4 class="text-info mb-0">${{ number_format($data['statement']['balance_summary']['total_deposits'], 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3 mb-3">
                                <div class="card border-danger">
                                    <div class="card-body text-center py-3">
                                        <h6 class="text-danger mb-1">Total Withdrawals</h6>
                                        <h4 class="text-danger mb-0">${{ number_format($data['statement']['balance_summary']['total_withdrawals'], 2) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Period Statistics --}}
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fa-solid fa-calculator me-2"></i>Period Statistics</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td>Total Transactions:</td>
                                <td class="text-end"><strong>{{ $data['statement']['statistics']['total_transactions'] }}</strong></td>
                            </tr>
                            <tr class="text-success">
                                <td>Deposits:</td>
                                <td class="text-end"><strong>${{ number_format($data['statement']['statistics']['total_deposits'], 2) }}</strong></td>
                            </tr>
                            <tr class="text-danger">
                                <td>Withdrawals:</td>
                                <td class="text-end"><strong>${{ number_format($data['statement']['statistics']['total_withdrawals'], 2) }}</strong></td>
                            </tr>
                            <tr class="text-info">
                                <td>Allocations:</td>
                                <td class="text-end"><strong>${{ number_format($data['statement']['statistics']['total_allocations'], 2) }}</strong></td>
                            </tr>
                            <tr class="text-primary">
                                <td>Refunds:</td>
                                <td class="text-end"><strong>${{ number_format($data['statement']['statistics']['total_refunds'], 2) }}</strong></td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Net Change:</strong></td>
                                <td class="text-end">
                                    <strong class="{{ $data['statement']['statistics']['net_change'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        ${{ number_format($data['statement']['statistics']['net_change'], 2) }}
                                    </strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Transaction History --}}
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa-solid fa-list me-2"></i>Transaction History</h5>
                        <span class="badge bg-primary">{{ $data['statement']['transactions']->count() }} transactions</span>
                    </div>
                    <div class="card-body">
                        @if($data['statement']['transactions']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Student</th>
                                            <th class="text-end">Amount</th>
                                            <th class="text-end">Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['statement']['transactions'] as $transaction)
                                            <tr>
                                                <td>{{ $transaction->transaction_date->format('M d, Y H:i') }}</td>
                                                <td>
                                                    <small class="text-muted">{{ $transaction->reference_number }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $transaction->type_color }}">
                                                        <i class="fa {{ $transaction->type_icon }} me-1"></i>
                                                        {{ $transaction->getTransactionTypeLabel() }}
                                                    </span>
                                                </td>
                                                <td>{{ $transaction->description }}</td>
                                                <td>{{ $transaction->getStudentName() }}</td>
                                                <td class="text-end">
                                                    <span class="text-{{ $transaction->isPositive() ? 'success' : 'danger' }}">
                                                        {{ $transaction->isPositive() ? '+' : '-' }}{{ $transaction->getFormattedAmount() }}
                                                    </span>
                                                </td>
                                                <td class="text-end">{{ $transaction->getFormattedBalanceAfter() }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No transactions found for the selected period.</p>
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