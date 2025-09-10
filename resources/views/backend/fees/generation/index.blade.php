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

        {{-- Enhanced Fee System Status & Toggle --}}
        <div class="card mb-4" id="system-status-card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0">{{ ___('fees.fee_processing_system') }}</h5>
                        <small class="text-muted">{{ ___('fees.system_management') }}</small>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="d-flex justify-content-end align-items-center">
                            <span class="me-2">{{ ___('fees.legacy_system') }}</span>
                            <div class="form-check form-switch me-2">
                                <input class="form-check-input" type="checkbox" id="systemToggle" checked>
                            </div>
                            <span>{{ ___('fees.enhanced_system') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row" id="system-status-display">
                    <div class="col-12 text-center">
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <span class="ms-2">{{ ___('fees.loading_system_status') }}</span>
                    </div>
                </div>
            </div>
        </div>

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
                                    <h3 class="text-primary mb-1">{{ count($data['classes']) }}</h3>
                                    <small class="text-muted">{{ ___('academic.total_classes') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card ot-card border-info">
                                <div class="card-body text-center p-3">
                                    <h3 class="text-info mb-1">{{ count($data['fees_groups']) }}</h3>
                                    <small class="text-muted">{{ ___('fees.fee_groups') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card ot-card border-success">
                                <div class="card-body text-center p-3">
                                    <h3 class="text-success mb-1">{{ $data['generations']->where('status', 'completed')->count() }}</h3>
                                    <small class="text-muted">{{ ___('fees.completed_generations') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card ot-card border-warning">
                                <div class="card-body text-center p-3">
                                    <h3 class="text-warning mb-1">{{ $data['generations']->where('status', 'processing')->count() }}</h3>
                                    <small class="text-muted">{{ ___('fees.processing_generations') }}</small>
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
                        
                        {{-- Generation Parameters --}}
                        <div class="row mb-3">
                                            {{-- Class Selection --}}
                            <div class="col-md-6 mb-3">
                                                <label for="classes" class="form-label">{{ ___('academic.class') }} <span class="text-danger">*</span></label>
                                <select name="classes[]" id="classes" class="form-control select2" multiple required>
                                                    @foreach($data['classes'] as $classSetup)
                                                        <option value="{{ $classSetup->classes_id }}">{{ $classSetup->class->name ?? 'Unknown Class' }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="form-check mt-1">
                                                    <input class="form-check-input" type="checkbox" id="select-all-classes">
                                    <label class="form-check-label small" for="select-all-classes">
                                                        {{ ___('common.select_all') }}
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Section Selection --}}
                            <div class="col-md-6 mb-3">
                                                <label for="sections" class="form-label">{{ ___('academic.section') }}</label>
                                                <select name="sections[]" id="sections" class="form-control select2" multiple>
                                                    <option value="">{{ ___('common.select_class_first') }}</option>
                                                </select>
                                                <div class="form-check mt-1">
                                                    <input class="form-check-input" type="checkbox" id="select-all-sections">
                                    <label class="form-check-label small" for="select-all-sections">
                                                        {{ ___('common.select_all') }}
                                                    </label>
                                </div>
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

                            {{-- Due Date --}}
                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label">{{ ___('fees.due_date') }}</label>
                                <input type="date" name="due_date" id="due_date" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            {{-- Fee Groups (Legacy System) --}}
                            <div class="col-md-6 mb-3" id="legacy-fee-groups">
                                                <label for="fees_groups" class="form-label">{{ ___('fees.fee_groups') }}</label>
                                                <select name="fees_groups[]" id="fees_groups" class="form-control select2" multiple>
                                                    @foreach($data['fees_groups'] as $group)
                                                        <option value="{{ $group->id }}">{{ $group->name ?? 'Unknown Group' }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">{{ ___('fees.leave_empty_for_all') }}</small>
                                            </div>

                            {{-- Service Categories (Enhanced System) --}}
                            <div class="col-md-6 mb-3" id="enhanced-service-categories" style="display: none;">
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
                                        <small class="d-block text-muted">{{ ___('fees.students_matching_criteria') }}</small>
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
    let currentSystem = 'enhanced'; // Default to enhanced system
    
    // Load system status on page load
    loadSystemStatus();

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
        
        // Custom handler for classes selection display
        $('#classes').on('select2:select select2:unselect', function() {
            updateClassesDisplay();
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
        
        // Custom handler for sections selection display
        $('#sections').on('select2:select select2:unselect', function() {
            updateSectionsDisplay();
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
        
        // Custom handler for fee groups selection display
        $('#fees_groups').on('select2:select select2:unselect', function() {
            updateFeeGroupsDisplay();
        });
    });

    // Reset modal when closed
    $('#feeGenerationModal').on('hidden.bs.modal', function() {
        resetGenerationForm();
    });

    // Select All Classes
    $('#select-all-classes').on('change', function() {
        if ($(this).is(':checked')) {
            $('#classes option').prop('selected', true);
        } else {
            $('#classes option').prop('selected', false);
        }
        $('#classes').trigger('change');
        setTimeout(updateClassesDisplay, 100); // Update display after select2 processes
    });

    // Select All Sections
    $('#select-all-sections').on('change', function() {
        if ($(this).is(':checked')) {
            $('#sections option').prop('selected', true);
        } else {
            $('#sections option').prop('selected', false);
        }
        $('#sections').trigger('change');
        setTimeout(updateSectionsDisplay, 100); // Update display after select2 processes
    });

    // System toggle functionality
    $('#systemToggle').on('change', function() {
        const targetSystem = $(this).is(':checked') ? 'enhanced' : 'legacy';
        switchSystem(targetSystem);
    });

    // Load sections when classes change
    $('#classes').on('change', function() {
        const classIds = $(this).val();
        loadSections(classIds);
        updateStudentCount();
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
        updateStudentCount();
    });

    // Preview button click
    $('#preview-btn').on('click', function() {
        loadPreview();
    });

    // Generate button from preview
    $('#generate-all-btn').on('click', function() {
        startGeneration(true);
    });

    // Cancel generation
    $('#cancel-generation-btn').on('click', function() {
        if (currentBatchId) {
            cancelGeneration(currentBatchId);
        }
    });

    function loadSections(classIds) {
        if (!classIds || classIds.length === 0) {
            $('#sections').html('<option value="">{{ ___("common.select_class_first") }}</option>');
            return;
        }

        $.get('{{ route("fees-generation.index") }}/get-sections', {
            class_ids: classIds
        }).done(function(response) {
            if (response.success) {
                let options = '<option value="">{{ ___("common.all_sections") }}</option>';
                response.data.forEach(section => {
                    options += `<option value="${section.id}">${section.name}</option>`;
                });
                $('#sections').html(options);
            }
        }).fail(function() {
            showAlert('{{ ___("common.error") }}', 'error');
        });
    }

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

    function loadPreview() {
        const formData = $('#fee-generation-form').serialize();
        
        $('#preview-content').html('<div class="text-center"><i class="fa-solid fa-spinner fa-spin"></i> {{ ___("common.loading") }}</div>');
        $('#preview-section').show();

        $.post('{{ route("fees-generation.preview") }}', formData)
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

    function displayPreview(data) {
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
                            <h3 class="text-warning">${Object.keys(data.classes_breakdown).length}</h3>
                            <p class="mb-0">{{ ___('fees.classes_affected') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        if (data.duplicate_warning.has_duplicates) {
            html += `<div class="alert alert-warning mt-3">
                <i class="fa-solid fa-exclamation-triangle"></i> ${data.duplicate_warning.message}
            </div>`;
        }

        html += '<div class="mt-4"><h6>{{ ___("fees.class_breakdown") }}</h6><div class="table-responsive"><table class="table table-sm"><thead><tr><th>{{ ___("academic.class") }}</th><th>{{ ___("fees.students") }}</th><th>{{ ___("fees.amount") }}</th></tr></thead><tbody>';
        
        Object.entries(data.classes_breakdown).forEach(([className, breakdown]) => {
            html += `<tr><td>${className}</td><td>${breakdown.students}</td><td>${formatCurrency(breakdown.amount)}</td></tr>`;
        });
        
        html += '</tbody></table></div></div>';

        $('#preview-content').html(html);
    }

    function startGeneration(generateAll) {
        const formData = $('#fee-generation-form').serializeArray();
        
        $.post('{{ route("fees-generation.generate") }}', formData)
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
        // Implement your preferred alert system (toastr, SweetAlert, etc.)
        alert(message);
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
        $('#select-all-classes').prop('checked', false);
        $('#select-all-sections').prop('checked', false);
        
        // Hide sections
        $('#student-count-display').hide();
        $('#preview-section').hide();
        $('#generate-all-btn').hide();
        
        // Reset sections dropdown
        $('#sections').html('<option value="">{{ ___("common.select_class_first") }}</option>');
    }

    // Custom display functions for better UX
    function updateClassesDisplay() {
        const $container = $('#classes').next('.select2-container').find('.select2-selection__rendered');
        const selectedCount = $('#classes').val() ? $('#classes').val().length : 0;
        
        if (selectedCount > 3) {
            // Hide individual selections and show count
            $container.find('.select2-selection__choice').hide();
            
            // Add or update count display
            let $countDisplay = $container.find('.select2-selection__choice--count');
            if ($countDisplay.length === 0) {
                $countDisplay = $('<li class="select2-selection__choice select2-selection__choice--count">' + selectedCount + ' {{ ___("academic.classes_selected") }}</li>');
                $container.prepend($countDisplay);
            } else {
                $countDisplay.text(selectedCount + ' {{ ___("academic.classes_selected") }}');
            }
        } else {
            // Show individual selections
            $container.find('.select2-selection__choice').show();
            $container.find('.select2-selection__choice--count').remove();
        }
    }

    function updateSectionsDisplay() {
        const $container = $('#sections').next('.select2-container').find('.select2-selection__rendered');
        const selectedCount = $('#sections').val() ? $('#sections').val().length : 0;
        
        if (selectedCount > 3) {
            $container.find('.select2-selection__choice').hide();
            
            let $countDisplay = $container.find('.select2-selection__choice--count');
            if ($countDisplay.length === 0) {
                $countDisplay = $('<li class="select2-selection__choice select2-selection__choice--count">' + selectedCount + ' {{ ___("academic.sections_selected") }}</li>');
                $container.prepend($countDisplay);
            } else {
                $countDisplay.text(selectedCount + ' {{ ___("academic.sections_selected") }}');
            }
        } else {
            $container.find('.select2-selection__choice').show();
            $container.find('.select2-selection__choice--count').remove();
        }
    }

    function updateFeeGroupsDisplay() {
        const $container = $('#fees_groups').next('.select2-container').find('.select2-selection__rendered');
        const selectedCount = $('#fees_groups').val() ? $('#fees_groups').val().length : 0;
        
        if (selectedCount > 2) {
            $container.find('.select2-selection__choice').hide();
            
            let $countDisplay = $container.find('.select2-selection__choice--count');
            if ($countDisplay.length === 0) {
                $countDisplay = $('<li class="select2-selection__choice select2-selection__choice--count">' + selectedCount + ' {{ ___("fees.fee_groups_selected") }}</li>');
                $container.prepend($countDisplay);
            } else {
                $countDisplay.text(selectedCount + ' {{ ___("fees.fee_groups_selected") }}');
            }
        } else {
            $container.find('.select2-selection__choice').show();
            $container.find('.select2-selection__choice--count').remove();
        }
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

    // Enhanced Fee System Management Functions
    function loadSystemStatus() {
        $.get('{{ route("fees-generation.system-status") }}')
            .done(function(response) {
                if (response.success) {
                    displaySystemStatus(response.data);
                } else {
                    $('#system-status-display').html('<div class="col-12 text-center text-danger">Failed to load system status</div>');
                }
            })
            .fail(function() {
                $('#system-status-display').html('<div class="col-12 text-center text-danger">Error loading system status</div>');
            });
    }

    function displaySystemStatus(data) {
        const compatibility = data.compatibility_report;
        const statistics = data.usage_statistics;
        
        let html = `
            <div class="col-md-3">
                <div class="text-center">
                    <h6 class="text-primary">${statistics.active_system || 'Enhanced'}</h6>
                    <small class="text-muted">{{ ___('fees.active_system') }}</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h6 class="text-success">${statistics.students_with_services || 0}</h6>
                    <small class="text-muted">{{ ___('fees.students_with_services') }}</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h6 class="text-info">${statistics.total_active_services || 0}</h6>
                    <small class="text-muted">{{ ___('fees.total_active_services') }}</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center">
                    <h6 class="${compatibility.migration_ready ? 'text-success' : 'text-warning'}">${compatibility.migration_ready ? '✓' : '⚠'}</h6>
                    <small class="text-muted">{{ ___('fees.system_compatibility') }}</small>
                </div>
            </div>
        `;

        $('#system-status-display').html(html);
        
        // Update system toggle based on current system
        currentSystem = statistics.active_system === 'Enhanced' ? 'enhanced' : 'legacy';
        $('#systemToggle').prop('checked', currentSystem === 'enhanced');
        updateSystemUI();
    }

    function switchSystem(targetSystem) {
        if (targetSystem === currentSystem) {
            return; // No change needed
        }

        const confirmMessage = targetSystem === 'enhanced' 
            ? '{{ ___("fees.confirm_switch_to_enhanced") }}' 
            : '{{ ___("fees.confirm_switch_to_legacy") }}';
            
        if (!confirm(confirmMessage)) {
            // Reset toggle to current system
            $('#systemToggle').prop('checked', currentSystem === 'enhanced');
            return;
        }

        $.post('{{ route("fees-generation.switch-system") }}', {
            system: targetSystem,
            _token: '{{ csrf_token() }}'
        })
        .done(function(response) {
            if (response.success) {
                currentSystem = targetSystem;
                updateSystemUI();
                showAlert(response.message, 'success');
                
                if (response.warnings && response.warnings.length > 0) {
                    setTimeout(() => {
                        showAlert('Warnings: ' + response.warnings.join(', '), 'warning');
                    }, 2000);
                }
                
                // Reload system status
                loadSystemStatus();
            } else {
                showAlert(response.message, 'error');
                // Reset toggle
                $('#systemToggle').prop('checked', currentSystem === 'enhanced');
            }
        })
        .fail(function(xhr) {
            const message = xhr.responseJSON?.message || '{{ ___("common.error") }}';
            showAlert(message, 'error');
            // Reset toggle
            $('#systemToggle').prop('checked', currentSystem === 'enhanced');
        });
    }

    function updateSystemUI() {
        if (currentSystem === 'enhanced') {
            $('#legacy-fee-groups').hide();
            $('#enhanced-service-categories').show();
            
            // Update modal title to indicate enhanced system
            $('#feeGenerationModalLabel').html('<i class="fa-solid fa-cogs me-2"></i>{{ ___("fees.enhanced_fee_generation") }}');
        } else {
            $('#enhanced-service-categories').hide();
            $('#legacy-fee-groups').show();
            
            // Update modal title to indicate legacy system
            $('#feeGenerationModalLabel').html('<i class="fa-solid fa-cogs me-2"></i>{{ ___("fees.legacy_fee_generation") }}');
        }
    }

    // Override preview and generation functions to use appropriate system
    function loadPreview() {
        const formData = $('#fee-generation-form').serialize();
        const endpoint = currentSystem === 'enhanced' ? 
            '{{ route("fees-generation.preview-managed") }}' : 
            '{{ route("fees-generation.preview") }}';
        
        $('#preview-content').html('<div class="text-center"><i class="fa-solid fa-spinner fa-spin"></i> {{ ___("common.loading") }}</div>');
        $('#preview-section').show();

        $.post(endpoint, formData)
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
        const endpoint = currentSystem === 'enhanced' ? 
            '{{ route("fees-generation.generate-managed") }}' : 
            '{{ route("fees-generation.generate") }}';
        
        $.post(endpoint, formData)
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