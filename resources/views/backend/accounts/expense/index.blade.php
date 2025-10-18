@extends('backend.master')
@section('title')
    {{ @$data['title'] }}
@endsection
@section('content')
    <div class="page-content">

        {{-- bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        {{-- Filtering Section S t a r t --}}
        <div class="col-12">
            <div class="card ot-card mb-24 position-relative z_1">
                <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                    <h3 class="mb-0">{{ ___('common.Filtering') }}</h3>

                    <div class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">
                        <!-- Category Filter -->
                        <div class="single_large_selectBox">
                            <select id="categoryFilter" class="form-select nice-select niceSelect bordered_style wide" name="category_id">
                                <option value="">{{ ___('account.all_categories') }}</option>
                                @foreach ($data['categories'] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date Filter -->
                        <div class="single_large_selectBox">
                            <input type="date" class="form-control ot-input" id="startDateFilter" placeholder="{{ ___('account.start_date') }}">
                        </div>

                        <!-- End Date Filter -->
                        <div class="single_large_selectBox">
                            <input type="date" class="form-control ot-input" id="endDateFilter" placeholder="{{ ___('account.end_date') }}">
                        </div>

                        <!-- Min Amount Filter -->
                        <div class="single_large_selectBox">
                            <input type="number" class="form-control ot-input" id="minAmountFilter" placeholder="{{ ___('account.min_amount') }}" step="0.01" min="0">
                        </div>

                        <!-- Max Amount Filter -->
                        <div class="single_large_selectBox">
                            <input type="number" class="form-control ot-input" id="maxAmountFilter" placeholder="{{ ___('account.max_amount') }}" step="0.01" min="0">
                        </div>

                        <!-- Keyword Search -->
                        <div class="single_large_selectBox">
                            <input class="form-control ot-input" id="keywordFilter" placeholder="{{ ___('common.search_keyword') }}">
                        </div>

                        <!-- Clear Filters Button -->
                        <button class="btn btn-lg ot-btn-primary" id="clearFilters" type="button">
                            {{ ___('common.Clear') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- Filtering Section E n d --}}

        <!--  table content start -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $data['title'] }}</h4>
                    @if (hasPermission('expense_create'))
                        <a href="{{ route('expense.create') }}" class="btn btn-lg ot-btn-primary">
                            <span><i class="fa-solid fa-plus"></i> </span>
                            <span class="">{{ ___('common.add') }}</span>
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="expensesTable" class="table table-bordered expense-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('common.name') }}</th>
                                    <th class="purchase">{{ ___('account.expense_category') }}</th>
                                    <th class="purchase">{{ ___('account.date') }}</th>
                                    <th class="purchase">{{ ___('account.invoice_number') }}</th>
                                    <th class="purchase">{{ ___('account.amount') }} ({{ Setting('currency_symbol') }})</th>
                                    <th class="purchase">{{ ___('common.document') }}</th>
                                    @if (hasPermission('expense_update') || hasPermission('expense_delete'))
                                        <th class="action">{{ ___('common.action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <!--  table end -->
                </div>
            </div>
        </div>
        <!--  table content end -->

    </div>
@endsection

@push('script')
    @include('backend.partials.delete-ajax')

    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- Custom DataTables Theme Integration -->
    <style>
        /* Restore original table-content.table-basic styling */
        .table-content.table-basic #expensesTable {
            font-size: 14px;
        }

        .table-content.table-basic #expensesTable thead th {
            background-color: var(--ot-bg-primary, #f8f9fa);
            color: var(--ot-text-body, #1a1d1f);
            border-bottom: 1px solid var(--ot-border-color, #eaeaea);
            font-weight: 600;
            font-size: 14px;
            padding: 12px 8px;
            vertical-align: middle;
        }

        .table-content.table-basic #expensesTable tbody td {
            padding: 10px 8px;
            font-size: 13px;
            border-bottom: 1px solid var(--ot-border-light, #f5f5f5);
            vertical-align: middle;
        }

        .table-content.table-basic #expensesTable tbody tr:nth-of-type(odd) {
            background-color: var(--ot-bg-table-row, #fafafa);
        }

        /* DataTables controls styling to match theme */
        .table-content .dataTables_wrapper .dataTables_length,
        .table-content .dataTables_wrapper .dataTables_filter,
        .table-content .dataTables_wrapper .dataTables_info {
            font-size: 14px;
            color: var(--ot-text-body, #1a1d1f);
            margin-bottom: 15px;
        }

        .table-content .dataTables_wrapper .dataTables_length select,
        .table-content .dataTables_wrapper .dataTables_filter input {
            border: 1px solid var(--ot-border-color, #eaeaea);
            border-radius: 4px;
            padding: 6px 10px;
            font-size: 13px;
            background-color: var(--ot-bg-white, #ffffff);
            color: var(--ot-text-body, #1a1d1f);
        }

        .table-content .dataTables_wrapper .dataTables_length select:focus,
        .table-content .dataTables_wrapper .dataTables_filter input:focus {
            border-color: var(--ot-primary-color, #5764c6);
            box-shadow: 0 0 0 0.2rem rgba(87, 100, 198, 0.25);
            outline: 0;
        }

        /* DataTables wrapper layout improvements */
        .table-content .dataTables_wrapper .row {
            margin: 0;
        }

        .table-content .dataTables_wrapper .row [class*="col-"] {
            padding-left: 0;
            padding-right: 15px;
        }

        .table-content .dataTables_wrapper .dataTables_filter {
            text-align: right;
        }

        .table-content .dataTables_wrapper .dataTables_info {
            color: var(--ot-text-muted, #9c9c9c);
            font-size: 13px;
            padding-top: 8px;
        }

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

        /* DataTables processing indicator styling */
        .table-content .dataTables_wrapper .dataTables_processing {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid var(--ot-border-color, #eaeaea);
            border-radius: 4px;
            color: var(--ot-text-body, #1a1d1f);
            font-size: 14px;
        }

        /* Custom loading spinner to match pagination color scheme */
        .ot-loading-spinner {
            border-color: var(--ot-primary-color, #5764c6) !important;
            border-right-color: transparent !important;
        }

        /* Animated loading dots with pagination color */
        .ot-loading-dots::after {
            content: '';
            animation: dots 1.5s steps(4, end) infinite;
            color: var(--ot-primary-color, #5764c6);
            font-size: 16px;
            font-weight: bold;
        }

        @keyframes dots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }

        /* Ensure table header sorting indicators match theme */
        .table-content.table-basic #expensesTable thead th.sorting,
        .table-content.table-basic #expensesTable thead th.sorting_asc,
        .table-content.table-basic #expensesTable thead th.sorting_desc {
            cursor: pointer;
            position: relative;
        }

        .table-content.table-basic #expensesTable thead th.sorting:after,
        .table-content.table-basic #expensesTable thead th.sorting_asc:after,
        .table-content.table-basic #expensesTable thead th.sorting_desc:after {
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ot-text-muted, #9c9c9c);
            font-size: 12px;
        }

        .table-content.table-basic #expensesTable thead th.sorting:after {
            content: "\f0dc";
        }

        .table-content.table-basic #expensesTable thead th.sorting_asc:after {
            content: "\f0de";
            color: var(--ot-primary-color, #5764c6);
        }

        .table-content.table-basic #expensesTable thead th.sorting_desc:after {
            content: "\f0dd";
            color: var(--ot-primary-color, #5764c6);
        }
    </style>

    <script>
        let expensesTable;
        let ajaxUrl = '{{ route("expense.ajaxData") }}';

        $(document).ready(function() {
            // Initialize DataTables
            initializeExpensesTable();

            // Setup filter event handlers
            setupFilterHandlers();
        });

        /**
         * Initialize DataTables with server-side processing
         */
        function initializeExpensesTable() {
            expensesTable = $('#expensesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: ajaxUrl,
                    type: 'GET',
                    data: function(d) {
                        // Add custom filters to DataTables request
                        d.category_id = $('#categoryFilter').val();
                        d.start_date = $('#startDateFilter').val();
                        d.end_date = $('#endDateFilter').val();
                        d.min_amount = $('#minAmountFilter').val();
                        d.max_amount = $('#maxAmountFilter').val();
                        d.keyword = $('#keywordFilter').val();
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX error:', error, thrown);
                        showErrorMessage('Failed to load expense data. Please refresh the page.');
                    }
                },
                // Configure DOM structure for better theme integration
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                columns: [
                    { data: 0, name: 'id', orderable: false, searchable: false, width: '5%' },
                    { data: 1, name: 'name', orderable: true, searchable: true },
                    { data: 2, name: 'expense_category_id', orderable: true, searchable: false },
                    { data: 3, name: 'date', orderable: true, searchable: false },
                    { data: 4, name: 'invoice_number', orderable: true, searchable: true },
                    { data: 5, name: 'amount', orderable: true, searchable: false },
                    { data: 6, name: 'upload_id', orderable: false, searchable: false },
                    @if (hasPermission('expense_update') || hasPermission('expense_delete'))
                    { data: 7, name: 'actions', orderable: false, searchable: false, width: '10%' }
                    @endif
                ],
                order: [[3, 'desc']], // Order by date descending by default
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                searchDelay: 300,
                // Enhanced language configuration
                language: {
                    processing: '<div class="d-flex align-items-center justify-content-center"><div class="spinner-border ot-loading-spinner me-2" role="status"><span class="visually-hidden">Loading...</span></div><span class="ot-loading-dots"></span></div>',
                    emptyTable: '<div class="text-center gray-color p-5"><img src="{{ asset("images/no_data.svg") }}" alt="" class="mb-primary" width="100"><p class="mb-0 text-center">{{ ___("common.no_data_available") }}</p><p class="mb-0 text-center text-secondary font-size-90">{{ ___("common.please_add_new_entity_regarding_this_table") }}</p></div>',
                    zeroRecords: '<div class="text-center gray-color p-5"><img src="{{ asset("images/no_data.svg") }}" alt="" class="mb-primary" width="100"><p class="mb-0 text-center">No matching records found</p><p class="mb-0 text-center text-secondary font-size-90">Try adjusting your search filters</p></div>',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '(filtered from _MAX_ total entries)',
                    search: 'Search:',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                },
                // Improve responsiveness
                responsive: false,
                scrollX: false,
                autoWidth: false,
                // Theme integration
                stateSave: false,
                drawCallback: function(settings) {
                    // Re-initialize any tooltips or other UI elements after table draw
                    $('[data-bs-toggle="tooltip"]').tooltip();

                    // Apply theme classes to pagination
                    $('.dataTables_paginate .pagination').addClass('ot-pagination');
                }
            });
        }

        /**
         * Setup filter event handlers with debouncing for real-time filtering
         */
        function setupFilterHandlers() {
            // Category filter change handler
            $('#categoryFilter').on('change', function() {
                expensesTable.ajax.reload();
            });

            // Date filters change handlers
            $('#startDateFilter, #endDateFilter').on('change', function() {
                expensesTable.ajax.reload();
            });

            // Amount filters with debounce (300ms)
            let amountTimeout;
            $('#minAmountFilter, #maxAmountFilter').on('input', function() {
                clearTimeout(amountTimeout);
                amountTimeout = setTimeout(function() {
                    expensesTable.ajax.reload();
                }, 300);
            });

            // Keyword filter with debounce (300ms)
            let keywordTimeout;
            $('#keywordFilter').on('input', function() {
                clearTimeout(keywordTimeout);
                keywordTimeout = setTimeout(function() {
                    expensesTable.ajax.reload();
                }, 300);
            });

            // Clear filters button
            $('#clearFilters').on('click', function() {
                // Reset all filter inputs
                $('#categoryFilter').val('').trigger('change');
                $('#startDateFilter').val('');
                $('#endDateFilter').val('');
                $('#minAmountFilter').val('');
                $('#maxAmountFilter').val('');
                $('#keywordFilter').val('');

                // Clear DataTables built-in search and reload
                expensesTable.search('').ajax.reload();
            });
        }

        /**
         * Display error message to user
         */
        function showErrorMessage(message) {
            if (typeof Toast !== 'undefined' && Toast.fire) {
                Toast.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            } else {
                alert(message);
            }
        }

        /**
         * Override delete_row function to refresh DataTable after deletion
         */
        function delete_row(url, id) {
            Swal.fire({
                title: '{{ ___("alert.are_you_sure") }}',
                text: '{{ ___("alert.you_wont_be_able_to_revert_this") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '{{ ___("alert.yes_delete_it") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: baseUrl + '/' + url + '/' + id,
                        type: 'DELETE',
                        dataType: 'json',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response[1] === 'success') {
                                Swal.fire(response[2], response[0], response[1]);
                                // Refresh DataTable instead of page reload
                                if (typeof expensesTable !== 'undefined') {
                                    expensesTable.ajax.reload(null, false);
                                }
                            } else {
                                Swal.fire(response[2], response[0], response[1]);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Delete error:', error);
                            Swal.fire('Error!', 'An error occurred while deleting.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
