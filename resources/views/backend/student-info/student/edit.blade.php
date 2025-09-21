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
                                    href="{{ route('student.index') }}">{{ ___('student_info.student_list') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ ___('common.add_new') }}</li>
                        </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}


        <div class="card ot-card">
            <div class="card-body">
                {{-- @dd($data) --}}
                <form action="{{ route('student.update') }}" enctype="multipart/form-data" method="post" id="visitForm">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="user_id" value="{{ @$data['student']->user_id }}">
                    <input type="hidden" name="id" value="{{ @$data['student']->id }}">
                    <input type="hidden" id="url" value="{{ url('') }}">

                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('student_info.first_name') }}
                                        <span class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('first_first_name') is-invalid @enderror"
                                        name="first_name" list="datalistOptions" id="exampleDataList"
                                        placeholder="{{ ___('student_info.enter_first_name') }}"
                                        value="{{ old('first_name', @$data['student']->first_name) }}">
                                    @error('first_name')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('student_info.last_name') }}
                                        <span class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('last_name') is-invalid @enderror"
                                        name="last_name" list="datalistOptions" id="exampleDataList"
                                        placeholder="{{ ___('student_info.enter_last_name') }}"
                                        value="{{ old('last_name', @$data['student']->last_name) }}">
                                    @error('last_name')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="gradeSelect" class="form-label">{{ ___('student_info.grade') }}
                                        <span class="fillable">*</span></label>
                                    <select class="nice-select niceSelect bordered_style wide @error('grade') is-invalid @enderror"
                                        name="grade" id="gradeSelect" required>
                                        <option value="">{{ ___('student_info.select_grade') }}</option>
                                        <optgroup label="Kindergarten">
                                            <option {{ old('grade', @$data['student']->grade) == 'KG-1' ? 'selected' : '' }} value="KG-1">KG-1</option>
                                            <option {{ old('grade', @$data['student']->grade) == 'KG-2' ? 'selected' : '' }} value="KG-2">KG-2</option>
                                        </optgroup>
                                        <optgroup label="Primary">
                                            <option {{ old('grade', @$data['student']->grade) == 'Grade1' ? 'selected' : '' }} value="Grade1">Grade 1</option>
                                            <option {{ old('grade', @$data['student']->grade) == 'Grade2' ? 'selected' : '' }} value="Grade2">Grade 2</option>
                                            <option {{ old('grade', @$data['student']->grade) == 'Grade3' ? 'selected' : '' }} value="Grade3">Grade 3</option>
                                            <option {{ old('grade', @$data['student']->grade) == 'Grade4' ? 'selected' : '' }} value="Grade4">Grade 4</option>
                                            <option {{ old('grade', @$data['student']->grade) == 'Grade5' ? 'selected' : '' }} value="Grade5">Grade 5</option>
                                            <option {{ old('grade', @$data['student']->grade) == 'Grade6' ? 'selected' : '' }} value="Grade6">Grade 6</option>
                                            <option {{ old('grade', @$data['student']->grade) == 'Grade7' ? 'selected' : '' }} value="Grade7">Grade 7</option>
                                            <option {{ old('grade', @$data['student']->grade) == 'Grade8' ? 'selected' : '' }} value="Grade8">Grade 8</option>
                                        </optgroup>
                                        <optgroup label="Secondary">
                                            <option {{ old('grade', @$data['student']->grade) == 'Form1' ? 'selected' : '' }} value="Form1">Form 1</option>
                                            <option {{ old('grade', @$data['student']->grade) == 'Form2' ? 'selected' : '' }} value="Form2">Form 2</option>
                                            <option {{ old('grade', @$data['student']->grade) == 'Form3' ? 'selected' : '' }} value="Form3">Form 3</option>
                                            <option {{ old('grade', @$data['student']->grade) == 'Form4' ? 'selected' : '' }} value="Form4">Form 4</option>
                                        </optgroup>
                                    </select>
                                    @error('grade')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('student_info.mobile') }} <span
                                            class="fillable"></span></label>
                                    <input class="form-control ot-input @error('mobile') is-invalid @enderror"
                                        name="mobile" list="datalistOptions" id="exampleDataList" type="number"
                                        placeholder="{{ ___('student_info.enter_mobile') }}"
                                        value="{{ old('mobile', @$data['student']->mobile) }}">
                                    @error('mobile')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('common.email') }} <span
                                            class="fillable"></span></label>
                                    <input class="form-control ot-input @error('email') is-invalid @enderror"
                                        name="email" list="datalistOptions" id="exampleDataList" type="email"
                                        placeholder="{{ ___('student_info.enter_email') }}"
                                        value="{{ old('email', @$data['student']->email) }}">
                                    @error('email')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">{{ ___('frontend.Username') }} </label>
                                    <input name="username" placeholder="{{ ___('frontend.Username') }}"
                                        class="username form-control ot-input mb_30" type="text"
                                        value="{{ old('username', @$data['student']->user->username) }}">
                                    @if ($errors->has('username'))
                                        <div class="error text-danger">{{ $errors->first('username') }}</div>
                                    @endif
                                </div>


                                <!-- Class Field - Second Priority -->
                                <div class="col-md-3">
                                    <label for="validationServer04" class="form-label">{{ ___('student_info.class') }}
                                        <span class="fillable">*</span></label>
                                    <select id="getSections"
                                        class="nice-select niceSelect bordered_style wide @error('class') is-invalid @enderror"
                                        name="class"
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_class') }}</option>
                                        @foreach ($data['classes'] as $item)
                                            <option
                                                {{ @$data['session_class_student']->classes_id == $item->class->id ? 'selected' : '' }}
                                                value="{{ $item->class->id }}">{{ $item->class->name }}
                                        @endforeach
                                        </option>
                                    </select>
                                    @error('class')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Section Field - Third Priority -->
                                <div class="col-md-3">
                                    <label for="validationServer04" class="form-label">{{ ___('student_info.section') }}
                                        <span class="fillable">*</span></label>
                                    <select id="sectionSelect"
                                        class="nice-select sections niceSelect bordered_style wide @error('section') is-invalid @enderror"
                                        name="section"
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_section') }}</option>
                                        @foreach ($data['sections'] as $item)
                                            <option
                                                {{ @$data['session_class_student']->section_id == $item->section->id ? 'selected' : '' }}
                                                value="{{ $item->section->id }}">{{ $item->section->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('section')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-3">

                                    <label for="validationServer04" class="form-label">{{ ___('student_info.shift') }}
                                        <span class="fillable"></span></label>
                                    <select
                                        class="nice-select niceSelect bordered_style wide @error('shift') is-invalid @enderror"
                                        name="shift" id="validationServer04"
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_shift') }}</option>
                                        @foreach ($data['shifts'] as $item)
                                            <option
                                                {{ @$data['student']->session_class_student->shift->id == $item->id ? 'selected' : '' }}
                                                value="{{ $item->id }}">{{ $item->name }}
                                        @endforeach
                                    </select>

                                    @error('shift')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('common.date_of_birth') }}
                                        <span class="fillable">*</span></label>
                                    <input type="date"
                                        class="form-control ot-input @error('date_of_birth') is-invalid @enderror"
                                        name="date_of_birth" list="datalistOptions" id="exampleDataList"
                                        placeholder="{{ ___('common.date_of_birth') }}"
                                        value="{{ old('date_of_birth', @$data['student']->dob) }}">
                                    @error('date_of_birth')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-3">

                                    <label for="validationServer04" class="form-label">{{ ___('common.gender') }} <span
                                            class="fillable"></span></label>
                                    <select
                                        class="nice-select niceSelect bordered_style wide @error('gender') is-invalid @enderror"
                                        name="gender" id="validationServer04"
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_gender') }}</option>
                                        @foreach ($data['genders'] as $item)
                                            <option {{ @$data['student']->gender_id == $item->id ? 'selected' : '' }}
                                                value="{{ $item->id }}">{{ $item->name }}
                                        @endforeach
                                    </select>

                                    @error('gender')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>
                                <div class="col-md-3">

                                    <label for="validationServer04" class="form-label">{{ ___('common.category') }} <span
                                            class="fillable"></span></label>
                                    <select
                                        class="nice-select niceSelect bordered_style wide @error('category') is-invalid @enderror"
                                        name="category" id="validationServer04"
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_category') }}</option>
                                        @foreach ($data['categories'] as $item)
                                            <option
                                                {{ @$data['student']->student_category_id == $item->id ? 'selected' : '' }}
                                                value="{{ $item->id }}">{{ $item->name }}
                                        @endforeach
                                    </select>

                                    @error('category')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="exampleDataList"
                                        class="form-label ">{{ ___('student_info.admission_date') }} <span
                                            class="fillable">*</span></label>
                                    <input type="date"
                                        class="form-control ot-input @error('admission_date') is-invalid @enderror"
                                        name="admission_date" list="datalistOptions" id="exampleDataList"
                                        placeholder="{{ ___('student_info.admission_date') }}"
                                        value="{{ old('admission_date', @$data['student']->admission_date) }}">
                                    @error('admission_date')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('common.image') }}
                                        {{ ___('common.(100 x 100 px)') }}<span class="fillable"></span></label>


                                    <div class="ot_fileUploader left-side mb-3">
                                        <input class="form-control" type="text"
                                            placeholder="{{ ___('common.image') }}" readonly="" id="placeholder">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="btn btn-lg ot-btn-primary"
                                                for="fileBrouse">{{ ___('common.browse') }}</label>
                                            <input type="file" class="d-none form-control" name="image"
                                                id="fileBrouse" accept="image/*">
                                        </button>
                                    </div>
                                    
                                    {{-- Current Image Preview --}}
                                    @if(@$data['student']->upload)
                                        <div class="current-image-preview mb-2">
                                            <label class="form-label small text-muted">{{ ___('common.current_image') }}</label>
                                            <div class="current-image-container">
                                                <img src="{{ asset(@$data['student']->upload->path) }}" 
                                                     alt="{{ @$data['student']->first_name }}" 
                                                     class="current-student-image" 
                                                     style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 2px solid #e9ecef;">
                                            </div>
                                        </div>
                                    @endif

                                </div>
                                <div class="col-md-3 parent">

                                    <label for="validationServer04"
                                        class="form-label">{{ ___('student_info.select_parent') }}
                                        <span class="fillable">*</span></label>
                                    <select
                                        class="parent nice-select niceSelect bordered_style wide @error('parent') is-invalid @enderror"
                                        name="parent" id="validationServer04"
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_parent') }}</option>
                                        @foreach ($data['parentGuardians'] as $parentGuardian)
                                            <option {{ @$data['student']->parent_guardian_id == $parentGuardian->id ? 'selected' : '' }} value="{{ $parentGuardian->id }}">
                                                {{ $parentGuardian->guardian_name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('parent')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label"
                                        for="#">{{ ___('frontend.attend_school_previously') }} </label>
                                    <div class="input-check-radio academic-section mt-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="previous_school"
                                                value="1" id="previous_school"
                                                {{ @$data['student']->previous_school ? 'checked' : '' }}>
                                            <label class="form-check-label ps-2 pe-5"
                                                for="previous_school">{{ ___('common.Yes') }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 d-none mb-3" id="previous_school_info">
                                    <label class="form-label"
                                        for="#">{{ ___('frontend.previous_school_information') }} </label>
                                    <textarea class="form-control" rows="2" name="previous_school_info">{{ old('previous_school_info', @$data['student']->previous_school_info) }}</textarea>

                                </div>

                                <div class="col-xl-3 d-none mb-3" id="previous_school_doc">
                                    <label for="exampleDataList"
                                        class="form-label">{{ ___('frontend.previous_school_documents') }}<span
                                            class="fillable"></span>

                                    </label>

                                    <div class="ot_fileUploader left-side mb-3">
                                        <input class="form-control" type="text"
                                            placeholder="{{ ___('common.image') }}" readonly="" id="placeholder1">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="btn btn-lg ot-btn-primary"
                                                for="fileBrouse1">{{ ___('common.browse') }}</label>
                                            <input type="file" class="d-none form-control"
                                                name="previous_school_image" id="fileBrouse1" accept="image/*">
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">{{ ___('frontend.Place_Of_Birth') }} </label>
                                    <input name="place_of_birth" placeholder="{{ ___('frontend.Place_Of_Birth') }}"
                                        class="email form-control ot-input mb_30" type="text"
                                        value="{{ old('place_of_birth', @$data['student']->place_of_birth) }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">{{ ___('frontend.Residance_Address') }} </label>
                                    <input name="residance_address"
                                        placeholder="{{ ___('frontend.Residance_Address') }}"
                                        class="email form-control ot-input mb_30" type="text"
                                        value="{{ old('residance_address', @$data['student']->residance_address) }}">
                                </div>

                                <div class="col-md-3">

                                    <label for="validationServer04" class="form-label">{{ ___('common.status') }} <span
                                            class="fillable">*</span></label>
                                    <select
                                        class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                        name="status" id="validationServer04"
                                        aria-describedby="validationServer04Feedback">
                                        <option
                                            {{ @$data['student']->status == App\Enums\Status::ACTIVE ? 'selected' : '' }}
                                            value="{{ App\Enums\Status::ACTIVE }}">{{ ___('common.active') }}
                                        </option>
                                        <option
                                            {{ @$data['student']->status == App\Enums\Status::INACTIVE ? 'selected' : '' }}
                                            value="{{ App\Enums\Status::INACTIVE }}">{{ ___('common.inactive') }}
                                        </option>
                                    </select>

                                    @error('status')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>






                            </div>

                            {{-- Student Services Management Section --}}
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center gap-4 flex-wrap">
                                        <h3 class="m-0 flex-fill">
                                            {{ ___('fees.service_subscriptions') ?? 'Fee Service Subscriptions' }}
                                        </h3>
                                        <button type="button" class="btn btn-lg ot-btn-primary radius_30px small_add_btn" onclick="addNewService()">
                                            <span><i class="fa-solid fa-plus"></i> </span>
                                            {{ ___('common.add') }} {{ ___('fees.service') ?? 'Service' }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered role-table" id="servicesTable">
                                            <thead class="thead">
                                                <tr>
                                                    <th scope="col">{{ ___('fees.service_type') ?? 'Service Type' }}</th>
                                                    <th scope="col">{{ ___('fees.category') ?? 'Category' }}</th>
                                                    <th scope="col">{{ ___('fees.amount') ?? 'Amount' }}</th>
                                                    <th scope="col">{{ ___('fees.discount') ?? 'Discount' }}</th>
                                                    <th scope="col">{{ ___('common.status') ?? 'Status' }}</th>
                                                    <th scope="col">{{ ___('common.action') ?? 'Action' }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="servicesTableBody">
                                                @php
                                                    $existingServices = $data['student']->studentServices ?? collect();
                                                    $currentAcademicYear = session('academic_year_id') ?? \App\Models\Session::active()->value('id');
                                                    $studentServices = $existingServices->where('academic_year_id', $currentAcademicYear);
                                                @endphp

                                                @forelse($studentServices as $key => $service)
                                                    <tr id="service-row-{{ $key }}">
                                                        <td>
                                                            <select name="services[{{ $key }}][fee_type_id]" class="form-control ot-input service-type-select" required>
                                                                <option value="">{{ ___('fees.select_service_type') ?? 'Select Service Type' }}</option>
                                                                @foreach($data['fee_types'] ?? \App\Models\Fees\FeesType::all() as $feeType)
                                                                    <option value="{{ $feeType->id }}" 
                                                                            data-amount="{{ $feeType->amount }}"
                                                                            data-category="{{ $feeType->getFormattedCategory() }}"
                                                                            {{ $service->fee_type_id == $feeType->id ? 'selected' : '' }}>
                                                                        {{ $feeType->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            <input type="hidden" name="services[{{ $key }}][id]" value="{{ $service->id }}">
                                                        </td>
                                                        <td>
                                                            <span class="service-category">{{ $service->feeType->getFormattedCategory() }}</span>
                                                        </td>
                                                        <td>
                                                            <input type="number" name="services[{{ $key }}][amount]" 
                                                                   class="form-control ot-input service-amount" 
                                                                   value="{{ $service->amount }}" 
                                                                   step="0.01" min="0" required>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center gap-2">
                                                                <select name="services[{{ $key }}][discount_type]" class="form-control ot-input discount-type-select">
                                                                    <option value="none" {{ $service->discount_type == 'none' ? 'selected' : '' }}>{{ ___('fees.no_discount') ?? 'No Discount' }}</option>
                                                                    <option value="percentage" {{ $service->discount_type == 'percentage' ? 'selected' : '' }}>{{ ___('fees.percentage') ?? 'Percentage' }}</option>
                                                                    <option value="fixed" {{ $service->discount_type == 'fixed' ? 'selected' : '' }}>{{ ___('fees.fixed_amount') ?? 'Fixed Amount' }}</option>
                                                                </select>
                                                                <input type="number" name="services[{{ $key }}][discount_value]" 
                                                                       class="form-control ot-input discount-value" 
                                                                       value="{{ $service->discount_value }}" 
                                                                       step="0.01" min="0" placeholder="0">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <select name="services[{{ $key }}][is_active]" class="form-control ot-input">
                                                                <option value="1" {{ $service->is_active ? 'selected' : '' }}>{{ ___('common.active') ?? 'Active' }}</option>
                                                                <option value="0" {{ !$service->is_active ? 'selected' : '' }}>{{ ___('common.inactive') ?? 'Inactive' }}</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeService({{ $key }})">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr id="no-services-row">
                                                        <td colspan="7" class="text-center text-muted">
                                                            {{ ___('fees.no_services_assigned') ?? 'No services assigned yet. Click "Add Service" to get started.' }}
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center gap-4 flex-wrap">
                                        <h3 class="m-0 flex-fill">
                                            {{ ___('student_info.upload_documents') }}
                                        </h3>
                                        <button type="button"
                                            class="btn btn-lg ot-btn-primary radius_30px small_add_btn addNewDocument"
                                            onclick="addNewDocument()">
                                            <span><i class="fa-solid fa-plus"></i> </span>
                                            {{ ___('common.add') }}</button>
                                        <input type="hidden" name="counter" id="counter"
                                            value="{{ count(@$data['student']->upload_documents ?? []) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table school_borderLess_table table_border_hide2"
                                            id="student-document">
                                            <thead>
                                                <tr>
                                                    <td scope="col">{{ ___('common.name') }} <span
                                                            class="text-danger"></span>
                                                        @if ($errors->any())
                                                            @if ($errors->has('school_user_name.*'))
                                                                <span
                                                                    class="custom-message">{{ 'the fields are required' }}
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td scope="col">
                                                        {{ ___('common.document') }}
                                                        <span class="text-danger"></span>
                                                        @if ($errors->any())
                                                            @if ($errors->has('school_user_telephone.*'))
                                                                <span
                                                                    class="custom-message">{{ 'the fields are required' }}
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td scope="col">
                                                        {{ ___('common.action') }}
                                                    </td>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach (@$data['student']->upload_documents ?? [] as $key => $item)
                                                    <tr id="document-file">
                                                        <td>
                                                            <input type="text"
                                                                name="document_names[{{ $key }}]"
                                                                value="{{ $item['title'] }}"
                                                                class="form-control ot-input min_width_200 "
                                                                placeholder="{{ ___('student_info.enter_name') }}"
                                                                required>
                                                            <input type="hidden" name="document_rows[]"
                                                                value="{{ $key }}">

                                                        </td>
                                                        <td class="w-100 mw-50">
                                                            <div class="school_primary_fileUplaoder mb-3">
                                                                <label for="awesomefile{{ $key }}"
                                                                    class="filelabel">{{ ___('school.browse') }}</label>
                                                                <input type="file"
                                                                    name="document_files[{{ $key }}]"
                                                                    id="awesomefile{{ $key }}"
                                                                    value="{{ $item['file'] }}">
                                                                <input type="text" class="redonly_input" readonly
                                                                    placeholder="{{ ___('school.Document upload') }}">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button class="drax_close_icon" onclick="removeRow(this)">
                                                                <i class="fa-solid fa-xmark"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>



                            <div class="row">
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
        $(document).ready(function() {
            var fileInp1 = document.getElementById("fileBrouse1");
            if (fileInp1) {
                fileInp1.addEventListener("change", showFileName);

                function showFileName(event) {
                    var fileInp = event.srcElement;
                    var fileName = fileInp.files[0].name;
                    document.getElementById("placeholder1").placeholder = fileName;
                }
            }

            function checkCheckboxState() {
                var isChecked = $('#previous_school').prop('checked');
                console.log(isChecked)
                if (isChecked) {
                    $('#previous_school_info').removeClass('d-none');
                    $('#previous_school_doc').removeClass('d-none');
                } else {
                    $('#previous_school_info').addClass('d-none');
                    $('#previous_school_doc').addClass('d-none');
                }
            }

            $('#previous_school').change(checkCheckboxState);
            checkCheckboxState();

            // Service management functionality
            window.serviceRowCounter = {{ count($studentServices ?? []) }};
        });

        // Add new service row
        function addNewService() {
            const tableBody = document.getElementById('servicesTableBody');
            const noServicesRow = document.getElementById('no-services-row');
            
            if (noServicesRow) {
                noServicesRow.remove();
            }

            const newRow = document.createElement('tr');
            newRow.id = `service-row-${window.serviceRowCounter}`;
            newRow.innerHTML = `
                <td>
                    <select name="services[${window.serviceRowCounter}][fee_type_id]" class="form-control ot-input service-type-select" required>
                        <option value="">{{ ___('fees.select_service_type') ?? 'Select Service Type' }}</option>
                        @foreach($data['fee_types'] ?? \App\Models\Fees\FeesType::all() as $feeType)
                            <option value="{{ $feeType->id }}" 
                                    data-amount="{{ $feeType->amount }}"
                                    data-category="{{ $feeType->getFormattedCategory() }}">
                                {{ $feeType->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <span class="service-category">-</span>
                </td>
                <td>
                    <input type="number" name="services[${window.serviceRowCounter}][amount]" 
                           class="form-control ot-input service-amount" 
                           value="0" step="0.01" min="0" required>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <select name="services[${window.serviceRowCounter}][discount_type]" class="form-control ot-input discount-type-select">
                            <option value="none">{{ ___('fees.no_discount') ?? 'No Discount' }}</option>
                            <option value="percentage">{{ ___('fees.percentage') ?? 'Percentage' }}</option>
                            <option value="fixed">{{ ___('fees.fixed_amount') ?? 'Fixed Amount' }}</option>
                        </select>
                        <input type="number" name="services[${window.serviceRowCounter}][discount_value]" 
                               class="form-control ot-input discount-value" 
                               value="0" step="0.01" min="0" placeholder="0">
                    </div>
                </td>
                <td>
                    <select name="services[${window.serviceRowCounter}][is_active]" class="form-control ot-input">
                        <option value="1">{{ ___('common.active') ?? 'Active' }}</option>
                        <option value="0">{{ ___('common.inactive') ?? 'Inactive' }}</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeService(${window.serviceRowCounter})">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;
            
            tableBody.appendChild(newRow);
            window.serviceRowCounter++;

            // Add event listener to the new service type select
            const newSelect = newRow.querySelector('.service-type-select');
            newSelect.addEventListener('change', handleServiceTypeChange);
        }

        // Remove service row
        function removeService(rowIndex) {
            const row = document.getElementById(`service-row-${rowIndex}`);
            if (row) {
                row.remove();
            }

            // Check if no services left, show empty message
            const tableBody = document.getElementById('servicesTableBody');
            if (tableBody.children.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.id = 'no-services-row';
                emptyRow.innerHTML = `
                    <td colspan="7" class="text-center text-muted">
                        {{ ___('fees.no_services_assigned') ?? 'No services assigned yet. Click "Add Service" to get started.' }}
                    </td>
                `;
                tableBody.appendChild(emptyRow);
            }
        }

        // Handle service type change
        function handleServiceTypeChange(event) {
            const select = event.target;
            const selectedOption = select.options[select.selectedIndex];
            const row = select.closest('tr');
            
            if (selectedOption.value) {
                const amount = selectedOption.getAttribute('data-amount');
                const category = selectedOption.getAttribute('data-category');
                
                // Update amount field
                const amountInput = row.querySelector('.service-amount');
                if (amountInput && amount) {
                    amountInput.value = amount;
                }
                
                // Update category display
                const categorySpan = row.querySelector('.service-category');
                if (categorySpan && category) {
                    categorySpan.textContent = category;
                }
            } else {
                // Reset fields
                const amountInput = row.querySelector('.service-amount');
                const categorySpan = row.querySelector('.service-category');
                
                if (amountInput) amountInput.value = '0';
                if (categorySpan) categorySpan.textContent = '-';
            }
        }

        // Note: Section loading in edit form is also handled by custom.js
        // The existing system should work with the restored #getSections ID

        // Add event listeners to existing service selects
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.service-type-select').forEach(select => {
                select.addEventListener('change', handleServiceTypeChange);
            });
        });
    </script>
@endpush
