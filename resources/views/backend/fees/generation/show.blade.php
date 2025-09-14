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
                                    @php
                                        $filters = $data['generation']->filters ?? [];
                                        // Support legacy (classes/sections) and enhanced (class_ids/section_ids)
                                        $filterClasses = $filters['classes'] ?? $filters['class_ids'] ?? [];
                                        $filterSections = $filters['sections'] ?? $filters['section_ids'] ?? [];

                                        $classNames = !empty($filterClasses)
                                            ? \App\Models\Academic\Classes::whereIn('id', (array)$filterClasses)->pluck('name')->toArray()
                                            : [];
                                        $sectionNames = !empty($filterSections)
                                            ? \App\Models\Academic\Section::whereIn('id', (array)$filterSections)->pluck('name')->toArray()
                                            : [];

                                        // If no filters were specified, infer from logs
                                        if (empty($classNames) && !empty($data['generation']->logs)) {
                                            $classNames = collect($data['generation']->logs)
                                                ->map(function($log){ return $log->student->sessionStudentDetails->class->name ?? null; })
                                                ->filter()
                                                ->unique()
                                                ->values()
                                                ->toArray();
                                        }
                                        if (empty($sectionNames) && !empty($data['generation']->logs)) {
                                            $sectionNames = collect($data['generation']->logs)
                                                ->map(function($log){ return $log->student->sessionStudentDetails->section->name ?? null; })
                                                ->filter()
                                                ->unique()
                                                ->values()
                                                ->toArray();
                                        }

                                        // Resolve fee period from filters
                                        $periodLabel = null;
                                        if (!empty($filters['generation_month'])) {
                                            // Enhanced monthly format 'Y-m'
                                            try {
                                                $periodLabel = \Carbon\Carbon::createFromFormat('Y-m', $filters['generation_month'])->format('F Y');
                                            } catch (Exception $e) {
                                                $periodLabel = $filters['generation_month'];
                                            }
                                        } elseif (!empty($filters['month']) && !empty($filters['year'])) {
                                            $m = (int)$filters['month'];
                                            $y = (int)$filters['year'];
                                            $periodLabel = date('F', mktime(0, 0, 0, $m, 1)) . ' ' . $y;
                                        } elseif (!empty($data['generation']->feesCollects) && $data['generation']->feesCollects->count() > 0) {
                                            // Fallback: infer from generated fee records
                                            $first = $data['generation']->feesCollects->first();
                                            if (!empty($first->billing_period)) {
                                                try {
                                                    $periodLabel = \Carbon\Carbon::createFromFormat('Y-m', $first->billing_period)->format('F Y');
                                                } catch (Exception $e) {
                                                    $periodLabel = $first->billing_period;
                                                }
                                            } elseif (!empty($first->date)) {
                                                try {
                                                    $periodLabel = \Carbon\Carbon::parse($first->date)->format('F Y');
                                                } catch (Exception $e) {
                                                    $periodLabel = null;
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="alert alert-info">
                                        <strong>{{ ___('fees.classes') }}:</strong> {{ !empty($classNames) ? implode(', ', $classNames) : ___('common.all') }}<br>
                                        <strong>{{ ___('fees.sections') }}:</strong> {{ !empty($sectionNames) ? implode(', ', $sectionNames) : ___('common.all') }}<br>
                                        <strong>{{ ___('fees.fee_period') }}:</strong> {{ $periodLabel ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Student Details -->
                            @if($data['generation']->logs && $data['generation']->logs->count() > 0)
                            <div class="row">
                                <div class="col-12">
                                    <h5>{{ ___('fees.student_details') }}</h5>
                                    @php
                                        // Global fee period for this generation (fallback for rows)
                                        $globalFilters = $data['generation']->filters ?? [];
                                        $globalPeriod = null;
                                        if (!empty($globalFilters['generation_month'])) {
                                            try {
                                                $globalPeriod = \Carbon\Carbon::createFromFormat('Y-m', $globalFilters['generation_month'])->format('F Y');
                                            } catch (Exception $e) {
                                                $globalPeriod = $globalFilters['generation_month'];
                                            }
                                        } elseif (!empty($globalFilters['month']) && !empty($globalFilters['year'])) {
                                            $gm = (int)$globalFilters['month'];
                                            $gy = (int)$globalFilters['year'];
                                            $globalPeriod = date('F', mktime(0, 0, 0, $gm, 1)) . ' ' . $gy;
                                        } elseif (!empty($data['generation']->feesCollects) && $data['generation']->feesCollects->count() > 0) {
                                            // Fallback: infer from generated fee records
                                            $first = $data['generation']->feesCollects->first();
                                            if (!empty($first->billing_period)) {
                                                try {
                                                    $globalPeriod = \Carbon\Carbon::createFromFormat('Y-m', $first->billing_period)->format('F Y');
                                                } catch (Exception $e) {
                                                    $globalPeriod = $first->billing_period;
                                                }
                                            } elseif (!empty($first->date)) {
                                                try {
                                                    $globalPeriod = \Carbon\Carbon::parse($first->date)->format('F Y');
                                                } catch (Exception $e) {
                                                    $globalPeriod = null;
                                                }
                                            }
                                        }
                                    @endphp
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ ___('student.student_name') }}</th>
                                                    <th>{{ ___('student.admission_no') }}</th>
                                                    <th>{{ ___('academic.class') }}</th>
                                                    <th>{{ ___('academic.section') }}</th>
                                                    <th>{{ ___('fees.fee_period') }}</th>
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
                                                        @php
                                                            $rowPeriod = null;
                                                            if (!empty($log->feesCollect) && !empty($log->feesCollect->billing_period)) {
                                                                try {
                                                                    $rowPeriod = \Carbon\Carbon::createFromFormat('Y-m', $log->feesCollect->billing_period)->format('F Y');
                                                                } catch (Exception $e) {
                                                                    $rowPeriod = $log->feesCollect->billing_period;
                                                                }
                                                            }
                                                        @endphp
                                                        {{ $rowPeriod ?? $globalPeriod ?? 'N/A' }}
                                                    </td>
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
