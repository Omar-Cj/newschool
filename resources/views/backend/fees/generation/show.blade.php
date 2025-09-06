@extends('backend.master')

@section('title')
{{ $data['title'] }}
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">{{ ___('fees.generation_details') }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('fees-generation.index') }}">{{ ___('fees.fee_generation') }}</a></li>
                            <li class="breadcrumb-item active">{{ ___('fees.generation_details') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- End page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">{{ ___('fees.generation_details') }}</h4>
                        <div class="card-title-desc">{{ ___('fees.view_generation_details') }}</div>
                    </div>
                    <div class="card-body">
                        @if($data['generation'])
                            <!-- Generation Summary -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h5>{{ ___('fees.generation_summary') }}</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ ___('fees.batch_id') }}:</strong></td>
                                            <td>{{ $data['generation']->batch_id }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ ___('common.status') }}:</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $data['generation']->status == 'completed' ? 'success' : ($data['generation']->status == 'failed' ? 'danger' : 'warning') }}">
                                                    {{ __(ucfirst($data['generation']->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ ___('fees.total_students') }}:</strong></td>
                                            <td>{{ $data['generation']->total_students }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ ___('fees.successful_students') }}:</strong></td>
                                            <td>{{ $data['generation']->successful_students }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ ___('fees.failed_students') }}:</strong></td>
                                            <td>{{ $data['generation']->failed_students }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ ___('fees.total_amount') }}:</strong></td>
                                            <td>{{ setting('currency_symbol') }} {{ number_format($data['generation']->total_amount ?? 0, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>{{ ___('fees.generation_timeline') }}</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>{{ ___('common.created_at') }}:</strong></td>
                                            <td>{{ dateFormat($data['generation']->created_at) }}</td>
                                        </tr>
                                        @if($data['generation']->started_at)
                                        <tr>
                                            <td><strong>{{ ___('fees.started_at') }}:</strong></td>
                                            <td>{{ dateFormat($data['generation']->started_at) }}</td>
                                        </tr>
                                        @endif
                                        @if($data['generation']->completed_at)
                                        <tr>
                                            <td><strong>{{ ___('fees.completed_at') }}:</strong></td>
                                            <td>{{ dateFormat($data['generation']->completed_at) }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>{{ ___('fees.created_by') }}:</strong></td>
                                            <td>{{ $data['generation']->creator->name ?? 'Unknown' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Filters Used -->
                            @if($data['generation']->filters)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5>{{ ___('fees.filters_used') }}</h5>
                                    <div class="alert alert-info">
                                        <strong>{{ ___('fees.classes') }}:</strong> {{ implode(', ', $data['generation']->filters['classes'] ?? []) }}<br>
                                        <strong>{{ ___('fees.sections') }}:</strong> {{ implode(', ', $data['generation']->filters['sections'] ?? []) }}<br>
                                        <strong>{{ ___('fees.month') }}:</strong> {{ $data['generation']->filters['month'] ?? 'N/A' }}<br>
                                        <strong>{{ ___('fees.year') }}:</strong> {{ $data['generation']->filters['year'] ?? 'N/A' }}<br>
                                        <strong>{{ ___('fees.fees_groups') }}:</strong> {{ implode(', ', $data['generation']->filters['fees_groups'] ?? []) }}
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Student Details -->
                            @if($data['generation']->logs && $data['generation']->logs->count() > 0)
                            <div class="row">
                                <div class="col-12">
                                    <h5>{{ ___('fees.student_details') }}</h5>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ ___('student.student_name') }}</th>
                                                    <th>{{ ___('student.admission_no') }}</th>
                                                    <th>{{ ___('academic.class') }}</th>
                                                    <th>{{ ___('academic.section') }}</th>
                                                    <th>{{ ___('common.status') }}</th>
                                                    <th>{{ ___('fees.amount') }}</th>
                                                    <th>{{ ___('common.error') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data['generation']->logs as $log)
                                                <tr>
                                                    <td>{{ $log->student->full_name ?? 'Unknown' }}</td>
                                                    <td>{{ $log->student->admission_no ?? 'N/A' }}</td>
                                                    <td>{{ $log->student->sessionStudentDetails->class->name ?? 'N/A' }}</td>
                                                    <td>{{ $log->student->sessionStudentDetails->section->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $log->status == 'success' ? 'success' : ($log->status == 'failed' ? 'danger' : 'warning') }}">
                                                            {{ __(ucfirst($log->status)) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ setting('currency_symbol') }} {{ number_format($log->amount ?? 0, 2) }}</td>
                                                    <td>
                                                        @if($log->error_message)
                                                            <span class="text-danger" title="{{ $log->error_message }}">
                                                                {{ Str::limit($log->error_message, 50) }}
                                                            </span>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($data['generation']->notes)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5>{{ ___('common.notes') }}</h5>
                                    <div class="alert alert-secondary">
                                        {{ $data['generation']->notes }}
                                    </div>
                                </div>
                            </div>
                            @endif
                        @else
                            <div class="alert alert-warning">
                                {{ ___('fees.generation_not_found') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
