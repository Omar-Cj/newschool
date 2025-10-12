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

    table {
        border-collapse: collapse;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        margin: 0;
        color: #000;
    }

    h5 {
        font-size: 12px;
        font-weight: 500;
    }

    h6 {
        font-size: 10px;
        font-weight: 300;
    }

    p {
        font-size: 14px;
        color: #000;
        font-weight: 400;
        margin: 0;
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

    .routine_part_iner {
        background-color: #fff;
    }

    .routine_part_iner h4 {
        font-size: 30px;
        font-weight: 500;
        margin-bottom: 40px;
    }

    .routine_part_iner h3 {
        font-size: 25px;
        font-weight: 500;
        margin-bottom: 5px;
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

    .routine_wrapper_header h4 {
        font-size: 24px;
        color: #FF5170;
        font-weight: 500;
        margin: 7px 0 7px 0;
    }

    .routine_wrapper_header p {
        font-weight: 400;
        font-size: 14px;
        color: #D6D6D6;
        margin: 0;
    }

    .routine_wrapper_header_logo .header_logo {
        max-width: 193px;
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
        display: block;
    }

    .routine_header h3 {
        font-size: 24px;
        font-weight: 500;
    }

    .routine_header p {
        font-size: 14px;
        font-weight: 400;
        margin-bottom: 15px !important;
    }

    /* Table Styles */
    .table {
        width: 100%;
        margin-bottom: 1rem;
        color: #212529;
    }

    .table td,
    .table th {
        padding: 0px 0;
        vertical-align: top;
        border-top: 0 solid transparent;
        color: #000;
    }

    .table th {
        color: #000;
        font-weight: 300;
        border-bottom: 1px solid #000 !important;
        background-color: #fff;
    }

    .table_border thead {
        background-color: #F6F8FA;
    }

    .table_border tr {
        border-bottom: 1px solid #000 !important;
    }

    .border_table thead tr th {
        border-right: 0;
        border-color: transparent !important;
        text-align: left;
        background: #EAEAEA;
        white-space: nowrap;
        background: #E6E6E6 !important;
        color: #1A1A21 !important;
        font-size: 16px;
        font-weight: 500;
        text-transform: capitalize;
        padding: 8px 12px;
    }

    .border_table tbody tr td,
    .border_table tfoot tr td {
        border-bottom: 0;
        text-align: center;
        font-size: 12px;
        padding: 5px;
        border-right: 0;
    }

    .border_table tbody tr th {
        background: #EAEAEA;
        border: 1px solid #FFFFFF;
        font-weight: 700;
        font-size: 18px;
        line-height: 30px;
        border-color: #fff !important;
        color: #424242;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 140px;
        padding: 2px 6px;
    }

    .border_table tr:nth-of-type(n) {
        border: 0;
    }

    .border_table tr:nth-of-type(odd) {
        border: 0;
        background: #F8F8F8;
    }

    .border_table tr:nth-of-type(even) {
        border: 0;
        background: #EFEFEF;
    }

    .border_table tfoot tr:first-of-type {
        border: 0;
    }

    .border_table tfoot tr:first-of-type td {
        border: 0;
    }

    .table_style th,
    .table_style td {
        padding: 20px;
    }

    .routine_info_table td {
        font-size: 10px;
        padding: 0px;
    }

    .routine_info_table td h6 {
        color: #6D6D6D;
        font-weight: 400;
    }

    /* Student Info */
    .student_info_wrapper {
        background: #F5F5F5;
        border-radius: 8px;
        padding: 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .student_info_single {
        width: 45%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        white-space: nowrap;
        margin-bottom: 8px;
    }

    .student_info_single span {
        min-width: 170px;
        color: #424242;
        font-size: 16px;
        line-height: 24px;
        text-transform: capitalize;
    }

    .student_info_single h5 {
        margin: 0;
        color: #1A1A21;
        font-weight: 400;
        font-size: 16px;
    }

    /* Class Box */
    .classBox_wiz {
        min-height: 26px;
        vertical-align: middle;
        display: flex;
        align-items: center;
        padding: 8px 6px;
    }

    .classBox_wiz h5 {
        font-weight: 400;
        font-size: 16px;
        line-height: 22px;
        color: #424242;
        margin: 0 0 5px 0;
        white-space: nowrap
    }

    .classBox_wiz p {
        font-weight: 500;
        font-size: 14px;
        line-height: 18px;
        color: #6B6B6B;
        margin: 0 0 5px 0;
    }

    .break_text {
        min-height: 129px;
        vertical-align: middle;
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 15px;
    }

    .break_text h5 {
        font-weight: 600;
        font-size: 18px;
        line-height: 22px;
        color: #424242;
        transform: rotate(-30deg);
    }

    .marked_bg {
        background: #E6E6E6 !important;
        color: #1A1A21 !important;
        font-size: 16px;
        font-weight: 500;
        text-transform: capitalize;
        padding: 8px 12px;
    }

    /* Download/Print Buttons */
    .download_print_btns {
        display: flex;
        align-items: center;
        justify-content: start;
        grid-gap: 12px;
        background: #F3F3F3;
        padding: 20px;
        flex-wrap: wrap;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .print_copyright_text {
        flex-direction: column;
        align-items: center;
        justify-content: center;
        grid-gap: 10px;
        margin: 20px 0;
        display: flex;
    }

    .print_copyright_text h5 {
        font-weight: 400;
        font-size: 16px;
        color: #424242;
    }

    .print_copyright_text p {
        font-size: 12px;
        color: #818181;
    }

    /* Utility Classes */
    .border_none {
        border: 0px solid transparent;
        border-top: 0px solid transparent !important;
    }

    .border_bottom {
        border-bottom: 1px solid #000;
    }

    .text_right {
        text-align: right;
    }

    .virtical_middle {
        vertical-align: middle !important;
    }

    .td-text-center {
        text-align: center !important;
    }

    .font_18 {
        font-size: 18px;
    }

    .bold_text {
        font-weight: 600;
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .mb_10 {
        margin-bottom: 10px !important;
    }

    .mb_20 {
        margin-bottom: 20px !important;
    }

    .mb_30 {
        margin-bottom: 30px !important;
    }

    .mb_40 {
        margin-bottom: 40px !important;
    }

    .mt_40 {
        margin-top: 40px;
    }

    /* Layout Utilities */
    .title_header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 40px 0 15px 0;
    }

    .line_grid {
        display: grid;
        grid-template-columns: 100px auto;
        grid-gap: 10px;
    }

    .line_grid span {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Responsive Styles */
    @media (max-width: 768px) {
        .student_info_single {
            width: 100%;
            flex-wrap: wrap;
        }

        .vertical_seperator {
            display: none !important;
        }

        .routine_wrapper {
            width: 100%;
        }

        .routine_wrapper_body {
            padding: 20px;
        }

        .download_print_btns {
            margin-top: 30px;
            flex-direction: column;
        }

        .routine_wrapper_header {
            padding: 20px 20px;
        }

        .routine_wrapper_header h3 {
            font-size: 24px;
        }
    }

    /* Print Styles */
    @media print {
        @page {
            size: A4 landscape;
            margin: 20mm;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .routine_wrapper_header h3 {
            font-size: 24px;
        }

        -webkit-print-color-adjust: exact !important;

        .btn, .card-header, .breadcrumb, .page-header, .download_print_btns {
            display: none !important;
        }

        .routine_wrapper {
            width: 100%;
            max-width: 100%;
            page-break-inside: avoid;
        }

        .routine_wrapper_body {
            padding: 36px;
        }

        table {
            page-break-inside: auto;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }

    /* Ultra-aggressive hiding for native select elements to prevent duplicate dropdown display */
    select.niceSelect,
    select.nice-select {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        position: absolute !important;
        left: -9999px !important;
        top: -9999px !important;
        width: 0 !important;
        height: 0 !important;
        z-index: -9999 !important;
        pointer-events: none !important;
    }

    /* Ensure nice-select divs are visible */
    div.nice-select {
        display: inline-block !important;
        visibility: visible !important;
    }

</style>

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            {{-- Breadcrumb Area Start --}}
            <div class="page-header">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="bradecrumb-title mb-1">{{ ___('settings.examination_reports') }}</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ ___('common.home') }}</a></li>
                            <li class="breadcrumb-item">{{ ___('settings.examination_reports') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
            {{-- Breadcrumb Area End --}}

            <!-- Accordion for reports -->
            <div class="row">
                <div class="col-12">
                    <div class="accordion custom-accordion" id="examinationReportsAccordion">
                        @php
                            $activeReport = $data['report_type'] ?? 'none';
                        @endphp

                        <!-- Collapsible 1: Exam Report (Marksheet) -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingExamReport">
                                <button class="accordion-button {{ $activeReport !== 'marksheet' ? 'collapsed' : '' }}"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseExamReport"
                                        aria-expanded="{{ $activeReport === 'marksheet' ? 'true' : 'false' }}"
                                        aria-controls="collapseExamReport">
                                    <i class="las la-file-alt me-2"></i> {{ ___('settings.exam_report') }}
                                </button>
                            </h2>
                            <div id="collapseExamReport"
                                 class="accordion-collapse collapse {{ $activeReport === 'marksheet' ? 'show' : '' }}"
                                 aria-labelledby="headingExamReport"
                                 data-bs-parent="#examinationReportsAccordion">
                                <div class="accordion-body">

                                    <!-- Filter Form -->
                                    <form action="{{ route('report-examination.search-marksheet') }}" method="GET" id="marksheetForm">
                                        <div class="card ot-card mb-24 position-relative z_1">
                                            <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                                                <h3 class="mb-0">{{ ___('common.Filtering') }}</h3>

                                                <div class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">

                                                    <!-- Session -->
                                                    <div class="single_large_selectBox">
                                                        <select id="getTermsMarksheet" class="session nice-select niceSelect bordered_style wide @error('session') is-invalid @enderror" name="session">
                                                            <option value="">{{ ___('examination.select_session') }} *</option>
                                                            @foreach ($data['sessions'] as $session)
                                                                <option {{ old('session', @$data['request']->session) == $session->id ? 'selected' : '' }} value="{{ $session->id }}">{{ $session->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('session')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Term -->
                                                    <div class="single_large_selectBox">
                                                        <select class="term nice-select niceSelect bordered_style wide @error('term') is-invalid @enderror" name="term">
                                                            <option value="">{{ ___('examination.select_term') }} *</option>
                                                            @if(isset($data['request']) && $activeReport === 'marksheet')
                                                                @foreach ($data['terms'] ?? [] as $term)
                                                                    <option {{ old('term', @$data['request']->term) == $term->id ? 'selected' : '' }} value="{{ $term->id }}">{{ $term->termDefinition->name ?? 'Term ' . $term->id }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('term')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Class -->
                                                    <div class="single_large_selectBox">
                                                        <select id="getSectionsMarksheet" class="class nice-select niceSelect bordered_style wide @error('class') is-invalid @enderror" name="class">
                                                            <option value="">{{ ___('student_info.select_class') }} *</option>
                                                            @foreach ($data['classes'] as $item)
                                                                <option {{ old('class', @$data['student']->session_class_student->class->id) == $item->class->id ? 'selected' : '' }} value="{{ $item->class->id }}">{{ $item->class->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('class')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Section -->
                                                    <div class="single_large_selectBox">
                                                        <select class="sections section nice-select niceSelect bordered_style wide @error('section') is-invalid @enderror" name="section">
                                                            <option value="">{{ ___('student_info.select_section') }} *</option>
                                                            @if($activeReport === 'marksheet')
                                                                @foreach ($data['sections'] as $item)
                                                                    <option {{ old('section', @$data['student']->session_class_student->section->id) == $item->section->id ? 'selected' : '' }} value="{{ $item->section->id }}">{{ $item->section->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('section')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Exam Type -->
                                                    <div class="single_large_selectBox">
                                                        <select class="exam_types nice-select niceSelect bordered_style wide @error('exam_type') is-invalid @enderror" name="exam_type">
                                                            <option value="">{{ ___('examination.select_exam_type') }} *</option>
                                                        </select>
                                                        @error('exam_type')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Student -->
                                                    <div class="single_large_selectBox">
                                                        <select class="students nice-select niceSelect bordered_style wide @error('student') is-invalid @enderror" name="student">
                                                            <option value="">{{ ___('student_info.select_student') }} *</option>
                                                            @if($activeReport === 'marksheet')
                                                                @foreach ($data['students'] as $item)
                                                                    <option {{ old('student', @$data['student']->id) == $item->student_id ? 'selected' : '' }} value="{{ $item->student_id }}">{{ $item->student->first_name }} {{ $item->student->last_name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('student')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <button class="btn btn-lg ot-btn-primary" type="submit">
                                                        {{___('common.Search')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Results Section -->
                                    @if(isset($data['resultData']) && $activeReport === 'marksheet')
                                        <div class="col-lg-12">
                                            <div class="col-lg-12">
                                                <div class="download_print_btns d-flex justify-content-between align-items-center flex-wrap">
                                                    <div class="mb-2">
                                                        <button class="btn btn-lg ot-btn-primary" onclick="printDiv('printableAreaMarksheet')">
                                                            {{ ___('common.print_now') }}
                                                            <span><i class="fa-solid fa-print"></i></span>
                                                        </button>

                                                        <a class="btn btn-lg ot-btn-primary" href="{{ route('report-marksheet.pdf-generate', ['id'=>$data['request']->student, 'type'=>$data['request']->exam_type, 'class'=>$data['request']->class, 'section'=>$data['request']->section, 'session'=>$data['request']->session, 'term'=>$data['request']->term ]) }}">
                                                            {{ ___('common.pdf_download') }}
                                                            <span><i class="fa-brands fa-dochub"></i></span>
                                                        </a>
                                                    </div>

                                                    @if (isset($data['markSheetApproval']) && $data['markSheetApproval'])
                                                        <div class="mb-2">
                                                            <div class="{{ $data['markSheetApproval']->status === 'approved' ? 'text-success' : 'text-danger' }}">
                                                                @if ($data['markSheetApproval']->status === 'approved')
                                                                    <i class="fa-solid fa-circle-check me-1"></i>
                                                                    {{ __('Marksheet approved at') }} {{ dateFormat($data['markSheetApproval']->updated_at) }}
                                                                @elseif ($data['markSheetApproval']->status === 'rejected')
                                                                    <i class="fa-solid fa-circle-xmark me-1"></i>
                                                                    {{ __('Marksheet rejected at') }} {{ dateFormat($data['markSheetApproval']->updated_at) }}
                                                                @endif
                                                            </div>

                                                            @if (!empty($data['markSheetApproval']->remarks))
                                                                <div class="mt-1 ps-4">
                                                                    <small class="text-muted">
                                                                        <i class="fa-solid me-1"></i>
                                                                        {{ __('Remarks') }}: {{ $data['markSheetApproval']->remarks }}
                                                                    </small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <div class="mb-2">
                                                        @if (!isset($data['markSheetApproval']) || $data['markSheetApproval']->status === 'rejected')
                                                            <button class="btn btn-lg btn-success" type="button" id="btn-approval"
                                                                    data-bs-target="#approvalModal" data-bs-toggle="modal">
                                                                {{ ___('common.Approval') }}
                                                                <span><i class="fa-solid fa-check"></i></span>
                                                            </button>
                                                        @elseif ($data['markSheetApproval']->status === 'approved')
                                                            <button class="btn btn-lg btn-danger" type="button" id="btn-approval"
                                                                    data-bs-target="#approvalModal" data-bs-toggle="modal">
                                                                {{ ___('common.Reject') }}
                                                                <span><i class="fa-solid fa-check"></i></span>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card ot-card mb-24" id="printableAreaMarksheet">
                                                <div class="routine_wrapper">
                                                    <!-- Header -->
                                                    <div class="routine_wrapper_header">
                                                        <div class="routine_wrapper_header_logo">
                                                            <img class="header_logo" src="{{ @globalAsset(setting('light_logo'), '154X38.webp') }}" alt="{{ __('light logo') }}">
                                                        </div>
                                                        <div class="vertical_seperator"></div>
                                                        <div class="routine_wrapper_header_content">
                                                            <h3>{{ setting('application_name') }}</h3>
                                                            <p>{{ setting('address') }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="routine_wrapper_body">
                                                        <!-- Student Info -->
                                                        <div class="student_info_wrapper">
                                                            <div class="student_info_single">
                                                                <span>{{___('student_info.student_name')}} :</span>
                                                                <h5>{{ @$data['student']->first_name }} {{ @$data['student']->last_name }}</h5>
                                                            </div>
                                                            <div class="student_info_single">
                                                                <span>{{___('report.guardian_name')}} :</span>
                                                                <h5>{{ @$data['student']->parent->guardian_name }}</h5>
                                                            </div>
                                                            <div class="student_info_single">
                                                                <span>{{___('common.date_of_birth')}} :</span>
                                                                <h5>{{ dateFormat(@$data['student']->dob) }}</h5>
                                                            </div>
                                                            <div class="student_info_single">
                                                                <span>{{___('report.guardian_phone')}} :</span>
                                                                <h5>{{ @$data['student']->parent->guardian_mobile }}</h5>
                                                            </div>
                                                            <div class="student_info_single">
                                                                <span>{{___('academic.class')}} ({{___('academic.section')}}) :</span>
                                                                <h5>{{ @$data['student']->session_class_student->class->name }} ({{ @$data['student']->session_class_student->section->name }})</h5>
                                                            </div>
                                                            <div class="student_info_single">
                                                                <span>{{___('examination.exam_type')}} :</span>
                                                                <h5>{{ @$data['examType']->name ?? 'N/A' }}</h5>
                                                            </div>
                                                            <div class="student_info_single">
                                                                <span>{{___('report.Result')}} :</span>
                                                                <h5>{{ @$data['resultData']['result'] }}</h5>
                                                            </div>
                                                        </div>

                                                        <!-- Report Title -->
                                                        <div class="markseet_title">
                                                            <h5>{{___('report.grade_sheet')}}</h5>
                                                        </div>

                                                        <!-- Results Table -->
                                                        <div class="table-responsive">
                                                            <table class="table border_table mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="marked_bg">{{___('report.subject_name')}}</th>
                                                                        <th class="marked_bg">{{___('report.result_marks')}}</th>
                                                                        <th class="marked_bg">{{___('report.Grade')}}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse (@$data['resultData']['exam_results'] as $result)
                                                                        <tr>
                                                                            <td>
                                                                                <div class="classBox_wiz">
                                                                                    <h5>{{ $result->subject_name }}</h5>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="classBox_wiz">
                                                                                    @if($result->is_absent)
                                                                                        <h5 class="text-danger">{{ ___('examination.Absent') }}</h5>
                                                                                    @else
                                                                                        <h5>{{ number_format($result->result, 2) }}</h5>
                                                                                    @endif
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="classBox_wiz">
                                                                                    <h5>{{ $result->grade }}</h5>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="3" class="td-text-center">
                                                                                @include('backend.includes.no-data')
                                                                            </td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>

                        <!-- Collapsible 2: Progress Card -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingProgressCard">
                                <button class="accordion-button {{ $activeReport !== 'progress_card' ? 'collapsed' : '' }}"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseProgressCard"
                                        aria-expanded="{{ $activeReport === 'progress_card' ? 'true' : 'false' }}"
                                        aria-controls="collapseProgressCard">
                                    <i class="las la-chart-line me-2"></i> {{ ___('settings.progress_card') }}
                                </button>
                            </h2>
                            <div id="collapseProgressCard"
                                 class="accordion-collapse collapse {{ $activeReport === 'progress_card' ? 'show' : '' }}"
                                 aria-labelledby="headingProgressCard"
                                 data-bs-parent="#examinationReportsAccordion">
                                <div class="accordion-body">

                                    <!-- Filter Form -->
                                    <form action="{{ route('report-examination.search-progress-card') }}" method="POST" id="progressCardForm">
                                        @csrf
                                        <div class="card ot-card mb-24 position-relative z_1">
                                            <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                                                <h3 class="mb-0">{{ ___('common.Filtering') }}</h3>

                                                <div class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">

                                                    <!-- Session -->
                                                    <div class="single_large_selectBox">
                                                        <select id="getTermsProgress" class="session_progress nice-select niceSelect bordered_style wide @error('session') is-invalid @enderror" name="session">
                                                            <option value="">{{ ___('examination.select_session') }} *</option>
                                                            @foreach ($data['sessions'] as $session)
                                                                <option {{ old('session', @$data['request']->session) == $session->id ? 'selected' : '' }} value="{{ $session->id }}">{{ $session->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('session')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Term -->
                                                    <div class="single_large_selectBox">
                                                        <select class="term_progress nice-select niceSelect bordered_style wide @error('term') is-invalid @enderror" name="term">
                                                            <option value="">{{ ___('examination.select_term') }} *</option>
                                                            @if(isset($data['request']) && $activeReport === 'progress_card')
                                                                @foreach ($data['terms'] ?? [] as $term)
                                                                    <option {{ old('term', @$data['request']->term) == $term->id ? 'selected' : '' }} value="{{ $term->id }}">{{ $term->termDefinition->name ?? 'Term ' . $term->id }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('term')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Class -->
                                                    <div class="single_large_selectBox">
                                                        <select id="getSectionsProgress" class="class_progress nice-select niceSelect bordered_style wide @error('class') is-invalid @enderror" name="class">
                                                            <option value="">{{ ___('student_info.select_class') }} *</option>
                                                            @foreach ($data['classes'] as $item)
                                                                <option {{ old('class', @$data['student']->session_class_student->class->id) == $item->class->id ? 'selected' : '' }} value="{{ $item->class->id }}">{{ $item->class->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('class')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Section -->
                                                    <div class="single_large_selectBox">
                                                        <select class="sections_progress section_progress nice-select niceSelect bordered_style wide @error('section') is-invalid @enderror" name="section">
                                                            <option value="">{{ ___('student_info.select_section') }} *</option>
                                                            @if($activeReport === 'progress_card')
                                                                @foreach ($data['sections'] as $item)
                                                                    <option {{ old('section', @$data['student']->session_class_student->section->id) == $item->section->id ? 'selected' : '' }} value="{{ $item->section->id }}">{{ $item->section->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('section')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <!-- Student -->
                                                    <div class="single_large_selectBox">
                                                        <select class="students_progress nice-select niceSelect bordered_style wide @error('student') is-invalid @enderror" name="student">
                                                            <option value="">{{ ___('student_info.select_student') }} *</option>
                                                            @if($activeReport === 'progress_card')
                                                                @foreach ($data['students'] as $item)
                                                                    <option {{ old('student', @$data['student']->id) == $item->student_id ? 'selected' : '' }} value="{{ $item->student_id }}">{{ $item->student->first_name }} {{ $item->student->last_name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        @error('student')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <button class="btn btn-lg ot-btn-primary" type="submit">
                                                        {{___('common.Search')}}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Progress Card Results Section -->
                                    @if(isset($data['marks_registers']) && $activeReport === 'progress_card')
                                        <div class="col-lg-12">
                                            <div class="download_print_btns">
                                                <button class="btn btn-lg ot-btn-primary" onclick="printDiv('printableAreaProgress')">
                                                    {{___('common.print_now')}}
                                                    <span><i class="fa-solid fa-print"></i></span>
                                                </button>
                                                <a class="btn btn-lg ot-btn-primary" href="{{ route('report-progress-card.pdf-generate', ['session'=>$data['request']->session, 'term'=>$data['request']->term, 'class'=>$data['request']->class, 'section'=>$data['request']->section, 'student'=>$data['request']->student]) }}">
                                                    {{___('common.Pdf Preview')}}
                                                    <span><i class="fa-brands fa-dochub"></i></span>
                                                </a>
                                            </div>

                                            <div class="card ot-card mb-24" id="printableAreaProgress">
                                                <div class="routine_wrapper">
                                                    <!-- routine_wrapper_header part here -->
                                                    <div class="routine_wrapper_header">
                                                        <div class="routine_wrapper_header_logo">
                                                            <img class="header_logo" src="{{ @globalAsset(setting('light_logo'), '154X38.webp') }}"
                                                                alt="{{ __('light logo') }}">
                                                        </div>
                                                        <div class="vertical_seperator"></div>
                                                        <div class="routine_wrapper_header_content">
                                                            <h3>{{___('common.Progress Card Report')}}</h3>
                                                            <p>{{___('common.Name')}}: {{ @$data['student']->first_name }} {{ @$data['student']->last_name }}
                                                            <br> {{___('common.Class(Section)')}}: {{ @$data['student']->session_class_student->class->name }}
                                                            ({{ @$data['student']->session_class_student->section->name }}) , {{___('common.Roll No')}} : {{@$data['student']->session_class_student->roll}}</p>
                                                        </div>
                                                    </div>
                                                    <div class="routine_wrapper_body">
                                                        <!-- student_info_wrapper part end -->
                                                        <div class="markseet_title">
                                                            <h5>{{___('report.grade_sheet')}}</h5>
                                                        </div>

                                                        <!-- Progress Card Table -->
                                                        <div class="table-responsive">
                                                            <table class="table border_table mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="marked_bg">{{___('report.subject_name')}}</th>
                                                                        @foreach (@$data['exams'] as $item)
                                                                            <th class="marked_bg">{{$item->exam_type->name}} <small>{{___('report.mark_grade')}}</small></th>
                                                                        @endforeach
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @if(isset($data['subjects']) && count($data['subjects']) > 0)
                                                                        @foreach (@$data['subjects'] as $item)
                                                                            <tr>
                                                                                <td>
                                                                                    <div class="classBox_wiz">
                                                                                        <h5>{{ $item->subject->name }}</h5>
                                                                                    </div>
                                                                                </td>
                                                                                @foreach (@$data['exams'] as $key=>$exam)
                                                                                    <td>
                                                                                        @foreach ($data['marks_registers'][$key] as $result)
                                                                                            @if ($result->subject_id == $item->subject->id)
                                                                                                <div class="classBox_wiz">
                                                                                                    @php
                                                                                                        $n = 0;
                                                                                                    @endphp
                                                                                                    @foreach ($result->marksRegisterChilds as $mark)
                                                                                                        @php
                                                                                                            $n += $mark->mark;
                                                                                                        @endphp
                                                                                                    @endforeach
                                                                                                    <h5>{{$n}} - {{ markGrade($n) }}</h5>
                                                                                                </div>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </td>
                                                                                @endforeach
                                                                            </tr>
                                                                        @endforeach
                                                                    @else
                                                                        <tr>
                                                                            <td colspan="{{ 1 + count(@$data['exams']) }}" class="td-text-center">
                                                                                @include('backend.includes.no-data')
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <table class="table border_table mt-5">
                                                            <thead>
                                                                <tr>
                                                                    <th>{{___('report.exam_name')}}</th>
                                                                    <th>{{___('report.Result')}}</th>
                                                                    <th>{{___('report.total_mark')}}</th>
                                                                    <th>{{___('report.avg_marks')}}</th>
                                                                    <th>{{___('report.avg_grade')}}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($data['exams'] as $key=>$item)
                                                                    <tr>
                                                                        <td>{{ $item->exam_type->name }}</td>
                                                                        <td>{{ $data['result'][$key] }}</td>
                                                                        <td>{{ $data['total_marks'][$key] }}</td>
                                                                        <td>{{ substr($data['avg_marks'][$key],0,5) }}</td>
                                                                        <td>{{ $data['result'][$key] == 'Failed' ? 'F' : markGrade((int)$data['avg_marks'][$key]) }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="print_copyright_text d-flex">
                                                        <img src="{{ globalAsset(setting('favicon')) }}" alt="Icon">
                                                        <p>{{ setting('footer_text') }}</p>
                                                    </div>
                                                </div>
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

    @push('script')
    <script>
        $(document).ready(function() {
            var url = $('#url').val();

            // Initialize nice-select with proper timing
            if ($(".niceSelect").length) {
                $(".niceSelect").niceSelect();

                // Force update after short delay to handle any layout issues
                setTimeout(function() {
                    $(".niceSelect").niceSelect('update');
                }, 100);

                // JavaScript enforcement: Force hide original select elements to prevent duplicate display
                setTimeout(function() {
                    $('.niceSelect').each(function() {
                        $(this).hide().css({
                            'display': 'none',
                            'visibility': 'hidden',
                            'position': 'absolute',
                            'left': '-9999px'
                        });
                    });
                }, 150);
            }

            // Re-initialize nice-select when accordions are opened
            var accordionInitialized = {
                marksheet: false,
                progressCard: false
            };

            $('#collapseExamReport').on('shown.bs.collapse', function () {
                if (!accordionInitialized.marksheet) {
                    // Reinitialize nice-select for marksheet section
                    $('#collapseExamReport .niceSelect').niceSelect('update');
                    accordionInitialized.marksheet = true;
                }
            });

            $('#collapseProgressCard').on('shown.bs.collapse', function () {
                if (!accordionInitialized.progressCard) {
                    // Reinitialize nice-select for progress card section
                    $('#collapseProgressCard .niceSelect').niceSelect('update');
                    accordionInitialized.progressCard = true;
                }
            });

            // MARKSHEET SECTION HANDLERS
            // Session change - Load terms for marksheet
            $('#getTermsMarksheet').on('change', function (e) {
                var sessionId = $(this).val();
                var ajaxUrl = '{{ url("") }}/report-examination/get-terms/' + sessionId;

                $("select.term").html('<option value="">{{ ___("examination.select_term") }} *</option>');

                if (sessionId) {
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        success: function(response) {
                            $.each(response, function(key, term) {
                                $("select.term").append('<option value="' + term.id + '">' + term.name + '</option>');
                            });
                            $("select.term").niceSelect('update');
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to load terms');
                        }
                    });
                } else {
                    $("select.term").niceSelect('update');
                }
            });

            // Class change - Load sections for marksheet
            $('#getSectionsMarksheet').on('change', function (e) {
                var classId = $(this).val();
                var formData = { id: classId };

                $("select.sections option").not(':first').remove();
                $("select.students option").not(':first').remove();
                $("select.exam_types option").not(':first').remove();

                if (classId) {
                    $.ajax({
                        type: "GET",
                        dataType: 'html',
                        data: formData,
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: url + '/class-setup/get-sections',
                        success: function (data) {
                            var section_options = '';
                            $.each(JSON.parse(data), function (i, item) {
                                section_options += "<option value=" + item.section.id + ">" + item.section.name + "</option>";
                            });
                            $("select.sections").append(section_options);
                            $("select.sections").niceSelect('update');
                        },
                        error: function (data) {
                            console.error('Failed to load sections');
                        }
                    });
                } else {
                    $("select.sections").niceSelect('update');
                    $("select.students").niceSelect('update');
                    $("select.exam_types").niceSelect('update');
                }
            });

            // Section change - Load students and exam types for marksheet
            $("form#marksheetForm .section").on('change', function (e) {
                var classId = $("#getSectionsMarksheet").val();
                var sectionId = $(this).val();

                $("select.students option").not(':first').remove();
                $("select.exam_types option").not(':first').remove();

                if (classId && sectionId) {
                    // Load students
                    $.ajax({
                        type: "GET",
                        dataType: 'json',
                        data: { class: classId, section: sectionId },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: '{{ route("report-examination.get-students") }}',
                        success: function (data) {
                            var student_options = '';
                            $.each(data, function (i, item) {
                                student_options += "<option value=" + item.student_id + ">" + item.student.first_name + ' ' + item.student.last_name + "</option>";
                            });
                            $("form#marksheetForm select.students").append(student_options);
                            $("form#marksheetForm select.students").niceSelect('update');
                        },
                        error: function (data) {
                            console.error('Failed to load students');
                        }
                    });

                    // Load exam types
                    $.ajax({
                        type: "GET",
                        dataType: 'html',
                        data: { class: classId, section: sectionId },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: url + '/exam-assign/get-exam-type',
                        success: function (data) {
                            var exam_type_options = '';
                            $.each(JSON.parse(data), function (i, item) {
                                exam_type_options += "<option value=" + item.id + ">" + item.name + "</option>";
                            });
                            $("form#marksheetForm select.exam_types").append(exam_type_options);
                            $("form#marksheetForm select.exam_types").niceSelect('update');
                        },
                        error: function (data) {
                            console.error('Failed to load exam types');
                        }
                    });
                } else {
                    $("form#marksheetForm select.students").niceSelect('update');
                    $("form#marksheetForm select.exam_types").niceSelect('update');
                }
            });

            // PROGRESS CARD SECTION HANDLERS
            // Session change - Load terms for progress card
            $('#getTermsProgress').on('change', function (e) {
                var sessionId = $(this).val();
                var ajaxUrl = '{{ url("") }}/report-examination/get-terms/' + sessionId;

                $("select.term_progress").html('<option value="">{{ ___("examination.select_term") }} *</option>');

                if (sessionId) {
                    $.ajax({
                        url: ajaxUrl,
                        type: 'GET',
                        success: function(response) {
                            $.each(response, function(key, term) {
                                $("select.term_progress").append('<option value="' + term.id + '">' + term.name + '</option>');
                            });
                            $("select.term_progress").niceSelect('update');
                        },
                        error: function(xhr, status, error) {
                            console.error('Failed to load terms');
                        }
                    });
                } else {
                    $("select.term_progress").niceSelect('update');
                }
            });

            // Class change - Load sections for progress card
            $('#getSectionsProgress').on('change', function (e) {
                var classId = $(this).val();
                var formData = { id: classId };

                $("select.sections_progress option").not(':first').remove();
                $("select.students_progress option").not(':first').remove();

                if (classId) {
                    $.ajax({
                        type: "GET",
                        dataType: 'html',
                        data: formData,
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: url + '/class-setup/get-sections',
                        success: function (data) {
                            var section_options = '';
                            $.each(JSON.parse(data), function (i, item) {
                                section_options += "<option value=" + item.section.id + ">" + item.section.name + "</option>";
                            });
                            $("select.sections_progress").append(section_options);
                            $("select.sections_progress").niceSelect('update');
                        },
                        error: function (data) {
                            console.error('Failed to load sections');
                        }
                    });
                } else {
                    $("select.sections_progress").niceSelect('update');
                    $("select.students_progress").niceSelect('update');
                }
            });

            // Section change - Load students for progress card
            $("form#progressCardForm .section_progress").on('change', function (e) {
                var classId = $("#getSectionsProgress").val();
                var sectionId = $(this).val();

                $("select.students_progress option").not(':first').remove();

                if (classId && sectionId) {
                    $.ajax({
                        type: "GET",
                        dataType: 'json',
                        data: { class: classId, section: sectionId },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: '{{ route("report-examination.get-students") }}',
                        success: function (data) {
                            var student_options = '';
                            $.each(data, function (i, item) {
                                student_options += "<option value=" + item.student_id + ">" + item.student.first_name + ' ' + item.student.last_name + "</option>";
                            });
                            $("form#progressCardForm select.students_progress").append(student_options);
                            $("form#progressCardForm select.students_progress").niceSelect('update');
                        },
                        error: function (data) {
                            console.error('Failed to load students');
                        }
                    });
                } else {
                    $("form#progressCardForm select.students_progress").niceSelect('update');
                }
            });

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

            // Make printDiv globally available
            window.printDiv = printDiv;
        });
    </script>
    @endpush
@endsection
