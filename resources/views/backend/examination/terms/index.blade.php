@extends('backend.master')
@section('title')
    @lang('Academic Terms Management')
@endsection
@section('content')
    <div class="page-content">

        {{-- breadcrumb Area Start --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ ___('examination.academic_terms_management') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('examination.terms') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area End --}}

        <!-- Dashboard Section -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-calendar-alt"></i> {{ ___('examination.terms_dashboard') }}
                    </h4>
                    <div>
                        <button type="button" class="btn btn-lg ot-btn-primary" id="openTermBtn">
                            <span><i class="fa-solid fa-plus"></i></span>
                            <span class="">{{ ___('examination.open_new_term') }}</span>
                        </button>
                        <a href="{{ route('terms.definitions') }}" class="btn btn-lg ot-btn-secondary">
                            <span><i class="fa-solid fa-cog"></i></span>
                            <span class="">{{ ___('examination.manage_templates') }}</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($dashboardData['active_term'])
                        <div class="active-term-info">
                            <h5>{{ $dashboardData['active_term']['name'] }}</h5>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $dashboardData['active_term']['progress_percentage'] }}%"
                                     aria-valuenow="{{ $dashboardData['active_term']['progress_percentage'] }}"
                                     aria-valuemin="0" aria-valuemax="100">
                                    {{ $dashboardData['active_term']['progress_percentage'] }}%
                                </div>
                            </div>
                            <small class="text-muted">
                                Week {{ $dashboardData['active_term']['current_week'] }} of {{ $dashboardData['active_term']['total_weeks'] }}
                                @if($dashboardData['active_term']['days_remaining'] > 0)
                                    • {{ $dashboardData['active_term']['days_remaining'] }} days remaining
                                @endif
                            </small>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> {{ ___('examination.no_active_term') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Terms Table Section -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">{{ ___('examination.terms_list') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control select2" id="filter-session">
                                <option value="">{{ ___('common.all_sessions') }}</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control select2" id="filter-status">
                                <option value="">{{ ___('common.all_status') }}</option>
                                <option value="draft">{{ ___('common.draft') }}</option>
                                <option value="upcoming">{{ ___('common.upcoming') }}</option>
                                <option value="active">{{ ___('common.active') }}</option>
                                <option value="closed">{{ ___('common.closed') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control select2" id="filter-term-definition">
                                <option value="">{{ ___('examination.all_terms') }}</option>
                                @foreach($termDefinitions as $definition)
                                    <option value="{{ $definition->id }}">{{ $definition->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-lg ot-btn-info" id="clearFilters">
                                <span><i class="fa-solid fa-sync"></i></span>
                                <span>{{ ___('common.clear_filters') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="termsTable" class="table table-bordered role-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('examination.term') }}</th>
                                    <th class="purchase">{{ ___('examination.session') }}</th>
                                    @if(hasModule('MultiBranch'))
                                    <th class="purchase">{{ ___('common.branch') }}</th>
                                    @endif
                                    <th class="purchase">{{ ___('examination.date_range') }}</th>
                                    <th class="purchase">{{ ___('examination.duration') }}</th>
                                    <th class="purchase">{{ ___('examination.progress') }}</th>
                                    <th class="purchase">{{ ___('common.status') }}</th>
                                    <th class="action">{{ ___('common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Open Term Modal -->
    @include('backend.examination.terms.modals.open-term')

    <!-- Edit Term Modal -->
    @include('backend.examination.terms.modals.edit-term')
@endsection

@push('script')
    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <!-- Select2 CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Custom DataTables Theme Integration -->
    <style>
        /* Restore original table-content.table-basic styling */
        .table-content.table-basic #termsTable {
            font-size: 14px;
        }

        .table-content.table-basic #termsTable thead th {
            background-color: var(--ot-bg-primary, #f8f9fa);
            color: var(--ot-text-body, #1a1d1f);
            border-bottom: 1px solid var(--ot-border-color, #eaeaea);
            font-weight: 600;
            font-size: 14px;
            padding: 12px 8px;
            vertical-align: middle;
        }

        .table-content.table-basic #termsTable tbody td {
            padding: 10px 8px;
            font-size: 13px;
            border-bottom: 1px solid var(--ot-border-light, #f5f5f5);
            vertical-align: middle;
        }

        .table-content.table-basic #termsTable tbody tr:nth-of-type(odd) {
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
        .table-content.table-basic #termsTable thead th.sorting,
        .table-content.table-basic #termsTable thead th.sorting_asc,
        .table-content.table-basic #termsTable thead th.sorting_desc {
            cursor: pointer;
            position: relative;
        }

        .table-content.table-basic #termsTable thead th.sorting:after,
        .table-content.table-basic #termsTable thead th.sorting_asc:after,
        .table-content.table-basic #termsTable thead th.sorting_desc:after {
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ot-text-muted, #9c9c9c);
            font-size: 12px;
        }

        .table-content.table-basic #termsTable thead th.sorting:after {
            content: "\f0dc";
        }

        .table-content.table-basic #termsTable thead th.sorting_asc:after {
            content: "\f0de";
            color: var(--ot-primary-color, #5764c6);
        }

        .table-content.table-basic #termsTable thead th.sorting_desc:after {
            content: "\f0dd";
            color: var(--ot-primary-color, #5764c6);
        }
    </style>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Initialize DataTable
            var table = $('#termsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('terms.ajax-data') }}",
                    type: 'GET',
                    data: function(d) {
                        d.session_id = $('#filter-session').val();
                        d.status = $('#filter-status').val();
                        d.term_definition_id = $('#filter-term-definition').val();
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX error:', error, thrown);
                        if (typeof Toast !== 'undefined' && Toast.fire) {
                            Toast.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to load terms data. Please refresh the page.'
                            });
                        }
                    }
                },
                // Configure DOM structure for better theme integration
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
                    {data: 'term_name', name: 'term_name'},
                    {data: 'session_name', name: 'session_name'},
                    @if(hasModule('MultiBranch'))
                    {data: 'branch_name', name: 'branch_name'},
                    @endif
                    {data: 'date_range', name: 'date_range'},
                    {data: 'duration', name: 'duration'},
                    {data: 'progress', name: 'progress', orderable: false},
                    {data: 'status_badge', name: 'status_badge'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, width: '10%'}
                ],
                order: [[1, 'asc']],
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

            // Filter handlers
            $('#filter-session, #filter-status, #filter-term-definition').on('change', function() {
                table.ajax.reload();
            });

            $('#clearFilters').on('click', function() {
                $('#filter-session').val('').trigger('change');
                $('#filter-status').val('').trigger('change');
                $('#filter-term-definition').val('').trigger('change');
                table.ajax.reload();
            });

            // Initialize Select2 when open term modal is shown
            $('#openTermModal').on('shown.bs.modal', function() {
                // Initialize Select2 for term definition dropdown
                $('#open_term_definition_id').select2({
                    placeholder: "{{ ___('examination.select_term_definition') }}",
                    allowClear: false,
                    width: '100%',
                    dropdownParent: $('#openTermModal')
                });

                // Initialize Select2 for session dropdown
                $('#open_session_id').select2({
                    placeholder: "{{ ___('examination.select_session') }}",
                    allowClear: false,
                    width: '100%',
                    dropdownParent: $('#openTermModal')
                });
            });

            // Open Term Modal
            $('#openTermBtn').on('click', function() {
                $('#openTermForm')[0].reset();
                $('.select2').val(null).trigger('change');
                $('#openTermModal').modal('show');
            });

            // Get suggested dates when term and session are selected
            $('#open_term_definition_id, #open_session_id').on('change', function() {
                var termDefinitionId = $('#open_term_definition_id').val();
                var sessionId = $('#open_session_id').val();

                if (termDefinitionId && sessionId) {
                    $.ajax({
                        url: "{{ route('terms.create') }}",
                        type: 'GET',
                        data: {
                            term_definition_id: termDefinitionId,
                            session_id: sessionId
                        },
                        success: function(response) {
                            if (response.success && response.suggested_dates) {
                                $('#open_start_date').val(response.suggested_dates.start_date);
                                $('#open_end_date').val(response.suggested_dates.end_date);
                                toastr.info('Suggested dates loaded. You can adjust them if needed.');
                            }
                        }
                    });
                }
            });

            // Submit Open Term Form
            $('#openTermForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ route('terms.store') }}",
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#openTermBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Opening...');
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#openTermModal').modal('hide');
                            table.ajax.reload();
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON;
                        if (errors.errors) {
                            $.each(errors.errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else if (errors.message) {
                            // Check for sequence warning
                            if (errors.sequence_warning) {
                                if (confirm(errors.message + '\n\nDo you want to proceed anyway?')) {
                                    // Resubmit with force_sequence flag
                                    $('#openTermForm').append('<input type="hidden" name="force_sequence" value="1">');
                                    $('#openTermForm').submit();
                                }
                            } else {
                                toastr.error(errors.message);
                            }
                        }
                    },
                    complete: function() {
                        $('#openTermBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Open Term');
                    }
                });
            });

            // Edit Term
            $(document).on('click', '.edit-term', function() {
                var termId = $(this).data('id');

                $.ajax({
                    url: "{{ url('terms') }}/" + termId + "/edit",
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var term = response.data;
                            $('#edit_term_id').val(term.id);
                            $('#edit_term_name').val(term.term_definition.name);
                            $('#edit_session_name').val(term.session.name);
                            $('#edit_start_date').val(term.start_date);
                            $('#edit_end_date').val(term.end_date);
                            $('#edit_notes').val(term.notes);
                            $('#editTermModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        toastr.error(xhr.responseJSON.message || 'Error loading term');
                    }
                });
            });

            // Update Term
            $('#editTermForm').on('submit', function(e) {
                e.preventDefault();
                var termId = $('#edit_term_id').val();
                var formData = $(this).serialize();

                $.ajax({
                    url: "{{ url('terms') }}/" + termId,
                    type: 'PUT',
                    data: formData,
                    beforeSend: function() {
                        $('#updateTermBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#editTermModal').modal('hide');
                            table.ajax.reload();
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON;
                        if (errors.errors) {
                            $.each(errors.errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else if (errors.message) {
                            toastr.error(errors.message);
                        }
                    },
                    complete: function() {
                        $('#updateTermBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Update Term');
                    }
                });
            });

            // Close Term
            $(document).on('click', '.close-term', function() {
                if (confirm('Are you sure you want to close this term? This action cannot be undone.')) {
                    var termId = $(this).data('id');

                    $.ajax({
                        url: "{{ url('terms') }}/" + termId + "/close",
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                toastr.success(response.message);
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || 'Error closing term');
                        }
                    });
                }
            });

            // Activate Term
            $(document).on('click', '.activate-term', function() {
                if (confirm('Are you sure you want to activate this term? Any currently active term will be closed.')) {
                    var termId = $(this).data('id');

                    $.ajax({
                        url: "{{ url('terms') }}/" + termId + "/activate",
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                toastr.success(response.message);
                                // Reload dashboard data
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || 'Error activating term');
                        }
                    });
                }
            });

            // View Term Details
            $(document).on('click', '.view-term', function() {
                var termId = $(this).data('id');
                // Implement view details modal or redirect to details page
                toastr.info('View details feature coming soon');
            });
        });
    </script>
@endpush