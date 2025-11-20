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
                                    href="{{ route('student.index') }}">{{ ___('student_info.student_list') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ ___('common.add_new') }}</li>
                        </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}
        <div class="card ot-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"> </h4>
                @if (hasPermission('student_create'))
                    <a href="{{ route('student.import') }}" class="btn btn-lg ot-btn-primary">
                        <span><i class="fa-solid fa-plus"></i> </span>
                        <span class="">{{ ___('common.Import') }}</span>
                    </a>
                @endif
            </div>


            <div class="card-body">
                @if(isset($data['branch_student_limit']) && $data['branch_student_limit'] > 0 && $data['branch_student_limit'] < 99999999)
                    @php
                        $percentUsed = $data['branch_student_limit'] > 0
                            ? ($data['branch_current_count'] / $data['branch_student_limit']) * 100
                            : 0;

                        // Color coding: green if >20% remaining, yellow if 10-20%, red if <10%
                        $percentRemaining = 100 - $percentUsed;
                        if ($percentRemaining > 20) {
                            $alertClass = 'alert-success';
                            $iconClass = 'fa-circle-check';
                        } elseif ($percentRemaining >= 10) {
                            $alertClass = 'alert-warning';
                            $iconClass = 'fa-triangle-exclamation';
                        } else {
                            $alertClass = 'alert-danger';
                            $iconClass = 'fa-circle-exclamation';
                        }

                        $atLimit = $data['branch_current_count'] >= $data['branch_student_limit'];
                    @endphp

                    <div class="alert {{ $alertClass }} alert-dismissible fade show mb-4" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="fa-solid {{ $iconClass }} me-3 mt-1" style="font-size: 1.5rem;"></i>
                            <div class="flex-grow-1">
                                <h5 class="alert-heading mb-2">
                                    <i class="fa-solid fa-building me-2"></i>{{ $data['branch_name'] }} - Student Enrollment Status
                                </h5>
                                <div class="mb-2">
                                    <strong>Package:</strong> {{ $data['package_name'] }}
                                </div>
                                <div class="mb-2">
                                    <strong>Students Enrolled:</strong>
                                    <span class="badge bg-primary">{{ $data['branch_current_count'] }} / {{ $data['branch_student_limit'] }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Remaining Slots:</strong>
                                    <span class="badge {{ $atLimit ? 'bg-danger' : 'bg-success' }}">
                                        {{ $data['branch_remaining_slots'] }}
                                    </span>
                                </div>
                                <div class="progress mt-2" style="height: 25px;">
                                    <div class="progress-bar {{ $atLimit ? 'bg-danger' : ($percentRemaining < 20 ? 'bg-warning' : 'bg-success') }}"
                                         role="progressbar"
                                         style="width: {{ $percentUsed }}%;"
                                         aria-valuenow="{{ $percentUsed }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        {{ number_format($percentUsed, 1) }}% Used
                                    </div>
                                </div>
                                @if($atLimit)
                                    <div class="mt-3 p-3 bg-light border-start border-danger border-4">
                                        <p class="mb-0">
                                            <i class="fa-solid fa-ban me-2"></i>
                                            <strong>Enrollment Limit Reached:</strong> This branch has reached its maximum student capacity.
                                            To enroll more students, please <a href="#" class="alert-link">upgrade to a higher package</a> or contact support.
                                        </p>
                                    </div>
                                @elseif($percentRemaining < 10)
                                    <div class="mt-3 p-3 bg-light border-start border-warning border-4">
                                        <p class="mb-0">
                                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                            <strong>Low Capacity Warning:</strong> Less than 10% of student slots remaining.
                                            Consider upgrading your package to avoid enrollment interruptions.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('student.store') }}" enctype="multipart/form-data" method="post" id="visitForm">
                    @csrf
                    <input type="hidden" id="url" value="{{ url('') }}">
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('student_info.first_name') }}
                                        <span class="fillable">*</span></label>
                                    <input class="form-control ot-input @error('first_name') is-invalid @enderror"
                                        name="first_name" list="datalistOptions" id="exampleDataList_first_name"
                                        placeholder="{{ ___('student_info.enter_first_name') }}"
                                        value="{{ old('first_name') }}">
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
                                        name="last_name" list="datalistOptions" id="exampleDataList_last_name"
                                        placeholder="{{ ___('student_info.enter_last_name') }}"
                                        value="{{ old('last_name') }}">
                                    @error('last_name')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('student_info.mobile') }} <span
                                            class="fillable"></span></label>
                                    <input class="form-control ot-input @error('mobile') is-invalid @enderror"
                                        name="mobile" list="datalistOptions" id="exampleDataList_mobile" type="number"
                                        placeholder="{{ ___('student_info.enter_mobile') }}" value="{{ old('mobile') }}">
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
                                        name="email" list="datalistOptions" id="exampleDataList_email" type="email"
                                        placeholder="{{ ___('student_info.enter_email') }}" value="{{ old('email') }}">
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
                                        value="{{ old('username') }}">
                                    @if ($errors->has('username'))
                                        <div class="error text-danger">{{ $errors->first('username') }}</div>
                                    @endif
                                </div>


                                <input type="hidden" id="siblings_discount" name="siblings_discount" value="0">
                                
                                <!-- Grade Field - First Priority -->
                                <div class="col-md-3 mb-3">
                                    <label for="gradeSelect" class="form-label">{{ ___('student_info.grade') }}
                                        <span class="fillable">*</span></label>
                                    <select class="nice-select niceSelect bordered_style wide @error('grade') is-invalid @enderror"
                                        name="grade" id="gradeSelect" required>
                                        <option value="">{{ ___('student_info.select_grade') }}</option>
                                        <optgroup label="Kindergarten">
                                            <option {{ old('grade') == 'KG-1' ? 'selected' : '' }} value="KG-1">KG-1</option>
                                            <option {{ old('grade') == 'KG-2' ? 'selected' : '' }} value="KG-2">KG-2</option>
                                        </optgroup>
                                        <optgroup label="Primary">
                                            <option {{ old('grade') == 'Grade1' ? 'selected' : '' }} value="Grade1">Grade 1</option>
                                            <option {{ old('grade') == 'Grade2' ? 'selected' : '' }} value="Grade2">Grade 2</option>
                                            <option {{ old('grade') == 'Grade3' ? 'selected' : '' }} value="Grade3">Grade 3</option>
                                            <option {{ old('grade') == 'Grade4' ? 'selected' : '' }} value="Grade4">Grade 4</option>
                                            <option {{ old('grade') == 'Grade5' ? 'selected' : '' }} value="Grade5">Grade 5</option>
                                            <option {{ old('grade') == 'Grade6' ? 'selected' : '' }} value="Grade6">Grade 6</option>
                                            <option {{ old('grade') == 'Grade7' ? 'selected' : '' }} value="Grade7">Grade 7</option>
                                            <option {{ old('grade') == 'Grade8' ? 'selected' : '' }} value="Grade8">Grade 8</option>
                                        </optgroup>
                                        <optgroup label="Secondary">
                                            <option {{ old('grade') == 'Form1' ? 'selected' : '' }} value="Form1">Form 1</option>
                                            <option {{ old('grade') == 'Form2' ? 'selected' : '' }} value="Form2">Form 2</option>
                                            <option {{ old('grade') == 'Form3' ? 'selected' : '' }} value="Form3">Form 3</option>
                                            <option {{ old('grade') == 'Form4' ? 'selected' : '' }} value="Form4">Form 4</option>
                                        </optgroup>
                                    </select>
                                    @error('grade')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <!-- Class Field - Second Priority -->
                                <div class="col-md-3">
                                    <label for="getSections" class="form-label">{{ ___('student_info.class') }}
                                        <span class="fillable">*</span></label>
                                    <select id="getSections"
                                        class="nice-select niceSelect bordered_style wide @error('class') is-invalid @enderror"
                                        name="class" 
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_class') }}</option>
                                        @foreach ($data['classes'] as $item)
                                            <option {{ old('class') == $item->id ? 'selected' : '' }}
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
                                    <label for="sectionSelect" class="form-label">{{ ___('student_info.section') }}
                                        <span class="fillable">*</span></label>
                                    <select id="sectionSelect"
                                        class="nice-select sections niceSelect bordered_style wide @error('section') is-invalid @enderror"
                                        name="section"
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_section') }}</option>
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
                                        name="shift" id="validationServer04_shift"
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_shift') }}</option>
                                        @foreach ($data['shifts'] as $item)
                                            <option {{ old('shift') == $item->id ? 'selected' : '' }}
                                                value="{{ $item->id }}">{{ $item->name }}
                                        @endforeach
                                    </select>

                                    @error('status')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="exampleDataList" class="form-label ">{{ ___('common.date_of_birth') }}
                                        <span class="fillable"></span></label>
                                    <input type="date"
                                        class="form-control ot-input @error('date_of_birth') is-invalid @enderror"
                                        name="date_of_birth" list="datalistOptions" id="exampleDataList_date_of_birth"
                                        placeholder="{{ ___('common.date_of_birth') }}"
                                        value="{{ old('date_of_birth') }}">
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
                                        name="gender" id="validationServer04_gender"
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_gender') }}</option>
                                        @foreach ($data['genders'] as $item)
                                            <option {{ old('gender') == $item->id ? 'selected' : '' }}
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
                                        name="category" id="validationServer04_category"
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_category') }}</option>
                                        @foreach ($data['categories'] as $item)
                                            <option {{ old('category') == $item->id ? 'selected' : '' }}
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
                                        name="admission_date" list="datalistOptions" id="exampleDataList_admission_date"
                                        placeholder="{{ ___('student_info.admission_date') }}"
                                        value="{{ old('admission_date', date('Y-m-d')) }}">
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
                                </div>
                                <div class="col-md-3 parent mb-3">

                                    <!-- Hidden input for parent creation mode -->
                                    <input type="hidden" name="parent_creation_mode" id="parent_creation_mode" value="existing">

                                    <label for="validationServer04"
                                        class="form-label">{{ ___('student_info.select_parent') }}
                                        <span class="fillable" id="parent_required_asterisk">*</span></label>

                                    <!-- Tab-style toggle for parent selection mode -->
                                    <div class="btn-group btn-group-sm d-flex mb-2" role="group" aria-label="Parent selection mode">
                                        <button type="button" class="btn btn-outline-secondary flex-fill" id="tab_existing_parent">
                                            <i class="fa fa-list"></i> {{ ___('student_info.select_existing') ?? 'Select Existing' }}
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary flex-fill" id="tab_create_parent">
                                            <i class="fa fa-plus"></i> {{ ___('student_info.create_new') ?? 'Create New' }}
                                        </button>
                                    </div>

                                    <!-- Existing parent dropdown (default visible) -->
                                    <div id="existing_parent_section">
                                        <select
                                            class="parent nice-select niceSelect bordered_style wide @error('parent') is-invalid @enderror"
                                            name="parent" id="validationServer04_parent"
                                            aria-describedby="validationServer04Feedback">
                                            <option value="">{{ ___('student_info.select_parent') }}</option>
                                            @foreach ($data['parentGuardians'] as $parentGuardian)
                                                <option {{ old('parent') == $parentGuardian->id ? 'selected' : '' }}
                                                    value="{{ $parentGuardian->id }}">
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

                                    <!-- Inline parent creation form (hidden by default) -->
                                    <div id="new_parent_section" style="display: none;">
                                        <div class="border rounded p-3" style="background-color: #f8f9fa;">
                                            <div class="row">
                                                <!-- Guardian Name -->
                                                <div class="col-md-12 mb-2">
                                                    <input type="text"
                                                        class="form-control ot-input @error('new_parent_name') is-invalid @enderror"
                                                        name="new_parent_name"
                                                        id="new_parent_name"
                                                        placeholder="{{ ___('student_info.guardian_name') }} *"
                                                        value="{{ old('new_parent_name') }}">
                                                    @error('new_parent_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Guardian Mobile -->
                                                <div class="col-md-12 mb-2">
                                                    <input type="text"
                                                        class="form-control ot-input @error('new_parent_mobile') is-invalid @enderror"
                                                        name="new_parent_mobile"
                                                        id="new_parent_mobile"
                                                        placeholder="{{ ___('student_info.guardian_mobile') }} *"
                                                        value="{{ old('new_parent_mobile') }}">
                                                    @error('new_parent_mobile')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                <!-- Guardian Relation -->
                                                <div class="col-md-12 mb-2">
                                                    <select class="nice-select niceSelect bordered_style wide @error('new_parent_relation') is-invalid @enderror"
                                                        name="new_parent_relation"
                                                        id="new_parent_relation">
                                                        <option value="">{{ ___('student_info.guardian_relation') }} *</option>
                                                        <option value="Father" {{ old('new_parent_relation') == 'Father' ? 'selected' : '' }}>{{ ___('student_info.father') }}</option>
                                                        <option value="Mother" {{ old('new_parent_relation') == 'Mother' ? 'selected' : '' }}>{{ ___('student_info.mother') }}</option>
                                                        <option value="Guardian" {{ old('new_parent_relation') == 'Guardian' ? 'selected' : '' }}>{{ ___('student_info.guardian') }}</option>
                                                        <option value="Other" {{ old('new_parent_relation') == 'Other' ? 'selected' : '' }}>{{ ___('common.other') }}</option>
                                                    </select>
                                                    @error('new_parent_relation')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div>
                                    <h5 id="discount-alert" class="text-success text-center"></h5>
                                </div>
                                <div class="row mb-3" id="child-info"></div>


                                <div class="col-md-3 mb-3">
                                    <label class="form-label"
                                        for="#">{{ ___('frontend.attend_school_previously') }} </label>
                                    <div class="input-check-radio academic-section mt-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="previous_school"
                                                value="1" id="previous_school">
                                            <label class="form-check-label ps-2 pe-5"
                                                for="previous_school">{{ ___('common.Yes') }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 d-none mb-3" id="previous_school_info">
                                    <label class="form-label"
                                        for="#">{{ ___('frontend.previous_school_information') }} </label>
                                    <textarea class="form-control" rows="2" name="previous_school_info"></textarea>

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
                                        class="email form-control ot-input mb_30" type="text">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">{{ ___('frontend.Residance_Address') }} </label>
                                    <input name="residance_address"
                                        placeholder="{{ ___('frontend.Residance_Address') }}"
                                        class="email form-control ot-input mb_30" type="text">
                                </div>

                                <!-- Bus Selection (Optional Transportation) -->
                                <div class="col-md-3 mb-3">
                                    <label for="busSelect" class="form-label">
                                        {{ ___('transportation.bus_area') }}
                                        <span class="text-muted">({{ ___('common.optional') }})</span>
                                    </label>
                                    <select class="nice-select niceSelect bordered_style wide @error('bus_id') is-invalid @enderror"
                                        name="bus_id" id="busSelect">
                                        <option value="">{{ ___('transportation.no_bus') }}</option>
                                        @foreach($data['buses'] ?? [] as $bus)
                                            <option value="{{ $bus->id }}" {{ old('bus_id') == $bus->id ? 'selected' : '' }}>
                                                {{ $bus->area_name }}
                                                @if($bus->capacity)
                                                    ({{ $bus->students_count }}/{{ $bus->capacity }})
                                                    @if($bus->isAtCapacity())
                                                        - {{ ___('transportation.full') }}
                                                    @endif
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bus_id')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>




                                <div class="col-md-3">

                                    <label for="validationServer04" class="form-label">{{ ___('common.status') }} <span
                                            class="fillable">*</span></label>
                                    <select
                                        class="nice-select niceSelect bordered_style wide @error('status') is-invalid @enderror"
                                        name="status" id="validationServer04_status"
                                        aria-describedby="validationServer04Feedback">
                                        <option {{ old('status') ? 'selected' : '' }}
                                            value="{{ App\Enums\Status::ACTIVE }}">{{ ___('common.active') }}
                                        </option>
                                        <option {{ old('status') ? 'selected' : '' }}
                                            value="{{ App\Enums\Status::INACTIVE }}">{{ ___('common.inactive') }}
                                        </option>
                                    </select>

                                    @error('status')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>




                                <div class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="password"
                                            class="form-label">{{ ___('frontend.Password') }}
                                        </label> <br>
                                        <input type="radio" name="password_type" value="default" id="password_type_default"
                                            checked> <span class="mr-4">{{ ___('frontend.Default Password') }}
                                            (123456)</span>
                                        <input type="radio" name="password_type" value="custom" id="password_type_custom">
                                        <span>{{ ___('frontend.Custom Password') }}</span>
                                    </div>
                                </div>


                                <div id="SelectionDiv" class="col-md-3">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="password"
                                            class="form-label">{{ ___('frontend.Password') }}
                                        </label>
                                        <input type="text" name="password"
                                            placeholder="{{ ___('frontend.Password') }}" autocomplete="off"
                                            class="form-control ot-form-control ot-input" value="{{ old('password') }}"
                                            id="password">
                                        @if ($errors->has('password'))
                                            <div class="error text-danger">{{ $errors->first('password') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Enhanced Fee Processing System - Service Selection Section --}}
                            <div class="row mt-24" id="service-selection-section" style="display: none;">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h4 class="mb-0">{{ ___('fees.optional_services') }}</h4>
                                            <small class="text-muted">
                                                <i class="fa fa-info-circle"></i>
                                                {{ ___('fees.mandatory_services_auto_assigned') }}
                                            </small>
                                            <div class="alert alert-info mt-2 mb-0">
                                                <strong>Note:</strong> Only optional services are shown below for manual selection.
                                                Mandatory services will be automatically assigned based on the student's grade level.
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div id="available-services-container">
                                                <div class="text-center">
                                                    <div class="spinner-border" role="status">
                                                        <span class="sr-only">Loading...</span>
                                                    </div>
                                                    <p class="mt-2">{{ ___('fees.loading_services') }}</p>
                                                </div>
                                            </div>
                                            
                                            <div id="selected-services-summary" class="mt-4 d-none">
                                                <h5>{{ ___('fees.service_summary') }}</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ ___('fees.service_name') }}</th>
                                                                <th>{{ ___('fees.amount') }}</th>
                                                                <th>{{ ___('fees.type') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="service-summary-body">
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="table-info">
                                                                <td><strong>{{ ___('fees.total_amount') }}</strong></td>
                                                                <td><strong id="total-service-amount">0.00</strong></td>
                                                                <td></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Student Services Management Section --}}
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center gap-4 flex-wrap">
                                        <h3 class="m-0 flex-fill">
                                            {{ ___('fees.service_subscriptions') ?? 'Fee Service Subscriptions' }}
                                            <small class="text-muted d-block" style="font-size: 0.85rem;">
                                                (Optional Services Only - Mandatory services are auto-assigned)
                                            </small>
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
                                                <tr id="no-services-row">
                                                    <td colspan="6" class="text-center text-muted">
                                                        {{ ___('fees.no_services_assigned') ?? 'No optional services assigned yet. Click "Add Service" above to add optional services manually. Mandatory services will be automatically assigned based on the student\'s grade level when saved.' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-24">
                                <div class="col-md-12">
                                    <div class="d-flex align-items-center gap-4 flex-wrap">
                                        <h3 class="m-0 flex-fill fs-4">
                                            {{ ___('student_info.upload_documents') }}
                                        </h3>
                                        <button type="button"
                                            class="btn btn-lg ot-btn-primary radius_30px small_add_btn addNewDocument"
                                            onclick="addNewDocument()">
                                            <span><i class="fa-solid fa-plus"></i> </span>
                                            {{ ___('common.add') }}</button>
                                        <input type="hidden" name="counter" id="counter" value="0">
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
                                                    <th scope="col">{{ ___('common.name') }} <span
                                                            class="text-danger"></span>
                                                        @if ($errors->any())
                                                            @if ($errors->has('document_names.*'))
                                                                <span class="text-danger">{{ 'the fields are required' }}
                                                            @endif
                                                        @endif
                                                    </th>
                                                    <th scope="col">
                                                        {{ ___('common.document') }}
                                                        <span class="text-danger"></span>
                                                        @if ($errors->any())
                                                            @if ($errors->has('document_files.*'))
                                                                <span class="text-danger">{{ 'The fields are required' }}
                                                            @endif
                                                        @endif
                                                    </th>
                                                    <th scope="col">
                                                        {{ ___('common.action') }}
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mt-24">
                                    <div class="text-end">
                                        <button
                                            class="btn btn-lg ot-btn-primary"
                                            id="studentSubmitBtn"
                                            @if(isset($data['branch_current_count']) && isset($data['branch_student_limit']) && $data['branch_current_count'] >= $data['branch_student_limit'] && $data['branch_student_limit'] > 0 && $data['branch_student_limit'] < 99999999)
                                                disabled
                                            @endif
                                        >
                                            <span><i class="fa-solid fa-save"></i></span>{{ ___('common.submit') }}
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

@push('script')
    <script>
        // Prevent Firebase messaging errors from breaking the page
        window.addEventListener('error', function(e) {
            if (e.message && (e.message.includes('Firebase') || e.message.includes('messaging'))) {
                console.warn('Firebase messaging error suppressed:', e.message);
                e.preventDefault();
                return true;
            }
        });
        
        window.addEventListener('unhandledrejection', function(e) {
            if (e.reason && e.reason.toString().includes('Firebase')) {
                console.warn('Firebase promise rejection suppressed:', e.reason);
                e.preventDefault();
                return true;
            }
        });
        // =======================================================================
        // GLOBAL FUNCTION DEFINITIONS - MUST BE FIRST FOR ONCLICK ACCESSIBILITY
        // =======================================================================
        
        // Define addServiceRow function first
        function addServiceRow(preSelectedService = null, isMandatory = false) {
            console.log('addServiceRow called with:', preSelectedService, 'mandatory:', isMandatory);
            
            // Validate critical dependencies
            if (!window.feeTypes) {
                console.error('Fee types not available - cannot create service row');
                alert('Service data not loaded. Please refresh the page.');
                return;
            }
            
            const tableBody = document.getElementById('servicesTableBody');
            console.log('Table body found:', tableBody);
            
            if (!tableBody) {
                console.error('Services table body not found!');
                alert('Service table not found on page. Please contact support.');
                return;
            }
            
            const noServicesRow = document.getElementById('no-services-row');
            console.log('No services row found:', noServicesRow);
            
            if (noServicesRow) {
                noServicesRow.remove();
                console.log('Removed no services row');
            }

            // Initialize counter if not exists
            if (typeof window.serviceRowCounter === 'undefined') {
                window.serviceRowCounter = 0;
            }

            const newRow = document.createElement('tr');
            newRow.id = `service-row-${window.serviceRowCounter}`;
            
            // Build fee type options with error handling
            let feeTypeOptions = '<option value="">{{ ___('fees.select_service_type') ?? 'Select Service Type' }}</option>';
            
            try {
                if (window.feeTypes && Array.isArray(window.feeTypes) && window.feeTypes.length > 0) {
                    console.log('Building options from', window.feeTypes.length, 'fee types');
                    window.feeTypes.forEach(feeType => {
                        if (feeType && feeType.id && feeType.name) {
                            const selected = preSelectedService && preSelectedService.id === feeType.id ? 'selected' : '';
                            feeTypeOptions += `<option value="${feeType.id}" 
                                                       data-amount="${feeType.amount || 0}"
                                                       data-category="${feeType.category || 'academic'}"
                                                       ${selected}>
                                                   ${feeType.name}
                                               </option>`;
                        } else {
                            console.warn('Invalid fee type object:', feeType);
                        }
                    });
                } else {
                    console.warn('Fee types array is empty or invalid:', window.feeTypes);
                    feeTypeOptions += '<option value="" disabled>No services available</option>';
                }
            } catch (error) {
                console.error('Error building fee type options:', error);
                feeTypeOptions += '<option value="" disabled>Error loading services</option>';
            }

            const defaultAmount = preSelectedService ? preSelectedService.amount : 0;
            const defaultCategory = preSelectedService ? (preSelectedService.category || 'Academic') : '-';
            const mandatoryBadge = isMandatory ? '<span class="badge badge-warning ml-2">Mandatory</span>' : '';

            newRow.innerHTML = `
                <td>
                    <select name="services[${window.serviceRowCounter}][fee_type_id]" 
                            class="form-control ot-input service-type-select" required>
                        ${feeTypeOptions}
                    </select>
                    ${mandatoryBadge}
                </td>
                <td>
                    <span class="service-category">${defaultCategory}</span>
                </td>
                <td>
                    <input type="number" name="services[${window.serviceRowCounter}][amount]"
                           class="form-control ot-input service-amount"
                           value="${defaultAmount}" step="0.01" min="0" required>
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

            // Add event listener to the new service type select
            const newSelect = newRow.querySelector('.service-type-select');
            if (newSelect) {
                newSelect.addEventListener('change', handleServiceTypeChange);
            }

            window.serviceRowCounter++;
            console.log('Service row added successfully, new counter:', window.serviceRowCounter);
        }

        // Global addNewService function - accessible to onclick
        window.addNewService = function() {
            console.log('Add new service button clicked');
            
            // Validate dependencies before proceeding
            if (!window.feeTypes) {
                console.error('Fee types not loaded - cannot add service');
                alert('Error: Service data not loaded. Please refresh the page and try again.');
                return;
            }
            
            try {
                addServiceRow(null, false);
                console.log('Service row added successfully');
            } catch (error) {
                console.error('Error adding service row:', error);
                alert('Error adding service. Please check the console for details and try again.');
            }
        };

        // Fallback function definition - fixed infinite recursion
        function addNewService() {
            console.log('Add new service button clicked (fallback)');
            try {
                addServiceRow(null, false); // Direct call to avoid infinite recursion
            } catch (error) {
                console.error('Error in fallback addNewService:', error);
                alert('Error adding service. Please refresh the page and try again.');
            }
        }

        // Global removeService function
        window.removeService = function(rowIndex) {
            console.log('Remove service called for row:', rowIndex);
            const row = document.getElementById(`service-row-${rowIndex}`);
            if (row) {
                row.remove();
            }

            // Check if no services left, show empty message
            const tableBody = document.getElementById('servicesTableBody');
            if (tableBody && tableBody.children.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.id = 'no-services-row';
                emptyRow.innerHTML = `
                    <td colspan="7" class="text-center text-muted">
                        {{ ___('fees.no_services_assigned') ?? 'No services assigned yet. Click "Add Service" to add services manually. Mandatory services will be assigned automatically when the student is saved.' }}
                    </td>
                `;
                tableBody.appendChild(emptyRow);
            }
        };

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
                    categorySpan.textContent = category.charAt(0).toUpperCase() + category.slice(1);
                }
            } else {
                // Reset fields
                const amountInput = row.querySelector('.service-amount');
                const categorySpan = row.querySelector('.service-category');
                
                if (amountInput) amountInput.value = '0';
                if (categorySpan) categorySpan.textContent = '-';
            }
        }

        // =======================================================================
        // DEPENDENCY SETUP  
        // =======================================================================
        
        // Initialize global variables first with comprehensive validation
        window.serviceRowCounter = 0;
        
        // Load and validate fee types
        try {
            window.feeTypes = @json($data['fee_types'] ?? []);
            console.log('Fee types raw data:', @json($data['fee_types'] ?? []));
        } catch (error) {
            console.error('Error loading fee types:', error);
            window.feeTypes = [];
        }
        
        // Create and validate class mapping
        window.classMapping = {};
        try {
            @foreach ($data['classes'] as $item)
                window.classMapping[{{ $item->class->id }}] = "{{ $item->class->name }}";
            @endforeach
        } catch (error) {
            console.error('Error creating class mapping:', error);
        }
        
        // Comprehensive dependency validation and logging
        console.log('=== DEPENDENCY LOADING REPORT ===');
        console.log('✓ Service counter initialized:', window.serviceRowCounter);
        console.log('✓ Fee types loaded:', Array.isArray(window.feeTypes) ? window.feeTypes.length : 'INVALID');
        console.log('✓ Fee types data:', window.feeTypes);
        console.log('✓ Class mapping loaded:', Object.keys(window.classMapping).length, 'classes');
        console.log('✓ Class mapping data:', window.classMapping);
        
        // Validate critical dependencies
        if (!Array.isArray(window.feeTypes)) {
            console.error('❌ CRITICAL: Fee types is not an array!');
        } else if (window.feeTypes.length === 0) {
            console.warn('⚠️  WARNING: No fee types available - service management will be limited');
        }
        
        if (Object.keys(window.classMapping).length === 0) {
            console.warn('⚠️  WARNING: No class mapping available - automatic services may not work');
        }
        
        console.log('=== END DEPENDENCY REPORT ===');

        // =======================================================================
        // EVENT HANDLERS & CLASS/SERVICE INTEGRATION
        // =======================================================================

        // Note: Automatic service population on class selection has been removed
        // Manual service addition via "Add Service" button is still available
        // Mandatory services are automatically assigned in the background when student is saved

        // Note: detectAndAddMandatoryServices, getAcademicLevelFromClassName, and clearAllServices functions removed
        // Manual service management is still available via "Add Service" button
        // Automatic mandatory service assignment happens in the background during student creation

        $(document).ready(function() {
            // Test if global functions are available
            console.log('Document ready - testing global functions:');
            console.log('addNewService available:', typeof window.addNewService);
            console.log('removeService available:', typeof window.removeService);
            
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

            // Initially hide the role selection div
            $('#SelectionDiv').hide();

            // Attach an event listener to the radio buttons
            $('input[name="password_type"]').on('change', function() {
                if ($(this).val() === 'custom') {

                    // If the 'custom' radio button is selected, show the role selection div
                    $('#SelectionDiv').show();
                } else {
                    // If the 'default' radio button is selected or other value, hide the  selection div
                    $('#SelectionDiv').hide();
                }
            });


        });

        // Enhanced Fee Processing System - Service Selection Functionality
        let availableServices = [];
        let selectedServices = [];

        // Load available services when class is selected (disabled - replaced by new service management)
        // $('select[name="class"]').on('change', function() {
        //     const classId = $(this).val();
        //     if (classId) {
        //         loadAvailableServices(classId);
        //     } else {
        //         $('#service-selection-section').hide();
        //     }
        // });

        function loadAvailableServices(classId) {
            // Create a temporary student object to determine academic level
            const tempStudentData = {
                class_id: classId,
                date_of_birth: $('input[name="date_of_birth"]').val()
            };

            $.ajax({
                url: '{{ route("student-services.registration-services") }}',
                type: 'GET',
                data: tempStudentData,
                success: function(response) {
                    if (response.success) {
                        availableServices = response.data.services;
                        displayAvailableServices();
                        $('#service-selection-section').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load services:', error);
                    // Hide service selection if failed to load
                    $('#service-selection-section').hide();
                }
            });
        }

        function displayAvailableServices() {
            const container = $('#available-services-container');
            let html = '';

            if (Object.keys(availableServices).length === 0) {
                html = '<div class="alert alert-info">{{ ___("fees.no_optional_services") }}</div>';
            } else {
                html = '<div class="row">';
                
                // Display mandatory services info first
                let mandatoryInfo = '<div class="col-12 mb-3"><div class="alert alert-info">';
                mandatoryInfo += '<i class="fa fa-info-circle"></i> {{ ___("fees.mandatory_services_will_be_auto_assigned") }}';
                mandatoryInfo += '</div></div>';
                
                // Group services by category
                for (const [category, categoryData] of Object.entries(availableServices)) {
                    if (categoryData.optional && categoryData.optional.length > 0) {
                        html += `
                            <div class="col-md-12 mb-4">
                                <h5 class="text-primary">${category.charAt(0).toUpperCase() + category.slice(1)} {{ ___("fees.optional_services") }}</h5>
                                <div class="row">
                        `;
                        
                        categoryData.optional.forEach(service => {
                            html += `
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <div class="form-check">
                                                <input class="form-check-input service-checkbox" 
                                                       type="checkbox" 
                                                       value="${service.id}" 
                                                       id="service_${service.id}"
                                                       data-service='${JSON.stringify(service)}'>
                                                <label class="form-check-label" for="service_${service.id}">
                                                    <strong>${service.name}</strong>
                                                </label>
                                            </div>
                                            <p class="card-text mt-2">
                                                <small class="text-muted">${service.description || ''}</small>
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="badge badge-success">{{ setting('currency_symbol') }}${service.amount}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        html += `
                                </div>
                            </div>
                        `;
                    }
                    
                    // Show mandatory services info
                    if (categoryData.mandatory && categoryData.mandatory.length > 0) {
                        mandatoryInfo += `<div class="text-muted mb-2"><strong>${category}:</strong> `;
                        mandatoryInfo += categoryData.mandatory.map(s => s.name).join(', ');
                        mandatoryInfo += '</div>';
                    }
                }
                
                html = mandatoryInfo + html + '</div>';
            }

            container.html(html);

            // Attach change event to service checkboxes
            $('.service-checkbox').on('change', function() {
                updateSelectedServices();
            });
        }

        function updateSelectedServices() {
            selectedServices = [];
            let totalAmount = 0;

            $('.service-checkbox:checked').each(function() {
                const service = JSON.parse($(this).data('service'));
                selectedServices.push(service);
                totalAmount += parseFloat(service.amount);
            });

            updateServiceSummary(totalAmount);
        }

        function updateServiceSummary(totalAmount) {
            const summaryContainer = $('#selected-services-summary');
            const summaryBody = $('#service-summary-body');

            if (selectedServices.length === 0) {
                summaryContainer.addClass('d-none');
                return;
            }

            let summaryHtml = '';
            selectedServices.forEach(service => {
                summaryHtml += `
                    <tr>
                        <td>${service.name}</td>
                        <td>{{ setting('currency_symbol') }}${service.amount}</td>
                        <td><span class="badge badge-info">Optional</span></td>
                    </tr>
                `;
            });

            summaryBody.html(summaryHtml);
            $('#total-service-amount').text('{{ setting('currency_symbol') }}' + totalAmount.toFixed(2));
            summaryContainer.removeClass('d-none');
        }

        // Add selected services to form submission
        $('form#visitForm').on('submit', function(e) {
            if (selectedServices.length > 0) {
                // Add selected service IDs as hidden inputs
                selectedServices.forEach(service => {
                    $(this).append(`<input type="hidden" name="selected_services[]" value="${service.id}">`);
                });
            }
        });

        // =======================================================================
        // CLEANUP & REMOVE DUPLICATE CODE
        // =======================================================================
        // (Previous duplicate code removed - now consolidated above)

        // Note: Section loading is now handled by custom.js
        // We removed our custom section loading to avoid conflicts

        // =======================================================================
        // DOCUMENT READY & INITIALIZATION
        // =======================================================================
        
        $(document).ready(function() {
            console.log('=== DOCUMENT READY - SYSTEM CHECK ===');
            
            // Test global function availability
            console.log('📋 Function Availability:');
            console.log('  • window.addNewService:', typeof window.addNewService);
            console.log('  • removeService:', typeof window.removeService);
            console.log('  • addServiceRow:', typeof addServiceRow);
            console.log('  • handleServiceTypeChange:', typeof handleServiceTypeChange);
            
            // Test DOM elements
            console.log('🎯 DOM Elements:');
            console.log('  • Services table body:', !!document.getElementById('servicesTableBody'));
            console.log('  • Add service button:', !!document.querySelector('[onclick="addNewService()"]'));
            console.log('  • Class select (#getSections):', !!document.getElementById('getSections'));
            console.log('  • Section select (.sections):', !!document.querySelector('.sections'));
            
            // Test data availability
            console.log('💾 Data Availability:');
            console.log('  • Fee types array:', Array.isArray(window.feeTypes) ? '✓' : '❌');
            console.log('  • Fee types count:', window.feeTypes ? window.feeTypes.length : 'N/A');
            console.log('  • Class mapping:', Object.keys(window.classMapping || {}).length + ' classes');
            console.log('  • Service counter:', window.serviceRowCounter);
            
            // Final readiness check
            const isReady = (
                typeof window.addNewService === 'function' &&
                typeof window.removeService === 'function' &&
                Array.isArray(window.feeTypes) &&
                document.getElementById('servicesTableBody')
            );
            
            console.log('🚀 System Status:', isReady ? '✅ READY' : '❌ NOT READY');
            
            if (!isReady) {
                console.error('❌ SYSTEM NOT READY - Some components are missing!');
            }
            
            // Add event listeners to existing service selects on page load
            const existingSelects = document.querySelectorAll('.service-type-select');
            console.log('🔗 Adding event listeners to', existingSelects.length, 'existing service selects');
            existingSelects.forEach(select => {
                select.addEventListener('change', handleServiceTypeChange);
            });
            
            console.log('=== END SYSTEM CHECK ===');
            
            // =======================================================================
            // ADDITIONAL DOCUMENT READY FUNCTIONALITY
            // =======================================================================
            $('.parent').on('change', function () {
                var parentId = $(this).val();
                if (parentId) {
                    $.ajax({
                        url: '/student/get-children/' + parentId,
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            console.log(response)
                            if (response.status === 'success') {
                                let html = '';
                                if (response.data.siblingsCount > 0) {
                                    html += `
                                        <div class="card mb-4">
                                            <div class="card-header mt-3">
                                                <h5 class="mb-0 text-center">Siblings Information</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                    `;

                                    $.each(response.data.children, function (i, child) {
                                        html += `
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100 shadow-sm">
                                                <div class="card-body p-3">
                                                    <h5 class="card-title">${child.full_name}</h5>
                                                    <p class="mb-1"><strong>Admission No:</strong> ${child.admission_no}</p>
                                                    <p class="mb-1"><strong>Roll No:</strong> ${child.roll_no}</p>
                                                    <p class="mb-0"> <strong>Class: </strong> ${child.session_class_student.class.name}</p>
                                                    <p class="mb-1"><strong>DOB:</strong> ${child.dob}</p>
                                                    <p class="mb-1"><strong>Email:</strong> ${child.email}</p>
                                                    <p class="mb-1"><strong>Mobile:</strong> ${child.mobile}</p>
                                                    <p class="mb-0"><strong>Admission Date:</strong> ${child.admission_date}</p>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    });

                                    html += `
                </div>
            </div>
        </div>
    `;

                                    if (response.data.isEligible){
                                        $('#discount-alert').text('A ' + response.data.siblingDiscount + '% sibling discount will be applied to all assigned fees for this student.');
                                        $('#siblings_discount').val(response.data.isEligible ? 1 : 0);
                                        toastr.success('Student is eligible for sibling discount');
                                    }
                                }

                                $('#child-info').html(html);

                            }
                        }
                    });
                } else {
                    $('#child-info').html('');
                }
            });
        });

        // =====================================================================
        // PARENT CREATION MODE TOGGLE - Inline Parent/Guardian Creation
        // =====================================================================

        $(document).ready(function() {
            // Initialize: Set default active tab
            $('#tab_existing_parent').removeClass('btn-outline-secondary').addClass('btn-primary');

            // Tab: Switch to "Create New Parent" mode
            $('#tab_create_parent').on('click', function() {
                // Update tab styling
                $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
                $('#tab_existing_parent').removeClass('btn-primary').addClass('btn-outline-secondary');

                // Smooth transition between sections
                $('#existing_parent_section').slideUp(200, function() {
                    $('#new_parent_section').slideDown(200);
                });

                // Update hidden mode field
                $('#parent_creation_mode').val('new');

                // Clear parent dropdown selection
                $('#validationServer04_parent').val('').trigger('change');

                // Clear child info when switching modes
                $('#child-info').html('');
                $('#discount-alert').text('');
            });

            // Tab: Switch back to "Select Existing Parent" mode
            $('#tab_existing_parent').on('click', function() {
                // Update tab styling
                $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
                $('#tab_create_parent').removeClass('btn-primary').addClass('btn-outline-secondary');

                // Smooth transition between sections
                $('#new_parent_section').slideUp(200, function() {
                    $('#existing_parent_section').slideDown(200);
                });

                // Update hidden mode field
                $('#parent_creation_mode').val('existing');

                // Clear new parent fields
                $('#new_parent_name').val('');
                $('#new_parent_mobile').val('');
                $('#new_parent_relation').val('').trigger('change');

                // Reinitialize nice-select for relation dropdown
                if (typeof $.fn.niceSelect !== 'undefined') {
                    $('#new_parent_relation').niceSelect('update');
                }
            });

            // Handle form validation errors - if new parent errors exist, show that section
            @if($errors->has('new_parent_name') || $errors->has('new_parent_mobile') || $errors->has('new_parent_relation'))
                // Validation errors exist for new parent fields, show the creation form
                $('#tab_create_parent').trigger('click');
            @endif
        });

    </script>
@endpush
