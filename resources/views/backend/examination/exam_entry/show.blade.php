@extends('backend.master')
@section('title')
    @lang('View Exam Entry')
@endsection
@section('content')
    <div class="page-content">

        {{-- breadcrumb Area Start --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ ___('examination.exam_entry_details') }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('exam-entry.index') }}">{{ ___('examination.exam_entry') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('common.view') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area End --}}

        <!-- Statistics Section -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-bar"></i> {{ ___('examination.statistics') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="stat-box text-center p-3 border rounded">
                                <h5>{{ $statistics['total_students'] }}</h5>
                                <p class="text-muted mb-0">{{ ___('examination.total_students') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box text-center p-3 border rounded">
                                <h5 class="text-success">{{ $statistics['present_students'] }}</h5>
                                <p class="text-muted mb-0">{{ ___('examination.present_students') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box text-center p-3 border rounded">
                                <h5 class="text-danger">{{ $statistics['absent_students'] }}</h5>
                                <p class="text-muted mb-0">{{ ___('examination.absent_students') }}</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box text-center p-3 border rounded">
                                <h5 class="text-info">{{ $statistics['pass_percentage'] }}%</h5>
                                <p class="text-muted mb-0">{{ ___('examination.pass_percentage') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="stat-box text-center p-3 border rounded">
                                <h5>{{ $statistics['average_marks'] }}</h5>
                                <p class="text-muted mb-0">{{ ___('examination.average_marks') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-box text-center p-3 border rounded">
                                <h5 class="text-success">{{ $statistics['highest_marks'] }}</h5>
                                <p class="text-muted mb-0">{{ ___('examination.highest_marks') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stat-box text-center p-3 border rounded">
                                <h5 class="text-warning">{{ $statistics['lowest_marks'] }}</h5>
                                <p class="text-muted mb-0">{{ ___('examination.lowest_marks') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Details Section -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-info-circle"></i> {{ ___('examination.exam_details') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ ___('examination.session') }}:</strong> {{ $examEntry->session->name }}</p>
                            <p><strong>{{ ___('examination.term') }}:</strong> {{ $examEntry->term->termDefinition->name ?? 'N/A' }}</p>
                            @if($examEntry->grade)
                                <p><strong>{{ ___('student_info.grade') }}:</strong> {{ $examEntry->grade }}</p>
                            @endif
                            <p><strong>{{ ___('academic.class') }}:</strong> {{ $examEntry->class->name }}</p>
                            <p><strong>{{ ___('academic.section') }}:</strong> {{ $examEntry->section->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ ___('examination.exam_type') }}:</strong> {{ $examEntry->examType->name }}</p>
                            <p><strong>{{ ___('academic.subject') }}:</strong>
                                @if($examEntry->is_all_subjects)
                                    <span class="text-primary">All Subjects</span>
                                @else
                                    {{ $examEntry->subject->name }}
                                @endif
                            </p>
                            <p><strong>{{ ___('examination.total_marks') }}:</strong> {{ $examEntry->total_marks }}</p>
                            <p><strong>{{ ___('examination.entry_method') }}:</strong>
                                @if($examEntry->entry_method === 'manual')
                                    <span class="badge bg-primary"><i class="fas fa-keyboard"></i> Manual</span>
                                @else
                                    <span class="badge bg-success"><i class="fas fa-file-excel"></i> Excel</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <p><strong>{{ ___('common.status') }}:</strong>
                                @php
                                    $statusBadge = [
                                        'draft' => 'warning',
                                        'completed' => 'info',
                                        'published' => 'success'
                                    ][$examEntry->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusBadge }}">{{ ucfirst($examEntry->status) }}</span>
                            </p>
                            <p><strong>{{ ___('common.created_by') }}:</strong> {{ $examEntry->creator->name }}</p>
                            <p><strong>{{ ___('common.created_at') }}:</strong> {{ $examEntry->created_at->format('d M, Y h:i A') }}</p>
                            @if($examEntry->published_at)
                                <p><strong>{{ ___('examination.published_at') }}:</strong> {{ $examEntry->published_at->format('d M, Y h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Table Section -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-list-alt"></i> {{ ___('examination.student_results') }}
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead">
                                <tr>
                                    <th>{{ ___('common.sr_no') }}</th>
                                    <th>{{ ___('student.student_name') }}</th>
                                    <th>{{ ___('student_info.grade') }}</th>
                                    <th>{{ ___('academic.class') }}</th>
                                    <th>{{ ___('academic.section') }}</th>
                                    @foreach($subjects as $subject)
                                        <th>{{ $subject->name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = 1;
                                @endphp
                                @foreach($studentResults as $student)
                                    <tr>
                                        <td>{{ $counter++ }}</td>
                                        <td>{{ $student['name'] }}</td>
                                        <td>{{ $student['grade'] }}</td>
                                        <td>{{ $student['class'] }}</td>
                                        <td>{{ $student['section'] }}</td>
                                        @foreach($subjects as $subject)
                                            <td>
                                                @php
                                                    $mark = $student['marks'][$subject->id] ?? null;
                                                @endphp
                                                @if($mark && $mark['is_absent'])
                                                    <span class="badge bg-danger">Absent</span>
                                                @elseif($mark && $mark['obtained_marks'] !== null)
                                                    {{ $mark['obtained_marks'] }}/{{ $mark['total_marks'] }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12 text-end">
                            <a href="{{ route('exam-entry.index') }}" class="btn btn-lg ot-btn-secondary">
                                <span><i class="fa-solid fa-arrow-left"></i></span>
                                <span>{{ ___('common.back') }}</span>
                            </a>
                            @if(hasPermission('exam_entry_update') && in_array($examEntry->status, ['draft', 'completed']))
                                <a href="{{ route('exam-entry.edit', $examEntry->id) }}" class="btn btn-lg ot-btn-primary">
                                    <span><i class="fa-solid fa-edit"></i></span>
                                    <span>{{ ___('common.edit') }}</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
