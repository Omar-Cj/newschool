@extends('mainapp::layouts.backend.master')
@section('title')
    {{ @$data['title'] }}
@endsection

@push('style')
<style>
    /* Print styles */
    @media print {
        .sidebar, .header, .footer, .page-header, .breadcrumb,
        form, .btn, .card-header .d-flex.gap-2, .no-print,
        .ot_crm_summeryBox, .card-header {
            display: none !important;
        }
        .print-header { display: block !important; }
        .page-content { padding: 0 !important; margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .card-body { padding: 0 !important; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000 !important; padding: 8px !important; }
        .badge { border: 1px solid #000; }
    }
    @media screen {
        .print-header { display: none !important; }
    }
</style>
@endpush

@section('content')
    <div class="page-content">

        {{-- Print Header (visible only when printing) --}}
        <div class="print-header text-center mb-4">
            <img src="{{ @globalAsset(setting('dark_logo'), '154X38.webp') }}" alt="Logo" style="max-height: 60px;">
            <h2>{{ $data['title'] }}</h2>
            <p class="text-muted">{{ ___('mainapp_common.Generated') }}: {{ now()->format('Y-m-d H:i') }}</p>
        </div>

        {{-- breadcrumb Area Start --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('mainapp_common.home') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('mainapp_subscriptions.Reports') }}</li>
                        <li class="breadcrumb-item active">{{ ___('mainapp_subscriptions.Outstanding Payments') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area End --}}

        {{-- Summary Cards Start --}}
        <div class="row">
            {{-- Total Outstanding Card --}}
            <div class="col-xl-4 col-lg-4 col-md-6">
                <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                    <div class="icon">
                        <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/school.svg') }}" alt="total">
                    </div>
                    <div class="summeryContent">
                        <h4>{{ ___('mainapp_subscriptions.Total Outstanding') }}</h4>
                        <h1>{{ number_format(@$data['totalOutstanding'], 2) }}</h1>
                        <small class="text-danger">
                            <i class="las la-exclamation-circle"></i>
                            {{ ___('mainapp_subscriptions.Total Amount Due') }}
                        </small>
                    </div>
                </div>
            </div>

            {{-- Overdue Schools Card --}}
            <div class="col-xl-4 col-lg-4 col-md-6">
                <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                    <div class="icon">
                        <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/package.svg') }}" alt="overdue">
                    </div>
                    <div class="summeryContent">
                        <h4>{{ ___('mainapp_subscriptions.Overdue Schools') }}</h4>
                        <h1>{{ @$data['overdueCount'] }}</h1>
                        <small class="text-warning">
                            <i class="las la-clock"></i>
                            {{ ___('mainapp_subscriptions.Past Grace Period') }}
                        </small>
                    </div>
                </div>
            </div>

            {{-- In Grace Period Card --}}
            <div class="col-xl-4 col-lg-4 col-md-6">
                <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                    <div class="icon">
                        <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/feature.svg') }}" alt="grace">
                    </div>
                    <div class="summeryContent">
                        <h4>{{ ___('mainapp_subscriptions.In Grace Period') }}</h4>
                        <h1>{{ @$data['graceCount'] }}</h1>
                        <small class="text-info">
                            <i class="las la-info-circle"></i>
                            {{ ___('mainapp_subscriptions.Action Required Soon') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
        {{-- Summary Cards End --}}

        {{-- Filter Section Start --}}
        <div class="row">
            <div class="col-12">
                <form method="GET" action="{{ route('reports.outstanding-payments') }}">
                    <div class="card ot-card mb-24 position-relative z_1">
                        <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                            <h3 class="mb-0">{{ ___('mainapp_common.Filtering') }}</h3>

                            <div class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">

                                {{-- Urgency Level Selector --}}
                                <div class="single_large_selectBox">
                                    <select name="urgency" id="urgency" class="form-select ot-input">
                                        <option value="">{{ ___('mainapp_common.All') }} {{ ___('mainapp_subscriptions.Urgency Level') }}</option>
                                        @foreach($data['urgencyLevels'] as $level)
                                            <option value="{{ $level }}" {{ request('urgency') == $level ? 'selected' : '' }}>
                                                @if($level == 'critical')
                                                    {{ ___('mainapp_subscriptions.Critical') }}
                                                @elseif($level == 'grace')
                                                    {{ ___('mainapp_subscriptions.In Grace Period') }}
                                                @elseif($level == 'expiring')
                                                    {{ ___('mainapp_subscriptions.Expiring Soon') }}
                                                @else
                                                    {{ ___('mainapp_common.All') }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- School Selector --}}
                                <div class="single_large_selectBox">
                                    <select name="school_id" id="school_id" class="form-select ot-input">
                                        <option value="">{{ ___('mainapp_common.All') }} {{ ___('mainapp_schools.School') }}</option>
                                        @foreach($data['allSchools'] ?? [] as $school)
                                            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                                {{ $school->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Sort By Selector --}}
                                <div class="single_large_selectBox">
                                    <select name="sort_by" id="sort_by" class="form-select ot-input">
                                        <option value="urgency" {{ request('sort_by') == 'urgency' ? 'selected' : '' }}>{{ ___('mainapp_subscriptions.Urgency') }}</option>
                                        <option value="amount" {{ request('sort_by') == 'amount' ? 'selected' : '' }}>{{ ___('mainapp_common.Amount') }}</option>
                                        <option value="expiry_date" {{ request('sort_by') == 'expiry_date' ? 'selected' : '' }}>{{ ___('mainapp_subscriptions.Expiry Date') }}</option>
                                        <option value="school_name" {{ request('sort_by') == 'school_name' ? 'selected' : '' }}>{{ ___('mainapp_schools.School Name') }}</option>
                                    </select>
                                </div>

                                {{-- Search Button --}}
                                <button class="btn btn-lg ot-btn-primary" type="submit">
                                    <i class="fa-solid fa-search me-2"></i>
                                    {{ ___('mainapp_common.Filter') }}
                                </button>

                                {{-- Reset Button --}}
                                <a href="{{ route('reports.outstanding-payments') }}" class="btn btn-lg btn-secondary">
                                    <i class="fa-solid fa-rotate-right me-2"></i>
                                    {{ ___('mainapp_common.Reset') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        {{-- Filter Section End --}}

        {{-- Table Content Start --}}
        <div class="table-content table-basic">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('mainapp_subscriptions.Outstanding Payments Report') }}</h4>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary">{{ count($data['schools']) }} {{ ___('mainapp_subscriptions.Schools') }}</span>
                        <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()">
                            <i class="fa-solid fa-print me-1"></i>{{ ___('mainapp_common.Print') }}
                        </button>
                        <a href="{{ route('reports.outstanding-payments.export', ['format' => 'excel'] + request()->all()) }}" class="btn btn-sm btn-success">
                            <i class="fa-solid fa-file-excel me-1"></i>{{ ___('mainapp_common.Export to Excel') }}
                        </a>
                        <a href="{{ route('reports.outstanding-payments.export', ['format' => 'pdf'] + request()->all()) }}" class="btn btn-sm btn-danger">
                            <i class="fa-solid fa-file-pdf me-1"></i>{{ ___('mainapp_common.Export to PDF') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered outstanding-payments-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('mainapp_common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('mainapp_schools.School Name') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Package Name') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Contact Phone') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Contact Email') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Expiry Date') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Grace Period End') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Days Overdue') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Outstanding Amount') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Urgency Level') }}</th>
                                    <th class="action">{{ ___('mainapp_common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @forelse ($data['schools'] as $key => $school)
                                @php
                                    // Determine row color class based on urgency
                                    $rowClass = '';
                                    $urgencyBadge = '';
                                    $urgencyLabel = '';

                                    if ($school->urgency_level == 'Critical') {
                                        $rowClass = 'table-danger';
                                        $urgencyBadge = 'badge-basic-danger-text';
                                        $urgencyLabel = ___('mainapp_subscriptions.Critical');
                                    } elseif ($school->urgency_level == 'In Grace Period') {
                                        $rowClass = 'table-warning';
                                        $urgencyBadge = 'badge-basic-warning-text';
                                        $urgencyLabel = ___('mainapp_subscriptions.In Grace Period');
                                    } elseif ($school->urgency_level == 'Expiring Soon') {
                                        $rowClass = 'table-info';
                                        $urgencyBadge = 'badge-basic-info-text';
                                        $urgencyLabel = ___('mainapp_subscriptions.Expiring Soon');
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}" id="row_{{ $school->school_id }}">
                                    <td class="serial">{{ $key + 1 }}</td>
                                    <td title="{{ @$school->school_name }}">
                                        <strong>{{ Str::limit(@$school->school_name, 30) ?? ___('mainapp_common.N/A') }}</strong>
                                        <br><small class="text-muted">{{ @$school->sub_domain_key ?? '' }}</small>
                                    </td>
                                    <td>{{ @$school->package_name ?? ___('mainapp_common.N/A') }}</td>
                                    <td>{{ @$school->school_phone ?? ___('mainapp_common.N/A') }}</td>
                                    <td title="{{ @$school->school_email ?? '' }}">{{ Str::limit(@$school->school_email ?? '', 25) ?: ___('mainapp_common.N/A') }}</td>
                                    <td>
                                        <strong>{{ dateFormat(@$school->expiry_date) ?? ___('mainapp_common.N/A') }}</strong>
                                        @if(@$school->expiry_date)
                                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($school->expiry_date)->diffForHumans() }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if(@$school->grace_expiry_date)
                                            {{ dateFormat($school->grace_expiry_date) }}
                                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($school->grace_expiry_date)->diffForHumans() }}</small>
                                        @else
                                            {{ ___('mainapp_common.N/A') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(@$school->days_overdue > 0)
                                            <span class="badge bg-danger">{{ $school->days_overdue }} {{ ___('mainapp_common.days') }} {{ ___('mainapp_common.overdue') }}</span>
                                        @elseif(@$school->days_overdue < 0)
                                            <span class="badge bg-warning text-dark">{{ abs($school->days_overdue) }} {{ ___('mainapp_common.days') }} {{ ___('mainapp_common.left') }}</span>
                                        @else
                                            <span class="badge bg-info">{{ ___('mainapp_common.Today') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-danger">{{ number_format(@$school->outstanding_amount, 2) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge {{ $urgencyBadge }}">{{ $urgencyLabel }}</span>
                                    </td>
                                    <td class="action">
                                        <div class="dropdown dropdown-action">
                                            <button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa-solid fa-ellipsis"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item quick-contact" href="javascript:void(0)"
                                                       data-school="{{ $school->school_name }}"
                                                       data-phone="{{ @$school->contact_phone ?? '' }}"
                                                       data-email="{{ @$school->contact_email ?? '' }}"
                                                       data-amount="{{ number_format($school->outstanding_amount, 2) }}">
                                                        <span class="icon mr-8"><i class="fa-solid fa-phone text-primary"></i></span>
                                                        {{ ___('mainapp_common.Quick Contact') }}
                                                    </a>
                                                </li>
                                                @if(!empty($school->contact_phone))
                                                    <li>
                                                        <a class="dropdown-item" href="tel:{{ $school->contact_phone }}" target="_blank">
                                                            <span class="icon mr-8"><i class="fa-solid fa-phone-volume text-success"></i></span>
                                                            {{ ___('mainapp_common.Call Now') }}
                                                        </a>
                                                    </li>
                                                @endif
                                                @if(!empty($school->contact_email))
                                                    <li>
                                                        <a class="dropdown-item" href="mailto:{{ $school->contact_email }}" target="_blank">
                                                            <span class="icon mr-8"><i class="fa-solid fa-envelope text-info"></i></span>
                                                            {{ ___('mainapp_common.Send Email') }}
                                                        </a>
                                                    </li>
                                                @endif
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('subscription-payments.history', $school->school_id) }}">
                                                        <span class="icon mr-8"><i class="fa-solid fa-history"></i></span>
                                                        {{ ___('mainapp_common.Payment History') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('subscription.edit', $school->subscription_id ?? $school->school_id) }}">
                                                        <span class="icon mr-8"><i class="fa-solid fa-eye"></i></span>
                                                        {{ ___('mainapp_common.View Details') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item send-reminder" href="javascript:void(0)"
                                                       data-school-id="{{ $school->school_id }}"
                                                       data-school-name="{{ $school->school_name }}">
                                                        <span class="icon mr-8"><i class="fa-solid fa-bell text-warning"></i></span>
                                                        {{ ___('mainapp_common.Send Reminder') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center gray-color">
                                        <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary" width="100">
                                        <p class="mb-0 text-center">{{ ___('mainapp_subscriptions.No outstanding payments found') }}</p>
                                        <p class="mb-0 text-center text-secondary font-size-90">
                                            {{ ___('mainapp_subscriptions.All schools have active subscriptions or no overdue payments') }}
                                        </p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        {{-- Table Content End --}}
    </div>

    {{-- Quick Contact Modal --}}
    <div class="modal fade" id="quickContactModal" tabindex="-1" aria-labelledby="quickContactModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quickContactModalLabel">{{ ___('mainapp_common.Quick Contact') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ ___('mainapp_schools.School') }}</label>
                        <p class="form-control-plaintext" id="contactSchoolName"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ ___('mainapp_common.Outstanding Amount') }}</label>
                        <p class="form-control-plaintext text-danger fw-bold" id="contactAmount"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ ___('mainapp_common.Phone') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="contactPhone" readonly>
                            <a href="#" id="callLink" class="btn btn-outline-primary" target="_blank">
                                <i class="fa-solid fa-phone"></i> {{ ___('mainapp_common.Call') }}
                            </a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ ___('mainapp_common.Email') }}</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="contactEmail" readonly>
                            <a href="#" id="emailLink" class="btn btn-outline-primary" target="_blank">
                                <i class="fa-solid fa-envelope"></i> {{ ___('mainapp_common.Email') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ ___('mainapp_common.Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Send Reminder Modal --}}
    <div class="modal fade" id="sendReminderModal" tabindex="-1" aria-labelledby="sendReminderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="sendReminderForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="sendReminderModalLabel">{{ ___('mainapp_common.Send Payment Reminder') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ ___('mainapp_subscriptions.Are you sure you want to send a payment reminder to') }} <strong id="reminderSchoolName"></strong>?</p>
                        <div class="mb-3">
                            <label for="reminder_method" class="form-label">{{ ___('mainapp_common.Reminder Method') }}</label>
                            <select name="reminder_method" id="reminder_method" class="form-select" required>
                                <option value="email">{{ ___('mainapp_common.Email') }}</option>
                                <option value="sms">{{ ___('mainapp_common.SMS') }}</option>
                                <option value="both">{{ ___('mainapp_common.Email & SMS') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reminder_message" class="form-label">{{ ___('mainapp_common.Additional Message') }}</label>
                            <textarea name="reminder_message" id="reminder_message" class="form-control" rows="3" placeholder="{{ ___('mainapp_subscriptions.Optional custom message') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ ___('mainapp_common.Cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="confirmSendReminder">
                            <i class="fa-solid fa-paper-plane me-2"></i>{{ ___('mainapp_common.Send Reminder') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    /* Urgency level row colors */
    .table-danger {
        background-color: rgba(220, 53, 69, 0.1) !important;
    }
    .table-warning {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }
    .table-info {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }

    /* Hover effect */
    .outstanding-payments-table tbody tr:hover {
        opacity: 0.9;
    }

    /* Badge styling */
    .badge-basic-danger-text {
        background-color: #dc3545;
        color: white;
        padding: 0.35em 0.65em;
        border-radius: 0.25rem;
        font-size: 0.875em;
    }

    .badge-basic-warning-text {
        background-color: #ffc107;
        color: #000;
        padding: 0.35em 0.65em;
        border-radius: 0.25rem;
        font-size: 0.875em;
    }

    .badge-basic-success-text {
        background-color: #198754;
        color: white;
        padding: 0.35em 0.65em;
        border-radius: 0.25rem;
        font-size: 0.875em;
    }

    .badge-basic-info-text {
        background-color: #0dcaf0;
        color: #000;
        padding: 0.35em 0.65em;
        border-radius: 0.25rem;
        font-size: 0.875em;
    }
</style>
@endpush

@push('script')
<script>
    $(document).ready(function() {
        // Quick Contact Modal
        $('.quick-contact').on('click', function() {
            const schoolName = $(this).data('school');
            const phone = $(this).data('phone');
            const email = $(this).data('email');
            const amount = $(this).data('amount');

            $('#contactSchoolName').text(schoolName);
            $('#contactAmount').text(amount);
            $('#contactPhone').val(phone || '{{ ___("mainapp_common.N/A") }}');
            $('#contactEmail').val(email || '{{ ___("mainapp_common.N/A") }}');

            // Update links
            $('#callLink').attr('href', phone ? 'tel:' + phone : '#');
            $('#emailLink').attr('href', email ? 'mailto:' + email : '#');

            $('#quickContactModal').modal('show');
        });

        // Send Reminder Modal
        $('.send-reminder').on('click', function() {
            const schoolId = $(this).data('school-id');
            const schoolName = $(this).data('school-name');

            $('#reminderSchoolName').text(schoolName);
            $('#sendReminderForm').attr('action', `/reports/outstanding-payments/${schoolId}/send-reminder`);
            $('#sendReminderModal').modal('show');
        });

        // Submit reminder form
        $('#sendReminderForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = $('#confirmSendReminder');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-2"></i>{{ ___("mainapp_common.Sending...") }}');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#sendReminderModal').modal('hide');
                    alert(response.message || '{{ ___("mainapp_subscriptions.Reminder sent successfully") }}');
                    form[0].reset();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON?.message || '{{ ___("mainapp_common.An error occurred") }}');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Auto-submit filter on urgency change
        $('#urgency').on('change', function() {
            $(this).closest('form').submit();
        });

        // Print functionality
        window.printReport = function() {
            window.print();
        };
    });
</script>
@endpush
