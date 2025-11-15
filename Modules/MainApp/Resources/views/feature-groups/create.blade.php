@extends('mainapp::layouts.backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection
@section('content')
    <div class="page-content">

        {{-- bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('mainapp_common.home') }}</a></li>
                        <li class="breadcrumb-item" aria-current="page"><a
                                href="{{ route('feature-groups.index') }}">{{ ___('common.Feature Groups') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ ___('mainapp_common.add') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <div class="card ot-card">
            <div class="card-body">
                <form action="{{ route('feature-groups.store') }}" method="post" id="feature-group-form-create">
                    @csrf
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">{{ ___('mainapp_common.name') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('name') is-invalid @enderror" name="name"
                                id="name" type="text"
                                placeholder="{{ ___('common.Enter name') }}" value="{{ old('name') }}">
                            @error('name')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="slug" class="form-label">{{ ___('common.Slug') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('slug') is-invalid @enderror" name="slug"
                                id="slug" type="text"
                                placeholder="{{ ___('common.Enter slug') }}" value="{{ old('slug') }}">
                            @error('slug')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">{{ ___('common.Description') }}</label>
                            <textarea class="form-control ot-input @error('description') is-invalid @enderror"
                                name="description" id="description" rows="3"
                                placeholder="{{ ___('common.Enter description') }}">{{ old('description') }}</textarea>
                            @error('description')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="icon" class="form-label">{{ ___('mainapp_common.Icon') }}</label>
                            <input class="form-control ot-input @error('icon') is-invalid @enderror" name="icon"
                                id="icon" type="text"
                                placeholder="las la-layer-group" value="{{ old('icon') }}">
                            <small class="text-muted">{{ ___('common.Use Line Awesome icon classes') }}</small>
                            @error('icon')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="position" class="form-label">{{ ___('common.Position') }}</label>
                            <input class="form-control ot-input @error('position') is-invalid @enderror" name="position"
                                id="position" type="number" min="0"
                                placeholder="0" value="{{ old('position', 0) }}">
                            @error('position')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">{{ ___('mainapp_common.status') }} <span
                                    class="fillable">*</span></label>
                            <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                name="status" id="status">
                                <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>{{ ___('mainapp_common.active') }}</option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>{{ ___('mainapp_common.inactive') }}</option>
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
                                    </span>{{ ___('mainapp_common.submit') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('script')
    <script>
        // Auto-generate slug from name
        document.getElementById('name').addEventListener('input', function() {
            const slug = this.value.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('slug').value = slug;
        });
    </script>
@endpush
