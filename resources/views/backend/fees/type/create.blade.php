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
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item" aria-current="page"><a
                                href="{{ route('fees-type.index') }}">{{ $data['title'] }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ ___('common.add_new') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <div class="card ot-card">
            <div class="card-body">
                <form action="{{ route('fees-type.store') }}" enctype="multipart/form-data" method="post" id="visitForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('common.name') }} <span
                                            class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('name') is-invalid @enderror" name="name"
                                        list="datalistOptions" id="exampleDataList" type="text"
                                        placeholder="{{ ___('common.enter_name') }}" value="{{ old('name') }}">
                                    @error('name')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('fees.code') }} </label>
                                    <input class="form-control ot-input @error('code') is-invalid @enderror" name="code"
                                        list="datalistOptions" id="exampleDataList" type="text"
                                        placeholder="{{ ___('fees.enter_code') }}" value="{{ old('code') }}">
                                    @error('code')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('fees.description') }}</label>
                                    <textarea class="form-control ot-textarea mt-0 @error('description') is-invalid @enderror" name="description"
                                    list="datalistOptions" id="exampleDataList"
                                    placeholder="{{ ___('fees.enter_description') }}">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- Enhanced Fee System Fields --}}
                                <div class="col-md-6 mb-3">
                                    <label for="academic_level" class="form-label">{{ ___('fees.academic_level') }} <span class="fillable">*</span></label>
                                    <select class="nice-select niceSelect bordered_style wide @error('academic_level') is-invalid @enderror"
                                    name="academic_level" id="academic_level" aria-describedby="academic_level_feedback">
                                        <option value="all" {{ old('academic_level') == 'all' ? 'selected' : '' }}>{{ ___('fees.all_levels') }}</option>
                                        <option value="kg" {{ old('academic_level') == 'kg' ? 'selected' : '' }}>{{ ___('fees.kindergarten') }}</option>
                                        <option value="primary" {{ old('academic_level') == 'primary' ? 'selected' : '' }}>{{ ___('fees.primary') }}</option>
                                        <option value="secondary" {{ old('academic_level') == 'secondary' ? 'selected' : '' }}>{{ ___('fees.secondary') }}</option>
                                        <option value="high_school" {{ old('academic_level') == 'high_school' ? 'selected' : '' }}>{{ ___('fees.high_school') }}</option>
                                    </select>
                                    @error('academic_level')
                                        <div id="academic_level_feedback" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">{{ ___('fees.category') }} <span class="fillable">*</span></label>
                                    <select class="nice-select niceSelect bordered_style wide @error('category') is-invalid @enderror"
                                    name="category" id="category" aria-describedby="category_feedback">
                                        <option value="academic" {{ old('category') == 'academic' ? 'selected' : '' }}>{{ ___('fees.academic') }}</option>
                                        <option value="transport" {{ old('category') == 'transport' ? 'selected' : '' }}>{{ ___('fees.transport') }}</option>
                                        <option value="meal" {{ old('category') == 'meal' ? 'selected' : '' }}>{{ ___('fees.meal') }}</option>
                                        <option value="accommodation" {{ old('category') == 'accommodation' ? 'selected' : '' }}>{{ ___('fees.accommodation') }}</option>
                                        <option value="activity" {{ old('category') == 'activity' ? 'selected' : '' }}>{{ ___('fees.activity') }}</option>
                                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>{{ ___('fees.other') }}</option>
                                    </select>
                                    @error('category')
                                        <div id="category_feedback" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="amount" class="form-label">{{ ___('fees.default_amount') }} <span class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('amount') is-invalid @enderror" name="amount"
                                        type="number" step="0.01" min="0" id="amount"
                                        placeholder="{{ ___('fees.enter_amount') }}" value="{{ old('amount', 0) }}">
                                    @error('amount')
                                        <div id="validationServer04Feedback" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="due_date_offset" class="form-label">{{ ___('fees.due_date_offset') }}</label>
                                    <input class="form-control ot-input @error('due_date_offset') is-invalid @enderror" name="due_date_offset"
                                        type="number" min="0" id="due_date_offset"
                                        placeholder="{{ ___('fees.days_from_term_start') }}" value="{{ old('due_date_offset', 30) }}">
                                    <small class="text-muted">{{ ___('fees.days_from_term_start_help') }}</small>
                                    @error('due_date_offset')
                                        <div id="validationServer04Feedback" class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="form-check mt-4">
                                        <input class="form-check-input @error('is_mandatory_for_level') is-invalid @enderror" 
                                               type="checkbox" name="is_mandatory_for_level" value="1" 
                                               id="is_mandatory_for_level" {{ old('is_mandatory_for_level') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_mandatory_for_level">
                                            {{ ___('fees.mandatory_for_academic_level') }}
                                        </label>
                                        <small class="d-block text-muted">{{ ___('fees.mandatory_help') }}</small>
                                        @error('is_mandatory_for_level')
                                            <div id="validationServer04Feedback" class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
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
                </form>
            </div>
        </div>
    </div>
@endsection
