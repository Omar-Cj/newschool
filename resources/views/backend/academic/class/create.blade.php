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
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item" aria-current="page"><a
                                href="{{ route('classes.index') }}">{{ ___('settings.class') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ ___('common.add_new') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <div class="card ot-card">
            <div class="card-body">
                <form action="{{ route('classes.store') }}" enctype="multipart/form-data" method="post" id="visitForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('common.name') }} <span
                                            class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('name') is-invalid @enderror" name="name"
                                        list="datalistOptions" id="exampleDataList"
                                        placeholder="{{ ___('common.enter_name') }}" value="{{ old('name') }}"
                                        onkeyup="suggestAcademicLevel()">
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
                                            <option value="{{ $value }}" {{ old('academic_level') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted mt-1" id="academic-level-help">
                                        <i class="fas fa-info-circle"></i> This determines which fee types will be automatically assigned to students in this class.
                                    </small>
                                    @error('academic_level')
                                        <div id="academicLevelFeedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6">

                                    <label for="validationServer04" class="form-label">{{ ___('common.status') }} <span class="fillable">*</span></label>
                                    <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                    name="status" id="validationServer04"
                                    aria-describedby="validationServer04Feedback">
                                        <option value="{{ App\Enums\Status::ACTIVE }}">{{ ___('common.active') }}</option>
                                        <option value="{{ App\Enums\Status::INACTIVE }}">{{ ___('common.inactive') }}
                                        </option>
                                    </select>

                                    @error('status')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>
                                <div class="col-md-12 mt-24">
                                    <div class="text-end">
                                        <button class="btn btn-lg ot-btn-primary"><span><i class="fa-solid fa-save"></i>
                                            </span>{{ ___('common.submit') }}</button>
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
    
    // Update the select and help text
    if (suggestion) {
        // Only auto-select if no option is currently selected
        if (!academicLevelSelect.value) {
            academicLevelSelect.value = suggestion;
            // Trigger nice-select update if it exists
            if (typeof $(academicLevelSelect).niceSelect !== 'undefined') {
                $(academicLevelSelect).niceSelect('update');
            }
        }
        
        // Update help text with suggestion
        const levelName = academicLevelSelect.options[academicLevelSelect.selectedIndex]?.text || suggestion;
        helpText.innerHTML = `<i class="fas fa-lightbulb text-warning"></i> <strong>Suggestion:</strong> ${levelName} (${confidence}) - You can change this if needed.`;
        helpText.classList.add('text-success');
    } else {
        // Reset help text
        helpText.innerHTML = '<i class="fas fa-info-circle"></i> This determines which fee types will be automatically assigned to students in this class.';
        helpText.classList.remove('text-success');
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

// Show fee information when academic level is selected
document.getElementById('academic_level').addEventListener('change', function() {
    const level = this.value;
    const helpText = document.getElementById('academic-level-help');
    
    if (level) {
        const feeInfo = {
            'kg': 'Students will get Kindergarten tuition fees',
            'primary': 'Students will get Primary school tuition fees (Grade 1-8)', 
            'secondary': 'Students will get Secondary school tuition fees (Form 1-4)',
            'high_school': 'Students will get High school tuition fees'
        };
        
        helpText.innerHTML = `<i class="fas fa-dollar-sign text-success"></i> <strong>Fee Impact:</strong> ${feeInfo[level]}`;
        helpText.classList.add('text-info');
    }
});
</script>
@endpush
