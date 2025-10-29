{{--
    Summary Tables Partial
    Reusable component for displaying report summaries
    Supports multiple summary types: exam gradebook, paid students, fee generation, fee generation & collection
--}}

@if(isset($summary) && !empty($summary))
    @php
        $summaryType = $summary['type'] ?? 'default';
    @endphp

    {{-- Fee Generation & Collection Summary (3-column layout) --}}
    @if($summaryType === 'fee_generation_collection' && isset($summary['sections']))
        <div class="summary-container mt-4 mb-4">
            <h5 class="mb-3 text-center font-weight-bold">Summary Report</h5>

            <div class="row">
                @foreach($summary['sections'] as $section)
                    <div class="col-md-4 mb-3">
                        <div class="card border">
                            <div class="card-header bg-light text-center py-2">
                                <h6 class="mb-0 font-weight-bold">{{ $section['title'] }}</h6>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered mb-0">
                                    <tbody>
                                        @foreach($section['rows'] as $row)
                                            <tr class="{{ $row['is_total'] ? 'table-active font-weight-bold' : '' }}">
                                                <td class="py-2 px-3" style="width: 60%;">
                                                    {{ $row['label'] }}
                                                </td>
                                                <td class="text-end py-2 px-3" style="width: 40%;">
                                                    ${{ number_format($row['value'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    {{-- Exam Gradebook Summary --}}
    @elseif($summaryType === 'exam_gradebook' && isset($summary['rows']))
        <div class="summary-container mt-4">
            <h5 class="mb-3">Summary - Total Marks by Exam</h5>
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Exam Name</th>
                                <th class="text-end">Total Marks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summary['rows'] as $row)
                                @php
                                    $isTotalRow = ($row['exam_name'] ?? '') === 'Total All Exams';
                                @endphp
                                <tr class="{{ $isTotalRow ? 'table-success font-weight-bold' : '' }}">
                                    <td>{{ $row['exam_name'] ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($row['total_marks'] ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    {{-- Financial Summary (Paid Students, Fee Generation) --}}
    @elseif($summaryType === 'financial' && isset($summary['rows']))
        <div class="summary-container mt-4">
            <h5 class="mb-3">Financial Summary</h5>
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-bordered">
                        <tbody>
                            @foreach($summary['rows'] as $row)
                                @php
                                    $isGrandTotal = in_array($row['metric'] ?? '', ['Grand Total', 'Total Invoices']);
                                @endphp
                                <tr class="{{ $isGrandTotal ? 'table-success font-weight-bold' : '' }}">
                                    <th class="text-end py-2 px-3" style="width: 50%;">
                                        {{ $row['metric'] ?? '-' }}:
                                    </th>
                                    <td class="text-end py-2 px-3" style="width: 50%;">
                                        ${{ number_format($row['value'] ?? 0, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    {{-- Count/Statistics Summary (Student Counts, etc.) --}}
    @elseif($summaryType === 'count' && isset($summary['rows']))
        <div class="summary-container mt-4">
            <h5 class="mb-3">Summary</h5>
            <div class="row">
                <div class="col-md-6 offset-md-6">
                    <table class="table table-bordered">
                        <tbody>
                            @foreach($summary['rows'] as $row)
                                <tr class="table-active font-weight-bold">
                                    <th class="text-end py-2 px-3" style="width: 50%;">
                                        {{ $row['metric'] ?? '-' }}:
                                    </th>
                                    <td class="text-end py-2 px-3" style="width: 50%;">
                                        {{ number_format($row['value'] ?? 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endif

{{-- Print-specific styles --}}
<style media="print">
    .summary-container {
        page-break-inside: avoid;
        margin-top: 30px;
    }

    .summary-container .card {
        border: 1px solid #000 !important;
        break-inside: avoid;
    }

    .summary-container .table {
        font-size: 11px;
    }

    .summary-container .font-weight-bold {
        font-weight: 700 !important;
    }
</style>

{{-- Screen styles --}}
<style>
    .summary-container .card-header {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .summary-container .table-active {
        background-color: #e9ecef;
    }

    .summary-container .font-weight-bold {
        font-weight: 700;
    }

    .summary-container .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
