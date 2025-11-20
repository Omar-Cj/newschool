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
                                href="{{ route('school.index') }}">{{ ___('mainapp_schools.School') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ ___('mainapp_common.add_new') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <div class="card ot-card">
            <div class="card-body">
                <form action="{{ route('school.store') }}" enctype="multipart/form-data" method="post" id="visitForm">
                    @csrf
                    <input type="hidden" name="source" id="form_type" value="admin"/>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.name') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('name') is-invalid @enderror" name="name" type="text"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter name') }}" value="{{ old('name') }}">
                            @error('name')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="validationServer04" class="form-label">{{ ___('mainapp_schools.Package') }} <span class="fillable">*</span></label>
                            <select class="nice-select niceSelect bordered_style wide @error('package') is-invalid @enderror"
                            name="package" id="validationServer04"
                            aria-describedby="validationServer04Feedback">
                                <option value="">{{ ___('mainapp_schools.Select package') }}</option>
                                @foreach ($data['packages'] as $item)
                                    <option {{ old('package') == $item->id ? 'selected':'' }} value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>

                            @error('package')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="number_of_branches" class="form-label">{{ ___('mainapp_schools.Number of Branches') }} <span class="fillable">*</span></label>
                            <input class="form-control ot-input @error('number_of_branches') is-invalid @enderror"
                                name="number_of_branches"
                                type="number"
                                id="number_of_branches"
                                min="1"
                                max="10"
                                placeholder="{{ ___('mainapp_schools.Enter number of branches') }}"
                                value="{{ old('number_of_branches', 1) }}">
                            <small class="form-text text-muted">{{ ___('mainapp_schools.Specify how many branches to create for this school (1-10)') }}</small>
                            @error('number_of_branches')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.phone') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('phone') is-invalid @enderror" name="phone" type="text"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter phone') }}" value="{{ old('phone') }}">
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
                                placeholder="{{ ___('mainapp_common.Enter email') }}" value="{{ old('email') }}">
                            @error('email')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="exampleDataList" class="form-label">{{ ___('mainapp_common.address') }} <span
                                    class="fillable">*</span></label>
                            <input class="form-control ot-input @error('address') is-invalid @enderror" name="address" type="text"
                                list="datalistOptions" id="exampleDataList"
                                placeholder="{{ ___('mainapp_common.Enter address') }}" value="{{ old('address') }}">
                            @error('address')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- School Administrator Section -->
                        <div class="col-md-12 mb-3">
                            <h5 class="mb-3 mt-2">{{ ___('mainapp_schools.School Administrator') }}</h5>
                            <hr>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="admin_name" class="form-label">{{ ___('mainapp_common.name') }} <span class="fillable">*</span></label>
                            <input class="form-control ot-input @error('admin_name') is-invalid @enderror" name="admin_name" type="text"
                                id="admin_name"
                                placeholder="{{ ___('mainapp_common.Enter name') }}" value="{{ old('admin_name') }}">
                            @error('admin_name')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="admin_email" class="form-label">{{ ___('mainapp_common.email') }} <span class="fillable">*</span></label>
                            <input class="form-control ot-input @error('admin_email') is-invalid @enderror" name="admin_email" type="email"
                                id="admin_email"
                                placeholder="{{ ___('mainapp_common.Enter email') }}" value="{{ old('admin_email') }}">
                            @error('admin_email')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="admin_password" class="form-label">{{ ___('mainapp_common.password') }} <span class="fillable">*</span></label>
                            <input class="form-control ot-input @error('admin_password') is-invalid @enderror" name="admin_password" type="password"
                                id="admin_password"
                                placeholder="{{ ___('mainapp_common.Enter password') }}">
                            @error('admin_password')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="admin_password_confirm" class="form-label">{{ ___('mainapp_common.confirm_password') }} <span class="fillable">*</span></label>
                            <input class="form-control ot-input @error('admin_password_confirmation') is-invalid @enderror" name="admin_password_confirmation" type="password"
                                id="admin_password_confirm"
                                placeholder="{{ ___('mainapp_common.Confirm password') }}">
                            @error('admin_password_confirmation')
                                <div id="validationServer04Feedback" class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-12 justify-content-center">
                           <code> {{___('mainapp_schools.School Superadmin Email Will Be As your Email Input')}} </code><br>
                           <code>{{ ___('mainapp_schools.Default Password Will Be : 123456') }} </code>
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
@endpush
