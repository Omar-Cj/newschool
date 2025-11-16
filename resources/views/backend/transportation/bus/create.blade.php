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
                                href="{{ route('bus.index') }}">{{ ___('transportation.buses') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ ___('common.add_new') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <div class="card ot-card">
            <div class="card-header">
                <h4>{{ ___('transportation.create_bus') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('bus.store') }}" method="post" id="busForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="area_name" class="form-label">{{ ___('transportation.area_name') }} <span
                                            class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('area_name') is-invalid @enderror"
                                        name="area_name"
                                        value="{{ old('area_name') }}"
                                        id="area_name"
                                        placeholder="{{ ___('transportation.enter_area_name') }}">
                                    @error('area_name')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="bus_number" class="form-label">{{ ___('transportation.bus_number') }}</label>
                                    <input class="form-control ot-input @error('bus_number') is-invalid @enderror"
                                        name="bus_number"
                                        value="{{ old('bus_number') }}"
                                        id="bus_number"
                                        placeholder="{{ ___('transportation.enter_bus_number') }}">
                                    @error('bus_number')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="capacity" class="form-label">{{ ___('transportation.capacity') }}</label>
                                    <input class="form-control ot-input @error('capacity') is-invalid @enderror"
                                        name="capacity"
                                        type="number"
                                        min="0"
                                        value="{{ old('capacity') }}"
                                        id="capacity"
                                        placeholder="{{ ___('transportation.enter_capacity') }}">
                                    @error('capacity')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="driver_name" class="form-label">{{ ___('transportation.driver_name') }}</label>
                                    <input class="form-control ot-input @error('driver_name') is-invalid @enderror"
                                        name="driver_name"
                                        value="{{ old('driver_name') }}"
                                        id="driver_name"
                                        placeholder="{{ ___('transportation.enter_driver_name') }}">
                                    @error('driver_name')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="driver_phone" class="form-label">{{ ___('transportation.driver_phone') }}</label>
                                    <input class="form-control ot-input @error('driver_phone') is-invalid @enderror"
                                        name="driver_phone"
                                        value="{{ old('driver_phone') }}"
                                        id="driver_phone"
                                        placeholder="{{ ___('transportation.enter_driver_phone') }}">
                                    @error('driver_phone')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="license_plate" class="form-label">{{ ___('transportation.license_plate') }}</label>
                                    <input class="form-control ot-input @error('license_plate') is-invalid @enderror"
                                        name="license_plate"
                                        value="{{ old('license_plate') }}"
                                        id="license_plate"
                                        placeholder="{{ ___('transportation.enter_license_plate') }}">
                                    @error('license_plate')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">{{ ___('common.status') }}</label>
                                    <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                        name="status" id="status">
                                        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>{{ ___('common.active') }}</option>
                                        <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>{{ ___('common.inactive') }}</option>
                                    </select>
                                    @error('status')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-12 mt-24">
                                    <div class="text-end">
                                        <a href="{{ route('bus.index') }}" class="btn btn-lg ot-btn-secondary me-2">
                                            <span><i class="fa-solid fa-arrow-left"></i> </span>
                                            {{ ___('common.cancel') }}
                                        </a>
                                        <button type="submit" class="btn btn-lg ot-btn-primary">
                                            <span><i class="fa-solid fa-save"></i> </span>
                                            {{ ___('common.submit') }}
                                        </button>
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
