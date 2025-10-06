@extends('backend.master')
@section('title')
    @lang('Create Exam Entry')
@endsection
@section('content')
    <div class="page-content">

        {{-- breadcrumb Area Start --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ ___('examination.create_exam_entry') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('exam-entry.index') }}">{{ ___('examination.exam_entry') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('common.create') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area End --}}

        <!-- Parameters Selection Section -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-cog"></i> {{ ___('examination.exam_parameters') }}
                    </h4>
                </div>
                <div class="card-body">
                    <form id="parametersForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ ___('examination.session') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="session_id" name="session_id" required>
                                    <option value="">{{ ___('common.select') }}</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}">{{ $session->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ ___('examination.term') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="term_id" name="term_id" required disabled>
                                    <option value="">{{ ___('common.select') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ ___('student_info.grade') }}</label>
                                <select class="form-control select2" id="grade" name="grade">
                                    <option value="">{{ ___('common.all') }}</option>
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

                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ ___('academic.class') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="class_id" name="class_id" required>
                                    <option value="">{{ ___('common.select') }}</option>
                                    @foreach($classes ?? [] as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ ___('academic.section') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="section_id" name="section_id" required disabled>
                                    <option value="">{{ ___('common.select') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ ___('examination.exam_type') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="exam_type_id" name="exam_type_id" required>
                                    <option value="">{{ ___('common.select') }}</option>
                                    @foreach($examTypes as $examType)
                                        <option value="{{ $examType->id }}">{{ $examType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ ___('academic.subject') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="subject_id" name="subject_id" required disabled>
                                    <option value="">{{ ___('common.select') }}</option>
                                </select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">{{ ___('examination.total_marks') }} <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="total_marks" name="total_marks" min="1" max="1000" value="100" required>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-lg ot-btn-success" id="getStudentsBtn">
                                        <span><i class="fa-solid fa-users"></i></span>
                                        <span>{{ ___('examination.get_students') }}</span>
                                    </button>
                                    <button type="button" class="btn btn-lg ot-btn-info" id="downloadTemplateBtn">
                                        <span><i class="fa-solid fa-download"></i></span>
                                        <span>{{ ___('examination.download_students') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Manual Entry Section (Hidden by default) -->
        <div class="table-content table-basic mt-20" id="manualEntrySection" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-keyboard"></i> {{ ___('examination.manual_entry') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="studentsMarksTable" class="table table-bordered">
                            <thead class="thead">
                                <tr id="tableHeaders">
                                    <th>{{ ___('common.sr_no') }}</th>
                                    <th>{{ ___('student.student_name') }}</th>
                                    <th>{{ ___('student_info.grade') }}</th>
                                    <th>{{ ___('academic.class') }}</th>
                                    <th>{{ ___('academic.section') }}</th>
                                    <!-- Subject columns will be added dynamically -->
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <!-- Rows will be added dynamically -->
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-end">
                            <button type="button" class="btn btn-lg ot-btn-danger" id="cancelManualEntryBtn">
                                <span><i class="fa-solid fa-times"></i></span>
                                <span>{{ ___('common.cancel') }}</span>
                            </button>
                            <button type="button" class="btn btn-lg ot-btn-primary" id="saveManualEntryBtn">
                                <span><i class="fa-solid fa-save"></i></span>
                                <span>{{ ___('common.save') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Excel Upload Section (Hidden by default) -->
        <div class="table-content table-basic mt-20" id="excelUploadSection" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-file-excel"></i> {{ ___('examination.upload_excel') }}
                    </h4>
                </div>
                <div class="card-body">
                    <form id="excelUploadForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label" for="exam_file">{{ ___('examination.exam_file') }} <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="exam_file" name="exam_file" accept=".xlsx,.xls">
                                <small class="form-text text-muted">{{ ___('examination.upload_excel_hint') }}</small>
                                <div id="file-selected-feedback" class="mt-2" style="display: none;">
                                    <span class="badge bg-success">
                                        <i class="fas fa-file-excel"></i> <span id="file-selected-name"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-end">
                                <button type="button" class="btn btn-lg ot-btn-danger" id="cancelExcelUploadBtn">
                                    <span><i class="fa-solid fa-times"></i></span>
                                    <span>{{ ___('common.cancel') }}</span>
                                </button>
                                <button type="submit" class="btn btn-lg ot-btn-primary" id="uploadExcelBtn">
                                    <span><i class="fa-solid fa-upload"></i></span>
                                    <span>{{ ___('examination.upload_exams') }}</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <!-- Select2 CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            var studentsData = {};
            var subjectsData = [];

            // Session change - load terms
            $('#session_id').on('change', function() {
                var sessionId = $(this).val();
                $('#term_id').prop('disabled', !sessionId).html('<option value="">{{ ___("common.select") }}</option>');

                if (sessionId) {
                    $.ajax({
                        url: "{{ route('exam-entry.get-terms') }}",
                        type: 'GET',
                        data: { session_id: sessionId },
                        success: function(response) {
                            if (response.success) {
                                $.each(response.data, function(index, term) {
                                    $('#term_id').append('<option value="' + term.id + '">' + term.name + '</option>');
                                });
                                $('#term_id').trigger('change');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading terms:', error);
                            console.log('Response:', xhr.responseText);
                        }
                    });
                }
            });

            // Class change - load sections
            $('#class_id').on('change', function() {
                var classId = $(this).val();
                $('#section_id').prop('disabled', !classId).html('<option value="">{{ ___("common.select") }}</option>');
                $('#subject_id').prop('disabled', true).html('<option value="">{{ ___("common.select") }}</option>');

                if (classId) {
                    $.ajax({
                        url: "{{ route('exam-entry.get-sections') }}",
                        type: 'GET',
                        data: { class_id: classId },
                        success: function(response) {
                            if (response.success) {
                                $.each(response.data, function(index, section) {
                                    $('#section_id').append('<option value="' + section.id + '">' + section.name + '</option>');
                                });
                                $('#section_id').trigger('change');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading sections:', error);
                            console.log('Response:', xhr.responseText);
                        }
                    });
                }
            });

            // Section change - load subjects
            $('#section_id').on('change', function() {
                var sectionId = $(this).val();
                var sessionId = $('#session_id').val();
                var classId = $('#class_id').val();

                $('#subject_id').prop('disabled', true).html('<option value="">{{ ___("common.select") }}</option>');

                if (sectionId && sessionId && classId) {
                    $.ajax({
                        url: "{{ route('exam-entry.get-subjects') }}",
                        type: 'GET',
                        data: {
                            session_id: sessionId,
                            class_id: classId,
                            section_id: sectionId
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#subject_id').prop('disabled', false);
                                $.each(response.data, function(index, subject) {
                                    $('#subject_id').append('<option value="' + subject.id + '">' + subject.name + '</option>');
                                });
                                $('#subject_id').trigger('change');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error loading subjects:', error);
                            console.log('Response:', xhr.responseText);
                        }
                    });
                }
            });

            // Get Students Button (Manual Entry)
            $('#getStudentsBtn').on('click', function() {
                if (!validateParameters()) return;

                var formData = $('#parametersForm').serialize();

                $.ajax({
                    url: "{{ route('exam-entry.get-students') }}",
                    type: 'GET',
                    data: formData,
                    beforeSend: function() {
                        $('#getStudentsBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
                    },
                    success: function(response) {
                        if (response.success) {
                            studentsData = response.data;
                            subjectsData = response.data.subjects;
                            renderManualEntryTable();
                            $('#excelUploadSection').hide();
                            $('#manualEntrySection').show();
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON;
                        if (errors && errors.errors) {
                            $.each(errors.errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else {
                            toastr.error('Error loading students');
                        }
                    },
                    complete: function() {
                        $('#getStudentsBtn').prop('disabled', false).html('<i class="fa-solid fa-users"></i> {{ ___("examination.get_students") }}');
                    }
                });
            });

            // Download Template Button (Excel Entry)
            $('#downloadTemplateBtn').on('click', function() {
                if (!validateParameters()) return;

                var formData = $('#parametersForm').serialize();
                window.location.href = "{{ route('exam-entry.download-template') }}?" + formData;

                $('#manualEntrySection').hide();
                $('#excelUploadSection').show();
            });

            // Save Manual Entry
            $('#saveManualEntryBtn').on('click', function() {
                var resultsData = collectManualEntryData();
                var formData = $('#parametersForm').serializeArray();

                formData.push({ name: 'entry_method', value: 'manual' });

                // Properly append nested results array structure
                Object.keys(resultsData).forEach(function(studentId) {
                    Object.keys(resultsData[studentId]).forEach(function(subjectId) {
                        formData.push({
                            name: 'results[' + studentId + '][' + subjectId + ']',
                            value: resultsData[studentId][subjectId]
                        });
                    });
                });

                $.ajax({
                    url: "{{ route('exam-entry.store') }}",
                    type: 'POST',
                    data: $.param(formData),
                    beforeSend: function() {
                        $('#saveManualEntryBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = "{{ route('exam-entry.index') }}";
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON;
                        if (errors && errors.errors) {
                            $.each(errors.errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else if (errors && errors.message) {
                            toastr.error(errors.message);
                        }
                    },
                    complete: function() {
                        $('#saveManualEntryBtn').prop('disabled', false).html('<i class="fa-solid fa-save"></i> {{ ___("common.save") }}');
                    }
                });
            });

            // Upload Excel
            $('#excelUploadForm').on('submit', function(e) {
                e.preventDefault();

                // Custom file validation
                var fileInput = $('#exam_file')[0];

                // Check if file is selected
                if (!fileInput.files || fileInput.files.length === 0) {
                    toastr.error('{{ ___("examination.please_select_excel_file") }}' || 'Please select an Excel file to upload');
                    $('#exam_file').focus();
                    return false;
                }

                // Validate file type
                var fileName = fileInput.files[0].name;
                var fileExt = fileName.split('.').pop().toLowerCase();
                if (fileExt !== 'xlsx' && fileExt !== 'xls') {
                    toastr.error('{{ ___("examination.invalid_file_type") }}' || 'Please select a valid Excel file (.xlsx or .xls)');
                    $('#exam_file').val('');
                    $('#file-selected-feedback').hide();
                    return false;
                }

                // Validate file size (max 5MB)
                var maxSize = 5 * 1024 * 1024; // 5MB in bytes
                if (fileInput.files[0].size > maxSize) {
                    toastr.error('{{ ___("examination.file_too_large") }}' || 'File size must not exceed 5MB');
                    $('#exam_file').val('');
                    $('#file-selected-feedback').hide();
                    return false;
                }

                var formData = new FormData(this);
                var parametersData = $('#parametersForm').serializeArray();

                $.each(parametersData, function(index, field) {
                    formData.append(field.name, field.value);
                });

                $.ajax({
                    url: "{{ route('exam-entry.upload-results') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#uploadExcelBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Uploading...');
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            window.location.href = "{{ route('exam-entry.index') }}";
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON;
                        if (errors && errors.errors) {
                            $.each(errors.errors, function(key, value) {
                                toastr.error(value[0]);
                            });
                        } else if (errors && errors.message) {
                            toastr.error(errors.message);
                        }
                    },
                    complete: function() {
                        $('#uploadExcelBtn').prop('disabled', false).html('<i class="fa-solid fa-upload"></i> {{ ___("examination.upload_exams") }}');
                    }
                });
            });

            // Cancel buttons
            $('#cancelManualEntryBtn').on('click', function() {
                $('#manualEntrySection').hide();
                $('#studentsTableBody').html('');
            });

            $('#cancelExcelUploadBtn').on('click', function() {
                $('#excelUploadSection').hide();
                $('#exam_file').val('');
                $('#file-selected-feedback').hide();
            });

            // File selection feedback
            $('#exam_file').on('change', function() {
                var files = this.files;
                if (files && files.length > 0) {
                    var fileName = files[0].name;
                    var fileSize = (files[0].size / 1024).toFixed(2); // Size in KB
                    $('#file-selected-name').text(fileName + ' (' + fileSize + ' KB)');
                    $('#file-selected-feedback').slideDown(200);
                } else {
                    $('#file-selected-feedback').slideUp(200);
                }
            });

            // Helper Functions
            function validateParameters() {
                var required = ['session_id', 'term_id', 'class_id', 'section_id', 'exam_type_id', 'subject_id', 'total_marks'];
                var isValid = true;

                $.each(required, function(index, field) {
                    if (!$('#' + field).val()) {
                        toastr.error('Please fill all required fields');
                        isValid = false;
                        return false;
                    }
                });

                return isValid;
            }

            function renderManualEntryTable() {
                // Clear existing table
                $('#tableHeaders').html('');
                $('#studentsTableBody').html('');

                // Build headers
                var headers = `
                    <th>{{ ___('common.sr_no') }}</th>
                    <th>{{ ___('student.student_name') }}</th>
                    <th>{{ ___('student_info.grade') }}</th>
                    <th>{{ ___('academic.class') }}</th>
                    <th>{{ ___('academic.section') }}</th>
                `;

                $.each(subjectsData, function(index, subject) {
                    headers += '<th>' + subject.name + ' (/' + subject.total_marks + ')</th>';
                });

                $('#tableHeaders').html(headers);

                // Build rows
                $.each(studentsData.students, function(index, student) {
                    var row = `
                        <tr data-student-id="${student.id}">
                            <td>${index + 1}</td>
                            <td>${student.full_name || '-'}</td>
                            <td>${student.grade || '-'}</td>
                            <td>${student.class_name || '-'}</td>
                            <td>${student.section_name || '-'}</td>
                    `;

                    $.each(subjectsData, function(idx, subject) {
                        row += `<td><input type="number" class="form-control form-control-sm marks-input" data-student-id="${student.id}" data-subject-id="${subject.id}" min="0" max="${subject.total_marks}" step="0.01"></td>`;
                    });

                    row += '</tr>';
                    $('#studentsTableBody').append(row);
                });
            }

            function collectManualEntryData() {
                var results = {};

                $('.marks-input').each(function() {
                    var studentId = $(this).data('student-id');
                    var subjectId = $(this).data('subject-id');
                    var marks = $(this).val();

                    if (!results[studentId]) {
                        results[studentId] = {};
                    }

                    if (marks !== '') {
                        results[studentId][subjectId] = parseFloat(marks);
                    }
                });

                return results;
            }
        });
    </script>
@endpush
