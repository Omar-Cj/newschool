@extends('backend.master')
@section('title')
    @lang('Exam Entry Management')
@endsection
@section('content')
    <div class="page-content">

        {{-- breadcrumb Area Start --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ ___('examination.exam_entry_management') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('examination.exam_entry') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area End --}}

        <!-- Exam Entry Table Section -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-clipboard-list"></i> {{ ___('examination.exam_entries_list') }}
                    </h4>
                    <div>
                        <a href="{{ route('exam-entry.create') }}" class="btn btn-lg ot-btn-primary">
                            <span><i class="fa-solid fa-plus"></i></span>
                            <span class="">{{ ___('examination.create_exam_entry') }}</span>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <select class="form-control select2" id="filter-session">
                                <option value="">{{ ___('common.all_sessions') }}</option>
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control select2" id="filter-term">
                                <option value="">{{ ___('examination.all_terms') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control select2" id="filter-class">
                                <option value="">{{ ___('academic.all_classes') }}</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control select2" id="filter-section">
                                <option value="">{{ ___('academic.all_sections') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control select2" id="filter-exam-type">
                                <option value="">{{ ___('examination.all_exam_types') }}</option>
                                @foreach($examTypes as $examType)
                                    <option value="{{ $examType->id }}">{{ $examType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-control select2" id="filter-status">
                                <option value="">{{ ___('common.all_status') }}</option>
                                <option value="draft">{{ ___('common.draft') }}</option>
                                <option value="completed">{{ ___('common.completed') }}</option>
                                <option value="published">{{ ___('common.published') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <select class="form-control select2" id="filter-grade">
                                <option value="">{{ ___('common.all_grades') }}</option>
                                <optgroup label="Kindergarten">
                                    <option value="KG-1">KG-1</option>
                                    <option value="KG-2">KG-2</option>
                                </optgroup>
                                <optgroup label="Primary">
                                    <option value="Grade1">Grade 1</option>
                                    <option value="Grade2">Grade 2</option>
                                    <option value="Grade3">Grade 3</option>
                                    <option value="Grade4">Grade 4</option>
                                    <option value="Grade5">Grade 5</option>
                                    <option value="Grade6">Grade 6</option>
                                    <option value="Grade7">Grade 7</option>
                                    <option value="Grade8">Grade 8</option>
                                </optgroup>
                                <optgroup label="Secondary">
                                    <option value="Form1">Form 1</option>
                                    <option value="Form2">Form 2</option>
                                    <option value="Form3">Form 3</option>
                                    <option value="Form4">Form 4</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-md-10"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-lg ot-btn-info" id="clearFilters">
                                <span><i class="fa-solid fa-sync"></i></span>
                                <span>{{ ___('common.clear_filters') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="examEntriesTable" class="table table-bordered role-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('examination.session') }}</th>
                                    <th class="purchase">{{ ___('examination.term') }}</th>
                                    <th class="purchase">{{ ___('academic.class') }}</th>
                                    <th class="purchase">{{ ___('academic.section') }}</th>
                                    <th class="purchase">{{ ___('examination.exam_type') }}</th>
                                    <th class="purchase">{{ ___('academic.subject') }}</th>
                                    <th class="purchase">{{ ___('examination.entry_method') }}</th>
                                    <th class="purchase">{{ ___('examination.results_count') }}</th>
                                    <th class="purchase">{{ ___('common.status') }}</th>
                                    <th class="purchase">{{ ___('common.created_at') }}</th>
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

    {{-- Hidden inputs for SweetAlert2 i18n --}}
    <input type="hidden" id="alert_title" value="{{ ___('examination.delete_exam_entry') }}">
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
        .table-content.table-basic #examEntriesTable {
            font-size: 14px;
        }

        .table-content.table-basic #examEntriesTable thead th {
            background-color: var(--ot-bg-primary, #f8f9fa);
            color: var(--ot-text-body, #1a1d1f);
            border-bottom: 1px solid var(--ot-border-color, #eaeaea);
            font-weight: 600;
            font-size: 14px;
            padding: 12px 8px;
            vertical-align: middle;
        }

        .table-content.table-basic #examEntriesTable tbody td {
            padding: 10px 8px;
            font-size: 13px;
            border-bottom: 1px solid var(--ot-border-light, #f5f5f5);
            vertical-align: middle;
        }

        .table-content.table-basic #examEntriesTable tbody tr:nth-of-type(odd) {
            background-color: var(--ot-bg-table-row, #fafafa);
        }

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
    </style>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Initialize DataTable
            var table = $('#examEntriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('exam-entry.ajax-data') }}",
                    type: 'GET',
                    data: function(d) {
                        d.session_id = $('#filter-session').val();
                        d.term_id = $('#filter-term').val();
                        d.class_id = $('#filter-class').val();
                        d.section_id = $('#filter-section').val();
                        d.exam_type_id = $('#filter-exam-type').val();
                        d.grade = $('#filter-grade').val();
                        d.status = $('#filter-status').val();
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX error:', error, thrown);
                        toastr.error('Failed to load exam entries. Please refresh the page.');
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
                    {data: 'session_name', name: 'session_name'},
                    {data: 'term_name', name: 'term_name'},
                    {data: 'class_name', name: 'class_name'},
                    {data: 'section_name', name: 'section_name'},
                    {data: 'exam_type_name', name: 'exam_type_name'},
                    {data: 'subject_info', name: 'subject_info'},
                    {data: 'entry_method', name: 'entry_method'},
                    {data: 'results_count', name: 'results_count'},
                    {data: 'status_badge', name: 'status_badge'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, width: '10%'}
                ],
                order: [[10, 'desc']],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                searchDelay: 300,
                language: {
                    processing: '<div class="d-flex align-items-center justify-content-center"><div class="spinner-border me-2" role="status"><span class="visually-hidden">Loading...</span></div><span>Loading...</span></div>',
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
                responsive: false,
                scrollX: false,
                autoWidth: false,
                stateSave: false
            });

            // Cascading dropdown: Session change → load terms
            $('#filter-session').on('change', function() {
                var sessionId = $(this).val();
                $('#filter-term').html('<option value="">{{ ___("examination.all_terms") }}</option>');

                if (sessionId) {
                    $.ajax({
                        url: "{{ route('exam-entry.get-terms') }}",
                        type: 'GET',
                        data: { session_id: sessionId },
                        success: function(response) {
                            if (response.success) {
                                $.each(response.data, function(index, term) {
                                    $('#filter-term').append('<option value="' + term.id + '">' + term.name + '</option>');
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading terms:', error);
                            toastr.error('Failed to load terms');
                        }
                    });
                }
                table.ajax.reload();
            });

            // Cascading dropdown: Class change → load sections
            $('#filter-class').on('change', function() {
                var classId = $(this).val();
                $('#filter-section').html('<option value="">{{ ___("academic.all_sections") }}</option>');

                if (classId) {
                    $.ajax({
                        url: "{{ route('exam-entry.get-sections') }}",
                        type: 'GET',
                        data: { class_id: classId },
                        success: function(response) {
                            if (response.success) {
                                $.each(response.data, function(index, section) {
                                    $('#filter-section').append('<option value="' + section.id + '">' + section.name + '</option>');
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading sections:', error);
                            toastr.error('Failed to load sections');
                        }
                    });
                }
                table.ajax.reload();
            });

            // Filter handlers for non-cascading dropdowns
            $('#filter-term, #filter-section, #filter-exam-type, #filter-grade, #filter-status').on('change', function() {
                table.ajax.reload();
            });

            $('#clearFilters').on('click', function() {
                $('#filter-session').val('').trigger('change');
                $('#filter-term').val('').trigger('change');
                $('#filter-class').val('').trigger('change');
                $('#filter-section').val('').trigger('change');
                $('#filter-exam-type').val('').trigger('change');
                $('#filter-grade').val('').trigger('change');
                $('#filter-status').val('').trigger('change');
                table.ajax.reload();
            });

            // Delete exam entry - SweetAlert2 Implementation
            $(document).on('click', '.delete-entry', function() {
                const entryId = $(this).data('id');
                const examType = $(this).data('exam-type');
                const className = $(this).data('class');
                const resultsCount = $(this).data('results-count');

                // Build confirmation message with details
                let detailsHtml = `<p>${$('#alert_subtitle').val()}</p>
                                   <p><strong>Exam Type:</strong> ${examType}</p>
                                   <p><strong>Class:</strong> ${className}</p>`;

                if (resultsCount > 0) {
                    detailsHtml += `<p class="text-warning"><strong>Warning:</strong> ${resultsCount} student result(s) will also be deleted</p>`;
                }

                detailsHtml += `<p class="text-danger"><small>${$('#alert_cannot_undo').val()}</small></p>`;

                // Show confirmation dialog
                Swal.fire({
                    title: $('#alert_title').val(),
                    html: detailsHtml,
                    icon: resultsCount > 0 ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: $('#alert_yes_btn').val(),
                    cancelButtonText: $('#alert_cancel_btn').val()
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Execute deletion
                        $.ajax({
                            url: "{{ url('exam-entry') }}/" + entryId,
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
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Cannot Delete',
                                    html: `<p class="text-danger">${xhr.responseJSON?.message || 'Error deleting exam entry'}</p>`
                                });
                            }
                        });
                    }
                });
            });

            // Publish exam entry
            $(document).on('click', '.publish-entry', function() {
                if (confirm('Are you sure you want to publish this exam entry? Results will be visible to students.')) {
                    var entryId = $(this).data('id');

                    $.ajax({
                        url: "{{ url('exam-entry') }}/" + entryId + "/publish",
                        type: 'PUT',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                table.ajax.reload();
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            toastr.error(xhr.responseJSON.message || 'Error publishing exam entry');
                        }
                    });
                }
            });
        });
    </script>
@endpush
