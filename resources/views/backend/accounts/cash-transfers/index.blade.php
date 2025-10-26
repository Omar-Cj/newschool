@extends('backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection

@section('content')
    <div class="page-content">
        {{-- Breadcrumb Area Start --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="#">{{ ___('account.Accounts') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- Breadcrumb Area End --}}

        {{-- Filters Section --}}
        @include('backend.accounts.cash-transfers.partials.filters')

        {{-- Table Content Start --}}
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $data['title'] }}</h4>
                    @if (hasPermission('cash_transfer_create'))
                        <a href="{{ route('cash-transfers.create') }}" class="btn btn-lg ot-btn-primary">
                            <span><i class="fa-solid fa-plus"></i> </span>
                            <span class="">{{ ___('common.add') }}</span>
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered class-table" id="cash-transfers-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('cash_transfer.date_transferred') }}</th>
                                    <th class="purchase">{{ ___('cash_transfer.transferred_by') }}</th>
                                    <th class="purchase">{{ ___('cash_transfer.journal') }}</th>
                                    <th class="purchase">{{ ___('cash_transfer.transferred_amount') }} ({{ Setting('currency_symbol') }})</th>
                                    <th class="purchase">{{ ___('cash_transfer.approved_by') }}</th>
                                    <th class="purchase">{{ ___('cash_transfer.date_approved') }}</th>
                                    <th class="purchase">{{ ___('cash_transfer.status') }}</th>
                                    <th class="action">{{ ___('common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                {{-- DataTable will populate this via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Table Content End --}}

        {{-- Modals --}}
        @include('backend.accounts.cash-transfers.partials.view-modal')
        @include('backend.accounts.cash-transfers.partials.action-modals')
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">

    <style>
        /* Custom pagination styling to match .ot-pagination */
        .table-content .dataTables_wrapper .dataTables_paginate {
            margin-top: 20px;
        }

        .table-content .dataTables_wrapper .dataTables_paginate .pagination {
            justify-content: center;
            margin: 0;
        }

        .table-content .dataTables_wrapper .dataTables_paginate .page-item .page-link {
            background-color: var(--ot-bg-table-pagination, #ffffff);
            color: var(--ot-text-table-pagination, #1a1d1f);
            border: 1px solid var(--ot-border-table-pagination, #eaeaea);
            padding: 8px 12px;
            font-size: 13px;
            border-radius: 4px;
            margin: 0 2px;
        }

        .table-content .dataTables_wrapper .dataTables_paginate .page-item .page-link:hover {
            background-color: var(--ot-primary-color, #5764c6);
            color: #ffffff;
            border-color: var(--ot-primary-color, #5764c6);
        }

        .table-content .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
            background-color: var(--ot-primary-color, #5764c6) !important;
            color: #ffffff !important;
            border-color: var(--ot-primary-color, #5764c6) !important;
            box-shadow: none;
        }

        .table-content .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
            background-color: var(--ot-bg-table-pagination, #ffffff);
            color: var(--ot-text-muted, #9c9c9c);
            border-color: var(--ot-border-table-pagination, #eaeaea);
        }

        /* DataTables search field alignment - positioned on right */
        .table-content .dataTables_wrapper .dataTables_filter {
            text-align: right;
        }

        .table-content .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--ot-border-table-pagination, #eaeaea);
            border-radius: 4px;
            padding: 6px 12px;
            font-size: 13px;
        }

        .table-content .dataTables_wrapper .dataTables_filter input:focus {
            outline: none;
            border-color: var(--ot-primary-color, #5764c6);
            box-shadow: 0 0 0 0.2rem rgba(87, 100, 198, 0.25);
        }

        /* DataTables processing indicator styling */
        .dataTables_processing {
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--ot-primary-color, #5764c6);
            color: var(--ot-primary-color, #5764c6);
            padding: 1rem;
            border-radius: 4px;
        }

        /* Table hover effects */
        .table-content .table tbody tr:hover {
            background-color: rgba(87, 100, 198, 0.05);
            transition: background-color 0.3s ease;
        }
    </style>
@endpush

@push('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // CRITICAL: Define configuration BEFORE loading cash-transfers.js
        window.cashTransferConfig = {
            apiBaseUrl: '{{ url('/cash-transfers/ajax-data') }}',
            statisticsUrl: '{{ url('/cash-transfers/statistics') }}',
            showUrl: '{{ url('/cash-transfers/:id') }}',
            deleteUrl: '{{ url('/api/cash-transfers/:id') }}',
            approveUrl: '{{ url('/api/cash-transfers/:id/approve') }}',
            rejectUrl: '{{ url('/api/cash-transfers/:id/reject') }}',
            journalsUrl: '{{ url('/journals-data') }}',
            currencySymbol: '{{ Setting('currency_symbol') }}',
            canApprove: {{ hasPermission('cash_transfer_approve') ? 'true' : 'false' }},
            canReject: {{ hasPermission('cash_transfer_reject') ? 'true' : 'false' }},
            canDelete: {{ hasPermission('cash_transfer_delete') ? 'true' : 'false' }},
            isSuperAdmin: {{ auth()->user()->role_id == 1 ? 'true' : 'false' }},
            translations: {
                pending: '{{ ___('cash_transfer.pending') }}',
                approved: '{{ ___('cash_transfer.approved') }}',
                rejected: '{{ ___('cash_transfer.rejected') }}',
                view: '{{ ___('cash_transfer.view_details') }}',
                approve: '{{ ___('cash_transfer.approve') }}',
                reject: '{{ ___('cash_transfer.reject') }}',
                delete: '{{ ___('common.delete') }}',
                noData: '{{ ___('common.no_data_available') }}',
                loading: '{{ ___('cash_transfer.loading') }}',
                confirmApprove: '{{ ___('cash_transfer.confirm_approve') }}',
                confirmReject: '{{ ___('cash_transfer.confirm_reject') }}',
                confirmDelete: '{{ ___('cash_transfer.confirm_delete') }}'
            }
        };
    </script>

    <!-- Now load cash-transfers.js AFTER configuration is defined -->
    <!-- Fixed syntax errors in error handlers -->
    <script src="{{ asset('backend/js/cash-transfers.js') }}?v={{ time() }}&fix=1"></script>
@endpush
