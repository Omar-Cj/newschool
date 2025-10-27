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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-body">
                <form action="{{ route('student.importSubmit') }}" enctype="multipart/form-data" method="post"
                    id="visitForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <div class="row">

                                <!-- Notice Section -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <code class="text-primary fs-6 fw-bold">
                                        Fadlan Lasoo Dag Sample Fileka Kadib Kusoo Buuxi Xogta ardayda Adigoo Raacaya Tilmaamahan:-
                                    </code>
                                    <div>
                                        <a href="{{ route('student.sampleDownload') }}"
                                            class="btn btn-sm btn-primary">
                                            <i class="fa-solid fa-download"></i> {{ ___('student_info.Sample File') }}
                                        </a>
                                    </div>
                                </div>

                                <!-- Instructions List (V2 Template) -->
                                <div class="alert alert-info mb-3">
                                    <h6 class="alert-heading"><i class="fa fa-info-circle"></i> Tilmaamaha Loo Baahanyahay inaad Raacdid</h6>
                                    <ul class="mb-0">
                                        <li><strong>Grade, Class, Section</strong> Ka dooro Saddexdan Formka Xaga Hoose Yaal</li>
                                        <li><strong>Columnska Loo Baahanyahay</strong>: first_name, last_name,shift,gender,category,parent_mobile,parent_name,parent_relation</li>
                                    </ul>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <code class="d-block mb-2"><strong>Gender</strong>: 1 = Male, 2 = Female</code>
                                        <code class="d-block mb-2"><strong>Parent Relation</strong>: Father, Mother, Guardian, Other</code>
                                        <code class="d-block mb-2">
                                            <strong>Student Category</strong>:
                                            @foreach ($data['categories'] as $key => $item)
                                                {{ $item->id }} = {{ $item->name }}{{ $loop->last ? '' : ', ' }}
                                            @endforeach
                                        </code>
                                    </div>
                                    <div class="col-md-6">
                                        <code class="d-block mb-2"><strong>Parent Mobile</strong>: Used to lookup/create parent (prevents duplicates)</code>
                                        <code class="d-block mb-2"><strong>Fee Services</strong>: Comma-separated optional service IDs (e.g., 3,5,7)</code>
                                        <code class="d-block mb-2"><strong>Note</strong>: Mandatory services auto-assigned based on grade</code>
                                    </div>
                                </div>


                            </div>

                            <div class="row mt-10">
                                <!-- Grade Field - First Priority -->
                                <div class="col-md-4 mb-3">
                                    <label for="gradeSelect" class="form-label">{{ ___('student_info.grade') }}
                                        <span class="fillable">*</span></label>
                                    <select class="nice-select niceSelect bordered_style wide @error('grade') is-invalid @enderror"
                                        name="grade" id="gradeSelect">
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
                                <div class="col-md-4">
                                    <label for="validationServer04" class="form-label">{{ ___('student_info.class') }}
                                        <span class="fillable">*</span></label>
                                    <select id="getSections"
                                        class="nice-select niceSelect bordered_style wide @error('class') is-invalid @enderror"
                                        name="class" id="validationServer04"
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

                                <div class="col-md-4">
                                    <label for="validationServer04" class="form-label">{{ ___('student_info.section') }}
                                        <span class="fillable">*</span></label>
                                    <select id="getSections"
                                        class="nice-select sections niceSelect bordered_style wide @error('section') is-invalid @enderror"
                                        name="section" id="validationServer04"
                                        aria-describedby="validationServer04Feedback">
                                        <option value="">{{ ___('student_info.select_section') }}</option>
                                    </select>
                                    @error('section')
                                        <div id="validationServer04Feedback" class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>


                                <div class="col-md-4">
                                    <label for="exampleDataList" class="form-label ">{{ ___('common.File') }}
                                        {{ ___('common.(100 x 100 px)') }}<span class="fillable"> *</span></label>
                                    <div class="ot_fileUploader left-side mb-0">
                                        <input class="form-control" type="text" placeholder="{{ ___('common.File') }}"
                                            readonly="" id="placeholder">
                                        <button class="primary-btn-small-input" type="button">
                                            <label class="btn btn-lg ot-btn-primary"
                                                for="fileBrouse">{{ ___('common.browse') }}</label>
                                            <input type="file" class="d-none form-control" name="file"
                                                id="fileBrouse">
                                        </button>
                                    </div>
                                    @error('file')
                                        <span class="text-danger">
                                            {{ $message }}
                                        </span>
                                    @enderror
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
        });
    </script>
@endpush
