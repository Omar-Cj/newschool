@extends('mainapp::layouts.backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection


@section('css')
    <style>
        .form-control:disabled {
            background-color: #e9ecef !important;
        }
    </style>
@endsection


@section('content')
    <div class="page-content">

        {{-- bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"> {{ ___('mainapp_common.home') }} </a></li>
                        <li class="breadcrumb-item"><a href="{{ route('school.index') }}">{{ ___('mainapp_schools.schools') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('mainapp_common.edit') }}</li>

                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}
        <div class="card ot-card">
            <div class="card-body">
                <form action="{{ route('school.update', @$data['school']->id) }}" enctype="multipart/form-data" method="post"
                    id="visitForm">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.name') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('name') is-invalid @enderror" name="name"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter name') }}" value="{{ old('name', @$data['school']->name) }}">
                            @error('name')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="validationServer04" class="form-label">{{ ___('mainapp_common.Package') }} <span class="fillable">*</span></label>
                            <input class="form-control ot-input" placeholder="{{ ___('mainapp_common.Enter phone') }}" value="{{ @$data['school']->package->name }}" disabled>
                            {{-- <select class="nice-select niceSelect bordered_style wide @error('package') is-invalid @enderror"
                            name="package" id="validationServer04"
                            aria-describedby="validationServer04Feedback" @disabled(true)>
                                <option value="">{{ ___('common.Select package') }}</option>
                                @foreach ($data['packages'] as $item)
                                    <option {{ old('package', @$data['school']->package_id) == $item->id ? 'selected':'' }} value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>

                            @error('package')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror --}}
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.phone') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('phone') is-invalid @enderror" name="phone"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter phone') }}" value="{{ old('phone', @$data['school']->phone) }}">
                            @error('phone')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.email') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('email') is-invalid @enderror" name="email" type="email"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter email') }}" value="{{ old('email', @$data['school']->email) }}">
                            @error('email')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.Sub domain key') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('sub_domain_key') is-invalid @enderror" name="sub_domain_key" readonly
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter sub domain key') }}" value="{{ old('sub_domain_key', @$data['school']->sub_domain_key) }}" disabled>
                            @error('sub_domain_key')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="validationServer04" class="form-label">{{ ___('mainapp_common.status') }} <span class="fillable">*</span></label>
                            <select class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                            name="status" id="validationServer04"
                            aria-describedby="validationServer04Feedback">
                                <option {{ old('status', @$data['school']->status) == App\Enums\Status::ACTIVE ? 'selected':'' }} value="{{ App\Enums\Status::ACTIVE }}">{{ ___('mainapp_common.active') }}</option>
                                <option {{ old('status', @$data['school']->status) == App\Enums\Status::INACTIVE ? 'selected':'' }} value="{{ App\Enums\Status::INACTIVE }}">{{ ___('mainapp_common.inactive') }}
                                </option>
                            </select>

                            @error('status')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.address') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('address') is-invalid @enderror" name="address"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter address') }}" value="{{ old('address', @$data['school']->address) }}">
                            @error('address')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Super Admin Credentials Section --}}
                        @if(isset($data['superAdmin']))
                        <div class="col-md-12 mb-3">
                            <hr>
                            <h5 class="text-primary mb-3">
                                <i class="fa-solid fa-user-shield"></i>
                                {{ ___('mainapp_common.Super Admin Credentials') }}
                            </h5>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="admin_email" class="form-label">
                                {{ ___('mainapp_common.Super Admin Email') }}
                            </label>
                            <input type="email"
                                   class="form-control ot-input @error('admin_email') is-invalid @enderror"
                                   name="admin_email"
                                   id="admin_email"
                                   placeholder="{{ ___('mainapp_common.Enter super admin email') }}"
                                   value="{{ old('admin_email', @$data['superAdmin']->email) }}">
                            @error('admin_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                {{ ___('mainapp_common.Leave blank to keep current email') }}
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="admin_password" class="form-label">
                                {{ ___('mainapp_common.New Password') }}
                            </label>
                            <input type="password"
                                   class="form-control ot-input @error('admin_password') is-invalid @enderror"
                                   name="admin_password"
                                   id="admin_password"
                                   placeholder="{{ ___('mainapp_common.Enter new password') }}">
                            @error('admin_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                {{ ___('mainapp_common.Leave blank to keep current password') }}
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="admin_password_confirmation" class="form-label">
                                {{ ___('mainapp_common.Confirm Password') }}
                            </label>
                            <input type="password"
                                   class="form-control ot-input"
                                   name="admin_password_confirmation"
                                   id="admin_password_confirmation"
                                   placeholder="{{ ___('mainapp_common.Confirm new password') }}">
                        </div>
                        @endif

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
