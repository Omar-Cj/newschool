@extends('mainapp::layouts.backend.master')

@section('title')
{{ ___('mainapp_dashboard.Dashboard') }}
@endsection

@section('content')
<div class="page-content">

    {{-- Enhanced Metric Cards --}}
    <div class="row">
        {{-- Total Revenue Card --}}
        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                <div class="icon">
                    <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/package.svg') }}" alt="revenue">
                </div>
                <div class="summeryContent">
                    <h4>{{ ___('common.Total Revenue') }}</h4>
                    <h1>${{ number_format($data['metrics']['total_revenue']['value'] ?? 0, 2) }}</h1>
                    @php
                        $revenueTrend = is_numeric($data['metrics']['total_revenue']['trend'] ?? 0)
                            ? (float) $data['metrics']['total_revenue']['trend']
                            : 0;
                    @endphp
                    @if($revenueTrend != 0)
                        <small class="text-{{ $revenueTrend >= 0 ? 'success' : 'danger' }}">
                            <i class="las la-arrow-{{ $revenueTrend >= 0 ? 'up' : 'down' }}"></i>
                            {{ number_format(abs($revenueTrend), 1) }}% vs last month
                        </small>
                    @endif
                </div>
            </div>
        </div>

        {{-- Active Subscriptions Card --}}
        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                <div class="icon">
                    <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/school.svg') }}" alt="subscriptions">
                </div>
                <div class="summeryContent">
                    <h4>{{ ___('common.Active Subscriptions') }}</h4>
                    <h1>{{ $data['metrics']['active_subscriptions']['value'] ?? 0 }}</h1>
                    @php
                        $subscriptionsTrend = is_numeric($data['metrics']['active_subscriptions']['trend'] ?? 0)
                            ? (float) $data['metrics']['active_subscriptions']['trend']
                            : 0;
                    @endphp
                    @if($subscriptionsTrend != 0)
                        <small class="text-{{ $subscriptionsTrend >= 0 ? 'success' : 'danger' }}">
                            <i class="las la-arrow-{{ $subscriptionsTrend >= 0 ? 'up' : 'down' }}"></i>
                            {{ number_format(abs($subscriptionsTrend), 1) }}% vs last month
                        </small>
                    @endif
                </div>
            </div>
        </div>

        {{-- Outstanding Payments Card --}}
        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                <div class="icon">
                    <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/faq.svg') }}" alt="outstanding">
                </div>
                <div class="summeryContent">
                    <h4>{{ ___('common.Outstanding Payments') }}</h4>
                    <h1>${{ number_format($data['metrics']['outstanding_payments']['amount'] ?? 0, 2) }}</h1>
                    <small class="text-muted">{{ $data['metrics']['outstanding_payments']['count'] ?? 0 }} schools</small>
                </div>
            </div>
        </div>

        {{-- School Growth Card --}}
        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                <div class="icon">
                    <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/feature.svg') }}" alt="growth">
                </div>
                <div class="summeryContent">
                    <h4>{{ ___('common.Total Schools') }}</h4>
                    <h1>{{ $data['totalSchool'] }}</h1>
                    @if(isset($data['metrics']['school_growth']['new_this_month']))
                        <small class="text-success">
                            <i class="las la-plus-circle"></i>
                            {{ $data['metrics']['school_growth']['new_this_month'] }} new this month
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row">
        {{-- Revenue Trends Chart --}}
        <div class="col-xl-8">
            <div class="ot_crm_summeryBox mb-24">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('mainapp_dashboard.Revenue') }} {{ ___('common.Trends') }}</h4>
                </div>
                <div id="revenueChart"></div>
            </div>
        </div>

        {{-- Package Distribution Chart --}}
        <div class="col-xl-4">
            <div class="ot_crm_summeryBox mb-24">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('common.Package Distribution') }}</h4>
                </div>
                <div id="packageChart"></div>
            </div>
        </div>
    </div>

    {{-- Data Tables Row --}}
    <div class="row">
        {{-- Recent Payments Table --}}
        <div class="col-xl-6">
            <div class="ot_crm_summeryBox mb-24">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('common.Recent Payments') }}</h4>
                    <a href="{{ route('subscription-payments.index') }}" class="btn btn-sm btn-primary">
                        {{ ___('common.View All') }}
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ ___('common.School') }}</th>
                                <th>{{ ___('common.Amount') }}</th>
                                <th>{{ ___('common.Date') }}</th>
                                <th>{{ ___('common.Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['recentPayments'] ?? [] as $payment)
                                <tr>
                                    <td>{{ $payment['school_name'] ?? 'N/A' }}</td>
                                    <td>${{ number_format($payment['amount'] ?? 0, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($payment['payment_date'])->format('M d, Y') }}</td>
                                    <td>
                                        @if($payment['status_code'] == 1)
                                            <span class="badge badge-basic-success-text">{{ ___('common.Approved') }}</span>
                                        @elseif($payment['status_code'] == 2)
                                            <span class="badge badge-basic-danger-text">{{ ___('common.Rejected') }}</span>
                                        @else
                                            <span class="badge badge-basic-warning-text">{{ ___('common.Pending') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">{{ ___('common.No recent payments') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Schools Near Expiry Table --}}
        <div class="col-xl-6">
            <div class="ot_crm_summeryBox mb-24">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('common.Subscriptions Expiring Soon') }}</h4>
                    <a href="{{ route('reports.outstanding-payments') }}" class="btn btn-sm btn-warning">
                        {{ ___('common.View All') }}
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ ___('common.School') }}</th>
                                <th>{{ ___('common.Package') }}</th>
                                <th>{{ ___('common.Expires In') }}</th>
                                <th>{{ ___('common.Status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['schoolsNearExpiry'] ?? [] as $school)
                                <tr>
                                    <td>{{ $school['school_name'] ?? 'N/A' }}</td>
                                    <td>{{ $school['package_name'] ?? 'N/A' }}</td>
                                    <td>
                                        @php
                                            $daysOverdue = (int) ($school['days_overdue'] ?? 0);
                                            $urgency = $school['urgency_level'] ?? 'low';
                                        @endphp
                                        <span class="text-{{ $urgency == 'critical' ? 'danger' : ($urgency == 'high' ? 'warning' : 'info') }}">
                                            @if($daysOverdue > 0)
                                                {{ $daysOverdue }} {{ ___('common.days overdue') }}
                                            @else
                                                {{ abs($daysOverdue) }} {{ ___('common.days left') }}
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if($urgency == 'critical')
                                            <span class="badge badge-basic-danger-text">{{ ___('common.Expired') }}</span>
                                        @elseif($urgency == 'high')
                                            <span class="badge badge-basic-warning-text">{{ ___('common.Expiring Soon') }}</span>
                                        @else
                                            <span class="badge badge-basic-info-text">{{ ___('common.Active') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">{{ ___('common.No schools expiring soon') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Hidden inputs for legacy chart compatibility --}}
<input type="hidden" id="active-school" value="{{ $data['activeSchools'] }}">
<input type="hidden" id="inactive-school" value="{{ $data['inactiveSchools'] }}">

@endsection

@push('script')
<script>
    $(document).ready(function() {

        // Revenue Trends Chart
        var revenueOptions = {
            chart: {
                height: 380,
                width: "100%",
                type: "line",
                toolbar: {
                    show: true
                }
            },
            series: [
                {
                    name: "{{ ___('common.Revenue') }}",
                    data: [ <?php echo implode(', ', $data['incomes']); ?> ]
                }
            ],
            stroke: {
                width: 3,
                curve: 'smooth'
            },
            xaxis: {
                categories: [ <?php echo implode(', ', $data['months']); ?> ],
            },
            grid: {
                borderColor: '#EFEFEF',
            },
            fill: {
                opacity: 1
            },
            colors: ['#5669FF'],
            markers: {
                size: 5,
                hover: {
                    size: 7
                }
            }
        };

        if($("#revenueChart").length) {
            var revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
            revenueChart.render();
        }

        // Package Distribution Donut Chart
        @php
            $packages = $data['packageDistribution']['details'] ?? [];
            $packageNames = array_column($packages, 'package_name');
            $packageCounts = array_column($packages, 'school_count');
        @endphp

        var packageOptions = {
            chart: {
                height: 380,
                type: "donut"
            },
            series: [ <?php echo !empty($packageCounts) ? implode(', ', $packageCounts) : '0'; ?> ],
            labels: [ <?php echo !empty($packageNames) ? '"' . implode('", "', $packageNames) . '"' : '"No Data"'; ?> ],
            colors: ['#5669FF', '#FF6B6B', '#4ECDC4', '#FFD93D', '#95E1D3'],
            legend: {
                show: true,
                position: 'bottom'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(0) + '%'
                }
            }
        };

        if($("#packageChart").length) {
            var packageChart = new ApexCharts(document.querySelector("#packageChart"), packageOptions);
            packageChart.render();
        }

    });
</script>
@endpush
