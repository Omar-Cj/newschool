@extends('backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection
@section('content')
    <div class="page-content">

        {{-- bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"> {{ ___('common.home') }} </a></li>
                        <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">{{ $data['title'] }}</a></li>
                        <li class="breadcrumb-item">{{ ___('common.edit') }}</li>

                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <div class="card ot-card">
            <div class="card-body">
                <form action="{{ route('classes.update', @$data['class']->id) }}" enctype="multipart/form-data" method="post"
                    id="visitForm">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('common.name') }} <span
                                            class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name',@$data['class']->name) }}" list="datalistOptions" id="exampleDataList"
                                        placeholder="{{ ___('common.enter_name') }}" onkeyup="suggestAcademicLevel()">
                                    @error('name')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="academic_level" class="form-label">{{ ___('Academic Level') }} <span
                                            class="fillable">*</span></label>
                                    <select class="nice-select niceSelect bordered_style wide @error('academic_level') is-invalid @enderror"
                                            name="academic_level" id="academic_level"
                                            aria-describedby="academicLevelFeedback">
                                        <option value="">{{ ___('Select Academic Level') }}</option>
                                        @foreach(\App\Models\Academic\Classes::getAcademicLevelOptions() as $value => $label)
                                            <option value="{{ $value }}" 
                                                {{ old('academic_level', @$data['class']->academic_level) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted mt-1" id="academic-level-help">
                                        @if(@$data['class']->hasAcademicLevel())
                                            <i class="fas fa-check-circle text-success"></i> Currently assigned: {{ @$data['class']->formatted_academic_level }}
                                        @else
                                            <i class="fas fa-exclamation-triangle text-warning"></i> <strong>No academic level assigned!</strong> This class needs an academic level for proper fee assignment.
                                        @endif
                                    </small>
                                    @error('academic_level')
                                        <div id="academicLevelFeedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    {{-- Status  --}}
                                    <label for="validationServer04" class="form-label">{{ ___('common.status') }} <span class="fillable">*</span></label>

                                    <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                    name="status" id="validationServer04"
                                    aria-describedby="validationServer04Feedback">

                                        <option value="{{ App\Enums\Status::ACTIVE }}"
                                            {{ @$data['class']->status == App\Enums\Status::ACTIVE ? 'selected' : '' }}>
                                            {{ ___('common.active') }}</option>
                                        <option value="{{ App\Enums\Status::INACTIVE }}"
                                            {{ @$data['class']->status == App\Enums\Status::INACTIVE ? 'selected' : '' }}>
                                            {{ ___('common.inactive') }}
                                        </option>
                                    </select>
                                </div>
                                @error('status')
                                    <div id="validationServer04Feedback" class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                                <div class="col-md-12 mt-24">
                                    <div class="text-end">
                                        <button class="btn btn-lg ot-btn-primary"><span><i class="fa-solid fa-save"></i>
                                            </span>{{ ___('common.update') }}</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
function suggestAcademicLevel() {
    const className = document.getElementById('exampleDataList').value.toLowerCase();
    const academicLevelSelect = document.getElementById('academic_level');
    const helpText = document.getElementById('academic-level-help');
    
    // Don't auto-suggest if a level is already selected
    if (academicLevelSelect.value) {
        return;
    }
    
    if (!className.trim()) {
        return;
    }
    
    let suggestion = null;
    let confidence = '';
    
    // KG patterns
    if (/\b(kg|kindergarten|nursery|pre-?k|pre-?school)\b/i.test(className)) {
        suggestion = 'kg';
        confidence = 'High confidence';
    }
    // Form patterns (secondary)  
    else if (/\bform\s*[1-4]\b/i.test(className)) {
        suggestion = 'secondary';
        confidence = 'High confidence';
    }
    // Grade/Class number patterns
    else if (/\b(?:grade|class)?\s*(\d+)\b/i.test(className)) {
        const match = className.match(/\b(?:grade|class)?\s*(\d+)\b/i);
        const number = parseInt(match[1]);
        
        if (number >= 1 && number <= 8) {
            suggestion = 'primary';
            confidence = 'High confidence';
        } else if (number >= 9 && number <= 10) {
            suggestion = 'secondary';
            confidence = 'Medium confidence';
        } else if (number >= 11 && number <= 12) {
            suggestion = 'high_school';
            confidence = 'High confidence';
        } else if (number < 1) {
            suggestion = 'kg';
            confidence = 'Medium confidence';
        }
    }
    // Subject-based patterns
    else if (/\b(advanced|algebra|calculus|physics|chemistry|biology)\b/i.test(className)) {
        suggestion = 'secondary';
        confidence = 'Medium confidence';
    }
    
    // Update help text with suggestion
    if (suggestion && !academicLevelSelect.value) {
        helpText.innerHTML = `<i class="fas fa-lightbulb text-warning"></i> <strong>Suggestion:</strong> Consider setting this to ${suggestion} (${confidence})`;
        helpText.classList.add('text-success');
    }
}

// Validate academic level is selected before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const academicLevel = document.getElementById('academic_level').value;
    if (!academicLevel) {
        e.preventDefault();
        alert('Please select an Academic Level. This is required for proper fee assignment.');
        document.getElementById('academic_level').focus();
    }
});
</script>
@endpush
