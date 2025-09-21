<div class="p-3">
    <!-- System Type Indicator -->
    <div class="alert alert-info d-flex justify-content-between align-items-center">
        <div>
            <i class="fa-solid fa-info-circle me-2"></i>
            @if($fees['system_type'] === 'service_based')
                <strong>{{ ___('fees.service_based_system') }}</strong> - {{ ___('fees.fees_calculated_from_services') }}
            @else
                <strong>{{ ___('fees.legacy_system') }}</strong> - {{ ___('fees.fees_calculated_from_groups') }}
            @endif
        </div>
        @if($fees['system_type'] === 'service_based')
            <button class="btn btn-sm btn-outline-primary" onclick="manageStudentServices({{ $data->id }})">
                <i class="fa-solid fa-cogs"></i> {{ ___('fees.manage_services') }}
            </button>
        @endif
    </div>

    <div class="row g-4">
        <!-- Overview Section -->
        <div class="col-12">
            <div class="card border shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ ___('student.Fees Overview') }}</h5>
                    @if($fees['system_type'] === 'service_based')
                        <span class="badge badge-success">{{ ___('fees.enhanced_system') }}</span>
                    @else  
                        <span class="badge badge-secondary">{{ ___('fees.legacy_system') }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($fees['system_type'] === 'service_based')
                        <!-- Service-Based Overview -->
                        <div class="row text-center">
                            @php $currency = setting('currency_symbol'); @endphp

                            <div class="col-md-2 col-6 mb-3">
                                <div class="fw-bold text-muted">{{ ___('student.Total Services') }}</div>
                                <div class="h6">{{ count($fees['services'] ?? []) }}</div>
                            </div>

                            <div class="col-md-2 col-6 mb-3">
                                <div class="fw-bold text-muted">{{ ___('student.Total Fees') }}</div>
                                <div class="h6">{{ $currency }} {{ number_format($fees['total_fees'] ?? 0, 2) }}</div>
                            </div>

                            <div class="col-md-2 col-6 mb-3">
                                <div class="fw-bold text-muted">{{ ___('student.Total Paid') }}</div>
                                <div class="h6">{{ $currency }} {{ number_format($fees['total_paid'] ?? 0, 2) }}</div>
                            </div>

                            <div class="col-md-2 col-6 mb-3">
                                <div class="fw-bold text-muted">{{ ___('student.Total Due') }}</div>
                                <div class="h6 text-danger">{{ $currency }} {{ number_format($fees['fees_due'] ?? 0, 2) }}</div>
                            </div>

                            <div class="col-md-2 col-6 mb-3">
                                <div class="fw-bold text-muted">{{ ___('student.Total Discounts') }}</div>
                                <div class="h6 text-success">{{ $currency }} {{ number_format($fees['total_discounts'] ?? 0, 2) }}</div>
                            </div>

                            <div class="col-md-2 col-6 mb-3">
                                <div class="fw-bold text-muted">{{ ___('student.Outstanding Services') }}</div>
                                <div class="h6 text-warning">{{ count($fees['outstanding_services'] ?? []) }}</div>
                            </div>
                        </div>

                        {{-- Prominent Payment Collection Button --}}
                        @if($fees['fees_due'] > 0 && hasPermission('fees_collect_update'))
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-center">
                                        <button type="button" class="btn ot-btn-primary shadow-sm px-4 py-2"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalCustomizeWidth"
                                                onclick="openFeeCollectionModal({{ $data->id }}, '{{ $data->first_name }} {{ $data->last_name }}')">
                                            <i class="fa-solid fa-credit-card me-2"></i>
                                            {{ ___('common.pay') }}
                                            <span class="ms-2 fw-bold">
                                                {{ $currency }} {{ number_format($fees['fees_due'], 2) }}
                                            </span>
                                        </button>
                                    </div>
                                    <div class="text-center mt-2">
                                        <small class="text-muted">
                                            <i class="fa-solid fa-info-circle me-1"></i>
                                            {{ ___('fees.redirect_to_payment_collection') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @elseif($fees['fees_due'] <= 0)
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="alert alert-success text-center">
                                        <i class="fa-solid fa-check-circle me-2"></i>
                                        <strong>{{ ___('fees.all_fees_paid') }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Legacy Overview -->
                        <div class="row text-center">
                            @php $currency = setting('currency_symbol'); @endphp

                            <div class="col-md-2 col-6 mb-3">
                                <div class="fw-bold text-muted">{{ ___('student.Total Fees') }}</div>
                                @php
                                    $totalFees = 0;
                                    foreach ($fees['fees_assigned'] ?? [] as $assignment) {
                                        $totalFees += $assignment->feesMaster->amount ?? 0;
                                    }
                                @endphp
                                <div class="h6">{{ $currency }} {{ number_format($totalFees, 2) }}</div>
                            </div>

                            <div class="col-md-2 col-6 mb-3">
                                <div class="fw-bold text-muted">{{ ___('student.Total Paid') }}</div>
                                @php
                                    $totalPaid = 0;
                                    foreach ($fees['fees_assigned'] ?? [] as $assignment) {
                                        if ($assignment->feesCollect && $assignment->feesCollect->isPaid()) {
                                            $totalPaid += $assignment->feesCollect->amount;
                                        }
                                    }
                                @endphp
                                <div class="h6">{{ $currency }} {{ number_format($totalPaid, 2) }}</div>
                            </div>

                            <div class="col-md-2 col-6 mb-3">
                                <div class="fw-bold text-muted">{{ ___('student.Total Due') }}</div>
                                <div class="h6 text-danger">{{ $currency }} {{ number_format($fees['fees_due'] ?? 0, 2) }}</div>
                            </div>

                            <div class="col-md-2 col-6 mb-3">
                                <div class="fw-bold text-muted">{{ ___('student.Total Discount') }}</div>
                                <div class="h6 text-success">{{ $currency }} {{ number_format($fees['total_discounts'] ?? 0, 2) }}</div>
                            </div>

                            <div class="col-md-2 col-6 mb-3">
                                <div class="fw-bold text-muted">{{ ___('student.Total Fine') }}</div>
                                <div class="h6 text-warning">{{ $currency }} {{ number_format(@$data->feesMasters->sum('fine_amount') ?? 0, 2) }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="col-12">
            <div class="card border shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        @if($fees['system_type'] === 'service_based')
                            {{ ___('fees.service_subscriptions') }}
                        @else
                            {{ ___('fees.fees_details') }}
                        @endif
                    </h5>
                    @if($fees['system_type'] === 'service_based')
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fa-solid fa-filter"></i> {{ ___('common.filter') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="filterServices('all')">{{ ___('common.all') }}</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterServices('mandatory')">{{ ___('fees.mandatory_only') }}</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterServices('optional')">{{ ___('fees.optional_only') }}</a></li>
                                <li><a class="dropdown-item" href="#" onclick="filterServices('overdue')">{{ ___('fees.overdue_only') }}</a></li>
                            </ul>
                        </div>
                    @endif
                </div>
                <div class="card-body table-responsive">
                    @if($fees['system_type'] === 'service_based')
                        {{-- Service-Based Table --}}
                        <table class="table table-bordered table-hover" id="services_table">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th>{{ ___('common.Si') }}</th>
                                    <th>{{ ___('fees.service_name') }}</th>
                                    <th>{{ ___('fees.category') }}</th>
                                    <th>{{ ___('fees.type') }}</th>
                                    <th>{{ ___('fees.amount') }} ({{ $currency }})</th>
                                    <th>{{ ___('fees.discount') }} ({{ $currency }})</th>
                                    <th>{{ ___('fees.final_amount') }} ({{ $currency }})</th>
                                    <th>{{ ___('common.status') }}</th>
                                    <th>{{ ___('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fees['services'] ?? [] as $key => $service)
                                    <tr class="service-row" data-service-type="{{ $service->feeType->is_mandatory_for_level ? 'mandatory' : 'optional' }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <strong>{{ $service->feeType->name }}</strong>
                                            @if($service->feeType->is_mandatory_for_level)
                                                <span class="badge badge-warning ms-1">{{ ___('fees.mandatory') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ ucfirst($service->feeType->category) }}</span>
                                        </td>
                                        <td>{{ ___('fees.service_subscription') }}</td>
                                        <td>{{ $currency }} {{ number_format($service->amount, 2) }}</td>
                                        <td>
                                            @if($service->discount_type)
                                                {{ $currency }} {{ number_format($service->amount - $service->final_amount, 2) }}
                                                <small class="text-muted d-block">{{ ucfirst($service->discount_type) }}: {{ $service->discount_value }}{{ $service->discount_type === 'percentage' ? '%' : '' }}</small>
                                            @else
                                                {{ $currency }} 0.00
                                            @endif
                                        </td>
                                        <td><strong>{{ $currency }} {{ number_format($service->final_amount, 2) }}</strong></td>
                                        <td>
                                            @if($service->is_active)
                                                <span class="badge bg-success">{{ ___('fees.active') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ___('fees.inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group-sm">
                                                @if($service->discount_type)
                                                    <button class="btn btn-sm btn-outline-warning" onclick="removeServiceDiscount({{ $service->id }})" title="{{ ___('fees.remove_discount') }}">
                                                        <i class="fa-solid fa-minus"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline-success" onclick="applyServiceDiscount({{ $service->id }})" title="{{ ___('fees.apply_discount') }}">
                                                        <i class="fa-solid fa-percentage"></i>
                                                    </button>
                                                @endif
                                                @if(!$service->feeType->is_mandatory_for_level)
                                                    <button class="btn btn-sm btn-outline-danger" onclick="unsubscribeService({{ $service->id }})" title="{{ ___('fees.unsubscribe') }}">
                                                        <i class="fa-solid fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                @if(empty($fees['services']))
                                    <tr>
                                        <td colspan="10" class="text-center text-muted">
                                            {{ ___('fees.no_services_subscribed') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        @if(!empty($fees['services_summary']['services_by_category']))
                            {{-- Service Summary by Category --}}
                            <div class="mt-4">
                                <h6>{{ ___('fees.services_by_category') }}</h6>
                                <div class="row">
                                    @foreach($fees['services_summary']['services_by_category'] as $category => $summary)
                                        <div class="col-md-3 mb-2">
                                            <div class="card border-left-primary">
                                                <div class="card-body p-2">
                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ ucfirst($category) }}</div>
                                                    <div class="h6 mb-0">{{ $summary['count'] }} {{ ___('fees.services') }}</div>
                                                    <small class="text-muted">{{ $currency }} {{ number_format($summary['total_amount'], 2) }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        {{-- Legacy System Table --}}
                        <table class="table table-bordered table-hover" id="students_table">
                            <thead class="thead-light">
                                <tr class="text-center">
                                    <th>{{ ___('common.Si') }}</th>
                                    <th>{{ ___('fees.group') }}</th>
                                    <th>{{ ___('fees.type') }}</th>
                                    <th>{{ ___('fees.amount') }} ({{ $currency }})</th>
                                    <th>{{ ___('fees.Discount') }} ({{ $currency }})</th>
                                    <th>{{ ___('tax.Tax') }} ({{ $currency }})</th>
                                    <th>{{ ___('fees.Payable') }} ({{ $currency }})</th>
                                    <th>{{ ___('common.status') }}</th>
                                    <th>{{ ___('fees.fine_type') }}</th>
                                    <th>{{ ___('fees.percentage') }}</th>
                                    <th>{{ ___('fees.Fine') }} ({{ $currency }})</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (@$fees['fees_assigned'] ?? [] as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ @$item->feesMaster->group->name }}</td>
                                        <td>{{ @$item->feesMaster->type->name }}</td>
                                        <td>
                                            {{ number_format(@$item->feesMaster->amount ?? 0, 2) }}
                                            @if (date('Y-m-d') > @$item->feesMaster->date && (!@$item->feesCollect || !@$item->feesCollect->isPaid()))
                                                <span class="text-danger">+{{ number_format(@$item->feesMaster->fine_amount ?? 0, 2) }}</span>
                                            @elseif(@$item->feesCollect && @$item->feesCollect->isPaid() && @$item->feesMaster->date < @$item->feesCollect->date)
                                                <span class="text-danger">+{{ number_format(@$item->feesMaster->fine_amount ?? 0, 2) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format(calculateDiscount(@$item->feesMaster->amount, @$item->feesDiscount->discount_percentage ?? 0), 2) }}</td>
                                        <td>{{ number_format(calculateTax(@$item->feesMaster->amount), 2) }}</td>
                                        <td>
                                            {{ number_format(@$item->feesMaster->amount + calculateTax(@$item->feesMaster->amount) - calculateDiscount(@$item->feesMaster->amount, @$item->feesDiscount->discount_percentage ?? 0), 2) }}
                                        </td>
                                        <td>
                                            @if (@$item->feesCollect && @$item->feesCollect->isPaid())
                                                <span class="badge bg-success">{{ ___('fees.Paid') }}</span>
                                            @elseif (@$item->feesCollect && @$item->feesCollect->isPending())
                                                <span class="badge bg-warning">{{ ___('fees.Generated - Pending Payment') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ ___('fees.Unpaid') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (@$item->fine_type == 0)
                                                <span class="badge bg-info">{{ ___('fees.none') }}</span>
                                            @elseif(@$item->fine_type == 1)
                                                <span class="badge bg-warning">{{ ___('fees.percentage') }}</span>
                                            @elseif(@$item->fine_type == 2)
                                                <span class="badge bg-warning">{{ ___('fees.fixed') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ @$item->feesMaster->percentage ?? 0 }}</td>
                                        <td>
                                            {{ number_format(@$item->feesMaster->fine_amount ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        @if($fees['system_type'] === 'service_based' && !empty($fees['monthly_fees_by_period']))
            <!-- Monthly Fee Charges Section -->
            <div class="col-12">
                <div class="card border shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-calendar-alt me-2"></i>
                            Monthly Fee Charges
                        </h5>
                        <span class="badge bg-info">
                            {{ count($fees['monthly_fees_by_period']) }} Billing Periods
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            This section shows individual fee charges generated monthly from your service subscriptions.
                        </div>

                        @php
                            $sortedPeriods = collect($fees['monthly_fees_by_period'])->sortKeysDesc();
                        @endphp

                        @foreach($sortedPeriods as $billingPeriod => $periodFees)
                            @php
                                $periodLabel = $billingPeriod !== 'unknown'
                                    ? \Carbon\Carbon::createFromFormat('Y-m', $billingPeriod)->format('F Y')
                                    : 'Unknown Period';
                                $periodTotal = $periodFees->sum('amount');
                                $periodPaid = $periodFees->where('payment_method', '!=', null)->sum('amount');
                                $periodDue = $periodTotal - $periodPaid;
                            @endphp

                            <div class="billing-period-section mb-4">
                                <!-- Period Header -->
                                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                    <div>
                                        <h6 class="mb-0">
                                            <i class="fa-solid fa-calendar me-2 text-primary"></i>
                                            {{ $periodLabel }}
                                        </h6>
                                        <small class="text-muted">{{ $periodFees->count() }} charges</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">{{ $currency }} {{ number_format($periodTotal, 2) }}</div>
                                        <div class="small">
                                            <span class="text-success">Paid: {{ $currency }}{{ number_format($periodPaid, 2) }}</span>
                                            @if($periodDue > 0)
                                                | <span class="text-danger">Due: {{ $currency }}{{ number_format($periodDue, 2) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Period Fees Table -->
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Service</th>
                                                <th>Amount ({{ $currency }})</th>
                                                <th>Discount ({{ $currency }})</th>
                                                <th>Status</th>
                                                <th>Payment Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($periodFees->sortBy('fee_type_id') as $fee)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $fee->feeType->name ?? 'Unknown Service' }}</strong>
                                                        <small class="d-block text-muted">{{ ucfirst($fee->feeType->category ?? 'N/A') }}</small>
                                                    </td>
                                                    <td>{{ $currency }} {{ number_format($fee->amount, 2) }}</td>
                                                    <td>
                                                        @if($fee->discount_applied > 0)
                                                            {{ $currency }} {{ number_format($fee->discount_applied, 2) }}
                                                        @else
                                                            {{ $currency }} 0.00
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($fee->payment_method)
                                                            <span class="badge bg-success">Paid</span>
                                                        @elseif($fee->generation_method)
                                                            <span class="badge bg-warning">Generated</span>
                                                        @else
                                                            <span class="badge bg-secondary">Pending</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($fee->payment_method && $fee->date)
                                                            {{ \Carbon\Carbon::parse($fee->date)->format('M d, Y') }}
                                                        @else
                                                            <span class="text-muted">Not paid</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal Placeholder -->
        <div id="view-modal">
            <div class="modal fade" id="modalCustomizeWidth" tabindex="-1" aria-labelledby="modalWidth" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    {{-- Modal content will be injected dynamically --}}
                </div>
            </div>
        </div>
    </div>
</div>

@if($fees['system_type'] === 'service_based')
<script>
// Service Management Functions
function manageStudentServices(studentId) {
    window.location.href = `/student-services/student/${studentId}/manage`;
}

function filterServices(filter) {
    const rows = document.querySelectorAll('.service-row');
    
    rows.forEach(row => {
        let show = true;
        
        switch(filter) {
            case 'mandatory':
                show = row.dataset.serviceType === 'mandatory';
                break;
            case 'optional':
                show = row.dataset.serviceType === 'optional';
                break;
            case 'overdue':
                show = row.dataset.overdue === '1';
                break;
            case 'all':
            default:
                show = true;
                break;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

function applyServiceDiscount(serviceId) {
    const discountType = prompt('{{ ___("fees.enter_discount_type") }} (percentage/fixed):');
    if (!discountType || !['percentage', 'fixed'].includes(discountType.toLowerCase())) {
        alert('{{ ___("fees.invalid_discount_type") }}');
        return;
    }
    
    const discountValue = prompt(`{{ ___("fees.enter_discount_value") }} (${discountType}):`);
    if (!discountValue || isNaN(discountValue) || discountValue < 0) {
        alert('{{ ___("fees.invalid_discount_value") }}');
        return;
    }
    
    const notes = prompt('{{ ___("common.notes") }} (optional):') || '';
    
    $.post(`/student-services/service/${serviceId}/discount`, {
        _token: '{{ csrf_token() }}',
        discount_type: discountType.toLowerCase(),
        discount_value: parseFloat(discountValue),
        notes: notes
    })
    .done(function(response) {
        if (response.success) {
            alert('{{ ___("fees.discount_applied_successfully") }}');
            location.reload();
        } else {
            alert(response.message || '{{ ___("common.error") }}');
        }
    })
    .fail(function() {
        alert('{{ ___("common.error") }}');
    });
}

function removeServiceDiscount(serviceId) {
    if (!confirm('{{ ___("fees.confirm_remove_discount") }}')) {
        return;
    }
    
    const reason = prompt('{{ ___("fees.reason_for_removing_discount") }}:') || '';
    
    $.ajax({
        url: `/student-services/service/${serviceId}/discount`,
        type: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}',
            reason: reason
        }
    })
    .done(function(response) {
        if (response.success) {
            alert('{{ ___("fees.discount_removed_successfully") }}');
            location.reload();
        } else {
            alert(response.message || '{{ ___("common.error") }}');
        }
    })
    .fail(function() {
        alert('{{ ___("common.error") }}');
    });
}

function unsubscribeService(serviceId) {
    if (!confirm('{{ ___("fees.confirm_unsubscribe_service") }}')) {
        return;
    }
    
    const reason = prompt('{{ ___("fees.reason_for_unsubscribing") }}:') || '';
    
    $.ajax({
        url: `/student-services/service/${serviceId}/unsubscribe`,
        type: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}',
            reason: reason
        }
    })
    .done(function(response) {
        if (response.success) {
            alert('{{ ___("fees.unsubscribed_successfully") }}');
            location.reload();
        } else {
            alert(response.message || '{{ ___("common.error") }}');
        }
    })
    .fail(function() {
        alert('{{ ___("common.error") }}');
    });
}
</script>
@endif
