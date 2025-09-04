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
                <div class="col-sm-6">
                    <div class="d-flex justify-content-end align-items-center">
                        <a href="{{ route('fees-generation.history') }}" class="btn btn-secondary me-2">
                            <i class="fa-solid fa-history"></i> {{ ___('fees.generation_history') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area E n d --}}

        <!--  Fee Generation Form start -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('fees.bulk_fee_generation') }}</h4>
                </div>
                <div class="card-body">
                    
                    {{-- Filter Section --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ ___('fees.filters') }}</h5>
                                </div>
                                <div class="card-body">
                                    <form id="fee-generation-form">
                                        @csrf
                                        <div class="row">
                                            {{-- Class Selection --}}
                                            <div class="col-lg-3 col-md-6 mb-3">
                                                <label for="classes" class="form-label">{{ ___('academic.class') }} <span class="text-danger">*</span></label>
                                                <select name="classes[]" id="classes" class="form-control select2" multiple>
                                                    @foreach($data['classes'] as $class)
                                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="form-check mt-1">
                                                    <input class="form-check-input" type="checkbox" id="select-all-classes">
                                                    <label class="form-check-label" for="select-all-classes">
                                                        {{ ___('common.select_all') }}
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Section Selection --}}
                                            <div class="col-lg-3 col-md-6 mb-3">
                                                <label for="sections" class="form-label">{{ ___('academic.section') }}</label>
                                                <select name="sections[]" id="sections" class="form-control select2" multiple>
                                                    <option value="">{{ ___('common.select_class_first') }}</option>
                                                </select>
                                                <div class="form-check mt-1">
                                                    <input class="form-check-input" type="checkbox" id="select-all-sections">
                                                    <label class="form-check-label" for="select-all-sections">
                                                        {{ ___('common.select_all') }}
                                                    </label>
                                                </div>
                                            </div>

                                            {{-- Month Selection --}}
                                            <div class="col-lg-2 col-md-6 mb-3">
                                                <label for="month" class="form-label">{{ ___('common.month') }} <span class="text-danger">*</span></label>
                                                <select name="month" id="month" class="form-control" required>
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>

                                            {{-- Year Selection --}}
                                            <div class="col-lg-2 col-md-6 mb-3">
                                                <label for="year" class="form-label">{{ ___('common.year') }} <span class="text-danger">*</span></label>
                                                <select name="year" id="year" class="form-control" required>
                                                    @for($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>

                                            {{-- Fee Group Selection --}}
                                            <div class="col-lg-2 col-md-6 mb-3">
                                                <label for="fees_groups" class="form-label">{{ ___('fees.fee_groups') }}</label>
                                                <select name="fees_groups[]" id="fees_groups" class="form-control select2" multiple>
                                                    @foreach($data['fees_groups'] as $group)
                                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">{{ ___('fees.leave_empty_for_all') }}</small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            {{-- Due Date --}}
                                            <div class="col-lg-3 col-md-6 mb-3">
                                                <label for="due_date" class="form-label">{{ ___('fees.due_date') }}</label>
                                                <input type="date" name="due_date" id="due_date" class="form-control" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                            </div>

                                            {{-- Notes --}}
                                            <div class="col-lg-6 col-md-6 mb-3">
                                                <label for="notes" class="form-label">{{ ___('common.notes') }}</label>
                                                <textarea name="notes" id="notes" class="form-control" rows="2" placeholder="{{ ___('fees.generation_notes_placeholder') }}"></textarea>
                                            </div>

                                            {{-- Action Buttons --}}
                                            <div class="col-lg-3 col-md-12 mb-3 d-flex align-items-end">
                                                <div class="w-100">
                                                    <button type="button" id="preview-btn" class="btn btn-info me-2">
                                                        <i class="fa-solid fa-eye"></i> {{ ___('fees.preview') }}
                                                    </button>
                                                    <button type="button" id="generate-btn" class="btn btn-primary" disabled>
                                                        <i class="fa-solid fa-cogs"></i> {{ ___('fees.generate') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Student Count Display --}}
                    <div class="row mb-3" id="student-count-display" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fa-solid fa-users"></i>
                                <span id="student-count-text">{{ ___('fees.calculating_students') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Preview Section --}}
                    <div class="row" id="preview-section" style="display: none;">
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">{{ ___('fees.generation_preview') }}</h5>
                                </div>
                                <div class="card-body" id="preview-content">
                                    {{-- Preview content will be loaded here --}}
                                </div>
                                <div class="card-footer">
                                    <button type="button" id="generate-all-btn" class="btn btn-success me-2">
                                        <i class="fa-solid fa-bolt"></i> {{ ___('fees.generate_all') }}
                                    </button>
                                    <button type="button" id="generate-selected-btn" class="btn btn-warning" style="display: none;">
                                        <i class="fa-solid fa-check-square"></i> {{ ___('fees.generate_selected') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Section --}}
                    <div class="row" id="progress-section" style="display: none;">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">{{ ___('fees.generation_progress') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="progress mb-3">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="progress-bar" 
                                             role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                            0%
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 id="total-students" class="text-primary">0</h4>
                                                <small>{{ ___('fees.total_students') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 id="processed-students" class="text-info">0</h4>
                                                <small>{{ ___('fees.processed') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 id="successful-students" class="text-success">0</h4>
                                                <small>{{ ___('fees.successful') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 id="failed-students" class="text-danger">0</h4>
                                                <small>{{ ___('fees.failed') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <p id="progress-message" class="mb-0">{{ ___('fees.initializing') }}</p>
                                    </div>
                                    <div class="mt-3" id="progress-actions" style="display: none;">
                                        <button type="button" id="cancel-generation-btn" class="btn btn-danger me-2">
                                            <i class="fa-solid fa-stop"></i> {{ ___('fees.cancel_generation') }}
                                        </button>
                                        <button type="button" id="view-results-btn" class="btn btn-info" style="display: none;">
                                            <i class="fa-solid fa-eye"></i> {{ ___('fees.view_results') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Recent Generations --}}
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">{{ ___('fees.recent_generations') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead">
                                <tr>
                                    <th>{{ ___('common.batch_id') }}</th>
                                    <th>{{ ___('common.status') }}</th>
                                    <th>{{ ___('fees.students') }}</th>
                                    <th>{{ ___('fees.amount') }}</th>
                                    <th>{{ ___('common.created_at') }}</th>
                                    <th>{{ ___('common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['generations'] as $generation)
                                    <tr>
                                        <td>{{ $generation->batch_id }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($generation->status) {
                                                    'completed' => 'badge-success',
                                                    'processing' => 'badge-warning',
                                                    'failed' => 'badge-danger',
                                                    'cancelled' => 'badge-secondary',
                                                    default => 'badge-info'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ ucfirst($generation->status) }}</span>
                                        </td>
                                        <td>{{ $generation->successful_students }}/{{ $generation->total_students }}</td>
                                        <td>{{ number_format($generation->total_amount, 2) }}</td>
                                        <td>{{ $generation->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('fees-generation.show', $generation->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            @if($generation->canBeCancelled())
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger cancel-generation" 
                                                        data-id="{{ $generation->id }}">
                                                    <i class="fa-solid fa-stop"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ ___('common.no_data_available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('script')
<script>
$(document).ready(function() {
    let currentBatchId = null;
    let progressInterval = null;

    // Initialize Select2
    $('.select2').select2({
        placeholder: "{{ ___('common.select_option') }}",
        allowClear: true
    });

    // Select All Classes
    $('#select-all-classes').on('change', function() {
        if ($(this).is(':checked')) {
            $('#classes option').prop('selected', true);
        } else {
            $('#classes option').prop('selected', false);
        }
        $('#classes').trigger('change');
    });

    // Select All Sections
    $('#select-all-sections').on('change', function() {
        if ($(this).is(':checked')) {
            $('#sections option').prop('selected', true);
        } else {
            $('#sections option').prop('selected', false);
        }
        $('#sections').trigger('change');
    });

    // Load sections when classes change
    $('#classes').on('change', function() {
        const classIds = $(this).val();
        loadSections(classIds);
        updateStudentCount();
    });

    // Update student count when filters change
    $('#sections, #month, #year, #fees_groups').on('change', function() {
        updateStudentCount();
    });

    // Preview button click
    $('#preview-btn').on('click', function() {
        loadPreview();
    });

    // Generate buttons
    $('#generate-all-btn, #generate-selected-btn').on('click', function() {
        const generateAll = $(this).attr('id') === 'generate-all-btn';
        startGeneration(generateAll);
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
                    $('#generate-all-btn').prop('disabled', false);
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
                    $('#preview-section').hide();
                    $('#progress-section').show();
                    $('#progress-actions').show();
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
                            $('#view-results-btn').show().attr('onclick', `window.location.href='{{ url("fees-generation/show") }}/${response.data.id}'`);
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
        $('#progress-bar').css('width', percentage + '%').text(percentage + '%');
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
                    setTimeout(() => location.reload(), 2000);
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
});
</script>
@endpush