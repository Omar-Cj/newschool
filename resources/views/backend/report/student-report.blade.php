@extends('backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection

<style>
    body {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact !important;
    }

    /* Report Wrapper Styles */
    .routine_wrapper {
        max-width: 900px;
        margin: auto;
        background: #fff;
        padding: 0px;
        border-radius: 8px;
        background: #ECECEC;
    }

    .routine_wrapper_body {
        padding: 36px;
    }

    /* Header Styles */
    .routine_wrapper_header {
        background: #392C7D;
        padding: 32px 36px;
        border-radius: 8px 8px 0 0;
        margin-bottom: 0;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        grid-gap: 20px;
    }

    .routine_wrapper_header h3 {
        font-weight: 500;
        font-size: 36px;
        line-height: 40px;
        color: #FFFFFF;
        margin: 0;
    }

    .routine_wrapper_header p {
        font-size: 16px;
        color: #FFFFFF;
        margin: 0;
    }

    .routine_wrapper_header_logo .header_logo {
        height: 60px;
    }

    .vertical_seperator {
        border-right: 1px solid #FFFFFF;
        height: 93px;
        margin: 0 30px 0 40px;
    }

    /* Report Title */
    .markseet_title h5 {
        color: #242424;
        font-weight: 600;
        font-size: 24px;
        line-height: 36px;
        margin: 30px 0 30px 0;
        padding: 26px 0 12px 0;
        text-align: center;
    }

    /* Table Styles */
    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
    }

    .table td,
    .table th {
        padding: 12px;
        vertical-align: top;
        border-top: 0 solid transparent;
        color: #000;
    }

    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered thead th {
        background-color: #EAEAEA;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .routine_wrapper_header {
            padding: 20px;
        }

        .routine_wrapper_header h3 {
            font-size: 24px;
        }

        .vertical_seperator {
            display: none !important;
        }

        .routine_wrapper {
            width: 100%;
        }
    }

    /* Print Styles */
    @media print {
        .routine_wrapper_header h3 {
            font-size: 24px;
        }

        -webkit-print-color-adjust: exact !important;

        .btn, .card-header {
            display: none !important;
        }

        .routine_wrapper {
            width: 100%;
            max-width: 100%;
        }
    }
