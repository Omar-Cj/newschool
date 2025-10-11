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
                                            <form action="#" method="GET">
                                                <div class="row g-3">

                                                    <!-- Session -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="session" id="paid_session">
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
                                                            <select class="nice-select niceSelect bordered_style wide" name="class" id="paid_class">
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
                                                            <select class="nice-select niceSelect bordered_style wide" name="section" id="paid_section">
                                                                <option value="">{{ ___('common.select_section') }}</option>
                                                                @foreach ($data['sections'] as $section)
                                                                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Payment Status -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="payment_status" id="payment_status">
                                                                <option value="">{{ ___('common.payment_status') }}</option>
                                                                <option value="full">{{ ___('fees.fully_paid') }}</option>
                                                                <option value="partial">{{ ___('fees.partially_paid') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Start Date -->
                                                    <div class="col-md-6">
                                                        <label for="paid_start_date" class="form-label">{{ ___('common.start_date') }}</label>
                                                        <input type="date" class="form-control" name="start_date" id="paid_start_date">
                                                    </div>

                                                    <!-- End Date -->
                                                    <div class="col-md-6">
                                                        <label for="paid_end_date" class="form-label">{{ ___('common.end_date') }}</label>
                                                        <input type="date" class="form-control" name="end_date" id="paid_end_date">
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
                                            <form action="#" method="GET">
                                                <div class="row g-3">

                                                    <!-- Session -->
                                                    <div class="col-md-3">
                                                        <div class="single_large_selectBox">
                                                            <select class="nice-select niceSelect bordered_style wide" name="session" id="unpaid_session">
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
                                                            <select class="nice-select niceSelect bordered_style wide" name="class" id="unpaid_class">
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
                                                            <select class="nice-select niceSelect bordered_style wide" name="section" id="unpaid_section">
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
                                                            <select class="nice-select niceSelect bordered_style wide" name="fee_type" id="fee_type">
                                                                <option value="">{{ ___('fees.fee_type') }}</option>
                                                                <option value="tuition">{{ ___('fees.tuition_fee') }}</option>
                                                                <option value="transport">{{ ___('fees.transport_fee') }}</option>
                                                                <option value="library">{{ ___('fees.library_fee') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- Due Date Start -->
                                                    <div class="col-md-6">
                                                        <label for="unpaid_due_start" class="form-label">{{ ___('fees.due_date_from') }}</label>
                                                        <input type="date" class="form-control" name="due_start" id="unpaid_due_start">
                                                    </div>

                                                    <!-- Due Date End -->
                                                    <div class="col-md-6">
                                                        <label for="unpaid_due_end" class="form-label">{{ ___('fees.due_date_to') }}</label>
                                                        <input type="date" class="form-control" name="due_end" id="unpaid_due_end">
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
        // Initialize nice-select
        $(document).ready(function() {
            if ($(".niceSelect").length) {
                $(".niceSelect").niceSelect();
            }
        });
    </script>
    @endpush
@endsection
