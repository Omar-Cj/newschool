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
                        <h4 class="bradecrumb-title mb-1">{{ ___('settings.billing_report') }}</h4>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ ___('common.home') }}</a></li>
                            <li class="breadcrumb-item">{{ ___('settings.billing_report') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
            {{-- bradecrumb Area E n d --}}

            <!-- accordion for billing reports -->
            <div class="row">
                <div class="col-12">
                    <div class="accordion custom-accordion" id="billingReportsAccordion">

                        <!-- Paid Students Collapsible -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingPaidStudents">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePaidStudents" aria-expanded="false" aria-controls="collapsePaidStudents">
                                    <i class="las la-check-circle me-2"></i> {{ ___('settings.paid_students') }}
                                </button>
                            </h2>
                            <div id="collapsePaidStudents" class="accordion-collapse collapse" aria-labelledby="headingPaidStudents" data-bs-parent="#billingReportsAccordion">
                                <div class="accordion-body">

                                    <!-- Filter Card -->
                                    <div class="card">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">{{ ___('settings.filter_options') }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('report-billing.search-paid-students') }}" method="POST" id="paidStudentsForm">
                                                @csrf
                                                <div class="row g-3">

                                                    <!-- Start Date -->
                                                    <div class="col-md-6">
                                                        <label for="paid_start_date" class="form-label">{{ ___('common.start_date') }} <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" name="start_date" id="paid_start_date" required>
                                                    </div>

                                                    <!-- End Date -->
                                                    <div class="col-md-6">
                                                        <label for="paid_end_date" class="form-label">{{ ___('common.end_date') }} <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" name="end_date" id="paid_end_date" required>
                                                    </div>

                                                    <!-- Grade -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="grade" id="paid_grade">
                                                                <option value="">{{ ___('common.select_grade') }}</option>
                                                                @foreach ($data['grades'] as $grade)
                                                                    <option value="{{ $grade }}">{{ $grade }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Class -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="class_id" id="paid_class">
                                                                <option value="">{{ ___('common.select_class') }}</option>
                                                                @foreach ($data['classes'] as $class)
                                                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Section -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="section_id" id="paid_section">
                                                                <option value="">{{ ___('common.select_section') }}</option>
                                                                @foreach ($data['sections'] as $section)
                                                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Gender -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="gender_id" id="paid_gender">
                                                                <option value="">{{ ___('common.select_gender') }}</option>
                                                                @foreach ($data['genders'] as $gender)
                                                                    <option value="{{ $gender->id }}">{{ $gender->name }}</option>
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
                                    <div id="paidStudentsResults" style="display: none;">
                                        <!-- Action Buttons -->
                                        <div class="card mt-3">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                    <h5 class="mb-0">{{ ___('common.search_results') }}</h5>
                                                    <div>
                                                        <button type="button" class="btn btn-success" onclick="printPaidStudentsReport()">
                                                            <i class="las la-print me-1"></i> {{ ___('common.print') }}
                                                        </button>
                                                        <button type="button" class="btn btn-danger" onclick="exportPaidStudentsPDF()">
                                                            <i class="las la-file-pdf me-1"></i> {{ ___('common.export_pdf') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Results Table -->
                                        <div class="card mt-3" id="paidStudentsPrintArea">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>{{ ___('common.payment_date') }}</th>
                                                                <th>{{ ___('common.journal') }}</th>
                                                                <th>{{ ___('common.student_name') }}</th>
                                                                <th>{{ ___('common.mobile') }}</th>
                                                                <th class="text-end">{{ ___('fees.paid_amount') }}</th>
                                                                <th class="text-end">{{ ___('fees.deposit') }}</th>
                                                                <th class="text-end">{{ ___('fees.discount') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="paidStudentsTableBody">
                                                            <!-- Results will be populated via JavaScript -->
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <!-- Summary Section -->
                                                <div class="row mt-4">
                                                    <div class="col-md-6 offset-md-6">
                                                        <table class="table table-bordered">
                                                            <tbody>
                                                                <tr>
                                                                    <th class="text-end">{{ ___('fees.total_paid_amount') }}:</th>
                                                                    <td class="text-end" id="totalPaidAmount">0.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="text-end">{{ ___('fees.total_deposit') }}:</th>
                                                                    <td class="text-end" id="totalDeposit">0.00</td>
                                                                </tr>
                                                                <tr>
                                                                    <th class="text-end">{{ ___('fees.total_discount') }}:</th>
                                                                    <td class="text-end" id="totalDiscount">0.00</td>
                                                                </tr>
                                                                <tr class="table-success">
                                                                    <th class="text-end"><strong>{{ ___('fees.net_total') }}:</strong></th>
                                                                    <td class="text-end"><strong id="netTotal">0.00</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Unpaid Students Collapsible -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingUnpaidStudents">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUnpaidStudents" aria-expanded="false" aria-controls="collapseUnpaidStudents">
                                    <i class="las la-exclamation-circle me-2"></i> {{ ___('settings.unpaid_students') }}
                                </button>
                            </h2>
                            <div id="collapseUnpaidStudents" class="accordion-collapse collapse" aria-labelledby="headingUnpaidStudents" data-bs-parent="#billingReportsAccordion">
                                <div class="accordion-body">

                                    <!-- Filter Card -->
                                    <div class="card">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">{{ ___('settings.filter_options') }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('report-billing.search-unpaid-students') }}" method="POST" id="unpaidStudentsForm">
                                                @csrf
                                                <div class="row g-3">

                                                    <!-- Start Date (Required) -->
                                                    <div class="col-md-6">
                                                        <label for="unpaid_start_date" class="form-label">{{ ___('common.start_date') }} <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" name="start_date" id="unpaid_start_date" required>
                                                    </div>

                                                    <!-- End Date (Required) -->
                                                    <div class="col-md-6">
                                                        <label for="unpaid_end_date" class="form-label">{{ ___('common.end_date') }} <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" name="end_date" id="unpaid_end_date" required>
                                                    </div>

                                                    <!-- Grade (Optional) -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="grade" id="unpaid_grade">
                                                                <option value="">{{ ___('academic.select_grade') }}</option>
                                                                @foreach ($data['grades'] as $grade)
                                                                    <option value="{{ $grade }}">{{ $grade }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Class (Optional) -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="class_id" id="unpaid_class">
                                                                <option value="">{{ ___('common.select_class') }}</option>
                                                                @foreach ($data['classes'] as $class)
                                                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Section (Optional) -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="section_id" id="unpaid_section">
                                                                <option value="">{{ ___('common.select_section') }}</option>
                                                                @foreach ($data['sections'] as $section)
                                                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Status (Optional) -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="status" id="unpaid_status">
                                                                <option value="">{{ ___('common.select_status') }}</option>
                                                                <option value="1">{{ ___('common.active') }}</option>
                                                                <option value="0">{{ ___('common.inactive') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Shift (Optional) -->
                                                    <div class="col-md-12">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="shift_id" id="unpaid_shift">
                                                                <option value="">{{ ___('academic.select_shift') }}</option>
                                                                @foreach ($data['shifts'] as $shift)
                                                                    <option value="{{ $shift->id }}">{{ $shift->name }}</option>
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
                                    <div id="unpaidStudentsResults" style="display: none;">
                                        <!-- Action Buttons -->
                                        <div class="card mt-3">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                    <h5 class="mb-0">{{ ___('common.search_results') }}</h5>
                                                    <div>
                                                        <button type="button" class="btn btn-success" onclick="printUnpaidStudentsReport()">
                                                            <i class="las la-print me-1"></i> {{ ___('common.print') }}
                                                        </button>
                                                        <button type="button" class="btn btn-danger" onclick="exportUnpaidStudentsPDF()">
                                                            <i class="las la-file-pdf me-1"></i> {{ ___('common.export_pdf') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Results Table -->
                                        <div class="card mt-3" id="unpaidStudentsPrintArea">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>{{ ___('common.date') }}</th>
                                                                <th>{{ ___('common.student_name') }}</th>
                                                                <th>{{ ___('common.mobile') }}</th>
                                                                <th>{{ ___('academic.grade') }}</th>
                                                                <th>{{ ___('academic.class') }}</th>
                                                                <th>{{ ___('academic.section') }}</th>
                                                                <th class="text-end">{{ ___('fees.outstanding_amount') }}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="unpaidStudentsTableBody">
                                                            <!-- Results will be populated via JavaScript -->
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <!-- Summary Section -->
                                                <div class="row mt-4">
                                                    <div class="col-md-6 offset-md-6">
                                                        <table class="table table-bordered">
                                                            <tbody>
                                                                <tr class="table-danger">
                                                                    <th class="text-end"><strong>{{ ___('fees.total_outstanding') }}:</strong></th>
                                                                    <td class="text-end"><strong id="totalOutstanding">0.00</strong></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Discounts Collapsible -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDiscounts">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiscounts" aria-expanded="false" aria-controls="collapseDiscounts">
                                    <i class="las la-percentage me-2"></i> {{ ___('settings.discounts') }}
                                </button>
                            </h2>
                            <div id="collapseDiscounts" class="accordion-collapse collapse" aria-labelledby="headingDiscounts" data-bs-parent="#billingReportsAccordion">
                                <div class="accordion-body">

                                    <!-- Filter Card -->
                                    <div class="card">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">{{ ___('settings.filter_options') }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <form action="#" method="GET">
                                                <div class="row g-3">

                                                    <!-- Session -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="session" id="discount_session">
                                                                <option value="">{{ ___('common.select_session') }}</option>
                                                                @foreach ($data['sessions'] as $session)
                                                                    <option value="{{ $session->id }}">{{ $session->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Discount Type -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="discount_type" id="discount_type">
                                                                <option value="">{{ ___('fees.discount_type') }}</option>
                                                                <option value="sibling">{{ ___('fees.sibling_discount') }}</option>
                                                                <option value="early_payment">{{ ___('fees.early_payment_discount') }}</option>
                                                                <option value="scholarship">{{ ___('fees.scholarship') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Class -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="class" id="discount_class">
                                                                <option value="">{{ ___('common.select_class') }}</option>
                                                                @foreach ($data['classes'] as $class)
                                                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Section -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="section" id="discount_section">
                                                                <option value="">{{ ___('common.select_section') }}</option>
                                                                @foreach ($data['sections'] as $section)
                                                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Start Date -->
                                                    <div class="col-md-6">
                                                        <label for="discount_start_date" class="form-label">{{ ___('common.start_date') }}</label>
                                                        <input type="date" class="form-control" name="start_date" id="discount_start_date">
                                                    </div>

                                                    <!-- End Date -->
                                                    <div class="col-md-6">
                                                        <label for="discount_end_date" class="form-label">{{ ___('common.end_date') }}</label>
                                                        <input type="date" class="form-control" name="end_date" id="discount_end_date">
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

                                    <!-- Upcoming Placeholder -->
                                    <div class="alert alert-info mt-3" role="alert">
                                        <i class="las la-clock me-2"></i>
                                        <strong>{{ ___('common.upcoming') }}</strong> - Backend implementation in progress
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Fee Generation Collapsible -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFeeGeneration">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFeeGeneration" aria-expanded="false" aria-controls="collapseFeeGeneration">
                                    <i class="las la-file-invoice-dollar me-2"></i> {{ ___('settings.fee_generation') }}
                                </button>
                            </h2>
                            <div id="collapseFeeGeneration" class="accordion-collapse collapse" aria-labelledby="headingFeeGeneration" data-bs-parent="#billingReportsAccordion">
                                <div class="accordion-body">

                                    <!-- Filter Card -->
                                    <div class="card">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">{{ ___('settings.filter_options') }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <form action="#" method="GET">
                                                <div class="row g-3">

                                                    <!-- Session -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="session" id="generation_session">
                                                                <option value="">{{ ___('common.select_session') }}</option>
                                                                @foreach ($data['sessions'] as $session)
                                                                    <option value="{{ $session->id }}">{{ $session->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Class -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="class" id="generation_class">
                                                                <option value="">{{ ___('common.select_class') }}</option>
                                                                @foreach ($data['classes'] as $class)
                                                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Section -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="section" id="generation_section">
                                                                <option value="">{{ ___('common.select_section') }}</option>
                                                                @foreach ($data['sections'] as $section)
                                                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Fee Type -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="fee_type" id="generation_fee_type">
                                                                <option value="">{{ ___('fees.fee_type') }}</option>
                                                                <option value="tuition">{{ ___('fees.tuition_fee') }}</option>
                                                                <option value="transport">{{ ___('fees.transport_fee') }}</option>
                                                                <option value="library">{{ ___('fees.library_fee') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Generation Date Start -->
                                                    <div class="col-md-6">
                                                        <label for="generation_start_date" class="form-label">{{ ___('fees.generation_date_from') }}</label>
                                                        <input type="date" class="form-control" name="generation_start" id="generation_start_date">
                                                    </div>

                                                    <!-- Generation Date End -->
                                                    <div class="col-md-6">
                                                        <label for="generation_end_date" class="form-label">{{ ___('fees.generation_date_to') }}</label>
                                                        <input type="date" class="form-control" name="generation_end" id="generation_end_date">
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

                                    <!-- Upcoming Placeholder -->
                                    <div class="alert alert-info mt-3" role="alert">
                                        <i class="las la-clock me-2"></i>
                                        <strong>{{ ___('common.upcoming') }}</strong> - Backend implementation in progress
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Receipts Collapsible -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingReceipts">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReceipts" aria-expanded="false" aria-controls="collapseReceipts">
                                    <i class="las la-receipt me-2"></i> {{ ___('settings.receipts') }}
                                </button>
                            </h2>
                            <div id="collapseReceipts" class="accordion-collapse collapse" aria-labelledby="headingReceipts" data-bs-parent="#billingReportsAccordion">
                                <div class="accordion-body">

                                    <!-- Filter Card -->
                                    <div class="card">
                                        <div class="card-header align-items-center d-flex">
                                            <h4 class="card-title mb-0 flex-grow-1">{{ ___('settings.filter_options') }}</h4>
                                        </div>
                                        <div class="card-body">
                                            <form action="#" method="GET">
                                                <div class="row g-3">

                                                    <!-- Session -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="session" id="receipt_session">
                                                                <option value="">{{ ___('common.select_session') }}</option>
                                                                @foreach ($data['sessions'] as $session)
                                                                    <option value="{{ $session->id }}">{{ $session->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Receipt Number -->
                                                    <div class="col-md-3">
                                                        <label for="receipt_number" class="form-label">{{ ___('fees.receipt_number') }}</label>
                                                        <input type="text" class="form-control" name="receipt_number" id="receipt_number" placeholder="{{ ___('fees.enter_receipt_number') }}">
                                                    </div>

                                                    <!-- Payment Method -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="payment_method" id="payment_method">
                                                                <option value="">{{ ___('fees.payment_method') }}</option>
                                                                <option value="cash">{{ ___('fees.cash') }}</option>
                                                                <option value="bank">{{ ___('fees.bank_transfer') }}</option>
                                                                <option value="card">{{ ___('fees.credit_card') }}</option>
                                                                <option value="cheque">{{ ___('fees.cheque') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Date Range -->
                                                    <div class="col-md-3">
                                                        <!-- Placeholder for alignment -->
                                                    </div>

                                                    <!-- Start Date -->
                                                    <div class="col-md-6">
                                                        <label for="receipt_start_date" class="form-label">{{ ___('common.start_date') }}</label>
                                                        <input type="date" class="form-control" name="start_date" id="receipt_start_date">
                                                    </div>

                                                    <!-- End Date -->
                                                    <div class="col-md-6">
                                                        <label for="receipt_end_date" class="form-label">{{ ___('common.end_date') }}</label>
                                                        <input type="date" class="form-control" name="end_date" id="receipt_end_date">
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

                                    <!-- Upcoming Placeholder -->
                                    <div class="alert alert-info mt-3" role="alert">
                                        <i class="las la-clock me-2"></i>
                                        <strong>{{ ___('common.upcoming') }}</strong> - Backend implementation in progress
                                    </div>

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
        $(document).ready(function() {
            // Initialize nice-select
            if ($(".niceSelect").length) {
                $(".niceSelect").niceSelect();
            }

            // Paid Students Form Submission
            $('#paidStudentsForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                // Disable button and show loading
                submitBtn.prop('disabled', true).html('<i class="las la-spinner la-spin me-1"></i> Searching...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            // Populate results table
                            let tableBody = '';
                            if (response.data.length > 0) {
                                response.data.forEach(function(row) {
                                    tableBody += `
                                        <tr>
                                            <td>${row.payment_date || '-'}</td>
                                            <td>${row.journal || '-'}</td>
                                            <td>${row.student_name || '-'}</td>
                                            <td>${row.mobile || '-'}</td>
                                            <td class="text-end">${parseFloat(row.paid_amount || 0).toFixed(2)}</td>
                                            <td class="text-end">${parseFloat(row.deposit_used || 0).toFixed(2)}</td>
                                            <td class="text-end">${parseFloat(row.discount || 0).toFixed(2)}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                tableBody = '<tr><td colspan="7" class="text-center">No records found</td></tr>';
                            }

                            $('#paidStudentsTableBody').html(tableBody);

                            // Update summary
                            $('#totalPaidAmount').text(response.summary.total_paid_amount);
                            $('#totalDeposit').text(response.summary.total_deposit);
                            $('#totalDiscount').text(response.summary.total_discount);
                            $('#netTotal').text(response.summary.net_total);

                            // Show results section
                            $('#paidStudentsResults').slideDown();

                            // Scroll to results
                            $('html, body').animate({
                                scrollTop: $('#paidStudentsResults').offset().top - 100
                            }, 500);
                        } else {
                            alert('Error: ' + (response.error || 'Failed to fetch results'));
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred while processing your request.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                    },
                    complete: function() {
                        // Re-enable button
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Unpaid Students Form Submission
            $('#unpaidStudentsForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                // Disable button and show loading
                submitBtn.prop('disabled', true).html('<i class="las la-spinner la-spin me-1"></i> Searching...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            // Populate results table
                            let tableBody = '';
                            if (response.data.length > 0) {
                                response.data.forEach(function(row) {
                                    tableBody += `
                                        <tr>
                                            <td>${row.date || '-'}</td>
                                            <td>${row.name || '-'}</td>
                                            <td>${row.mobile || '-'}</td>
                                            <td>${row.grade || '-'}</td>
                                            <td>${row.class || '-'}</td>
                                            <td>${row.section || '-'}</td>
                                            <td class="text-end">${parseFloat(row.total_amount || 0).toFixed(2)}</td>
                                        </tr>
                                    `;
                                });
                            } else {
                                tableBody = '<tr><td colspan="7" class="text-center">No records found</td></tr>';
                            }

                            $('#unpaidStudentsTableBody').html(tableBody);

                            // Update summary
                            $('#totalOutstanding').text(response.summary.total_outstanding);

                            // Show results section
                            $('#unpaidStudentsResults').slideDown();

                            // Scroll to results
                            $('html, body').animate({
                                scrollTop: $('#unpaidStudentsResults').offset().top - 100
                            }, 500);
                        } else {
                            alert('Error: ' + (response.error || 'Failed to fetch results'));
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'An error occurred while processing your request.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        alert(errorMsg);
                    },
                    complete: function() {
                        // Re-enable button
                        submitBtn.prop('disabled', false).html(originalText);
                    }
                });
            });
        }); // End of document ready

        // Print function for Paid Students Report
        function printPaidStudentsReport() {
            var printContents = document.getElementById('paidStudentsPrintArea').innerHTML;
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
            printWindow.document.write('<html><head><title>Paid Students Report</title>');
            printWindow.document.write('<style>' + styles + '</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h2>Paid Students Report</h2>');
            printWindow.document.write(printContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

        // Export PDF function for Paid Students Report
        function exportPaidStudentsPDF() {
            // Get form data
            const form = document.getElementById('paidStudentsForm');
            const formData = new FormData(form);

            // Build query string
            const params = new URLSearchParams(formData);

            // Open PDF export URL in new tab
            window.open('{{ route("report-billing.export-paid-students-pdf") }}?' + params.toString(), '_blank');
        }

        // Print function for Unpaid Students Report
        function printUnpaidStudentsReport() {
            var printContents = document.getElementById('unpaidStudentsPrintArea').innerHTML;
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
            printWindow.document.write('<html><head><title>Unpaid Students Report</title>');
            printWindow.document.write('<style>' + styles + '</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h2>Unpaid Students Report</h2>');
            printWindow.document.write(printContents);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

        // Export PDF function for Unpaid Students Report
        function exportUnpaidStudentsPDF() {
            // Get form data
            const form = document.getElementById('unpaidStudentsForm');
            const formData = new FormData(form);

            // Build query string
            const params = new URLSearchParams(formData);

            // Open PDF export URL in new tab
            window.open('{{ route("report-billing.export-unpaid-students-pdf") }}?' + params.toString(), '_blank');
        }
    </script>
    @endpush
@endsection
