@extends('backend.master')
@section('title')
    @lang('Edit Exam Entry')
@endsection
@section('content')
    <div class="page-content">

        {{-- breadcrumb Area Start --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ ___('examination.edit_exam_entry') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('exam-entry.index') }}">{{ ___('examination.exam_entry') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('common.edit') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area End --}}

        <!-- Edit Entry Section -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-edit"></i> {{ ___('examination.edit_marks') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <p><strong>{{ ___('examination.session') }}:</strong> {{ $examEntry->session->name }}</p>
                            <p><strong>{{ ___('examination.term') }}:</strong> {{ $examEntry->term->termDefinition->name ?? 'N/A' }}</p>
                            <p><strong>{{ ___('academic.class') }}:</strong> {{ $examEntry->class->name }} - {{ $examEntry->section->name }}</p>
                            <p><strong>{{ ___('examination.exam_type') }}:</strong> {{ $examEntry->examType->name }}</p>
                            <p><strong>{{ ___('examination.total_marks') }}:</strong> {{ $examEntry->total_marks }}</p>
                        </div>
                    </div>

                    <form id="editMarksForm">
                        @csrf
                        @method('PUT')
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead">
                                    <tr>
                                        <th>{{ ___('common.sr_no') }}</th>
                                        <th>{{ ___('student.student_name') }}</th>
                                        @foreach($subjects as $subject)
                                            <th>{{ $subject['name'] }} (/{{ $subject['total_marks'] }})</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $student->full_name }}</td>
                                            @foreach($subjects as $subject)
                                                @php
                                                    $result = $examEntry->results->where('student_id', $student->id)->where('subject_id', $subject['id'])->first();
                                                    $marks = $result ? $result->obtained_marks : '';
                                                @endphp
                                                <td>
                                                    <input type="number"
                                                           class="form-control form-control-sm marks-input"
                                                           data-student-id="{{ $student->id }}"
                                                           data-subject-id="{{ $subject['id'] }}"
                                                           min="0"
                                                           max="{{ $subject['total_marks'] }}"
                                                           step="0.01"
                                                           value="{{ $marks }}">
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12 text-end">
                                <a href="{{ route('exam-entry.index') }}" class="btn btn-lg ot-btn-danger">
                                    <span><i class="fa-solid fa-times"></i></span>
                                    <span>{{ ___('common.cancel') }}</span>
                                </a>
                                <button type="submit" class="btn btn-lg ot-btn-primary" id="updateMarksBtn">
                                    <span><i class="fa-solid fa-save"></i></span>
                                    <span>{{ ___('common.update') }}</span>
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
    <script>
        $(document).ready(function() {
            // Update marks
            $('#editMarksForm').on('submit', function(e) {
                e.preventDefault();

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

                $.ajax({
                    url: "{{ route('exam-entry.update', $examEntry->id) }}",
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        results: results
                    },
                    beforeSend: function() {
                        $('#updateMarksBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Updating...');
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
                        $('#updateMarksBtn').prop('disabled', false).html('<i class="fa-solid fa-save"></i> {{ ___("common.update") }}');
                    }
                });
            });
        });
    </script>
@endpush
