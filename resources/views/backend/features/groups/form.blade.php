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
                    <li class="breadcrumb-item"><a href="{{ route('feature-groups.index') }}">{{ ___('common.Feature Groups') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ isset($data['feature_group']) ? ___('common.edit') : ___('common.add_new') }}
                    </li>
                </ol>
            </div>
        </div>
    </div>
    {{-- Breadcrumb Area End --}}

    <div class="card ot-card">
        <div class="card-body">
            <form action="{{ isset($data['feature_group']) ? route('feature-groups.update', $data['feature_group']->id) : route('feature-groups.store') }}"
                  enctype="multipart/form-data"
                  method="post"
                  id="feature-group-form">
                @csrf
                @if(isset($data['feature_group']))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">
                            {{ ___('common.Name') }} <span class="fillable">*</span>
                        </label>
                        <input class="form-control ot-input @error('name') is-invalid @enderror"
                               name="name"
                               id="name"
                               placeholder="{{ ___('common.Enter name') }}"
                               value="{{ old('name', $data['feature_group']->name ?? '') }}">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="slug" class="form-label">
                            {{ ___('common.Slug') }}
                            <small class="text-muted">({{ ___('common.Leave empty to auto-generate') }})</small>
                        </label>
                        <input class="form-control ot-input @error('slug') is-invalid @enderror"
                               name="slug"
                               id="slug"
                               placeholder="{{ ___('common.Enter slug') }}"
                               value="{{ old('slug', $data['feature_group']->slug ?? '') }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="icon" class="form-label">{{ ___('common.Icon') }}</label>
                        <select class="form-control ot-input icon-select @error('icon') is-invalid @enderror"
                                name="icon"
                                id="icon">
                            <option value="">{{ ___('common.Select icon') }}</option>
                            @foreach($data['icons'] as $iconClass => $iconName)
                                <option value="{{ $iconClass }}"
                                        data-icon="{{ $iconClass }}"
                                        {{ old('icon', $data['feature_group']->icon ?? '') == $iconClass ? 'selected' : '' }}>
                                    {{ $iconName }}
                                </option>
                            @endforeach
                        </select>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="mt-2" id="icon-preview">
                            @if(isset($data['feature_group']->icon))
                                <i class="{{ $data['feature_group']->icon }} fa-3x"></i>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="position" class="form-label">{{ ___('common.Position') }}</label>
                        <input class="form-control ot-input @error('position') is-invalid @enderror"
                               name="position"
                               type="number"
                               id="position"
                               min="0"
                               placeholder="{{ ___('common.Enter position') }}"
                               value="{{ old('position', $data['feature_group']->position ?? 0) }}">
                        @error('position')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">{{ ___('common.Status') }} <span class="fillable">*</span></label>
                        <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                name="status"
                                id="status">
                            <option value="1" {{ old('status', $data['feature_group']->status ?? 1) == 1 ? 'selected' : '' }}>
                                {{ ___('common.active') }}
                            </option>
                            <option value="0" {{ old('status', $data['feature_group']->status ?? 1) == 0 ? 'selected' : '' }}>
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
                                  placeholder="{{ ___('common.Enter description') }}">{{ old('description', $data['feature_group']->description ?? '') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-12 mt-24">
                        <div class="text-end">
                            <a href="{{ route('feature-groups.index') }}" class="btn btn-lg btn-secondary me-2">
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
    // Auto-generate slug from name
    $('#name').on('keyup', function() {
        var name = $(this).val();
        var slug = name.toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#slug').val(slug);
    });

    // Icon select with preview
    $('.icon-select').select2({
        templateResult: formatIcon,
        templateSelection: formatIcon,
        width: '100%'
    });

    function formatIcon(icon) {
        if (!icon.id) {
            return icon.text;
        }
        var iconClass = $(icon.element).data('icon');
        if (!iconClass) {
            return icon.text;
        }
        return $('<span><i class="' + iconClass + '"></i> ' + icon.text + '</span>');
    }

    // Update icon preview
    $('#icon').on('change', function() {
        var iconClass = $(this).find(':selected').data('icon');
        if (iconClass) {
            $('#icon-preview').html('<i class="' + iconClass + ' fa-3x"></i>');
        } else {
            $('#icon-preview').html('');
        }
    });
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

#icon-preview {
    text-align: center;
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 4px;
}
</style>
@endpush
