@extends('backend.master')

@section('title')
    {{ ___('fees.receipts') ?? 'Receipts' }}
@endsection

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

    {{-- Filtering Section Start --}}
    <div class="col-12">
        <div class="card ot-card mb-24 position-relative z_1">
            <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                <h3 class="mb-0">{{ ___('common.Filtering') }}</h3>

                <div class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">
                    <!-- Student Search -->
                    <div class="single_large_selectBox">
                        <input class="form-control ot-input" id="studentSearchFilter"
                               placeholder="{{ ___('common.search') }} {{ ___('student_info.student_name') }}, {{ ___('fees.receipt_no') }}">
                    </div>

                    <!-- From Date Filter -->
                    <div class="single_large_selectBox">
                        <input type="date" class="form-control ot-input" id="fromDateFilter"
                               placeholder="{{ ___('fees.from_date') }}">
                    </div>

                    <!-- To Date Filter -->
                    <div class="single_large_selectBox">
                        <input type="date" class="form-control ot-input" id="toDateFilter"
                               placeholder="{{ ___('fees.to_date') }}">
                    </div>

                    <!-- Payment Method Filter -->
                    <div class="single_large_selectBox">
                        <select id="paymentMethodFilter" class="form-select nice-select niceSelect bordered_style wide">
                            <option value="">{{ ___('fees.all_payment_methods') }}</option>
                            @foreach($availableMethods as $methodValue => $methodLabel)
                                <option value="{{ $methodValue }}">{{ ___($methodLabel) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Collector Filter -->
                    <div class="single_large_selectBox">
                        <select id="collectorFilter" class="form-select nice-select niceSelect bordered_style wide">
                            <option value="">{{ ___('fees.all_collectors') }}</option>
                            @foreach($collectors as $collector)
                                <option value="{{ $collector->id }}">{{ $collector->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Family Payments Only Checkbox -->
                    <div class="single_large_selectBox d-flex align-items-center">
                        <label class="form-check-label mb-0">
                            <input type="checkbox" id="familyPaymentsFilter" class="form-check-input me-2">
                            <i class="fas fa-users"></i> {{ ___('fees.family_payments_only') ?? 'Family Payments' }}
                        </label>
                    </div>

                    <!-- Clear Filters Button -->
                    <button class="btn btn-lg ot-btn-primary" id="clearFilters" type="button">
                        {{ ___('common.Clear') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{-- Filtering Section End --}}

    <!--  table content start -->
    <div class="table-content table-basic mt-20">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ ___('fees.receipts') ?? 'Receipts' }}</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="receiptsTable" class="table table-bordered receipts-table">
                        <thead class="thead">
                            <tr>
                                <th class="serial">{{ ___('common.sr_no') }}</th>
                                <th class="purchase">{{ ___('fees.receipt_no') }}</th>
                                <th class="purchase">{{ ___('student_info.student_name') }}</th>
                                <th class="purchase">{{ ___('academic.class') }} ({{ ___('academic.section') }})</th>
                                <th class="purchase">{{ ___('fees.amount_paid') }} ({{ $currency }})</th>
                                <th class="purchase">{{ ___('fees.discount') }} ({{ $currency }})</th>
                                <th class="purchase">{{ ___('fees.payment_date') }}</th>
                                <th class="purchase">{{ ___('fees.payment_method') }}</th>
                                <th class="purchase">{{ ___('fees.collected_by') }}</th>
                                <th class="purchase">{{ ___('common.status') }}</th>
                                <th class="action">{{ ___('common.action') }}</th>
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
    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- Custom DataTables Theme Integration -->
    <style>
        /* Restore original table-content.table-basic styling */
        .table-content.table-basic #receiptsTable {
            font-size: 14px;
        }

        .table-content.table-basic #receiptsTable thead th {
            background-color: var(--ot-bg-primary, #f8f9fa);
            color: var(--ot-text-body, #1a1d1f);
            border-bottom: 1px solid var(--ot-border-color, #eaeaea);
            font-weight: 600;
            font-size: 14px;
            padding: 12px 8px;
            vertical-align: middle;
        }

        .table-content.table-basic #receiptsTable tbody td {
            padding: 10px 8px;
            font-size: 13px;
            border-bottom: 1px solid var(--ot-border-light, #f5f5f5);
            vertical-align: middle;
        }

        .table-content.table-basic #receiptsTable tbody tr:nth-of-type(odd) {
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
        .table-content.table-basic #receiptsTable thead th.sorting,
        .table-content.table-basic #receiptsTable thead th.sorting_asc,
        .table-content.table-basic #receiptsTable thead th.sorting_desc {
            cursor: pointer;
            position: relative;
        }

        .table-content.table-basic #receiptsTable thead th.sorting:after,
        .table-content.table-basic #receiptsTable thead th.sorting_asc:after,
        .table-content.table-basic #receiptsTable thead th.sorting_desc:after {
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ot-text-muted, #9c9c9c);
            font-size: 12px;
        }

        .table-content.table-basic #receiptsTable thead th.sorting:after {
            content: "\f0dc";
        }

        .table-content.table-basic #receiptsTable thead th.sorting_asc:after {
            content: "\f0de";
            color: var(--ot-primary-color, #5764c6);
        }

        .table-content.table-basic #receiptsTable thead th.sorting_desc:after {
            content: "\f0dd";
            color: var(--ot-primary-color, #5764c6);
        }
    </style>

    <script>
        let receiptsTable;
        let ajaxUrl = '{{ route("fees.receipt.ajaxData") }}';

        $(document).ready(function() {
            // Restore filter state from sessionStorage before initialization
            restoreFilterState();

            // Initialize DataTables with state persistence
            initializeReceiptsTable();

            // Setup filter event handlers
            setupFilterHandlers();

            // Save state when navigating away
            $(window).on('beforeunload', function() {
                saveFilterState();
            });
        });

        /**
         * Initialize DataTables with server-side processing and state persistence
         */
        function initializeReceiptsTable() {
            receiptsTable = $('#receiptsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: ajaxUrl,
                    type: 'GET',
                    data: function(d) {
                        // Add custom filters to DataTables request
                        d.student_search = $('#studentSearchFilter').val();
                        d.from_date = $('#fromDateFilter').val();
                        d.to_date = $('#toDateFilter').val();
                        d.payment_method = $('#paymentMethodFilter').val();
                        d.collector_id = $('#collectorFilter').val();
                        d.family_payments_only = $('#familyPaymentsFilter').is(':checked') ? '1' : '0';
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX error:', error, thrown);
                        showErrorMessage('Failed to load receipt data. Please refresh the page.');
                    }
                },
                // Configure DOM structure for better theme integration
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                columns: [
                    { data: 0, name: 'id', orderable: false, searchable: false, width: '5%' },
                    { data: 1, name: 'receipt_number', orderable: true, searchable: true },
                    { data: 2, name: 'student_name', orderable: true, searchable: true },
                    { data: 3, name: 'class', orderable: true, searchable: false },
                    { data: 4, name: 'total_amount', orderable: true, searchable: false },
                    { data: 5, name: 'discount_amount', orderable: true, searchable: false },
                    { data: 6, name: 'payment_date', orderable: true, searchable: false },
                    { data: 7, name: 'payment_method', orderable: true, searchable: false },
                    { data: 8, name: 'collected_by', orderable: false, searchable: false },
                    { data: 9, name: 'payment_status', orderable: true, searchable: false },
                    { data: 10, name: 'actions', orderable: false, searchable: false, width: '10%' }
                ],
                order: [[6, 'desc']], // Order by payment_date descending by default
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                searchDelay: 300,
                // Enable state saving for 1 hour
                stateSave: true,
                stateDuration: 60 * 60, // 1 hour in seconds
                // Restore custom filter states
                stateLoadParams: function(settings, data) {
                    // Restore custom filters from DataTables state
                    if (data.customFilters) {
                        $('#studentSearchFilter').val(data.customFilters.studentSearch || '');
                        $('#fromDateFilter').val(data.customFilters.fromDate || '');
                        $('#toDateFilter').val(data.customFilters.toDate || '');
                        $('#paymentMethodFilter').val(data.customFilters.paymentMethod || '');
                        $('#collectorFilter').val(data.customFilters.collectorId || '');
                        $('#familyPaymentsFilter').prop('checked', data.customFilters.familyPayments || false);

                        // Trigger change events to update UI components (like nice-select)
                        setTimeout(function() {
                            $('#paymentMethodFilter').trigger('change');
                            $('#collectorFilter').trigger('change');
                        }, 100);
                    }
                },
                // Save custom filter states
                stateSaveParams: function(settings, data) {
                    // Save custom filters to DataTables state
                    data.customFilters = {
                        studentSearch: $('#studentSearchFilter').val(),
                        fromDate: $('#fromDateFilter').val(),
                        toDate: $('#toDateFilter').val(),
                        paymentMethod: $('#paymentMethodFilter').val(),
                        collectorId: $('#collectorFilter').val(),
                        familyPayments: $('#familyPaymentsFilter').is(':checked')
                    };
                },
                // Enhanced language configuration
                language: {
                    processing: '<div class="d-flex align-items-center justify-content-center"><div class="spinner-border ot-loading-spinner me-2" role="status"><span class="visually-hidden">Loading...</span></div><span class="ot-loading-dots"></span></div>',
                    emptyTable: '<div class="text-center gray-color p-5"><img src="{{ asset("images/no_data.svg") }}" alt="" class="mb-primary" width="100"><p class="mb-0 text-center">{{ ___("common.no_data_available") }}</p><p class="mb-0 text-center text-secondary font-size-90">{{ ___("fees.no_receipts_found") ?? "No receipts found" }}</p></div>',
                    zeroRecords: '<div class="text-center gray-color p-5"><img src="{{ asset("images/no_data.svg") }}" alt="" class="mb-primary" width="100"><p class="mb-0 text-center">No matching receipts found</p><p class="mb-0 text-center text-secondary font-size-90">Try adjusting your search filters</p></div>',
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
         * Setup filter event handlers with debouncing and state persistence
         */
        function setupFilterHandlers() {
            // Student search filter with debounce (300ms)
            let studentSearchTimeout;
            $('#studentSearchFilter').on('input', function() {
                clearTimeout(studentSearchTimeout);
                studentSearchTimeout = setTimeout(function() {
                    saveFilterState(); // Save state before reload
                    receiptsTable.ajax.reload();
                }, 300);
            });

            // Date filters change handlers
            $('#fromDateFilter, #toDateFilter').on('change', function() {
                saveFilterState(); // Save state before reload
                receiptsTable.ajax.reload();
            });

            // Payment method filter change handler
            $('#paymentMethodFilter').on('change', function() {
                saveFilterState(); // Save state before reload
                receiptsTable.ajax.reload();
            });

            // Collector filter change handler
            $('#collectorFilter').on('change', function() {
                saveFilterState(); // Save state before reload
                receiptsTable.ajax.reload();
            });

            // Family payments checkbox change handler
            $('#familyPaymentsFilter').on('change', function() {
                saveFilterState(); // Save state before reload
                receiptsTable.ajax.reload();
            });

            // Clear filters button
            $('#clearFilters').on('click', function() {
                // Reset all filter inputs
                $('#studentSearchFilter').val('');
                $('#fromDateFilter').val('');
                $('#toDateFilter').val('');
                $('#paymentMethodFilter').val('').trigger('change');
                $('#collectorFilter').val('').trigger('change');
                $('#familyPaymentsFilter').prop('checked', false);

                // Clear saved state
                sessionStorage.removeItem('receiptFilters');

                // Clear URL parameters
                const url = new URL(window.location);
                url.search = '';
                window.history.replaceState({}, '', url);

                // Clear DataTables built-in search and reload
                receiptsTable.search('').ajax.reload();
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
         * Print receipt function
         */
        function printReceipt(receiptId) {
            const printUrl = '{{ route("fees.receipt.individual", ":id") }}'.replace(':id', receiptId) + '?print=1';
            window.open(printUrl, '_blank');
        }

        /**
         * Save filter state to sessionStorage (backup to DataTables state)
         */
        function saveFilterState() {
            try {
                const state = {
                    studentSearch: $('#studentSearchFilter').val(),
                    fromDate: $('#fromDateFilter').val(),
                    toDate: $('#toDateFilter').val(),
                    paymentMethod: $('#paymentMethodFilter').val(),
                    collectorId: $('#collectorFilter').val(),
                    familyPayments: $('#familyPaymentsFilter').is(':checked'),
                    timestamp: Date.now()
                };
                sessionStorage.setItem('receiptFilters', JSON.stringify(state));

                // Also save to URL parameters for sharing/bookmarking
                updateURLParameters(state);
            } catch (e) {
                console.error('Failed to save filter state:', e);
            }
        }

        /**
         * Restore filter state from sessionStorage or URL parameters
         */
        function restoreFilterState() {
            try {
                // First try URL parameters (highest priority)
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('student_search') || urlParams.has('from_date')) {
                    $('#studentSearchFilter').val(urlParams.get('student_search') || '');
                    $('#fromDateFilter').val(urlParams.get('from_date') || '');
                    $('#toDateFilter').val(urlParams.get('to_date') || '');
                    $('#paymentMethodFilter').val(urlParams.get('payment_method') || '');
                    $('#collectorFilter').val(urlParams.get('collector_id') || '');
                    $('#familyPaymentsFilter').prop('checked', urlParams.get('family_payments') === '1');
                    return;
                }

                // Then try sessionStorage (fallback)
                const saved = sessionStorage.getItem('receiptFilters');
                if (saved) {
                    const state = JSON.parse(saved);

                    // Check if state is not too old (max 1 hour)
                    const maxAge = 60 * 60 * 1000; // 1 hour in milliseconds
                    if (state.timestamp && (Date.now() - state.timestamp) < maxAge) {
                        $('#studentSearchFilter').val(state.studentSearch || '');
                        $('#fromDateFilter').val(state.fromDate || '');
                        $('#toDateFilter').val(state.toDate || '');
                        $('#paymentMethodFilter').val(state.paymentMethod || '');
                        $('#collectorFilter').val(state.collectorId || '');
                        $('#familyPaymentsFilter').prop('checked', state.familyPayments || false);
                    } else {
                        // Clear old state
                        sessionStorage.removeItem('receiptFilters');
                    }
                }
            } catch (e) {
                console.error('Failed to restore filter state:', e);
            }
        }

        /**
         * Update URL parameters without page reload
         */
        function updateURLParameters(state) {
            try {
                const url = new URL(window.location);

                // Set or remove parameters based on values
                if (state.studentSearch) {
                    url.searchParams.set('student_search', state.studentSearch);
                } else {
                    url.searchParams.delete('student_search');
                }

                if (state.fromDate) {
                    url.searchParams.set('from_date', state.fromDate);
                } else {
                    url.searchParams.delete('from_date');
                }

                if (state.toDate) {
                    url.searchParams.set('to_date', state.toDate);
                } else {
                    url.searchParams.delete('to_date');
                }

                if (state.paymentMethod) {
                    url.searchParams.set('payment_method', state.paymentMethod);
                } else {
                    url.searchParams.delete('payment_method');
                }

                if (state.collectorId) {
                    url.searchParams.set('collector_id', state.collectorId);
                } else {
                    url.searchParams.delete('collector_id');
                }

                if (state.familyPayments) {
                    url.searchParams.set('family_payments', '1');
                } else {
                    url.searchParams.delete('family_payments');
                }

                // Update URL without reloading page
                window.history.replaceState({}, '', url);
            } catch (e) {
                console.error('Failed to update URL parameters:', e);
            }
        }
    </script>
@endpush
