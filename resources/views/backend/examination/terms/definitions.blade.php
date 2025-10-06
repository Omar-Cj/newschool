@extends('backend.master')
@section('title')
    @lang('Term Templates Management')
@endsection
@section('content')
    <div class="page-content">

        {{-- breadcrumb Area Start --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ ___('examination.term_templates_management') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('terms.index') }}">{{ ___('examination.terms') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('examination.term_templates') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area End --}}

        <!--  table content start -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">{{ ___('examination.term_templates') }}</h4>
                        <p class="text-muted mb-0">{{ ___('examination.define_reusable_term_templates') }}</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-lg ot-btn-primary" id="addDefinitionBtn">
                            <span><i class="fa-solid fa-plus"></i></span>
                            <span class="">{{ ___('examination.add_template') }}</span>
                        </button>
                        <a href="{{ route('terms.index') }}" class="btn btn-lg ot-btn-secondary">
                            <span><i class="fa-solid fa-arrow-left"></i></span>
                            <span class="">{{ ___('examination.back_to_terms') }}</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="definitionsTable" class="table table-bordered role-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('common.name') }}</th>
                                    <th class="purchase">{{ ___('examination.code') }}</th>
                                    <th class="purchase">{{ ___('examination.sequence') }}</th>
                                    <th class="purchase">{{ ___('examination.duration') }}</th>
                                    <th class="purchase">{{ ___('examination.start_month') }}</th>
                                    <th class="purchase">{{ ___('examination.terms_count') }}</th>
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
        <!--  table content end -->
    </div>

    <!-- Add/Edit Definition Modal -->
    @include('backend.examination.terms.modals.term-definition')

    {{-- Hidden inputs for SweetAlert2 i18n --}}
    <input type="hidden" id="alert_title" value="{{ ___('examination.delete_term_definition') }}">
    <input type="hidden" id="alert_subtitle" value="{{ ___('examination.are_you_sure_delete') }}">
    <input type="hidden" id="alert_yes_btn" value="{{ ___('common.yes') }}">
    <input type="hidden" id="alert_cancel_btn" value="{{ ___('common.cancel') }}">
    <input type="hidden" id="alert_cannot_undo" value="{{ ___('examination.cannot_undo') }}">
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
        .table-content.table-basic #definitionsTable {
            font-size: 14px;
        }

        .table-content.table-basic #definitionsTable thead th {
            background-color: var(--ot-bg-primary, #f8f9fa);
            color: var(--ot-text-body, #1a1d1f);
            border-bottom: 1px solid var(--ot-border-color, #eaeaea);
            font-weight: 600;
            font-size: 14px;
            padding: 12px 8px;
            vertical-align: middle;
        }

        .table-content.table-basic #definitionsTable tbody td {
            padding: 10px 8px;
            font-size: 13px;
            border-bottom: 1px solid var(--ot-border-light, #f5f5f5);
            vertical-align: middle;
        }

        .table-content.table-basic #definitionsTable tbody tr:nth-of-type(odd) {
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
        .table-content.table-basic #definitionsTable thead th.sorting,
        .table-content.table-basic #definitionsTable thead th.sorting_asc,
        .table-content.table-basic #definitionsTable thead th.sorting_desc {
            cursor: pointer;
            position: relative;
        }

        .table-content.table-basic #definitionsTable thead th.sorting:after,
        .table-content.table-basic #definitionsTable thead th.sorting_asc:after,
        .table-content.table-basic #definitionsTable thead th.sorting_desc:after {
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ot-text-muted, #9c9c9c);
            font-size: 12px;
        }

        .table-content.table-basic #definitionsTable thead th.sorting:after {
            content: "\f0dc";
        }

        .table-content.table-basic #definitionsTable thead th.sorting_asc:after {
            content: "\f0de";
            color: var(--ot-primary-color, #5764c6);
        }

        .table-content.table-basic #definitionsTable thead th.sorting_desc:after {
            content: "\f0dd";
            color: var(--ot-primary-color, #5764c6);
        }
    </style>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Initialize DataTable
            var table = $('#definitionsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('terms.definitions.ajax-data') }}",
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX error:', error, thrown);
                        if (typeof Toast !== 'undefined' && Toast.fire) {
                            Toast.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to load term definitions. Please refresh the page.'
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
                    {data: 'name', name: 'name'},
                    {data: 'code', name: 'code'},
                    {data: 'sequence', name: 'sequence'},
                    {data: 'typical_duration_weeks', name: 'typical_duration_weeks', render: function(data) {
                        return data + ' weeks';
                    }},
                    {data: 'typical_start_month', name: 'typical_start_month', render: function(data) {
                        if (!data) return '-';
                        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                        return months[data - 1];
                    }},
                    {data: 'terms_count', name: 'terms_count'},
                    {data: 'status_badge', name: 'status_badge'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, width: '10%'}
                ],
                order: [[3, 'asc']], // Order by sequence
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                searchDelay: 300,
                // Enhanced language configuration
                language: {
                    processing: '<div class="d-flex align-items-center justify-content-center"><div class="spinner-border ot-loading-spinner me-2" role="status"><span class="visually-hidden">Loading...</span></div><span class="ot-loading-dots"></span></div>',
                    emptyTable: '<div class="text-center gray-color p-5"><img src="{{ asset("images/no_data.svg") }}" alt="" class="mb-primary" width="100"><p class="mb-0 text-center">{{ ___("common.no_data_available") }}</p><p class="mb-0 text-center text-secondary font-size-90">{{ ___("common.please_add_new_entity_regarding_this_table") }}</p></div>',
                    zeroRecords: '<div class="text-center gray-color p-5"><img src="{{ asset("images/no_data.svg") }}" alt="" class="mb-primary" width="100"><p class="mb-0 text-center">No matching records found</p><p class="mb-0 text-center text-secondary font-size-90">Try adjusting your search</p></div>',
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

            // Initialize Select2 when modal is shown
            $('#definitionModal').on('shown.bs.modal', function() {
                // Initialize Select2 for the month dropdown
                $('#typical_start_month').select2({
                    placeholder: "{{ ___('examination.select_month') }}",
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#definitionModal')
                });
            });

            // Add Definition
            $('#addDefinitionBtn').on('click', function() {
                $('#definitionForm')[0].reset();
                $('#definition_id').val('');
                $('#definitionModalLabel').text('Add Term Template');
                $('#is_active').prop('checked', true);
                $('#definitionModal').modal('show');
            });

            // Edit Definition
            $(document).on('click', '.edit-definition', function() {
                var definitionId = $(this).data('id');

                $.ajax({
                    url: "{{ url('terms/definitions') }}/" + definitionId + "/edit",
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var definition = response.data;
                            $('#definition_id').val(definition.id);
                            $('#name').val(definition.name);
                            $('#code').val(definition.code);
                            $('#sequence').val(definition.sequence);
                            $('#typical_duration_weeks').val(definition.typical_duration_weeks);
                            $('#typical_start_month').val(definition.typical_start_month);
                            $('#description').val(definition.description);
                            $('#is_active').prop('checked', definition.is_active);
                            $('#definitionModalLabel').text('Edit Term Template');

                            // Trigger change event to update Select2 display when modal opens
                            $('#typical_start_month').trigger('change');

                            $('#definitionModal').modal('show');
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error loading definition');
                    }
                });
            });

            // Submit Definition Form
            $('#definitionForm').on('submit', function(e) {
                e.preventDefault();
                var definitionId = $('#definition_id').val();
                var url = definitionId
                    ? "{{ url('terms/definitions') }}/" + definitionId
                    : "{{ route('terms.definitions.store') }}";
                var method = definitionId ? 'PUT' : 'POST';

                var formData = $(this).serialize();

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    beforeSend: function() {
                        $('#saveDefinitionBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#definitionModal').modal('hide');
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
                        $('#saveDefinitionBtn').prop('disabled', false).html('<i class="fas fa-save"></i> Save');
                    }
                });
            });

            // Delete Definition - SweetAlert2 Implementation with Contextual Warnings
            $(document).on('click', '.delete-definition', function() {
                const definitionId = $(this).data('id');
                const definitionName = $(this).data('name');
                const termsCount = parseInt($(this).data('terms-count')) || 0;

                // Build modal configuration based on terms count
                let modalConfig = {};

                if (termsCount === 0) {
                    // Pattern A: Simple Confirmation (Safe to Delete)
                    modalConfig = {
                        title: $('#alert_title').val(),
                        html: `<p>${$('#alert_subtitle').val()}</p>
                               <p><strong>Template:</strong> ${definitionName}</p>
                               <p class="text-info"><small>No terms have been created from this template.</small></p>
                               <p class="text-danger"><small>${$('#alert_cannot_undo').val()}</small></p>`,
                        icon: 'warning',
                        confirmButtonText: $('#alert_yes_btn').val()
                    };
                } else {
                    // Pattern B: Warning with Cascade Details
                    modalConfig = {
                        title: 'Delete Template with Terms?',
                        html: `<p><strong>Template:</strong> ${definitionName}</p>
                               <div class="alert alert-warning mt-3">
                                   <strong>Warning:</strong> This template has ${termsCount} term(s) created from it.
                               </div>
                               <p class="text-danger">Deleting this template may affect these terms.</p>
                               <p class="text-danger"><strong>${$('#alert_cannot_undo').val()}</strong></p>`,
                        icon: 'error',
                        confirmButtonText: 'Delete Anyway'
                    };
                }

                // Show confirmation dialog with appropriate configuration
                Swal.fire({
                    ...modalConfig,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: $('#alert_cancel_btn').val()
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Execute deletion
                        $.ajax({
                            url: "{{ url('terms/definitions') }}/" + definitionId,
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    table.ajax.reload();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Cannot Delete',
                                    html: `<p class="text-danger">${xhr.responseJSON?.message || 'Cannot delete term template with existing terms'}</p>`
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush