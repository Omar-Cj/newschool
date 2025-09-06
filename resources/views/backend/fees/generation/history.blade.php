@extends('backend.master')

@section('title')
{{ $data['title'] }}
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">{{ ___('fees.generation_history') }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('fees-generation.index') }}">{{ ___('fees.fee_generation') }}</a></li>
                            <li class="breadcrumb-item active">{{ ___('fees.generation_history') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- End page title -->

        <!-- Generation History Table -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('fees.generation_history') }}</h4>
                    <a href="{{ route('fees-generation.index') }}" class="btn btn-lg ot-btn-primary">
                        <i class="fa-solid fa-arrow-left"></i> {{ ___('common.back') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered role-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('common.batch_id') }}</th>
                                    <th class="purchase">{{ ___('common.status') }}</th>
                                    <th class="purchase">{{ ___('fees.students') }}</th>
                                    <th class="purchase">{{ ___('fees.amount') }}</th>
                                    <th class="purchase">{{ ___('common.created_at') }}</th>
                                    <th class="purchase">{{ ___('fees.completed_at') }}</th>
                                    <th class="purchase">{{ ___('fees.created_by') }}</th>
                                    <th class="action">{{ ___('common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @forelse($data['generations'] as $key => $generation)
                                    <tr id="row_{{ $generation->id }}">
                                        <td class="serial">{{ ++$key }}</td>
                                        <td>
                                            <span class="badge-basic-primary-text">{{ $generation->batch_id }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match($generation->status) {
                                                    'completed' => 'badge-basic-success-text',
                                                    'processing' => 'badge-basic-warning-text',
                                                    'failed' => 'badge-basic-danger-text',
                                                    'cancelled' => 'badge-basic-secondary-text',
                                                    default => 'badge-basic-info-text'
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }}">{{ ucfirst($generation->status) }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="text-success fw-bold">{{ $generation->successful_students }}</span>
                                                <span class="mx-1">/</span>
                                                <span class="text-muted">{{ $generation->total_students }}</span>
                                                @if($generation->failed_students > 0)
                                                    <span class="mx-1">|</span>
                                                    <span class="text-danger">{{ $generation->failed_students }} {{ ___('common.failed') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-primary">{{ setting('currency_symbol') }} {{ number_format($generation->total_amount ?? 0, 2) }}</span>
                                        </td>
                                        <td>{{ $generation->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($generation->completed_at)
                                                {{ $generation->completed_at->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $generation->creator->name ?? 'Unknown' }}</td>
                                        <td class="action">
                                            <div class="dropdown dropdown-action">
                                                <button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('fees-generation.show', $generation->id) }}">
                                                            <span class="icon mr-8"><i class="fa-solid fa-eye"></i></span>
                                                            {{ ___('common.view_details') }}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center gray-color">
                                            <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary" width="100">
                                            <p class="mb-0 text-center">{{ ___('fees.no_generation_history') }}</p>
                                            <p class="mb-0 text-center text-muted">{{ ___('fees.no_generation_history_desc') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($data['generations']->hasPages())
                        <div class="d-flex justify-content-end mt-3">
                            {{ $data['generations']->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        // Add any additional JavaScript if needed for the history view
        console.log('Fee Generation History loaded');
    });
</script>
@endpush
