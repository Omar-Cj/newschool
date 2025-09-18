@extends('backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection

@section('content')
    <div class="page-content">

        {{-- breadcrumb Area Start --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('journals.index') }}">{{ ___('journals.journals') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area End --}}

        <div class="col-12">
            <div class="card ot-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">{{ $data['title'] }}</h3>
                    <a href="{{ route('journals.index') }}" class="btn btn-lg ot-btn-primary-outline btn-right-icon radius-md">
                        <span><i class="fa-solid fa-arrow-left"></i></span>
                        <span class="">{{ ___('common.back') }}</span>
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('journals.update', $data['journal']->id) }}" method="post" id="journalEditForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Journal Name -->
                            <div class="col-md-6">
                                <div class="single-input">
                                    <label class="label-text-title color-heading font-medium mb-2">
                                        {{ ___('journals.name') }} <span class="text-danger">*</span>
                                    </label>
                                    <input class="form-control radius-md @error('name') is-invalid @enderror"
                                           type="text"
                                           name="name"
                                           value="{{ old('name', $data['journal']->name) }}"
                                           placeholder="{{ ___('journals.enter_journal_name') }}"
                                           required>
                                    @error('name')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Journal Branch -->
                            <div class="col-md-6">
                                <div class="single-input">
                                    <label class="label-text-title color-heading font-medium mb-2">
                                        {{ ___('journals.branch') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="nice-select niceSelect bordered_style wide @error('branch_id') is-invalid @enderror"
                                            name="branch_id" required>
                                        <option value="">{{ ___('journals.select_branch') }}</option>
                                        @foreach($data['branches'] as $branch)
                                            <option value="{{ $branch['id'] }}" {{ old('branch_id', $data['journal']->branch_id) == $branch['id'] ? 'selected' : '' }}>
                                                {{ $branch['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="single-input">
                                    <label class="label-text-title color-heading font-medium mb-2">
                                        {{ ___('common.status') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                            name="status" required>
                                        <option value="">{{ ___('common.select_status') }}</option>
                                        <option value="active" {{ old('status', $data['journal']->status) == 'active' ? 'selected' : '' }}>
                                            {{ ___('common.active') }}
                                        </option>
                                        <option value="inactive" {{ old('status', $data['journal']->status) == 'inactive' ? 'selected' : '' }}>
                                            {{ ___('common.inactive') }}
                                        </option>
                                    </select>
                                    @error('status')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="single-input">
                                    <label class="label-text-title color-heading font-medium mb-2">
                                        {{ ___('journals.description') }}
                                    </label>
                                    <textarea class="form-control radius-md @error('description') is-invalid @enderror"
                                              name="description"
                                              rows="4"
                                              placeholder="{{ ___('journals.enter_description') }}">{{ old('description', $data['journal']->description) }}</textarea>
                                    @error('description')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Journal Information (Read-only) -->
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="single-input">
                                            <label class="label-text-title color-heading font-medium mb-2">
                                                {{ ___('common.created_at') }}
                                            </label>
                                            <input class="form-control radius-md"
                                                   type="text"
                                                   value="{{ dateFormat($data['journal']->created_at) }}"
                                                   readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="single-input">
                                            <label class="label-text-title color-heading font-medium mb-2">
                                                {{ ___('common.updated_at') }}
                                            </label>
                                            <input class="form-control radius-md"
                                                   type="text"
                                                   value="{{ dateFormat($data['journal']->updated_at) }}"
                                                   readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-md-12">
                                <div class="single-input">
                                    <div class="d-flex justify-content-end gap-3">
                                        <a href="{{ route('journals.index') }}"
                                           class="btn btn-lg ot-btn-primary-outline radius-md">
                                            {{ ___('common.cancel') }}
                                        </a>
                                        <button type="submit" class="btn btn-lg ot-btn-primary radius-md">
                                            <span><i class="fa-solid fa-save"></i></span>
                                            <span class=""> {{ ___('common.update') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Form validation
        $('#journalEditForm').on('submit', function(e) {
            let isValid = true;
            let errorMessage = '';

            // Validate name
            if ($('input[name="name"]').val().trim() === '') {
                isValid = false;
                errorMessage += '{{ ___("journals.name_required") }}\n';
            }

            // Validate branch
            if ($('select[name="branch_id"]').val() === '') {
                isValid = false;
                errorMessage += '{{ ___("journals.branch_required") }}\n';
            }

            // Validate status
            if ($('select[name="status"]').val() === '') {
                isValid = false;
                errorMessage += '{{ ___("common.status_required") }}\n';
            }

            if (!isValid) {
                e.preventDefault();
                alert(errorMessage);
                return false;
            }
        });

        // Auto-focus on first input
        $('input[name="name"]').focus();
    });
</script>
@endsection