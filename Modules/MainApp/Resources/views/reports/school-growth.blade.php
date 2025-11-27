@extends('mainapp::layouts.backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection

@push('style')
<style>
    /* Print styles */
    @media print {
        /* Hide non-essential elements */
        .sidebar, .header, .footer, .page-header, .breadcrumb,
        form, .btn, .card-header .d-flex.gap-2, .no-print, #revenueChart,
        .ot_crm_summeryBox, .card-header {
            display: none !important;
        }

        /* Page setup */
        @page {
            size: landscape;
            margin: 15mm 15mm 0 15mm;
        }

        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            font-size: 9pt !important;
            color: #333;
            line-height: 1.3;
            padding-bottom: 15mm;
        }

        /* SOLUTION FOR ISSUE #2: Add container padding */
        .page-content {
            padding: 0 20mm !important; /* Add horizontal padding */
            margin: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .card {
            border: none !important;
            box-shadow: none !important;
            page-break-inside: avoid;
        }

        .card-body {
            padding: 0 !important;
            overflow: visible !important;
        }

        /* Print Header - Professional Design */
        .print-header {
            display: flex !important;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 3px solid #00C48C;
        }

        .print-header img {
            max-height: 70px;
            max-width: 100px;
        }

        .print-header h2 {
            flex: 1;
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .print-header p {
            font-size: 8pt;
            color: #7f8c8d;
            margin: 0;
        }

        /* Metadata Section - NEW */
        .print-metadata {
            background-color: #ecf0f1;
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 10px;
            border: 1px solid #bdc3c7;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .print-metadata-item {
            display: flex;
            padding: 2px 0;
            border-bottom: none;
        }

        .print-metadata-label {
            font-weight: 600;
            color: #00C48C;
            min-width: 100px;
            flex-shrink: 0;
            font-size: 8pt;
        }

        .print-metadata-value {
            color: #34495e;
            font-weight: 500;
            font-size: 8pt;
        }

        /* Table Styling - Refined */
        .table-responsive {
            overflow: visible !important;
            page-break-before: avoid !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 7pt !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            table-layout: fixed !important;
            page-break-before: avoid !important;
        }

        thead {
            display: table-header-group !important;
            background-color: #00C48C !important;
        }

        th {
            padding: 8px 8px !important;
            text-align: left;
            font-weight: 600 !important;
            color: #ffffff !important;
            background-color: #00C48C !important;
            border: 1px solid #00B87A !important;
            font-size: 7pt !important;
            text-transform: uppercase !important;
            letter-spacing: 0.3px !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        td {
            padding: 7px !important;
            border: 1px solid #d5d8dc !important;
            font-size: 7pt !important;
            color: #2c3e50;
            vertical-align: top !important;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* Prevent table rows from breaking across pages */
        tr {
            page-break-inside: avoid !important;
        }

        tfoot {
            display: table-footer-group !important;
        }

        /* Summary Section - Enhanced */
        .summary-section {
            display: block !important;
            margin: 5px 0;
            padding: 5px 0;
            background: #f8f9fa;
            border-radius: 5px;
            page-break-inside: avoid;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-cell {
            display: table-cell;
            padding: 8px;
            text-align: center;
            border-right: 1px solid #dee2e6;
        }

        .summary-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
        }

        .summary-value {
            font-size: 18pt;
            font-weight: bold;
            color: #333;
        }

        /* Badge styling for print */
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            border: 1px solid #000;
        }

        .badge-success {
            background: #28a745 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
        }

        .badge-danger {
            background: #dc3545 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
        }

        .badge-warning {
            background: #ffc107 !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact !important;
        }

        .badge-info {
            background: #17a2b8 !important;
            color: white !important;
            -webkit-print-color-adjust: exact !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        /* Print Summary Section - Matches PDF design */
        .print-summary-section {
            display: block !important;
            margin: 5px 0 !important;
            padding: 8px !important;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 5px;
            page-break-inside: avoid;
            page-break-after: avoid !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .print-summary-title {
            font-size: 10pt;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px !important;
        }

        .print-summary-grid {
            display: table;
            width: 100%;
        }

        .print-summary-row {
            display: table-row;
        }

        .print-summary-cell {
            display: table-cell;
            padding: 8px;
            width: 25%;
            text-align: center;
            border-right: 1px solid #dee2e6;
        }

        .print-summary-cell:last-child {
            border-right: none;
        }

        .print-summary-label {
            font-size: 8pt;
            color: #666;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .print-summary-value {
            font-size: 18pt;
            font-weight: bold;
            color: #333;
        }

        .print-summary-value.success {
            color: #00C48C;
        }

        .print-summary-value.danger {
            color: #dc3545;
        }

        .print-growth-indicator {
            font-size: 8pt;
            margin-top: 5px;
        }

        .print-growth-indicator.positive {
            color: #28a745;
        }

        .print-growth-indicator.negative {
            color: #dc3545;
        }
    }
    @media screen {
        .print-header { display: none !important; }
        .print-metadata { display: none !important; }
        .print-summary-section { display: none !important; }
    }
</style>
@endpush

@section('content')
<div class="page-content">

    {{-- Print Header (visible only when printing) --}}
    <div class="print-header">
        @if(setting('dark_logo'))
        <img src="{{ @globalAsset(setting('dark_logo'), '154X38.webp') }}" alt="Logo">
        @endif
        <h2>{{ $data['title'] }}</h2>
    </div>

    {{-- Print Metadata Section (visible only when printing) --}}
    <div class="print-metadata">
        <div class="print-metadata-item">
            <span class="print-metadata-label">{{ ___('mainapp_common.Generated') }}:</span>
            <span class="print-metadata-value">{{ now()->format('F d, Y - h:i A') }}</span>
        </div>
        <div class="print-metadata-item">
            <span class="print-metadata-label">{{ ___('mainapp_common.Generated By') }}:</span>
            <span class="print-metadata-value">{{ auth()->user()->name ?? 'System' }}</span>
        </div>
        <div class="print-metadata-item">
            <span class="print-metadata-label">{{ ___('common.Period From') }}:</span>
            <span class="print-metadata-value">{{ request('date_from', $data['dateFrom'] ?? now()->subMonths(12)->format('Y-m-d')) }}</span>
        </div>
        <div class="print-metadata-item">
            <span class="print-metadata-label">{{ ___('common.Period To') }}:</span>
            <span class="print-metadata-value">{{ request('date_to', $data['dateTo'] ?? now()->format('Y-m-d')) }}</span>
        </div>
    </div>

    {{-- Print Summary Section (visible only when printing) --}}
    <div class="print-summary-section">
        <div class="print-summary-title">GROWTH METRICS OVERVIEW</div>
        <div class="print-summary-grid">
            <div class="print-summary-row">
                <div class="print-summary-cell">
                    <div class="print-summary-label">Total Schools</div>
                    <div class="print-summary-value success">{{ number_format($data['totalSchools'] ?? 0) }}</div>
                    @if(isset($data['schoolsGrowthRate']))
                        <div class="print-growth-indicator {{ $data['schoolsGrowthRate'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $data['schoolsGrowthRate'] >= 0 ? '↑' : '↓' }}
                            {{ abs($data['schoolsGrowthRate']) }}% growth
                        </div>
                    @endif
                </div>
                <div class="print-summary-cell">
                    <div class="print-summary-label">New This Month</div>
                    <div class="print-summary-value">{{ number_format($data['newSchoolsThisMonth'] ?? 0) }}</div>
                    <div class="print-growth-indicator">Current Month</div>
                </div>
                <div class="print-summary-cell">
                    <div class="print-summary-label">Total Branches</div>
                    <div class="print-summary-value">{{ number_format($data['totalBranches'] ?? 0) }}</div>
                    @if(isset($data['branchesGrowthRate']))
                        <div class="print-growth-indicator {{ $data['branchesGrowthRate'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $data['branchesGrowthRate'] >= 0 ? '↑' : '↓' }}
                            {{ abs($data['branchesGrowthRate']) }}% growth
                        </div>
                    @endif
                </div>
                <div class="print-summary-cell">
                    <div class="print-summary-label">Total Students</div>
                    <div class="print-summary-value">{{ number_format($data['totalStudents'] ?? 0) }}</div>
                    @if(isset($data['studentsGrowthRate']))
                        <div class="print-growth-indicator {{ $data['studentsGrowthRate'] >= 0 ? 'positive' : 'negative' }}">
                            {{ $data['studentsGrowthRate'] >= 0 ? '↑' : '↓' }}
                            {{ abs($data['studentsGrowthRate']) }}% growth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Breadcrumb Area Start --}}
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('mainapp_common.home') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.Reports') }}</a></li>
                    <li class="breadcrumb-item active">{{ $data['title'] }}</li>
                </ol>
            </div>
        </div>
    </div>
    {{-- Breadcrumb Area End --}}

    {{-- Filter Section Start --}}
    <div class="row">
        <div class="col-12">
            <form action="{{ route('reports.school-growth') }}" method="GET" id="filterForm">
                <div class="card ot-card mb-24 position-relative z_1">
                    <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                        <h3 class="mb-0">{{ ___('mainapp_common.Filtering') }}</h3>

                        <div class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">

                            {{-- Date Range Input --}}
                            <div class="single_large_selectBox">
                                <input type="text"
                                    class="form-control ot-input"
                                    name="date_range"
                                    id="dateRangePicker"
                                    placeholder="{{ ___('common.Select Date Range') }}"
                                    value="{{ request('date_range') }}"
                                    autocomplete="off">
                            </div>

                            {{-- View Type Selector --}}
                            <div class="single_large_selectBox">
                                <select name="view_type" class="form-select ot-input">
                                    <option value="monthly" {{ request('view_type') == 'monthly' ? 'selected' : '' }}>
                                        {{ ___('common.Monthly') }}
                                    </option>
                                    <option value="yearly" {{ request('view_type') == 'yearly' ? 'selected' : '' }}>
                                        {{ ___('common.Yearly') }}
                                    </option>
                                </select>
                            </div>

                            {{-- Search Button --}}
                            <button class="btn btn-lg ot-btn-primary" type="submit">
                                <i class="fa-solid fa-search me-2"></i>
                                {{ ___('mainapp_common.Search') }}
                            </button>

                            {{-- Reset Button --}}
                            <a href="{{ route('reports.school-growth') }}" class="btn btn-lg btn-secondary">
                                <i class="fa-solid fa-rotate-right me-2"></i>
                                {{ ___('common.Reset') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- Filter Section End --}}

    {{-- Summary Cards Start --}}
    <div class="row">
        {{-- Total Schools Card --}}
        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                <div class="icon">
                    <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/school.svg') }}" alt="schools">
                </div>
                <div class="summeryContent">
                    <h4>{{ ___('common.Total Schools') }}</h4>
                    <h1>{{ number_format($data['totalSchools'] ?? 0) }}</h1>
                    @if(isset($data['schoolsGrowthRate']))
                        <small class="text-{{ $data['schoolsGrowthRate'] >= 0 ? 'success' : 'danger' }}">
                            <i class="las la-arrow-{{ $data['schoolsGrowthRate'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ abs($data['schoolsGrowthRate']) }}% {{ ___('common.growth') }}
                        </small>
                    @endif
                </div>
            </div>
        </div>

        {{-- New Schools This Month Card --}}
        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                <div class="icon">
                    <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/package.svg') }}" alt="new schools">
                </div>
                <div class="summeryContent">
                    <h4>{{ ___('common.New Schools This Month') }}</h4>
                    <h1>{{ number_format($data['newSchoolsThisMonth'] ?? 0) }}</h1>
                    <small class="text-success">
                        <i class="las la-plus-circle"></i>
                        {{ ___('common.Current Month') }}
                    </small>
                </div>
            </div>
        </div>

        {{-- Total Branches Card --}}
        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                <div class="icon">
                    <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/feature.svg') }}" alt="branches">
                </div>
                <div class="summeryContent">
                    <h4>{{ ___('common.Total Branches') }}</h4>
                    <h1>{{ number_format($data['totalBranches'] ?? 0) }}</h1>
                    @if(isset($data['branchesGrowthRate']))
                        <small class="text-{{ $data['branchesGrowthRate'] >= 0 ? 'success' : 'danger' }}">
                            <i class="las la-arrow-{{ $data['branchesGrowthRate'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ abs($data['branchesGrowthRate']) }}% {{ ___('common.growth') }}
                        </small>
                    @endif
                </div>
            </div>
        </div>

        {{-- Total Students Card --}}
        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="ot_crm_summeryBox d-flex align-items-center mb-24">
                <div class="icon">
                    <img class="img-fluid" src="{{ asset('backend/assets/images/dashboard/faq.svg') }}" alt="students">
                </div>
                <div class="summeryContent">
                    <h4>{{ ___('common.Total Students') }}</h4>
                    <h1>{{ number_format($data['totalStudents'] ?? 0) }}</h1>
                    @if(isset($data['studentsGrowthRate']))
                        <small class="text-{{ $data['studentsGrowthRate'] >= 0 ? 'success' : 'danger' }}">
                            <i class="las la-arrow-{{ $data['studentsGrowthRate'] >= 0 ? 'up' : 'down' }}"></i>
                            {{ abs($data['studentsGrowthRate']) }}% {{ ___('common.growth') }}
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- Summary Cards End --}}

    {{-- Growth Chart Start --}}
    <div class="row">
        <div class="col-12">
            <div class="ot_crm_summeryBox mb-24">
                <div class="card-header d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <h4 class="mb-0 me-3">{{ ___('common.Growth Trends') }}</h4>
                    <div class="d-flex gap-2 flex-wrap mt-2 mt-md-0">
                        {{-- Print Button --}}
                        <button type="button" class="btn btn-sm btn-secondary" onclick="window.print()">
                            <i class="fa-solid fa-print me-1"></i>
                            <span class="d-none d-sm-inline">{{ ___('mainapp_common.Print') }}</span>
                        </button>
                        {{-- Export Buttons --}}
                        <button type="button" class="btn btn-sm btn-success" onclick="exportToExcel()">
                            <i class="fa-solid fa-file-excel me-1"></i>
                            <span class="d-none d-sm-inline">{{ ___('common.Export to Excel') }}</span>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="exportToPDF()">
                            <i class="fa-solid fa-file-pdf me-1"></i>
                            <span class="d-none d-sm-inline">{{ ___('common.Export to PDF') }}</span>
                        </button>
                    </div>
                </div>
                <div id="growthChart"></div>
            </div>
        </div>
    </div>
    {{-- Growth Chart End --}}

    {{-- Data Table Start --}}
    <div class="table-content table-basic mt-20">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">{{ ___('common.Growth Data') }}</h4>
                <span class="badge badge-primary">
                    {{ ___('common.Total Records') }}: {{ count($data['growthData'] ?? []) }}
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="growthDataTable">
                        <thead class="thead">
                            <tr>
                                <th class="serial">{{ ___('mainapp_common.sr_no') }}</th>
                                <th>{{ ___('common.Period') }}</th>
                                <th class="text-center">{{ ___('common.New Schools') }}</th>
                                <th class="text-center">{{ ___('common.New Branches') }}</th>
                                <th class="text-center">{{ ___('common.New Students') }}</th>
                                <th class="text-center">{{ ___('common.Growth Rate') }} (%)</th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                            @forelse ($data['growthData'] ?? [] as $key => $row)
                            <tr>
                                <td class="serial">{{ $key + 1 }}</td>
                                <td>
                                    <strong>{{ $row['period'] ?? 'N/A' }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-basic-success-text">
                                        {{ number_format($row['new_schools'] ?? 0) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-basic-info-text">
                                        {{ number_format($row['new_branches'] ?? 0) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-basic-primary-text">
                                        {{ number_format($row['new_students'] ?? 0) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $growthRate = $row['growth_rate'] ?? 0;
                                        $isPositive = $growthRate >= 0;
                                    @endphp
                                    <span class="badge badge-basic-{{ $isPositive ? 'success' : 'danger' }}-text">
                                        <i class="las la-arrow-{{ $isPositive ? 'up' : 'down' }}"></i>
                                        {{ number_format(abs($growthRate), 2) }}%
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center gray-color">
                                    <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary" width="100">
                                    <p class="mb-0 text-center">{{ ___('mainapp_common.no_data_available') }}</p>
                                    <p class="mb-0 text-center text-secondary font-size-90">
                                        {{ ___('common.Please adjust your filters to view growth data') }}
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
    {{-- Data Table End --}}

</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {

        // Initialize date range picker
        if ($('#dateRangePicker').length) {
            $('#dateRangePicker').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD'
                },
                ranges: {
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Last 3 Months': [moment().subtract(3, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                    'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                }
            });

            $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
            });

            $('#dateRangePicker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        }

        // Growth Trends Chart
        @php
            $chartData = $data['chartData'] ?? [
                'periods' => [],
                'schools' => [],
                'branches' => [],
                'students' => []
            ];
        @endphp

        var growthChartOptions = {
            chart: {
                height: 380,
                width: "100%",
                type: "line",
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: false,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: false,
                        reset: true
                    }
                },
                zoom: {
                    enabled: true,
                    type: 'x',
                    autoScaleYaxis: true
                }
            },
            series: [
                {
                    name: "{{ ___('common.Schools') }}",
                    data: {!! json_encode($chartData['schools'] ?? []) !!}
                },
                {
                    name: "{{ ___('common.Branches') }}",
                    data: {!! json_encode($chartData['branches'] ?? []) !!}
                },
                {
                    name: "{{ ___('common.Students') }}",
                    data: {!! json_encode($chartData['students'] ?? []) !!}
                }
            ],
            stroke: {
                width: [3, 3, 3],
                curve: 'smooth'
            },
            xaxis: {
                categories: {!! json_encode($chartData['periods'] ?? []) !!},
                labels: {
                    rotate: -45,
                    rotateAlways: false
                }
            },
            yaxis: {
                title: {
                    text: "{{ ___('common.Count') }}"
                }
            },
            grid: {
                borderColor: '#EFEFEF',
            },
            fill: {
                opacity: 1
            },
            colors: ['#5669FF', '#4ECDC4', '#FFD93D'],
            markers: {
                size: 5,
                hover: {
                    size: 7
                }
            },
            legend: {
                show: true,
                position: 'bottom',
                horizontalAlign: 'center'
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val) {
                        return val.toLocaleString();
                    }
                }
            }
        };

        if($("#growthChart").length) {
            var growthChart = new ApexCharts(document.querySelector("#growthChart"), growthChartOptions);
            growthChart.render();
        }

    });

    // Export to Excel function
    function exportToExcel() {
        var params = new URLSearchParams(window.location.search);
        window.location.href = "{{ route('reports.school-growth.export', ['format' => 'excel']) }}?" + params.toString();
    }

    // Export to PDF function
    function exportToPDF() {
        var params = new URLSearchParams(window.location.search);
        window.location.href = "{{ route('reports.school-growth.export', ['format' => 'pdf']) }}?" + params.toString();
    }
</script>
@endpush
