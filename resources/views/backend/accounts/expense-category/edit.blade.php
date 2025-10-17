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
                        <li class="breadcrumb-item"><a href="{{ route('expense-category.index') }}">{{ ___('account.expense_categories') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('common.edit') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <div class="card ot-card">
            <div class="card-body">
                <form action="{{ route('expense-category.update', @$data['category']->id) }}" method="post"
                    id="categoryForm">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">{{ ___('common.name') }} <span
                                            class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('name') is-invalid @enderror"
                                        name="name"
                                        value="{{ old('name', @$data['category']->name) }}"
                                        id="name"
                                        placeholder="{{ ___('account.enter_category_name') }}">
                                    @error('name')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="code" class="form-label">{{ ___('account.category_code') }}</label>
                                    <input class="form-control ot-input @error('code') is-invalid @enderror"
                                        name="code"
                                        value="{{ old('code', @$data['category']->code) }}"
                                        id="code"
                                        placeholder="{{ ___('account.enter_category_code') }}">
                                    @error('code')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label for="description" class="form-label">{{ ___('account.description') }}</label>
                                    <textarea class="form-control ot-textarea @error('description') is-invalid @enderror"
                                        name="description"
                                        id="description"
                                        rows="3"
                                        placeholder="{{ ___('account.enter_description') }}">{{ old('description', @$data['category']->description) }}</textarea>
                                    @error('description')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ ___('common.status') }}</label>
                                    <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                        name="status" id="status">
                                        <option value="1" {{ old('status', @$data['category']->status) == 1 ? 'selected' : '' }}>{{ ___('common.active') }}</option>
                                        <option value="2" {{ old('status', @$data['category']->status) == 2 ? 'selected' : '' }}>{{ ___('common.inactive') }}</option>
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
                                            </span>{{ ___('common.update') }}</button>
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