</style>

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            {{-- bradecrumb Area S t a r t --}}
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="bradecrumb-title mb-1">{{ ___('settings.student_report') }}</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ ___('common.home') }}</a></li>
                            <li class="breadcrumb-item">{{ ___('settings.student_report') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
            {{-- bradecrumb Area E n d --}}

            <!-- accordion for reports -->
            <div class="row">
                <div class="col-12">
                    <div class="accordion custom-accordion" id="studentReportsAccordion">
                        @php
                            // Determine which accordion should be active based on report type
                            $activeReport = $data['report_type'] ?? 'list';
                        @endphp

                        <!-- Student List Collapsible -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingStudentList">
                                <button class="accordion-button {{ $activeReport !== 'list' ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStudentList" aria-expanded="{{ $activeReport === 'list' ? 'true' : 'false' }}" aria-controls="collapseStudentList">
                                    <i class="las la-users me-2"></i> {{ ___('settings.student_list') }}
                                </button>
                            </h2>
                            <div id="collapseStudentList" class="accordion-collapse collapse {{ $activeReport === 'list' ? 'show' : '' }}" aria-labelledby="headingStudentList" data-bs-parent="#studentReportsAccordion">
                                <div class="accordion-body">

                                    <!-- Filter Card -->
                                    <div class="card">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">{{ ___('settings.filter_options') }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('report-student.search-student-list') }}" method="GET" id="studentListForm">
                                                <div class="row g-3">

                                                    <!-- Session -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="session" id="session">
                                                                <option value="">{{ ___('common.select_session') }}</option>
                                                                @foreach ($data['sessions'] as $session)
                                                                    <option value="{{ $session->id }}" {{ isset($data['selectedFilters']['session']) && $data['selectedFilters']['session'] == $session->id ? 'selected' : '' }}>
                                                                        {{ $session->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Grade -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="grade" id="grade">
                                                                <option value="">{{ ___('common.select_grade') }}</option>
                                                                @foreach ($data['grades'] as $grade)
                                                                    <option value="{{ $grade->grade }}" {{ isset($data['selectedFilters']['grade']) && $data['selectedFilters']['grade'] == $grade->grade ? 'selected' : '' }}>
                                                                        {{ $grade->grade }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Class -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="class" id="class">
                                                                <option value="">{{ ___('common.select_class') }}</option>
                                                                @foreach ($data['classes'] as $class)
                                                                    <option value="{{ $class->id }}" {{ isset($data['selectedFilters']['class']) && $data['selectedFilters']['class'] == $class->id ? 'selected' : '' }}>
                                                                        {{ $class->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Section -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="section" id="section">
                                                                <option value="">{{ ___('common.select_section') }}</option>
                                                                @foreach ($data['sections'] as $section)
                                                                    <option value="{{ $section->id }}" {{ isset($data['selectedFilters']['section']) && $data['selectedFilters']['section'] == $section->id ? 'selected' : '' }}>
                                                                        {{ $section->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Shift -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="shift" id="shift">
                                                                <option value="">{{ ___('common.select_shift') }}</option>
                                                                @foreach ($data['shifts'] as $shift)
                                                                    <option value="{{ $shift->id }}" {{ isset($data['selectedFilters']['shift']) && $data['selectedFilters']['shift'] == $shift->id ? 'selected' : '' }}>
                                                                        {{ $shift->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Category -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="category" id="category">
                                                                <option value="">{{ ___('common.select_category') }}</option>
                                                                @foreach ($data['categories'] as $category)
                                                                    <option value="{{ $category->id }}" {{ isset($data['selectedFilters']['category']) && $data['selectedFilters']['category'] == $category->id ? 'selected' : '' }}>
                                                                        {{ $category->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Status -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="status" id="status">
                                                                <option value="">{{ ___('common.select_status') }}</option>
                                                                <option value="1" {{ isset($data['selectedFilters']['status']) && $data['selectedFilters']['status'] == 1 ? 'selected' : '' }}>{{ ___('common.active') }}</option>
                                                                <option value="0" {{ isset($data['selectedFilters']['status']) && $data['selectedFilters']['status'] == 0 ? 'selected' : '' }}>{{ ___('common.inactive') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Gender -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="gender" id="gender">
                                                                <option value="">{{ ___('common.select_gender') }}</option>
                                                                @foreach ($data['genders'] as $gender)
                                                                    <option value="{{ $gender->id }}" {{ isset($data['selectedFilters']['gender']) && $data['selectedFilters']['gender'] == $gender->id ? 'selected' : '' }}>
                                                                        {{ $gender->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Search Button -->
                                                    <div class="col-md-12">
                                                        <button type="submit" class="btn btn-lg ot-btn-primary">
                                                            <i class="las la-search me-1"></i> {{ ___('common.search') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Results Section -->
                                    @if(isset($data['reportData']))
                                    <div class="card mt-3" id="reportSection">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">{{ ___('settings.student_list_report') }}</h4>
                                            <div class="flex-shrink-0">
                                                <button class="btn btn-lg ot-btn-primary" onclick="printDiv('printableArea')">
                                                    {{ ___('common.print_now') }}
                                                    <span><i class="fa-solid fa-print"></i></span>
                                                </button>
                                                <a class="btn btn-lg ot-btn-primary" href="{{ route('report-student.pdf-student-list', request()->all()) }}">
                                                    {{ ___('common.pdf_download') }}
                                                    <span><i class="fa-brands fa-dochub"></i></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-body" id="printableArea">

                                            <div class="routine_wrapper">
                                                <!-- routine_wrapper_header part here -->
                                                <div class="routine_wrapper_header">
                                                    <div class="routine_wrapper_header_logo">
                                                        <img class="header_logo" src="{{ globalAsset(setting('light_logo'), '154X38.webp') }}" alt="{{ __('light logo') }}">
                                                    </div>
                                                    <div class="vertical_seperator"></div>
                                                    <div class="routine_wrapper_header_content">
                                                        <h3>{{ setting('application_name') }}</h3>
                                                        <p>{{ setting('address') }}</p>
                                                    </div>
                                                </div>

                                                <div class="routine_wrapper_body">
                                                    <!-- Report Title -->
                                                    <div class="markseet_title">
                                                        <h5>{{ ___('settings.student_list_report') }}</h5>
                                                    </div>

                                                    <!-- Report Table -->
                                                    <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ ___('common.full_name') }}</th>
                                                            <th>{{ ___('common.mobile') }}</th>
                                                            <th>{{ ___('common.grade') }}</th>
                                                            <th>{{ ___('common.class') }}</th>
                                                            <th>{{ ___('common.section') }}</th>
                                                            <th>{{ ___('common.guardian_name') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($data['reportData']['students'] as $index => $student)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $student->full_name }}</td>
                                                            <td>{{ $student->mobile }}</td>
                                                            <td>{{ $student->grade }}</td>
                                                            <td>{{ $student->class }}</td>
                                                            <td>{{ $student->section }}</td>
                                                            <td>{{ $student->guardian_name }}</td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="7" class="text-center">{{ ___('common.no_data_found') }}</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                    <tfoot class="table-light">
                                                        <tr>
                                                            <th colspan="6" class="text-end">{{ ___('common.total_students') }}:</th>
                                                            <th>{{ $data['reportData']['total_count'] }}</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                                </div>
                                                <!-- routine_wrapper_body end -->
                                            </div>
                                            <!-- routine_wrapper end -->
                                        </div>
                                    </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                        <!-- Student Registration Collapsible -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingStudentRegistration">
                                <button class="accordion-button {{ $activeReport !== 'registration' ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStudentRegistration" aria-expanded="{{ $activeReport === 'registration' ? 'true' : 'false' }}" aria-controls="collapseStudentRegistration">
                                    <i class="las la-user-plus me-2"></i> {{ ___('settings.student_registration') }}
                                </button>
                            </h2>
                            <div id="collapseStudentRegistration" class="accordion-collapse collapse {{ $activeReport === 'registration' ? 'show' : '' }}" aria-labelledby="headingStudentRegistration" data-bs-parent="#studentReportsAccordion">
                                <div class="accordion-body">

                                    <!-- Filter Card -->
                                    <div class="card">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">{{ ___('settings.filter_options') }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('report-student.search-student-registration') }}" method="GET" id="studentRegistrationForm">
                                                <div class="row g-3">

                                                    <!-- Start Date -->
                                                    <div class="col-md-6">
                                                        <label for="start_date" class="form-label">{{ ___('common.start_date') }} <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" name="start_date" id="start_date" required value="{{ $data['registrationSelectedFilters']['start_date'] ?? '' }}">
                                                    </div>

                                                    <!-- End Date -->
                                                    <div class="col-md-6">
                                                        <label for="end_date" class="form-label">{{ ___('common.end_date') }} <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" name="end_date" id="end_date" required value="{{ $data['registrationSelectedFilters']['end_date'] ?? '' }}">
                                                    </div>

                                                    <!-- Grade -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="grade" id="reg_grade">
                                                                <option value="">{{ ___('common.select_grade') }}</option>
                                                                @foreach ($data['grades'] as $grade)
                                                                    <option value="{{ $grade->grade }}" {{ isset($data['registrationSelectedFilters']['grade']) && $data['registrationSelectedFilters']['grade'] == $grade->grade ? 'selected' : '' }}>
                                                                        {{ $grade->grade }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Class -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="class" id="reg_class">
                                                                <option value="">{{ ___('common.select_class') }}</option>
                                                                @foreach ($data['classes'] as $class)
                                                                    <option value="{{ $class->id }}" {{ isset($data['registrationSelectedFilters']['class']) && $data['registrationSelectedFilters']['class'] == $class->id ? 'selected' : '' }}>
                                                                        {{ $class->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Section -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="section" id="reg_section">
                                                                <option value="">{{ ___('common.select_section') }}</option>
                                                                @foreach ($data['sections'] as $section)
                                                                    <option value="{{ $section->id }}" {{ isset($data['registrationSelectedFilters']['section']) && $data['registrationSelectedFilters']['section'] == $section->id ? 'selected' : '' }}>
                                                                        {{ $section->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Shift -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="shift" id="reg_shift">
                                                                <option value="">{{ ___('common.select_shift') }}</option>
                                                                @foreach ($data['shifts'] as $shift)
                                                                    <option value="{{ $shift->id }}" {{ isset($data['registrationSelectedFilters']['shift']) && $data['registrationSelectedFilters']['shift'] == $shift->id ? 'selected' : '' }}>
                                                                        {{ $shift->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Status -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="status" id="reg_status">
                                                                <option value="">{{ ___('common.select_status') }}</option>
                                                                <option value="1" {{ isset($data['registrationSelectedFilters']['status']) && $data['registrationSelectedFilters']['status'] == 1 ? 'selected' : '' }}>{{ ___('common.active') }}</option>
                                                                <option value="0" {{ isset($data['registrationSelectedFilters']['status']) && $data['registrationSelectedFilters']['status'] == 0 ? 'selected' : '' }}>{{ ___('common.inactive') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Gender -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="gender" id="reg_gender">
                                                                <option value="">{{ ___('common.select_gender') }}</option>
                                                                @foreach ($data['genders'] as $gender)
                                                                    <option value="{{ $gender->id }}" {{ isset($data['registrationSelectedFilters']['gender']) && $data['registrationSelectedFilters']['gender'] == $gender->id ? 'selected' : '' }}>
                                                                        {{ $gender->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Search Button -->
                                                    <div class="col-md-12">
                                                        <button type="submit" class="btn btn-lg ot-btn-primary">
                                                            <i class="las la-search me-1"></i> {{ ___('common.search') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Results Section -->
                                    @if(isset($data['registrationReportData']))
                                    <div class="card mt-3" id="registrationReportSection">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">{{ ___('settings.student_registration_report') }}</h4>
                                            <div class="flex-shrink-0">
                                                <button class="btn btn-lg ot-btn-primary" onclick="printDiv('printableAreaRegistration')">
                                                    {{ ___('common.print_now') }}
                                                    <span><i class="fa-solid fa-print"></i></span>
                                                </button>
                                                <a class="btn btn-lg ot-btn-primary" href="{{ route('report-student.pdf-student-registration', request()->all()) }}">
                                                    {{ ___('common.pdf_download') }}
                                                    <span><i class="fa-brands fa-dochub"></i></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-body" id="printableAreaRegistration">

                                            <div class="routine_wrapper">
                                                <!-- routine_wrapper_header part here -->
                                                <div class="routine_wrapper_header">
                                                    <div class="routine_wrapper_header_logo">
                                                        <img class="header_logo" src="{{ globalAsset(setting('light_logo'), '154X38.webp') }}" alt="{{ __('light logo') }}">
                                                    </div>
                                                    <div class="vertical_seperator"></div>
                                                    <div class="routine_wrapper_header_content">
                                                        <h3>{{ setting('application_name') }}</h3>
                                                        <p>{{ setting('address') }}</p>
                                                    </div>
                                                </div>

                                                <div class="routine_wrapper_body">
                                                    <!-- Report Title -->
                                                    <div class="markseet_title">
                                                        <h5>{{ ___('settings.student_registration_report') }}</h5>
                                                    </div>

                                                    <!-- Report Table -->
                                                    <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ ___('common.admission_date') }}</th>
                                                            <th>{{ ___('common.full_name') }}</th>
                                                            <th>{{ ___('common.mobile') }}</th>
                                                            <th>{{ ___('common.grade') }}</th>
                                                            <th>{{ ___('common.class') }}</th>
                                                            <th>{{ ___('common.section') }}</th>
                                                            <th>{{ ___('common.shift') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($data['registrationReportData']['students'] as $index => $student)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $student->admission_date }}</td>
                                                            <td>{{ $student->full_name }}</td>
                                                            <td>{{ $student->mobile }}</td>
                                                            <td>{{ $student->grade }}</td>
                                                            <td>{{ $student->class_name }}</td>
                                                            <td>{{ $student->section_name }}</td>
                                                            <td>{{ $student->shift_name }}</td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="8" class="text-center">{{ ___('common.no_data_found') }}</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                    <tfoot class="table-light">
                                                        <tr>
                                                            <th colspan="7" class="text-end">{{ ___('common.total_students') }}:</th>
                                                            <th>{{ $data['registrationReportData']['total_count'] }}</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                                </div>
                                                <!-- routine_wrapper_body end -->
                                            </div>
                                            <!-- routine_wrapper end -->
                                        </div>
                                    </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                        <!-- Guardian List Collapsible -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingGuardianList">
                                <button class="accordion-button {{ $activeReport !== 'guardian' ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGuardianList" aria-expanded="{{ $activeReport === 'guardian' ? 'true' : 'false' }}" aria-controls="collapseGuardianList">
                                    <i class="las la-user-friends me-2"></i> {{ ___('settings.guardian_list') }}
                                </button>
                            </h2>
                            <div id="collapseGuardianList" class="accordion-collapse collapse {{ $activeReport === 'guardian' ? 'show' : '' }}" aria-labelledby="headingGuardianList" data-bs-parent="#studentReportsAccordion">
                                <div class="accordion-body">
                                    <!-- Filter Form -->
                                    <div class="card">
                                        <div class="card-body">
                                            <form action="{{ route('report-student.search-guardian-list') }}" method="GET">
                                                <div class="row g-3">
                                                    <!-- Search Button -->
                                                    <div class="col-md-12">
                                                        <button type="submit" class="btn btn-lg ot-btn-primary">
                                                            <i class="las la-list me-1"></i> {{ ___('common.generate_report') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Results Section -->
                                    @if(isset($data['guardianReportData']))
                                    <div class="card mt-3" id="guardianReportSection">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">{{ ___('settings.guardian_list_report') }}</h4>
                                            <div class="flex-shrink-0">
                                                <button class="btn btn-lg ot-btn-primary" onclick="printDiv('printableAreaGuardian')">
                                                    {{ ___('common.print_now') }}
                                                    <span><i class="fa-solid fa-print"></i></span>
                                                </button>
                                                <a class="btn btn-lg ot-btn-primary" href="{{ route('report-student.pdf-guardian-list') }}">
                                                    {{ ___('common.pdf_download') }}
                                                    <span><i class="fa-brands fa-dochub"></i></span>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="card-body" id="printableAreaGuardian">

                                            <div class="routine_wrapper">
                                                <!-- routine_wrapper_header part here -->
                                                <div class="routine_wrapper_header">
                                                    <div class="routine_wrapper_header_logo">
                                                        <img class="header_logo" src="{{ globalAsset(setting('light_logo'), '154X38.webp') }}" alt="{{ __('light logo') }}">
                                                    </div>
                                                    <div class="vertical_seperator"></div>
                                                    <div class="routine_wrapper_header_content">
                                                        <h3>{{ setting('application_name') }}</h3>
                                                        <p>{{ setting('address') }}</p>
                                                    </div>
                                                </div>

                                                <div class="routine_wrapper_body">
                                                    <!-- Report Title -->
                                                    <div class="markseet_title">
                                                        <h5>{{ ___('settings.guardian_list_report') }}</h5>
                                                    </div>

                                                    <!-- Report Table -->
                                                    <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ ___('common.guardian_name') }}</th>
                                                            <th>{{ ___('common.mobile') }}</th>
                                                            <th>{{ ___('common.address') }}</th>
                                                            <th>{{ ___('common.total_students') }}</th>
                                                            <th>{{ ___('common.relation_type') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($data['guardianReportData']['guardians'] as $index => $guardian)
                                                        <tr>
                                                            <td>{{ $index + 1 }}</td>
                                                            <td>{{ $guardian->guardian_name }}</td>
                                                            <td>{{ $guardian->guardian_mobile }}</td>
                                                            <td>{{ $guardian->guardian_address }}</td>
                                                            <td>{{ $guardian->total_students }}</td>
                                                            <td>{{ $guardian->relation_type }}</td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center">{{ ___('common.no_data_found') }}</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                    <tfoot class="table-light">
                                                        <tr>
                                                            <th colspan="5" class="text-end">{{ ___('common.total_guardians') }}:</th>
                                                            <th>{{ $data['guardianReportData']['total_count'] }}</th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                                </div>
                                                <!-- routine_wrapper_body end -->
                                            </div>
                                            <!-- routine_wrapper end -->
                                        </div>
                                    </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        // Print function
        function printDiv(divId) {
            var printContents = document.getElementById(divId).innerHTML;
            var styles = Array.from(document.styleSheets)
                .map(styleSheet => {
                    try {
                        return Array.from(styleSheet.cssRules)
                            .map(rule => rule.cssText)
                            .join('\n');
                    } catch (e) {
                        return '';
                    }
                })
                .join('\n');

            var printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write('<html><head><title>Print</title>');
            printWindow.document.write('<style>' + styles + '</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(printContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

        // Initialize nice-select
        $(document).ready(function() {
            if ($(".niceSelect").length) {
                $(".niceSelect").niceSelect();
            }

            // Accordion state management fallback
            // Check if we have report data and ensure correct accordion is open
            const hasRegistrationData = {{ isset($data['registrationReportData']) ? 'true' : 'false' }};
            const hasGuardianData = {{ isset($data['guardianReportData']) ? 'true' : 'false' }};

            if (hasRegistrationData) {
                // Ensure registration accordion is open and others are closed
                $('#collapseStudentRegistration').addClass('show');
                $('#collapseStudentList').removeClass('show');
                $('#collapseGuardianList').removeClass('show');
            } else if (hasGuardianData) {
                // Ensure guardian accordion is open and others are closed
                $('#collapseGuardianList').addClass('show');
                $('#collapseStudentList').removeClass('show');
                $('#collapseStudentRegistration').removeClass('show');
            }
        });
    </script>
    @endpush
@endsection
