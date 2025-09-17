@extends('backend.master')
@section('title')
    {{ @$data['title'] }}
@endsection
@section('content')
    <div class="page-content">

        {{-- breadcrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('fees-generation.index') }}">{{ ___('fees.fee_generation') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area E n d --}}

        {{-- Filter Section --}}
        <div class="row">
            <div class="col-12">
                <div class="card ot-card mb-24 position-relative z_1">
                    <form action="{{ route('fees-generation.service-reports.search') }}" enctype="multipart/form-data" method="post" id="service-reports-filter">
                        @csrf
                        <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                            <h3 class="mb-0">{{ ___('common.Filtering') }}</h3>

                            <div class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">
                                <!-- Class Selection -->
                                <div class="single_selectBox">
                                    <select id="getSections" class="class nice-select niceSelect bordered_style wide" name="class">
                                        <option value="">{{ ___('student_info.select_class') }}</option>
                                        @foreach ($data['classes'] as $item)
                                            <option {{ old('class') == $item->class->id ? 'selected' : '' }} value="{{ $item->class->id }}">{{ $item->class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Section Selection -->
                                <div class="single_selectBox">
                                    <select class="sections section nice-select niceSelect bordered_style wide" name="section">
                                        <option value="">{{ ___('student_info.select_section') }}</option>
                                        @if(isset($data['sections']))
                                            @foreach($data['sections'] as $section)
                                                @php
                                                    // Support both array shape (['id','name']) and object shape (section relation)
                                                    $sectionId = data_get($section, 'id')
                                                        ?? data_get($section, 'section_id')
                                                        ?? data_get($section, 'section.id');
                                                    $sectionName = data_get($section, 'name')
                                                        ?? data_get($section, 'section.name');
                                                @endphp
                                                @if($sectionId)
                                                    <option {{ old('section') == $sectionId ? 'selected' : '' }} value="{{ $sectionId }}">{{ $sectionName }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                        </select>
                                    </div>

                                <!-- Payment Status Filter -->
                                <div class="single_selectBox">
                                    <select class="nice-select niceSelect bordered_style wide" name="payment_status">
                                        <option value="">{{ ___('fees.all_payment_status') }}</option>
                                        <option {{ old('payment_status') == 'paid' ? 'selected' : '' }} value="paid">{{ ___('fees.paid') }}</option>
                                        <option {{ old('payment_status') == 'unpaid' ? 'selected' : '' }} value="unpaid">{{ ___('fees.unpaid') }}</option>
                                        <option {{ old('payment_status') == 'overdue' ? 'selected' : '' }} value="overdue">{{ ___('fees.overdue') }}</option>
                                    </select>
                                </div>

                                <!-- Academic Year Filter -->
                                <div class="single_selectBox">
                                    <select class="nice-select niceSelect bordered_style wide" name="academic_year_id">
                                        <option value="">{{ ___('common.All') }} {{ ___('academic.academic_year') }}</option>
                                        @foreach($data['academic_years'] as $year)
                                            <option {{ old('academic_year_id') == $year['id'] ? 'selected' : '' }} value="{{ $year['id'] }}">
                                                {{ $year['name'] }} {{ $year['is_current'] ? '(' . ___('common.current') . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Fee Period Filter (Multi-select) -->
                                <div class="single_selectBox">
                                    <select class="form-control select2_multy wide nice-select bordered_style" name="billing_periods[]" multiple="multiple" id="billing-periods-select">
                                        <option value="">{{ ___('fees.select_fee_periods') }}</option>
                                        @foreach($data['billing_periods'] as $period)
                                            <option
                                                value="{{ $period['value'] }}"
                                                {{ in_array($period['value'], old('billing_periods', [])) ? 'selected' : '' }}
                                                class="{{ $period['is_current'] ? 'current-period' : ($period['is_past'] ? 'past-period' : 'future-period') }}">
                                                @if($period['is_current'])
                                                    🔥 {{ $period['label'] }} ({{ ___('common.current') }})
                                                @elseif($period['is_past'])
                                                    📅 {{ $period['label'] }}
                                                @else
                                                    ⏭️ {{ $period['label'] }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Student Search -->
                                <div class="input-group table_searchBox">
                                    <input name="name" type="text" class="form-control" placeholder="{{ ___('student_info.student_name') }}" aria-label="Search" value="{{ old('name') }}">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </span>
                                </div>

                                <!-- Search Button -->
                                <button type="submit" class="btn btn-lg ot-btn-primary">
                                    {{ ___('common.Search') }}
                                </button>

                                <!-- Clear Button -->
                                <a href="{{ route('fees-generation.service-reports') }}" class="btn btn-lg ot-btn-secondary">
                                    {{ ___('common.clear') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @isset($data['students'])
        {{-- Summary Cards --}}
        <div class="row mb-3">
            @php
                $totalStudents = $data['students']->total();
                $currentItems = $data['students']->getCollection();
                $paidCount = $currentItems->where('payment_method', '!=', null)->count();
                $unpaidCount = $currentItems->where('payment_method', null)->count();
                $overdueCount = $currentItems->where('payment_method', null)->filter(function($item) {
                    return $item->due_date && \Carbon\Carbon::parse($item->due_date)->isPast();
                })->count();
                $totalAmount = $currentItems->where('amount', '>', 0)->sum('amount');
                $paidAmount = $currentItems->where('payment_method', '!=', null)->sum('amount');
                $unpaidAmount = $currentItems->where('payment_method', null)->sum('amount');

                // Get unique periods from current data
                $uniquePeriods = $currentItems->where('billing_period', '!=', null)
                    ->pluck('billing_period')
                    ->unique()
                    ->map(function($period) {
                        try {
                            return \Carbon\Carbon::createFromFormat('Y-m', $period)->format('M Y');
                        } catch (Exception $e) {
                            return $period;
                        }
                    })
                    ->sort()
                    ->values()
                    ->toArray();
            @endphp
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card ot-card border-primary">
                    <div class="card-body text-center p-3">
                        <h3 class="text-primary mb-1">{{ $totalStudents }}</h3>
                        <small class="text-muted">{{ ___('fees.total_fee_records') }}</small>
                        @if(!empty($uniquePeriods))
                            <div class="mt-2">
                                @foreach(array_slice($uniquePeriods, 0, 3) as $period)
                                    <span class="badge badge-basic-info-text me-1">{{ $period }}</span>
                                @endforeach
                                @if(count($uniquePeriods) > 3)
                                    <small class="text-muted d-block">+{{ count($uniquePeriods) - 3 }} more</small>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card ot-card border-success">
                    <div class="card-body text-center p-3">
                        <h3 class="text-success mb-1">{{ $paidCount }}</h3>
                        <small class="text-muted">{{ ___('fees.paid_fees') }}</small>
                        @if($paidAmount > 0)
                            <small class="d-block text-success fw-bold">{{ setting('currency_symbol') }}{{ number_format($paidAmount, 0) }}</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card ot-card border-warning">
                    <div class="card-body text-center p-3">
                        <h3 class="text-warning mb-1">{{ $unpaidCount }}</h3>
                        <small class="text-muted">{{ ___('fees.unpaid_fees') }}</small>
                        @if($unpaidAmount > 0)
                            <small class="d-block text-warning fw-bold">{{ setting('currency_symbol') }}{{ number_format($unpaidAmount, 0) }}</small>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card ot-card border-danger">
                    <div class="card-body text-center p-3">
                        <h3 class="text-danger mb-1">{{ $overdueCount }}</h3>
                        <small class="text-muted">{{ ___('fees.overdue_fees') }}</small>
                        @if($overdueCount > 0)
                            @php
                                $overdueAmount = $currentItems->where('payment_method', null)->filter(function($item) {
                                    return $item->due_date && \Carbon\Carbon::parse($item->due_date)->isPast();
                                })->sum('amount');
                            @endphp
                            <small class="d-block text-danger fw-bold">{{ setting('currency_symbol') }}{{ number_format($overdueAmount, 0) }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Results Table --}}
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('fees.service_based_student_fees') }}</h4>
                    <div class="d-flex gap-2">
                        @if($data['students']->count() > 0)
                            <button class="btn btn-sm ot-btn-success" onclick="exportData('excel')">
                                <i class="fa-solid fa-file-excel"></i> {{ ___('common.export_excel') }}
                            </button>
                            <button class="btn btn-sm ot-btn-danger" onclick="exportData('pdf')">
                                <i class="fa-solid fa-file-pdf"></i> {{ ___('common.export_pdf') }}
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered role-table" id="service_reports_table">
                            <thead class="thead">
                                <tr>
                                    <th class="purchase">{{ ___('student_info.student_name') }}</th>
                                    <th class="purchase">{{ ___('academic.class') }} ({{ ___('academic.section') }})</th>
                                    <th class="purchase">{{ ___('fees.service_type') }}</th>
                                    <th class="purchase">{{ ___('fees.amount') }}</th>
                                    <th class="purchase">{{ ___('fees.fee_period') }}</th>
                                    <th class="purchase">{{ ___('fees.due_date') }}</th>
                                    <th class="purchase">{{ ___('fees.payment_status') }}</th>
                                    <th class="purchase">{{ ___('common.batch_id') }}</th>
                                    @if (hasPermission('fees_collect_create'))
                                        <th class="purchase">{{ ___('common.action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data['students'] as $item)
                                <tr>
                                    <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                                    <td>
                                        @if($item->class_name && $item->section_name)
                                            {{ $item->class_name }} ({{ $item->section_name }})
                                        @else
                                            {{ ___('common.not_available') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->fee_type_name)
                                            <span class="badge badge-basic-info-text">{{ $item->fee_type_name }}</span>
                                            @if($item->fee_category)
                                                <small class="d-block text-muted">{{ ucfirst($item->fee_category) }}</small>
                                            @endif
                                        @else
                                            {{ ___('common.not_available') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->amount)
                                            <span class="fw-bold text-primary">{{ setting('currency_symbol') }}{{ number_format($item->amount, 2) }}</span>
                                        @else
                                            {{ ___('common.not_available') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->billing_period)
                                            @php
                                                try {
                                                    $periodDate = \Carbon\Carbon::createFromFormat('Y-m', $item->billing_period);
                                                    $periodLabel = $periodDate->format('F Y');
                                                    $isCurrentPeriod = $periodDate->format('Y-m') === now()->format('Y-m');
                                                } catch (Exception $e) {
                                                    $periodLabel = $item->billing_period;
                                                    $isCurrentPeriod = false;
                                                }
                                            @endphp
                                            <span class="badge {{ $isCurrentPeriod ? 'badge-basic-success-text' : 'badge-basic-info-text' }}">
                                                {{ $periodLabel }}
                                            </span>
                                            @if($isCurrentPeriod)
                                                <small class="d-block text-success">({{ ___('common.current') }})</small>
                                            @endif
                                        @else
                                            {{ ___('common.not_available') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->due_date)
                                            {{ dateFormat($item->due_date) }}
                                            @if(\Carbon\Carbon::parse($item->due_date)->isPast() && !$item->payment_method)
                                                <small class="d-block text-danger">({{ ___('fees.overdue') }})</small>
                                            @endif
                                        @else
                                            {{ ___('common.not_available') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->payment_method)
                                            <span class="badge-basic-success-text">{{ ___('fees.paid') }}</span>
                                            <small class="d-block text-muted">{{ ucfirst($item->payment_method) }}</small>
                                        @elseif($item->due_date && \Carbon\Carbon::parse($item->due_date)->isPast())
                                            <span class="badge-basic-danger-text">{{ ___('fees.overdue') }}</span>
                                        @else
                                            <span class="badge-basic-warning-text">{{ ___('fees.unpaid') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->batch_id)
                                            <span class="badge-basic-primary-text">{{ $item->batch_id }}</span>
                                            @if($item->generation_date)
                                                <small class="d-block text-muted">{{ dateFormat($item->generation_date) }}</small>
                                                @php
                                                    $statusClass = match($item->generation_status) {
                                                        'completed' => 'badge-basic-success-text',
                                                        'processing' => 'badge-basic-warning-text',
                                                        'failed' => 'badge-basic-danger-text',
                                                        'cancelled' => 'badge-basic-secondary-text',
                                                        default => 'badge-basic-info-text'
                                                    };
                                                @endphp
                                                <small class="{{ $statusClass }}">{{ ucfirst($item->generation_status) }}</small>
                                            @endif
                                        @else
                                            {{ ___('common.not_available') }}
                                        @endif
                                    </td>
                                    @if (hasPermission('fees_collect_create'))
                                        <td>
                                            <div class="dropdown dropdown-action">
                                                <button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    @if($item->student_id)
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                               data-bs-toggle="modal"
                                                               data-bs-target="#modalCustomizeWidth"
                                                               onclick="openFeeCollectionModal({{ $item->student_id }}, '{{ $item->student->first_name ?? 'Student' }} {{ $item->student->last_name ?? '' }}')">
                                                                <span class="icon mr-8"><i class="fa-solid fa-money-bill"></i></span>
                                                                {{ ___('fees.collect_fee') }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if($item->batch_id)
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('fees-generation.show', ['id' => $item->batch_id]) }}">
                                                                <span class="icon mr-8"><i class="fa-solid fa-eye"></i></span>
                                                                {{ ___('fees.view_generation') }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if($item->payment_method)
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0);" onclick="generateReceipt('{{ $item->student_id }}', '{{ $item->batch_id }}')">
                                                                <span class="icon mr-8"><i class="fa-solid fa-receipt"></i></span>
                                                                {{ ___('fees.generate_receipt') }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="100%" class="text-center gray-color">
                                        <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary" width="100">
                                        <p class="mb-0 text-center">{{ ___('common.no_data_available') }}</p>
                                        <p class="mb-0 text-center text-secondary font-size-90">
                                            {{ ___('fees.no_service_based_fees_found') }}
                                        </p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if(isset($data['students']) && $data['students']->hasPages())
                    <div class="ot-pagination pagination-content d-flex justify-content-end align-content-center py-3">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-between">
                                {!! $data['students']->appends(\Request::capture()->except('page'))->links() !!}
                            </ul>
                        </nav>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endisset
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
<script>
$(document).ready(function() {
    // Load sections when class changes (same as other pages)
    $('#getSections').on('change', function() {
        const classId = $(this).val();
        const sectionSelect = $('.sections');

        if (classId) {
            $.get('{{ route("fees-generation.index") }}/get-sections', {
                class_ids: [classId]
            }).done(function(response) {
                if (response.success) {
                    let options = '<option value="">{{ ___("common.all_sections") }}</option>';
                    response.data.forEach(section => {
                        options += `<option value="${section.id}">${section.name}</option>`;
                    });
                    sectionSelect.html(options);

                    // Update NiceSelect to refresh the dropdown display
                    if (typeof sectionSelect.niceSelect !== 'undefined') {
                        sectionSelect.niceSelect('update');
                    }
                }
            }).fail(function() {
                sectionSelect.html('<option value="">{{ ___("common.error_loading_sections") }}</option>');
                if (typeof sectionSelect.niceSelect !== 'undefined') {
                    sectionSelect.niceSelect('update');
                }
            });
        } else {
            sectionSelect.html('<option value="">{{ ___("student_info.select_section") }}</option>');
            // Update NiceSelect to refresh the dropdown display
            if (typeof sectionSelect.niceSelect !== 'undefined') {
                sectionSelect.niceSelect('update');
            }
        }
    });

    // Export functionality
    window.exportData = function(format) {
        const form = $('#service-reports-filter');
        const formData = form.serialize() + '&export=' + format;

        // Create a temporary form for export
        const exportForm = $('<form>', {
            method: 'POST',
            action: form.attr('action'),
            target: '_blank'
        });

        // Add all form data as hidden inputs
        const params = new URLSearchParams(formData);
        for (const [key, value] of params) {
            exportForm.append($('<input>', {
                type: 'hidden',
                name: key,
                value: value
            }));
        }

        // Submit the form
        $('body').append(exportForm);
        exportForm.submit();
        exportForm.remove();
    };

    // Generate receipt functionality
    window.generateReceipt = function(studentId, batchId) {
        // This would need to be implemented based on existing receipt system
        console.log('Generate receipt for student:', studentId, 'batch:', batchId);
        // You can integrate with existing receipt generation routes
    };

    // Fee collection modal functionality
    window.openFeeCollectionModal = function(studentId, studentName, admissionNo = null) {
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

        // Fetch student's unpaid fees via AJAX
        fetchStudentFees(studentId).then(feesData => {
            if (feesData && feesData.success) {
                window.populateFeeCollectionModal(studentId, feesData.data, studentInfo);
            } else {
                showErrorMessage('Unable to load student fees. Please try again.');
            }
        }).catch(error => {
            console.error('Error fetching student fees:', error);
            showErrorMessage('An error occurred while loading student fees.');
        });
    };

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
});
</script>

@include('backend.fees.collect.fee-collection-modal-script')
@endpush

@push('style')
<style>
    .badge-basic-info-text {
        background-color: #d1ecf1;
        color: #0c5460;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .table td {
        vertical-align: middle;
    }

    .single_selectBox input[type="date"] {
        padding: 10px;
        border: 1px solid #e4e6ea;
        border-radius: 8px;
        font-size: 14px;
        min-width: 150px;
    }

    .card_header_right .single_selectBox {
        min-width: 120px;
    }

    .table_searchBox {
        min-width: 200px;
    }

    @media (max-width: 768px) {
        .card_header_right {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }

        .single_selectBox,
        .table_searchBox {
            min-width: 100%;
        }
    }
</style>
@endpush
