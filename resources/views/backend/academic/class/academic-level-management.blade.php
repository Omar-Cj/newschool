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
                    <h4 class="breadcrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">{{ ___('academic.class') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area E n d --}}

        <!-- Statistics Card -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📊 Academic Level Distribution</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-2">
                                <div class="stat-item">
                                    <span class="badge-basic-info-text">{{ $data['statistics']['kg'] ?? 0 }}</span>
                                    <p>Kindergarten</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stat-item">
                                    <span class="badge-basic-success-text">{{ $data['statistics']['primary'] ?? 0 }}</span>
                                    <p>Primary School</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stat-item">
                                    <span class="badge-basic-primary-text">{{ $data['statistics']['secondary'] ?? 0 }}</span>
                                    <p>Secondary School</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stat-item">
                                    <span class="badge-basic-dark-text">{{ $data['statistics']['high_school'] ?? 0 }}</span>
                                    <p>High School</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stat-item">
                                    <span class="badge-basic-warning-text">{{ $data['statistics']['unassigned'] ?? 0 }}</span>
                                    <p>⚠️ Unassigned</p>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="stat-item">
                                    <strong>{{ $data['classes']->count() }}</strong>
                                    <p>Total Classes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Management Form -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Academic Level Assignments</h4>
                <div>
                    <button type="button" class="btn btn-sm btn-secondary" id="auto-suggest-all">
                        🤖 Auto-suggest All
                    </button>
                    <button type="button" class="btn btn-sm btn-success" id="save-changes">
                        💾 Save Changes
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form id="bulk-assignment-form" method="POST" action="{{ route('classes.bulk-assign-academic-levels') }}">
                    @csrf
                    
                    <div class="alert alert-info">
                        <strong>Instructions:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Use the dropdowns to assign academic levels to classes</li>
                            <li>Click "Auto-suggest All" to get intelligent suggestions based on class names</li>
                            <li>Classes without academic levels are highlighted in yellow</li>
                            <li>Click "Save Changes" to apply all assignments</li>
                        </ul>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="35%">Class Name</th>
                                    <th width="20%">Current Level</th>
                                    <th width="25%">Assign Level</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['classes'] as $key => $class)
                                <tr class="{{ !$class->hasAcademicLevel() ? 'table-warning' : '' }}" 
                                    data-class-id="{{ $class->id }}" data-class-name="{{ $class->name }}">
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <strong>{{ $class->name }}</strong>
                                        @if(!$class->hasAcademicLevel())
                                            <span class="badge badge-warning ms-2">Needs Assignment</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($class->hasAcademicLevel())
                                            <span class="badge-basic-{{ $class->getAcademicLevelColor() }}-text">
                                                {{ $class->formatted_academic_level }}
                                            </span>
                                        @else
                                            <span class="badge-basic-warning-text">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <select name="assignments[{{ $key }}][academic_level]" 
                                                class="form-select academic-level-select" 
                                                data-class-id="{{ $class->id }}">
                                            <option value="">Select Level</option>
                                            <option value="kg" {{ $class->academic_level == 'kg' ? 'selected' : '' }}>
                                                Kindergarten
                                            </option>
                                            <option value="primary" {{ $class->academic_level == 'primary' ? 'selected' : '' }}>
                                                Primary School (Grade 1-8)
                                            </option>
                                            <option value="secondary" {{ $class->academic_level == 'secondary' ? 'selected' : '' }}>
                                                Secondary School (Form 1-4)
                                            </option>
                                            <option value="high_school" {{ $class->academic_level == 'high_school' ? 'selected' : '' }}>
                                                High School (Grade 11-12)
                                            </option>
                                        </select>
                                        <input type="hidden" name="assignments[{{ $key }}][class_id]" value="{{ $class->id }}">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary suggest-btn" 
                                                data-class-id="{{ $class->id }}">
                                            💡 Suggest
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- Loading Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 mb-0">Processing suggestions...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
$(document).ready(function() {
    // Individual suggest button
    $('.suggest-btn').click(function() {
        const classId = $(this).data('class-id');
        const row = $(this).closest('tr');
        const select = row.find('.academic-level-select');
        
        $.ajax({
            url: '{{ route("classes.suggest-academic-level") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                class_id: classId
            },
            success: function(response) {
                if (response.success && response.suggestion) {
                    select.val(response.suggestion);
                    select.addClass('border-success');
                    
                    // Show temporary success message
                    const btn = row.find('.suggest-btn');
                    const originalText = btn.html();
                    btn.html('✅ Applied').removeClass('btn-outline-primary').addClass('btn-success');
                    
                    setTimeout(() => {
                        btn.html(originalText).removeClass('btn-success').addClass('btn-outline-primary');
                        select.removeClass('border-success');
                    }, 2000);
                } else {
                    alert('No suggestion available for class: ' + response.class_name);
                }
            },
            error: function() {
                alert('Failed to get suggestion. Please try again.');
            }
        });
    });

    // Auto-suggest all button
    $('#auto-suggest-all').click(function() {
        const btn = $(this);
        const originalText = btn.html();
        
        btn.html('🔄 Processing...').prop('disabled', true);
        $('#loadingModal').modal('show');
        
        let completed = 0;
        const total = $('.suggest-btn').length;
        
        $('.suggest-btn').each(function(index) {
            const classId = $(this).data('class-id');
            const row = $(this).closest('tr');
            const select = row.find('.academic-level-select');
            
            // Delay each request to avoid overwhelming the server
            setTimeout(() => {
                $.ajax({
                    url: '{{ route("classes.suggest-academic-level") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        class_id: classId
                    },
                    success: function(response) {
                        if (response.success && response.suggestion) {
                            select.val(response.suggestion);
                            row.removeClass('table-warning').addClass('table-success');
                        }
                    },
                    complete: function() {
                        completed++;
                        if (completed === total) {
                            $('#loadingModal').modal('hide');
                            btn.html(originalText).prop('disabled', false);
                            
                            // Show completion message
                            $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                              '<strong>Auto-suggestions completed!</strong> Review the suggestions and click "Save Changes" to apply them.' +
                              '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                              '</div>').insertBefore('#bulk-assignment-form');
                        }
                    }
                });
            }, index * 200); // 200ms delay between requests
        });
    });

    // Save changes button
    $('#save-changes').click(function() {
        const form = $('#bulk-assignment-form');
        const hasChanges = $('.academic-level-select').filter(function() {
            return $(this).val() !== '';
        }).length > 0;
        
        if (!hasChanges) {
            alert('No changes to save. Please assign academic levels to classes first.');
            return;
        }
        
        if (confirm('Are you sure you want to save all academic level assignments?')) {
            form.submit();
        }
    });

    // Highlight rows when academic level is changed
    $('.academic-level-select').change(function() {
        const row = $(this).closest('tr');
        if ($(this).val()) {
            row.removeClass('table-warning').addClass('table-light');
        } else {
            row.removeClass('table-light').addClass('table-warning');
        }
    });
});
</script>
@endpush