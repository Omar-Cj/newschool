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


        <div class="col-12">
            <div class="card ot-card mb-24 position-relative z_1">
                <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                    <h3 class="mb-0">{{ ___('common.Filtering') }}</h3>

                    <div class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">
                        <!-- table_searchBox -->
                        <div class="single_large_selectBox">
                            <select id="classFilter"
                                class="form-select nice-select niceSelect bordered_style wide"
                                name="class_id">
                                <option value="">{{ ___('student_info.select_class') }}</option>
                                @foreach ($data['classes'] as $item)
                                    <option value="{{ $item->class->id }}">{{ $item->class->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="single_large_selectBox">
                            <select id="sectionFilter"
                                class="form-select nice-select niceSelect bordered_style wide"
                                name="section_id">
                                <option value="">{{ ___('student_info.select_section') }}</option>
                            </select>
                        </div>

                        <div class="single_large_selectBox">
                            <input class="form-control ot-input" id="keywordFilter"
                                placeholder="{{ ___('student_info.enter_keyword') }}">
                        </div>

                        <button class="btn btn-lg ot-btn-primary" id="clearFilters" type="button">
                            {{ ___('common.Clear') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <!--  table content start -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $data['title'] }}</h4>
                    @if (hasPermission('student_create'))
                        <a href="{{ route('student.create') }}" class="btn btn-lg ot-btn-primary">
                            <span><i class="fa-solid fa-plus"></i> </span>
                            <span class="">{{ ___('common.add') }}</span>
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="studentsTable" class="table table-bordered role-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('student_info.student_name') }}</th>
                                    <th class="purchase">{{ ___('student_info.Grade') }}</th>
                                    <th class="purchase">{{ ___('academic.class') }} ({{ ___('academic.section') }})</th>
                                    <th class="purchase">{{ ___('student_info.guardian_name') }}</th>
                                    <th class="purchase">{{ ___('student_info.date_of_birth') }}</th>
                                    <th class="purchase">{{ ___('common.gender') }}</th>
                                    <th class="purchase">{{ ___('student_info.mobile_number') }}</th>
                                    <th class="purchase">{{ ___('fees.total_amount') }}</th>
                                    <th class="purchase">{{ ___('common.status') }}</th>
                                    @if (hasPermission('student_update') || hasPermission('student_delete'))
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

    <!-- Fee Collection Modal -->
    <div id="view-modal">
        <div class="modal fade" id="modalCustomizeWidth" tabindex="-1" aria-labelledby="modalWidth"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                @include('backend.fees.collect.fee-collection-modal')
            </div>
        </div>
    </div>
@endsection

@push('script')
    @include('backend.fees.collect.fee-collection-modal-script')

    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- Custom DataTables Theme Integration -->
    <style>
        /* Restore original table-content.table-basic styling */
        .table-content.table-basic #studentsTable {
            font-size: 14px;
        }

        .table-content.table-basic #studentsTable thead th {
            background-color: var(--ot-bg-primary, #f8f9fa);
            color: var(--ot-text-body, #1a1d1f);
            border-bottom: 1px solid var(--ot-border-color, #eaeaea);
            font-weight: 600;
            font-size: 14px;
            padding: 12px 8px;
            vertical-align: middle;
        }

        .table-content.table-basic #studentsTable tbody td {
            padding: 10px 8px;
            font-size: 13px;
            border-bottom: 1px solid var(--ot-border-light, #f5f5f5);
            vertical-align: middle;
        }

        .table-content.table-basic #studentsTable tbody tr:nth-of-type(odd) {
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
        .table-content.table-basic #studentsTable thead th.sorting,
        .table-content.table-basic #studentsTable thead th.sorting_asc,
        .table-content.table-basic #studentsTable thead th.sorting_desc {
            cursor: pointer;
            position: relative;
        }

        .table-content.table-basic #studentsTable thead th.sorting:after,
        .table-content.table-basic #studentsTable thead th.sorting_asc:after,
        .table-content.table-basic #studentsTable thead th.sorting_desc:after {
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--ot-text-muted, #9c9c9c);
            font-size: 12px;
        }

        .table-content.table-basic #studentsTable thead th.sorting:after {
            content: "\f0dc";
        }

        .table-content.table-basic #studentsTable thead th.sorting_asc:after {
            content: "\f0de";
            color: var(--ot-primary-color, #5764c6);
        }

        .table-content.table-basic #studentsTable thead th.sorting_desc:after {
            content: "\f0dd";
            color: var(--ot-primary-color, #5764c6);
        }
    </style>

    <script>
        let studentsTable;
        let ajaxUrl = '{{ route("student.ajaxData") }}';
        let sectionsUrl = '{{ route("student.ajaxSections", ":classId") }}';

        $(document).ready(function() {
            // Initialize DataTables
            initializeStudentsTable();

            // Filter event handlers
            setupFilterHandlers();
        });

        function initializeStudentsTable() {
            studentsTable = $('#studentsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: ajaxUrl,
                    type: 'GET',
                    data: function(d) {
                        // Add custom filters to DataTables request
                        d.class_id = $('#classFilter').val();
                        d.section_id = $('#sectionFilter').val();
                        d.keyword = $('#keywordFilter').val();
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTables AJAX error:', error, thrown);
                        showErrorMessage('Failed to load student data. Please refresh the page.');
                    }
                },
                // Configure DOM structure for better theme integration
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                columns: [
                    { data: 0, name: 'id', orderable: false, searchable: false, width: '5%' },
                    { data: 1, name: 'student.first_name', orderable: true, searchable: true },
                    { data: 2, name: 'student.grade', orderable: true, searchable: false },
                    { data: 3, name: 'class.name', orderable: true, searchable: false },
                    { data: 4, name: 'student.parent.guardian_name', orderable: true, searchable: false },
                    { data: 5, name: 'student.dob', orderable: true, searchable: false },
                    { data: 6, name: 'student.gender.name', orderable: true, searchable: false },
                    { data: 7, name: 'student.mobile', orderable: true, searchable: false },
                    { data: 8, name: 'outstanding_amount', orderable: false, searchable: false },
                    { data: 9, name: 'student.status', orderable: true, searchable: false },
                    @if (hasPermission('student_update') || hasPermission('student_delete'))
                    { data: 10, name: 'actions', orderable: false, searchable: false, width: '10%' }
                    @endif
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
        }

        function setupFilterHandlers() {
            // Class filter change handler - loads sections dynamically
            $('#classFilter').on('change', function() {
                const classId = $(this).val();

                // Clear and disable section filter while loading
                const sectionFilter = $('#sectionFilter');
                sectionFilter.prop('disabled', true).empty().append('<option value="">Loading sections...</option>');

                if (classId) {
                    // Load sections for selected class
                    $.ajax({
                        url: sectionsUrl.replace(':classId', classId),
                        type: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            sectionFilter.empty().append('<option value="">{{ ___("student_info.select_section") }}</option>');

                            if (response.success && response.data) {
                                response.data.forEach(function(section) {
                                    sectionFilter.append(`<option value="${section.id}">${section.name}</option>`);
                                });
                            }

                            sectionFilter.prop('disabled', false);
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading sections:', error);
                            sectionFilter.empty().append('<option value="">{{ ___("student_info.select_section") }}</option>');
                            sectionFilter.prop('disabled', false);
                            showErrorMessage('Failed to load sections. Please try again.');
                        }
                    });
                } else {
                    // Reset sections if no class selected
                    sectionFilter.empty().append('<option value="">{{ ___("student_info.select_section") }}</option>');
                    sectionFilter.prop('disabled', false);
                }

                // Reload table data
                studentsTable.ajax.reload();
            });

            // Section filter change handler
            $('#sectionFilter').on('change', function() {
                studentsTable.ajax.reload();
            });

            // Keyword filter with debounce
            let keywordTimeout;
            $('#keywordFilter').on('input', function() {
                clearTimeout(keywordTimeout);
                keywordTimeout = setTimeout(function() {
                    studentsTable.ajax.reload();
                }, 300);
            });

            // Clear filters button
            $('#clearFilters').on('click', function() {
                $('#classFilter').val('').trigger('change');
                $('#sectionFilter').val('');
                $('#keywordFilter').val('');
                studentsTable.search('').ajax.reload();
            });

            // Handle DataTables search box
            $('#studentsTable_filter input').on('input', function() {
                // The DataTables built-in search will handle this automatically
            });
        }

        // Function to open fee collection modal for individual student
        function openFeeCollectionModal(studentId, studentName, admissionNo = null) {
            console.log(`Opening fee collection modal for student ${studentId}: ${studentName}`);

            // Show loading in modal
            $('#modal_student_id').val(studentId);
            $('#modalCustomizeWidth .modal-title').text(`Fee Collection - ${studentName}`);

            // Prepare student info object
            const studentInfo = {
                name: studentName,
                student_name: studentName,
                admission_no: admissionNo || '--'
            };

            // Debug: Check if modal exists
            console.log('Modal element exists:', $('#modalCustomizeWidth').length > 0);
            console.log('Form element exists:', $('#feeCollectionForm').length > 0);

            // Fetch student's unpaid fees via AJAX
            fetchStudentFees(studentId).then(feesData => {
                console.log('Fees data received:', feesData);
                if (feesData && feesData.success) {
                    window.populateFeeCollectionModal(studentId, feesData.data, studentInfo);
                } else {
                    console.error('Failed to load fees:', feesData);
                    showErrorMessage('Unable to load student fees. Please try again.');
                }
            }).catch(error => {
                console.error('Error fetching student fees:', error);
                showErrorMessage('An error occurred while loading student fees.');
            });
        }

        // Function to fetch student fees via AJAX
        function fetchStudentFees(studentId) {
            return $.ajax({
                url: '{{ route("fees-collect.fees-show") }}',
                method: 'GET',
                data: {
                    student_id: studentId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }

        function showErrorMessage(message) {
            if (typeof Toast !== 'undefined' && Toast.fire) {
                Toast.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            } else {
                alert(message);
            }
        }

        // Override delete_row function to refresh DataTable after deletion
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
                                if (typeof studentsTable !== 'undefined') {
                                    studentsTable.ajax.reload(null, false);
                                }
                            } else {
                                Swal.fire(response[2], response[0], response[1]);
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error!', 'An error occurred while deleting.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
