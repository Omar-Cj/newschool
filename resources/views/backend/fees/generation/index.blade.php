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
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area E n d --}}


        {{-- Fee Generation Overview --}}
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('fees.fee_generation_overview') }}</h4>
                    <button type="button" class="btn btn-lg ot-btn-primary" data-bs-toggle="modal" data-bs-target="#feeGenerationModal">
                        <i class="fa-solid fa-plus"></i> {{ ___('fees.generate_fees') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card ot-card border-primary">
                                <div class="card-body text-center p-3">
                                    <h3 class="text-primary mb-1">{{ $data['total_available_services'] }}</h3>
                                    <small class="text-muted">{{ ___('fees.available_services') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card ot-card border-info">
                                <div class="card-body text-center p-3">
                                    <h3 class="text-info mb-1">{{ $data['enhanced_stats']['students_with_services'] ?? 0 }}</h3>
                                    <small class="text-muted">{{ ___('fees.students_with_services') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card ot-card border-success">
                                <div class="card-body text-center p-3">
                                    <h3 class="text-success mb-1">{{ $data['enhanced_stats']['total_active_services'] ?? 0 }}</h3>
                                    <small class="text-muted">{{ ___('fees.active_service_subscriptions') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card ot-card border-warning">
                                <div class="card-body text-center p-3">
                                    <h3 class="text-warning mb-1">{{ $data['total_classes'] ?? 0 }}</h3>
                                    <small class="text-muted">{{ ___('academic.total_classes') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info d-flex align-items-center mt-3">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <div>
                            <strong>{{ ___('fees.quick_info') }}:</strong>
                            {{ ___('fees.generation_info_text') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Generations --}}
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('fees.recent_generations') }}</h4>
                    <a href="{{ route('fees-generation.history') }}" class="btn btn-lg ot-btn-secondary">
                        <i class="fa-solid fa-history"></i> {{ ___('fees.view_all_history') }}
                    </a>
                                </div>
                                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered role-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('common.batch_id') }}</th>
                                    <th class="purchase">{{ ___('common.status') }}</th>
                                    <th class="purchase">{{ ___('fees.students') }}</th>
                                    <th class="purchase">{{ ___('fees.amount') }}</th>
                                    <th class="purchase">{{ ___('common.created_at') }}</th>
                                    <th class="action">{{ ___('common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @forelse($data['generations'] as $key => $generation)
                                    <tr id="row_{{ $generation->id }}">
                                        <td class="serial">{{ ++$key }}</td>
                                        <td>
                                            <span class="badge-basic-primary-text">{{ $generation->batch_id }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($generation->status) {
                                                    'completed' => 'badge-basic-success-text',
                                                    'processing' => 'badge-basic-warning-text',
                                                    'failed' => 'badge-basic-danger-text',
                                                    'cancelled' => 'badge-basic-secondary-text',
                                                    default => 'badge-basic-info-text'
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }}">{{ ucfirst($generation->status) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="text-success fw-bold">{{ $generation->successful_students }}</span>
                                                <span class="mx-1">/</span>
                                                <span class="text-muted">{{ $generation->total_students }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">{{ number_format($generation->total_amount, 2) }}</span>
                                        </td>
                                        <td>{{ $generation->created_at->format('M d, Y H:i') }}</td>
                                        <td class="action">
                                            <div class="dropdown dropdown-action">
                                                <button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('fees-generation.show', $generation->id) }}">
                                                            <span class="icon mr-8"><i class="fa-solid fa-eye"></i></span>
                                                            {{ ___('common.view_details') }}
                                                        </a>
                                                    </li>
                                                    @if($generation->canBeCancelled())
                                                        <li>
                                                            <a class="dropdown-item cancel-generation" href="javascript:void(0);" data-id="{{ $generation->id }}">
                                                                <span class="icon mr-8"><i class="fa-solid fa-stop"></i></span>
                                                                {{ ___('fees.cancel_generation') }}
                                                            </a>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center gray-color">
                                            <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary" width="100">
                                            <p class="mb-0 text-center">{{ ___('common.no_data_available') }}</p>
                                            <p class="mb-0 text-center text-secondary font-size-90">
                                                {{ ___('fees.no_recent_generations') }}
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Fee Generation Modal --}}
    <div class="modal fade" id="feeGenerationModal" tabindex="-1" aria-labelledby="feeGenerationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="feeGenerationModalLabel">
                        <i class="fa-solid fa-cogs me-2"></i>{{ ___('fees.bulk_fee_generation') }}
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                                    <form id="fee-generation-form">
                                        @csrf
                        
                        {{-- Hidden input to always use grade-based selection --}}
                        <input type="hidden" name="selection_method" value="grade">


                        {{-- Grade Selection --}}
                        <div id="grade-selection" class="row mb-3">
                            <div class="col-md-12 mb-3">
                                <label for="grades" class="form-label">{{ ___('student_info.grade') }} <span class="text-danger">*</span></label>
                                <select name="grades[]" id="grades" class="form-control select2" multiple>
                                    <optgroup label="{{ ___('fees.kindergarten') }}">
                                        <option value="KG-1">KG-1</option>
                                        <option value="KG-2">KG-2</option>
                                    </optgroup>
                                    <optgroup label="{{ ___('fees.primary') }}">
                                        <option value="Grade1">Grade 1</option>
                                        <option value="Grade2">Grade 2</option>
                                        <option value="Grade3">Grade 3</option>
                                        <option value="Grade4">Grade 4</option>
                                        <option value="Grade5">Grade 5</option>
                                        <option value="Grade6">Grade 6</option>
                                        <option value="Grade7">Grade 7</option>
                                        <option value="Grade8">Grade 8</option>
                                    </optgroup>
                                    <optgroup label="{{ ___('fees.secondary') }}">
                                        <option value="Form1">Form 1</option>
                                        <option value="Form2">Form 2</option>
                                        <option value="Form3">Form 3</option>
                                        <option value="Form4">Form 4</option>
                                    </optgroup>
                                </select>
                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-all-grades">
                                            <label class="form-check-label small" for="select-all-grades">
                                                {{ ___('common.select_all') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-kindergarten">
                                            <label class="form-check-label small" for="select-kindergarten">
                                                {{ ___('fees.select_all_kindergarten') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-primary">
                                            <label class="form-check-label small" for="select-primary">
                                                {{ ___('fees.select_all_primary') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="select-secondary">
                                            <label class="form-check-label small" for="select-secondary">
                                                {{ ___('fees.select_all_secondary') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">{{ ___('fees.grade_selection_info') }}</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            {{-- Month & Year Combined --}}
                            <div class="col-md-6 mb-3">
                                <label for="month_year" class="form-label">{{ ___('fees.fee_period') }} <span class="text-danger">*</span></label>
                                <select name="month_year" id="month_year" class="form-control" required>
                                    @php
                                        $currentMonth = (int)date('n');
                                        $currentYear = (int)date('Y');
                                        $currentMonthValue = $currentMonth . '-' . $currentYear;
                                        
                                        // Generate 5 months: 2 past + current + 2 future
                                        $months = [];
                                        for($offset = -2; $offset <= 2; $offset++) {
                                            $targetDate = mktime(0, 0, 0, $currentMonth + $offset, 1, $currentYear);
                                            $month = (int)date('n', $targetDate);
                                            $year = (int)date('Y', $targetDate);
                                            $monthName = date('F', $targetDate);
                                            
                                            $months[] = [
                                                'value' => $month . '-' . $year,
                                                'label' => $monthName . ' ' . $year,
                                                'month' => $month,
                                                'year' => $year,
                                                'is_current' => ($offset === 0),
                                                'is_past' => ($offset < 0),
                                                'is_future' => ($offset > 0)
                                            ];
                                        }
                                    @endphp
                                    
                                    @foreach($months as $monthData)
                                        <option value="{{ $monthData['value'] }}" {{ $monthData['is_current'] ? 'selected' : '' }}
                                            @if($monthData['is_past']) class="text-muted" @endif
                                            @if($monthData['is_current']) class="fw-bold text-primary" @endif
                                            @if($monthData['is_future']) class="text-info" @endif>
                                            @if($monthData['is_past'])
                                                📅 {{ $monthData['label'] }} (Past)
                                            @elseif($monthData['is_current'])
                                                🔥 {{ $monthData['label'] }} (Current)
                                            @else
                                                ⏭️ {{ $monthData['label'] }} (Future)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ ___('fees.showing_current_plus_nearby_months') }}</small>
                                {{-- Hidden inputs for backward compatibility --}}
                                <input type="hidden" name="month" id="month" value="{{ date('n') }}">
                                <input type="hidden" name="year" id="year" value="{{ date('Y') }}">
                            </div>

                        </div>

                        <div class="row mb-3">
                            {{-- Service Categories --}}
                            <div class="col-md-6 mb-3" id="enhanced-service-categories">
                                <label for="service_categories" class="form-label">{{ ___('fees.service_categories') }}</label>
                                <div class="service-categories-container">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="mandatory-services" checked disabled>
                                        <label class="form-check-label" for="mandatory-services">
                                            <strong>{{ ___('fees.mandatory_services') }}</strong>
                                            <small class="d-block text-muted">{{ ___('fees.automatically_assigned') }}</small>
                                        </label>
                                    </div>
                                    <div id="optional-service-categories">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="academic" name="service_categories[]" id="academic-services">
                                            <label class="form-check-label" for="academic-services">
                                                {{ ___('fees.academic_services') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="extracurricular" name="service_categories[]" id="extracurricular-services">
                                            <label class="form-check-label" for="extracurricular-services">
                                                {{ ___('fees.extracurricular_services') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="transportation" name="service_categories[]" id="transportation-services">
                                            <label class="form-check-label" for="transportation-services">
                                                {{ ___('fees.transportation_services') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">{{ ___('fees.select_optional_services') }}</small>
                            </div>

                                            {{-- Notes --}}
                            <div class="col-md-6 mb-3">
                                                <label for="notes" class="form-label">{{ ___('common.notes') }}</label>
                                                <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="{{ ___('fees.generation_notes_placeholder') }}"></textarea>
                        </div>
                    </div>

                    {{-- Student Count Display --}}
                    <div class="row mb-3" id="student-count-display" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="fa-solid fa-users me-2"></i>
                                <div>
                                    <strong id="student-count-text">{{ ___('fees.calculating_students') }}</strong>
                                    <small class="d-block text-muted" id="student-count-details">{{ ___('fees.students_matching_criteria') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Grade Distribution Display (for grade-based selection) --}}
                    <div class="row mb-3" id="grade-distribution-display" style="display: none;">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body p-3">
                                    <h6 class="mb-3">{{ ___('fees.grade_distribution') }}</h6>
                                    <div id="grade-breakdown" class="row">
                                        {{-- Grade distribution will be loaded here --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Preview Section --}}
                        <div id="preview-section" style="display: none;">
                            <hr class="my-3">
                            <div class="mb-2">
                                <h6 class="mb-2">
                                    <i class="fa-solid fa-eye me-1"></i>{{ ___('fees.generation_preview') }}
                                </h6>
                                </div>
                            <div id="preview-content">
                                    {{-- Preview content will be loaded here --}}
                                </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn ot-btn-secondary" data-bs-dismiss="modal">
                        <i class="fa-solid fa-times"></i> {{ ___('common.cancel') }}
                    </button>
                    <button type="button" id="preview-btn" class="btn ot-btn-info">
                        <i class="fa-solid fa-eye"></i> {{ ___('fees.preview') }}
                    </button>
                    <button type="button" id="generate-all-btn" class="btn ot-btn-primary" style="display: none;">
                                        <i class="fa-solid fa-bolt"></i> {{ ___('fees.generate_all') }}
                                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress Modal --}}
    <div class="modal fade" id="progressModal" tabindex="-1" aria-labelledby="progressModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="progressModalLabel">
                        <i class="fa-solid fa-cogs me-2"></i>{{ ___('fees.generation_progress') }}
                    </h4>
                </div>
                <div class="modal-body">
                    {{-- Progress Bar --}}
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ ___('fees.generation_progress') }}</span>
                            <span class="text-muted" id="progress-percentage">0%</span>
                                </div>
                        <div class="progress" style="height: 12px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="progress-bar" 
                                 role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    {{-- Progress Statistics --}}
                                    <div class="row">
                        <div class="col-6 mb-2">
                            <div class="text-center p-2 border rounded">
                                <h5 id="total-students" class="text-primary mb-0">0</h5>
                                <small class="text-muted">{{ ___('fees.total_students') }}</small>
                                            </div>
                                        </div>
                        <div class="col-6 mb-2">
                            <div class="text-center p-2 border rounded">
                                <h5 id="processed-students" class="text-info mb-0">0</h5>
                                <small class="text-muted">{{ ___('fees.processed') }}</small>
                                            </div>
                                        </div>
                        <div class="col-6 mb-2">
                            <div class="text-center p-2 border rounded">
                                <h5 id="successful-students" class="text-success mb-0">0</h5>
                                <small class="text-muted">{{ ___('fees.successful') }}</small>
                                            </div>
                                        </div>
                        <div class="col-6 mb-2">
                            <div class="text-center p-2 border rounded">
                                <h5 id="failed-students" class="text-danger mb-0">0</h5>
                                <small class="text-muted">{{ ___('fees.failed') }}</small>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Message --}}
                    <div class="mt-3">
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <span id="progress-message">{{ ___('fees.initializing') }}</span>
            </div>
        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="cancel-generation-btn" class="btn ot-btn-danger">
                        <i class="fa-solid fa-stop"></i> {{ ___('fees.cancel_generation') }}
                    </button>
                    <button type="button" id="view-results-btn" class="btn ot-btn-primary" style="display: none;">
                        <i class="fa-solid fa-eye"></i> {{ ___('fees.view_results') }}
                                                </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('style')
<style>
    /* Improve Select2 multi-select appearance */
    .select2-container--default .select2-selection--multiple {
        min-height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #0d6efd;
        border: 1px solid #0d6efd;
        color: white;
        border-radius: 0.25rem;
        padding: 2px 8px;
        margin: 2px;
        font-size: 0.875rem;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: white;
        margin-right: 5px;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #fff;
        background-color: rgba(255,255,255,0.2);
        border-radius: 2px;
    }
    
    /* Custom styling for count display */
    .select2-selection__choice--count {
        background-color: #198754 !important;
        border-color: #198754 !important;
        font-weight: 500;
    }
    
    /* Limit height of select2 container */
    .select2-container--default .select2-selection--multiple {
        max-height: 76px;
        overflow-y: auto;
    }
    
    /* Modal Select2 dropdown positioning */
    .modal .select2-container--open .select2-dropdown {
        z-index: 9999;
    }
</style>
@endpush

@push('script')
<script>
$(document).ready(function() {
    let currentBatchId = null;
    let progressInterval = null;
    // Always use enhanced system
    let currentSystem = 'enhanced';

    // Initialize modal event handlers
    $('#feeGenerationModal').on('shown.bs.modal', function() {
        // Initialize Select2 for classes with custom display
        $('#classes').select2({
            placeholder: "{{ ___('student_info.select_class') }}",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#feeGenerationModal'),
            maximumSelectionLength: 50,
            closeOnSelect: false
        });


        // Initialize Select2 for sections with custom display
        $('#sections').select2({
            placeholder: "{{ ___('student_info.select_section') }}",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#feeGenerationModal'),
            maximumSelectionLength: 50,
            closeOnSelect: false
        });


        // Initialize Select2 for grades with custom display
        $('#grades').select2({
            placeholder: "{{ ___('student_info.select_grade') }}",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#feeGenerationModal'),
            maximumSelectionLength: 14,
            closeOnSelect: false
        });

        // Custom handler for grades selection display
        $('#grades').on('select2:select select2:unselect', function() {
            updateGradesDisplay();
            updateGradeBasedStudentCount();
        });

        // Initialize Select2 for fee groups with custom display
        $('#fees_groups').select2({
            placeholder: "{{ ___('fees.select_fee_groups') }}",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#feeGenerationModal'),
            maximumSelectionLength: 20,
            closeOnSelect: false
        });

    });

    // Reset modal when closed
    $('#feeGenerationModal').on('hidden.bs.modal', function() {
        resetGenerationForm();
    });



    // Grade selection helpers
    $('#select-all-grades').on('change', function() {
        if ($(this).is(':checked')) {
            $('#grades option').prop('selected', true);
        } else {
            $('#grades option').prop('selected', false);
        }
        $('#grades').trigger('change');
        setTimeout(updateGradesDisplay, 100);
    });

    $('#select-kindergarten').on('change', function() {
        const kgGrades = ['KG-1', 'KG-2'];
        if ($(this).is(':checked')) {
            kgGrades.forEach(grade => {
                $(`#grades option[value="${grade}"]`).prop('selected', true);
            });
        } else {
            kgGrades.forEach(grade => {
                $(`#grades option[value="${grade}"]`).prop('selected', false);
            });
        }
        $('#grades').trigger('change');
        setTimeout(updateGradesDisplay, 100);
    });

    $('#select-primary').on('change', function() {
        const primaryGrades = ['Grade1', 'Grade2', 'Grade3', 'Grade4', 'Grade5', 'Grade6', 'Grade7', 'Grade8'];
        if ($(this).is(':checked')) {
            primaryGrades.forEach(grade => {
                $(`#grades option[value="${grade}"]`).prop('selected', true);
            });
        } else {
            primaryGrades.forEach(grade => {
                $(`#grades option[value="${grade}"]`).prop('selected', false);
            });
        }
        $('#grades').trigger('change');
        setTimeout(updateGradesDisplay, 100);
    });

    $('#select-secondary').on('change', function() {
        const secondaryGrades = ['Form1', 'Form2', 'Form3', 'Form4'];
        if ($(this).is(':checked')) {
            secondaryGrades.forEach(grade => {
                $(`#grades option[value="${grade}"]`).prop('selected', true);
            });
        } else {
            secondaryGrades.forEach(grade => {
                $(`#grades option[value="${grade}"]`).prop('selected', false);
            });
        }
        $('#grades').trigger('change');
        setTimeout(updateGradesDisplay, 100);
    });



    // Handle month-year combined dropdown change
    $('#month_year').on('change', function() {
        const value = $(this).val();
        if (value) {
            const [month, year] = value.split('-');
            $('#month').val(month);
            $('#year').val(year);
        }
        updateStudentCount();
    });

    // Update student count when filters change
    $('#sections, #month_year, #fees_groups').on('change', function() {
        if ($('input[name="selection_method"]:checked').val() === 'grade') {
            updateGradeBasedStudentCount();
        } else {
            updateStudentCount();
        }
    });

    // Preview button click
    $('#preview-btn').on('click', function() {
        loadPreview();
    });

    // Generate button from preview
    $('#generate-all-btn').on('click', function() {
        checkForDuplicatesAndGenerate();
    });

    // Cancel generation
    $('#cancel-generation-btn').on('click', function() {
        if (currentBatchId) {
            cancelGeneration(currentBatchId);
        }
    });


    function updateStudentCount() {
        const formData = $('#fee-generation-form').serialize();
        
        $('#student-count-display').show();
        $('#student-count-text').text('{{ ___("fees.calculating_students") }}');

        $.get('{{ route("fees-generation.index") }}/get-student-count?' + formData)
            .done(function(response) {
                if (response.success) {
                    const count = response.data.count;
                    $('#student-count-text').text(`{{ ___("fees.total_students_found") }}: ${count}`);
                    $('#generate-btn').prop('disabled', count === 0);
                } else {
                    $('#student-count-text').text('{{ ___("fees.error_calculating_students") }}');
                }
            })
            .fail(function() {
                $('#student-count-text').text('{{ ___("fees.error_calculating_students") }}');
            });
    }


    // Store preview data for duplicate checking
    let currentPreviewData = null;

    function displayPreview(data) {
        // Store preview data for duplicate checking
        currentPreviewData = data;

        const isGradeSelection = data.selection_method === 'grade';
        const breakdownType = isGradeSelection ? 'grade' : 'class';
        const breakdown = isGradeSelection ? (data.grades_breakdown || {}) : (data.classes_breakdown || {});
        const classesAffectedText = @json(___('fees.classes_affected'));
        const gradeDistributionText = @json(___('fees.grade_distribution'));
        const breakdownLabel = isGradeSelection ? gradeDistributionText : classesAffectedText;
        const classBreakdownText = @json(___('fees.class_breakdown'));
        const gradeBreakdownText = @json(___('fees.grade_distribution'));
        const breakdownTitle = isGradeSelection ? gradeBreakdownText : classBreakdownText;
        const classColumnLabel = @json(___('academic.class'));
        const gradeColumnLabel = @json(___('student_info.grade'));
        const columnLabel = isGradeSelection ? gradeColumnLabel : classColumnLabel;
        const studentsLabel = @json(___('fees.students'));
        const amountLabel = @json(___('fees.amount'));

        let html = `
            <div class="row">
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <h3 class="text-primary">${data.total_students}</h3>
                            <p class="mb-0">{{ ___('fees.total_students') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <h3 class="text-success">${formatCurrency(data.estimated_amount)}</h3>
                            <p class="mb-0">{{ ___('fees.estimated_amount') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <h3 class="text-warning">${Object.keys(breakdown).length}</h3>
                            <p class="mb-0">${breakdownLabel}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Enhanced duplicate warning with more details
        if (data.duplicate_warning && data.duplicate_warning.has_duplicates) {
            html += `<div class="alert alert-warning mt-3">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    <div>
                        <strong>{{ ___('fees.duplicate_fees_detected') }}</strong><br>
                        <small>${data.duplicate_warning.message}</small>
                    </div>
                </div>
            </div>`;
        }

        html += `<div class="mt-4"><h6>${breakdownTitle}</h6><div class="table-responsive"><table class="table table-sm"><thead><tr><th>${columnLabel}</th><th>${studentsLabel}</th><th>${amountLabel}</th></tr></thead><tbody>`;

        Object.entries(breakdown).forEach(([groupName, groupData]) => {
            html += `<tr><td>${groupName}</td><td>${groupData.students}</td><td>${formatCurrency(groupData.amount)}</td></tr>`;
        });
        
        html += '</tbody></table></div></div>';

        if (isGradeSelection && data.classes_breakdown && Object.keys(data.classes_breakdown).length > 0) {
            html += `<div class="mt-4"><h6>${classBreakdownText}</h6><div class="table-responsive"><table class="table table-sm"><thead><tr><th>${classColumnLabel}</th><th>${studentsLabel}</th><th>${amountLabel}</th></tr></thead><tbody>`;

            Object.entries(data.classes_breakdown).forEach(([className, classData]) => {
                html += `<tr><td>${className}</td><td>${classData.students}</td><td>${formatCurrency(classData.amount)}</td></tr>`;
            });

            html += '</tbody></table></div></div>';
        }

        $('#preview-content').html(html);
    }


    function startProgressTracking(batchId) {
        progressInterval = setInterval(() => {
            $.get(`{{ route("fees-generation.index") }}/status/${batchId}`)
                .done(function(response) {
                    if (response.success) {
                        updateProgress(response.data);
                        
                        if (response.data.is_completed || response.data.status === 'failed' || response.data.status === 'cancelled') {
                            clearInterval(progressInterval);
                            $('#cancel-generation-btn').hide();
                            $('#view-results-btn').show().off('click').on('click', function() {
                                window.location.href = `{{ url("fees-generation/show") }}/${response.data.id}`;
                            });
                        }
                    }
                })
                .fail(function() {
                    clearInterval(progressInterval);
                });
        }, 2000);
    }

    function updateProgress(data) {
        const percentage = data.progress_percentage || 0;
        $('#progress-bar').css('width', percentage + '%');
        $('#progress-percentage').text(percentage + '%');
        $('#total-students').text(data.total_students || 0);
        $('#processed-students').text(data.processed_students || 0);
        $('#successful-students').text(data.successful_students || 0);
        $('#failed-students').text(data.failed_students || 0);
        
        let message = '';
        switch(data.status) {
            case 'pending':
                message = '{{ ___("fees.generation_pending") }}';
                break;
            case 'processing':
                message = `{{ ___("fees.processing_students") }} (${data.processed_students}/${data.total_students})`;
                break;
            case 'completed':
                message = '{{ ___("fees.generation_completed") }}';
                break;
            case 'failed':
                message = '{{ ___("fees.generation_failed") }}';
                break;
            case 'cancelled':
                message = '{{ ___("fees.generation_cancelled") }}';
                break;
        }
        $('#progress-message').text(message);
    }

    function cancelGeneration(batchId) {
        if (!confirm('{{ ___("fees.confirm_cancel_generation") }}')) {
            return;
        }

        $.post(`{{ route("fees-generation.index") }}/cancel/${batchId}`)
            .done(function(response) {
                if (response.success) {
                    clearInterval(progressInterval);
                    showAlert(response.message, 'success');
                    $('#progressModal').modal('hide');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(response.message, 'error');
                }
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON?.message || '{{ ___("common.error") }}';
                showAlert(message, 'error');
            });
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    }

    function showAlert(message, type) {
        // Use toastr for notifications
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "5000"
            };
            
            switch(type) {
                case 'success':
                    toastr.success(message);
                    break;
                case 'error':
                    toastr.error(message);
                    break;
                case 'warning':
                    toastr.warning(message);
                    break;
                case 'info':
                    toastr.info(message);
                    break;
                default:
                    toastr.info(message);
            }
        } else {
            // Fallback to alert
            alert(message);
        }
    }

    function checkForDuplicatesAndGenerate() {
        if (currentPreviewData && currentPreviewData.duplicate_warning && currentPreviewData.duplicate_warning.has_duplicates) {
            showDuplicateConfirmationDialog();
        } else {
            startGeneration(true);
        }
    }

    function showDuplicateConfirmationDialog() {
        const duplicateInfo = currentPreviewData.duplicate_warning;
        
        // Create confirmation modal HTML
        const modalHtml = `
            <div class="modal fade" id="duplicateConfirmModal" tabindex="-1" aria-labelledby="duplicateConfirmModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="duplicateConfirmModalLabel">
                                <i class="fa-solid fa-exclamation-triangle me-2"></i>{{ ___('fees.duplicate_fees_detected') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning">
                                <h6><i class="fa-solid fa-info-circle me-2"></i>{{ ___('fees.duplicate_detection_title') }}</h6>
                                <p class="mb-2">${duplicateInfo.message}</p>
                                <hr>
                                <small class="text-muted">
                                    <strong>{{ ___('fees.what_this_means') }}:</strong><br>
                                    • {{ ___('fees.duplicate_explanation_1') }}<br>
                                    • {{ ___('fees.duplicate_explanation_2') }}<br>
                                    • {{ ___('fees.duplicate_explanation_3') }}
                                </small>
                            </div>
                            
                            <div class="mt-3">
                                <h6>{{ ___('fees.generation_summary') }}:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fa-solid fa-users text-info me-2"></i><strong>{{ ___('fees.total_students') }}:</strong> ${currentPreviewData.total_students}</li>
                                    <li><i class="fa-solid fa-dollar-sign text-success me-2"></i><strong>{{ ___('fees.estimated_amount') }}:</strong> ${formatCurrency(currentPreviewData.estimated_amount)}</li>
                                    <li><i class="fa-solid fa-exclamation-triangle text-warning me-2"></i><strong>{{ ___('fees.existing_fees') }}:</strong> ${duplicateInfo.count} {{ ___('fees.records') }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn ot-btn-secondary" data-bs-dismiss="modal">
                                <i class="fa-solid fa-times me-1"></i>{{ ___('common.cancel') }}
                            </button>
                            <button type="button" class="btn ot-btn-primary" id="confirmGenerateBtn">
                                <i class="fa-solid fa-bolt me-1"></i>{{ ___('fees.proceed_anyway') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        $('#duplicateConfirmModal').remove();
        
        // Add modal to body
        $('body').append(modalHtml);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('duplicateConfirmModal'));
        modal.show();
        
        // Handle confirm button
        $('#confirmGenerateBtn').on('click', function() {
            modal.hide();
            showAlert('{{ ___('fees.proceeding_with_generation') }}', 'info');
            startGeneration(true);
        });
        
        // Clean up modal after hide
        document.getElementById('duplicateConfirmModal').addEventListener('hidden.bs.modal', function() {
            $('#duplicateConfirmModal').remove();
        });
    }

    function resetGenerationForm() {
        // Reset form
        $('#fee-generation-form')[0].reset();
        
        // Reset Select2 selections
        $('#classes').val(null).trigger('change');
        $('#sections').val(null).trigger('change');
        $('#fees_groups').val(null).trigger('change');
        
        // Reset month-year dropdown to current month (find the option marked as current)
        const currentMonth = {{ date('n') }};
        const currentYear = {{ date('Y') }};
        const currentMonthValue = currentMonth + '-' + currentYear;
        
        // Find and select the current month option
        $('#month_year option').each(function() {
            if ($(this).val() === currentMonthValue) {
                $(this).prop('selected', true);
                return false; // Break the loop
            }
        });
        
        $('#month').val(currentMonth);
        $('#year').val(currentYear);
        
        // Reset checkboxes
        // Hide sections
        $('#student-count-display').hide();
        $('#preview-section').hide();
        $('#generate-all-btn').hide();
    }


    // Cancel generation buttons in table
    $('.cancel-generation').on('click', function() {
        const generationId = $(this).data('id');
        if (confirm('{{ ___("fees.confirm_cancel_generation") }}')) {
            $.post(`{{ route("fees-generation.index") }}/cancel/${generationId}`)
                .done(function(response) {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showAlert(response.message, 'error');
                    }
                });
        }
    });

    // Simplified preview and generation functions (Enhanced System Only)
    function loadPreview() {
        const formData = $('#fee-generation-form').serialize();

        $('#preview-content').html('<div class="text-center"><i class="fa-solid fa-spinner fa-spin"></i> {{ ___("common.loading") }}</div>');
        $('#preview-section').show();

        $.post('{{ route("fees-generation.preview-managed") }}', formData)
            .done(function(response) {
                if (response.success) {
                    displayPreview(response.data);
                    $('#generate-all-btn').show().prop('disabled', false);
                } else {
                    $('#preview-content').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON?.message || '{{ ___("common.error") }}';
                $('#preview-content').html(`<div class="alert alert-danger">${message}</div>`);
            });
    }

    function startGeneration(generateAll) {
        const formData = $('#fee-generation-form').serializeArray();

        $.post('{{ route("fees-generation.generate-managed") }}', formData)
            .done(function(response) {
                if (response.success) {
                    currentBatchId = response.data.batch_id;
                    // Close generation modal and show progress modal
                    $('#feeGenerationModal').modal('hide');
                    $('#progressModal').modal('show');
                    startProgressTracking(currentBatchId);
                } else {
                    showAlert(response.message, 'error');
                }
            })
            .fail(function(xhr) {
                const message = xhr.responseJSON?.message || '{{ ___("common.error") }}';
                showAlert(message, 'error');
            });
    }

    // Grade-based selection functions
    function updateGradesDisplay() {
        const $container = $('#grades').next('.select2-container').find('.select2-selection__rendered');
        const selectedCount = $('#grades').val() ? $('#grades').val().length : 0;

        if (selectedCount > 4) {
            $container.find('.select2-selection__choice').hide();
            let $countDisplay = $container.find('.select2-selection__choice--count');
            if ($countDisplay.length === 0) {
                $countDisplay = $('<li class="select2-selection__choice select2-selection__choice--count">' + selectedCount + ' grades selected</li>');
                $container.prepend($countDisplay);
            } else {
                $countDisplay.text(selectedCount + ' grades selected');
            }
        } else {
            $container.find('.select2-selection__choice').show();
            $container.find('.select2-selection__choice--count').remove();
        }
    }

    function updateGradeBasedStudentCount() {
        const grades = $('#grades').val();
        if (!grades || grades.length === 0) {
            $('#student-count-display').hide();
            $('#grade-distribution-display').hide();
            return;
        }

        const formData = {
            grades: grades,
            month_year: $('#month_year').val(),
            _token: '{{ csrf_token() }}'
        };

        $('#student-count-display').show();
        $('#student-count-text').text('Calculating students...');
        $('#grade-distribution-display').hide();

        $.post('{{ route('fees-generation.student-count-by-grades') }}', formData)
            .done(function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#student-count-text').text(`Total students found: ${data.total_count}`);
                    $('#student-count-details').text(`Across ${grades.length} grades`);

                    if (data.grade_breakdown && Object.keys(data.grade_breakdown).length > 0) {
                        displayGradeDistribution(data.grade_breakdown);
                        $('#grade-distribution-display').show();
                    }

                    $('#preview-btn').prop('disabled', data.total_count === 0);
                } else {
                    $('#student-count-text').text('Error calculating students');
                    $('#student-count-details').text('');
                }
            })
            .fail(function() {
                $('#student-count-text').text('Error calculating students');
                $('#student-count-details').text('');
            });
    }

    function displayGradeDistribution(gradeBreakdown) {
        let html = '';
        Object.entries(gradeBreakdown).forEach(([grade, count]) => {
            const academicLevel = getAcademicLevelFromGrade(grade);
            const levelClass = {
                'kg': 'text-info',
                'primary': 'text-success',
                'secondary': 'text-warning'
            }[academicLevel] || 'text-muted';

            html += `
                <div class="col-md-3 mb-2">
                    <div class="text-center p-2 border rounded">
                        <h6 class="${levelClass} mb-1">${grade}</h6>
                        <small class="text-muted">${count} students</small>
                    </div>
                </div>
            `;
        });
        $('#grade-breakdown').html(html);
    }

    function getAcademicLevelFromGrade(grade) {
        const kgGrades = ['KG-1', 'KG-2'];
        const primaryGrades = ['Grade1', 'Grade2', 'Grade3', 'Grade4', 'Grade5', 'Grade6', 'Grade7', 'Grade8'];
        const secondaryGrades = ['Form1', 'Form2', 'Form3', 'Form4'];

        if (kgGrades.includes(grade)) return 'kg';
        if (primaryGrades.includes(grade)) return 'primary';
        if (secondaryGrades.includes(grade)) return 'secondary';
        return 'unknown';
    }

    function resetSelectionCounts() {
        $('#student-count-display').hide();
        $('#grade-distribution-display').hide();
        $('#preview-section').hide();
        $('#generate-all-btn').hide();
        $('#student-count-text').text('Calculating students...');
        $('#student-count-details').text('Students matching criteria');
    }
});
</script>
@endpush

@push('style')
<style>
    /* Enhanced month selector styling */
    #month_year option {
        padding: 8px 12px;
        font-size: 14px;
    }
    
    #month_year option.text-muted {
        color: #6c757d !important;
        font-style: italic;
    }
    
    #month_year option.fw-bold.text-primary {
        color: #0d6efd !important;
        font-weight: bold !important;
        background-color: #e7f3ff;
    }
    
    #month_year option.text-info {
        color: #0dcaf0 !important;
    }
    
    /* Enhance the dropdown appearance */
    #month_year {
        font-size: 14px;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }
    
    #month_year:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    /* Month period label enhancement */
    .form-label {
        font-weight: 600;
        margin-bottom: 8px;
        color: #495057;
    }
    
    /* Small helper text styling */
    .text-muted {
        font-size: 12px;
        margin-top: 4px;
    }
</style>
@endpush
