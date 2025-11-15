@extends('mainapp::layouts.backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection

@section('content')
<div class="page-content">
    {{-- Breadcrumb Area Start --}}
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('permission-features.index') }}">{{ ___('common.Permission Features') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ isset($data['permission_feature']) ? ___('common.edit') : ___('common.add_new') }}
                    </li>
                </ol>
            </div>
        </div>
    </div>
    {{-- Breadcrumb Area End --}}

    <div class="card ot-card">
        <div class="card-body">
            <form action="{{ isset($data['permission_feature']) ? route('permission-features.update', $data['permission_feature']->id) : route('permission-features.store') }}"
                  enctype="multipart/form-data"
                  method="post"
                  id="permission-feature-form">
                @csrf
                @if(isset($data['permission_feature']))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="permission_id" class="form-label">
                            {{ ___('common.Permission') }} <span class="fillable">*</span>
                        </label>
                        <select class="form-control permission-select @error('permission_id') is-invalid @enderror"
                                name="permission_id"
                                id="permission_id">
                            <option value="">{{ ___('common.Select permission') }}</option>
                            @foreach($data['permissions'] as $permission)
                                <option value="{{ $permission->id }}"
                                        data-keywords="{{ $permission->name }}"
                                        {{ old('permission_id', $data['permission_feature']->permission_id ?? '') == $permission->id ? 'selected' : '' }}>
                                    {{ $permission->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('permission_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="permission-keywords"></small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="feature_group_id" class="form-label">
                            {{ ___('common.Feature Group') }} <span class="fillable">*</span>
                        </label>
                        <select class="nice-select niceSelect bordered_style wide @error('feature_group_id') is-invalid @enderror"
                                name="feature_group_id"
                                id="feature_group_id">
                            <option value="">{{ ___('common.Select feature group') }}</option>
                            @foreach($data['feature_groups'] as $group)
                                <option value="{{ $group->id }}"
                                        {{ old('feature_group_id', $data['permission_feature']->feature_group_id ?? '') == $group->id ? 'selected' : '' }}>
                                    {{ $group->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('feature_group_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">
                            {{ ___('common.Display Name') }}
                            <small class="text-muted">({{ ___('common.Leave empty to use permission name') }})</small>
                        </label>
                        <input class="form-control ot-input @error('name') is-invalid @enderror"
                               name="name"
                               id="name"
                               placeholder="{{ ___('common.Enter display name') }}"
                               value="{{ old('name', $data['permission_feature']->name ?? '') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="position" class="form-label">{{ ___('common.Position') }}</label>
                        <input class="form-control ot-input @error('position') is-invalid @enderror"
                               name="position"
                               type="number"
                               id="position"
                               min="0"
                               placeholder="{{ ___('common.Enter position') }}"
                               value="{{ old('position', $data['permission_feature']->position ?? 0) }}">
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">{{ ___('common.Status') }} <span class="fillable">*</span></label>
                        <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                name="status"
                                id="status">
                            <option value="1" {{ old('status', $data['permission_feature']->status ?? 1) == 1 ? 'selected' : '' }}>
                                {{ ___('common.active') }}
                            </option>
                            <option value="0" {{ old('status', $data['permission_feature']->status ?? 1) == 0 ? 'selected' : '' }}>
                                {{ ___('common.inactive') }}
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">{{ ___('common.Description') }}</label>
                        <textarea class="form-control ot-textarea @error('description') is-invalid @enderror"
                                  name="description"
                                  id="description"
                                  rows="3"
                                  placeholder="{{ ___('common.Enter description') }}">{{ old('description', $data['permission_feature']->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_premium" id="is_premium" value="1"
                                   {{ old('is_premium', $data['permission_feature']->is_premium ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_premium">
                                <i class="fa-solid fa-star text-warning"></i>
                                {{ ___('common.Mark as Premium Feature') }}
                            </label>
                        </div>
                        <small class="text-muted">{{ ___('common.Premium features are only available in higher-tier packages') }}</small>
                    </div>

                    <div class="col-md-12 mt-24">
                        <div class="text-end">
                            <a href="{{ route('permission-features.index') }}" class="btn btn-lg btn-secondary me-2">
                                <span><i class="fa-solid fa-arrow-left"></i></span>
                                {{ ___('common.Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-lg ot-btn-primary">
                                <span><i class="fa-solid fa-save"></i></span>
                                {{ ___('common.submit') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for permission dropdown
    $('.permission-select').select2({
        placeholder: '{{ ___("common.Search and select permission") }}',
        allowClear: true,
        width: '100%'
    });

    // Show permission keywords on selection
    $('#permission_id').on('change', function() {
        var keywords = $(this).find(':selected').data('keywords');
        if (keywords) {
            $('#permission-keywords').text('Keywords: ' + keywords);
        } else {
            $('#permission-keywords').text('');
        }

        // Auto-fill name if empty
        if (!$('#name').val()) {
            var permissionName = $(this).find(':selected').text();
            var displayName = permissionName.replace(/_/g, ' ')
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
            $('#name').val(displayName);
        }
    });

    // Trigger change on load if permission is selected
    if ($('#permission_id').val()) {
        $('#permission_id').trigger('change');
    }
});
</script>

<style>
.select2-container--default .select2-selection--single {
    height: 46px;
    padding: 10px;
    border: 1px solid #E5E5E5;
    border-radius: 4px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 26px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 44px;
}
</style>
@endpush
