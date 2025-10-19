@extends('backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection

@section('content')
    <div class="page-content">

        {{-- breadcrumb Area Start --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('journals.index') }}">{{ ___('journals.journals') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area End --}}

        <div class="col-12">
            <div class="card ot-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h3 class="card-title">{{ $data['title'] }}</h3>
                    <div class="d-flex gap-2 flex-wrap">
                        @if (hasPermission('journal_update') && $data['journal']->status == 'active')
                            <a href="{{ route('journals.edit', $data['journal']->id) }}"
                               class="btn btn-lg ot-btn-primary btn-right-icon radius-md">
                                <span><i class="fa-solid fa-pen-to-square"></i></span>
                                <span class="">{{ ___('common.edit') }}</span>
                            </a>
                        @endif
                        @if (hasPermission('journal_update') && $data['journal']->status == 'active')
                            <button type="button" class="btn btn-lg ot-btn-warning btn-right-icon radius-md close-journal-btn"
                                    data-id="{{ $data['journal']->id }}">
                                <span><i class="fa-solid fa-lock"></i></span>
                                <span class="">{{ ___('journals.close_journal') }}</span>
                            </button>
                        @endif
                        @if (auth()->user()->role_id === 1 && $data['journal']->status == 'inactive')
                            <button type="button" class="btn btn-lg ot-btn-success btn-right-icon radius-md open-journal-btn"
                                    data-id="{{ $data['journal']->id }}">
                                <span><i class="fa-solid fa-lock-open"></i></span>
                                <span class="">{{ ___('journals.open_journal') }}</span>
                            </button>
                        @endif
                        <a href="{{ route('journals.index') }}"
                           class="btn btn-lg ot-btn-primary-outline btn-right-icon radius-md">
                            <span><i class="fa-solid fa-arrow-left"></i></span>
                            <span class="">{{ ___('common.back') }}</span>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Journal Information -->
                        <div class="col-md-8">
                            <div class="journal-details">
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{ ___('journals.name') }}:</label>
                                            <div class="detail-value">
                                                <h5 class="text-primary mb-0">{{ $data['journal']->name }}</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{ ___('journals.branch') }}:</label>
                                            <div class="detail-value">
                                                <span class="badge badge-info fs-6">{{ is_object($data['journal']->branch) ? $data['journal']->branch->name : $data['journal']->branch }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{ ___('common.status') }}:</label>
                                            <div class="detail-value">
                                                @if ($data['journal']->status == 'active')
                                                    <span class="badge badge-success fs-6">{{ ___('common.active') }}</span>
                                                @else
                                                    <span class="badge badge-danger fs-6">{{ ___('common.inactive') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{ ___('journals.display_name') }}:</label>
                                            <div class="detail-value">
                                                <span class="text-muted">{{ $data['journal']->display_name }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($data['journal']->description)
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <div class="detail-item">
                                            <label class="detail-label">{{ ___('journals.description') }}:</label>
                                            <div class="detail-value">
                                                <p class="text-muted mb-0">{{ $data['journal']->description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{ ___('common.created_at') }}:</label>
                                            <div class="detail-value">
                                                <span class="text-muted">{{ dateFormat($data['journal']->created_at) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">{{ ___('common.updated_at') }}:</label>
                                            <div class="detail-value">
                                                <span class="text-muted">{{ dateFormat($data['journal']->updated_at) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Journal Statistics -->
                        <div class="col-md-4">
                            <div class="card border-light bg-light">
                                <div class="card-header bg-transparent">
                                    <h6 class="card-title mb-0">{{ ___('journals.journal_statistics') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="stats-item mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="stats-label">{{ ___('journals.total_fees_collections') }}:</span>
                                            <span class="stats-value badge badge-primary">
                                                {{ $data['journal']->feesCollects()->count() }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="stats-item mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="stats-label">{{ ___('journals.total_amount_collected') }}:</span>
                                            <span class="stats-value text-success fw-bold">
                                                {{ Setting('currency_symbol') }}{{ number_format($data['journal']->feesCollects()->sum('total_paid'), 2) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="stats-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="stats-label">{{ ___('journals.last_transaction') }}:</span>
                                            <span class="stats-value text-muted">
                                                @if($data['journal']->feesCollects()->latest()->first())
                                                    {{ dateFormat($data['journal']->feesCollects()->latest()->first()->created_at) }}
                                                @else
                                                    {{ ___('common.no_data') }}
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card border-light bg-light mt-3">
                                <div class="card-header bg-transparent">
                                    <h6 class="card-title mb-0">{{ ___('common.quick_actions') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        @if (hasPermission('journal_update') && $data['journal']->status == 'active')
                                            <a href="{{ route('journals.edit', $data['journal']->id) }}"
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-pen-to-square me-1"></i>
                                                {{ ___('common.edit_journal') }}
                                            </a>
                                        @endif

                                        @if (hasPermission('journal_update') && $data['journal']->status == 'active')
                                            <button type="button"
                                                    class="btn btn-outline-warning btn-sm close-journal-btn"
                                                    data-id="{{ $data['journal']->id }}">
                                                <i class="fa-solid fa-lock me-1"></i>
                                                {{ ___('journals.close_journal') }}
                                            </button>
                                        @endif

                                        @if (auth()->user()->role_id === 1 && $data['journal']->status == 'inactive')
                                            <button type="button"
                                                    class="btn btn-outline-success btn-sm open-journal-btn"
                                                    data-id="{{ $data['journal']->id }}">
                                                <i class="fa-solid fa-lock-open me-1"></i>
                                                {{ ___('journals.open_journal') }}
                                            </button>
                                        @endif

                                        @if (hasPermission('fees_collection_read'))
                                            <a href="{{ route('fees-collect.index') }}?journal_id={{ $data['journal']->id }}"
                                               class="btn btn-outline-success btn-sm">
                                                <i class="fa-solid fa-money-bill-wave me-1"></i>
                                                {{ ___('journals.view_collections') }}
                                            </a>
                                        @endif

                                        @if (hasPermission('journal_delete') && $data['journal']->status == 'active')
                                            <button type="button"
                                                    class="btn btn-outline-danger btn-sm delete_data"
                                                    data-href="{{ route('journals.destroy', $data['journal']->id) }}">
                                                <i class="fa-solid fa-trash-can me-1"></i>
                                                {{ ___('common.delete_journal') }}
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Fee Collections -->
                    @if($data['journal']->feesCollects()->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-light">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="card-title mb-0">{{ ___('journals.recent_fee_collections') }}</h6>
                                    @if (hasPermission('fees_collection_read'))
                                        <a href="{{ route('fees-collect.index') }}?journal_id={{ $data['journal']->id }}"
                                           class="btn btn-sm btn-outline-primary">
                                            {{ ___('common.view_all') }}
                                        </a>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>{{ ___('common.date') }}</th>
                                                    <th>{{ ___('student_info.student') }}</th>
                                                    <th>{{ ___('fees.amount') }}</th>
                                                    <th>{{ ___('fees.payment_method') }}</th>
                                                    <th>{{ ___('common.status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data['journal']->feesCollects()->latest()->limit(5)->get() as $collection)
                                                <tr>
                                                    <td>{{ dateFormat($collection->created_at) }}</td>
                                                    <td>
                                                        @if($collection->student)
                                                            {{ $collection->student->name }}
                                                        @else
                                                            <span class="text-muted">{{ ___('common.not_available') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ Setting('currency_symbol') }}{{ number_format($collection->total_paid, 2) }}</td>
                                                    <td>
                                                        <span class="badge badge-secondary">{{ $collection->payment_method ?? ___('common.not_specified') }}</span>
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-success">{{ ___('fees.paid') }}</span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Audit Trail -->
                    @if(auth()->user()->role_id === 1 && $data['journal']->auditLogs()->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-light">
                                <div class="card-header">
                                    <h6 class="card-title mb-0">{{ ___('journals.audit_trail') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>{{ ___('common.date_time') }}</th>
                                                    <th>{{ ___('journals.action') }}</th>
                                                    <th>{{ ___('journals.performed_by') }}</th>
                                                    <th>{{ ___('journals.notes') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data['journal']->auditLogs as $log)
                                                <tr>
                                                    <td>{{ dateFormat($log->performed_at) }}</td>
                                                    <td>
                                                        @if($log->action === 'opened')
                                                            <span class="badge badge-success">
                                                                <i class="fa-solid fa-lock-open me-1"></i>
                                                                {{ ___('journals.journal_opened') }}
                                                            </span>
                                                        @else
                                                            <span class="badge badge-warning">
                                                                <i class="fa-solid fa-lock me-1"></i>
                                                                {{ ___('journals.journal_closed') }}
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($log->performedBy)
                                                            {{ $log->performedBy->name }}
                                                            <small class="text-muted">({{ $log->performedBy->email }})</small>
                                                        @else
                                                            <span class="text-muted">{{ ___('common.not_available') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($log->notes)
                                                            {{ $log->notes }}
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
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
    </div>

    @include('backend.partials.delete-ajax')
@endsection

@push('script')
<script>
$(document).ready(function() {
    // Close journal button handler
    $('.close-journal-btn').on('click', function() {
        const journalId = $(this).data('id');
        const closeUrl = '{{ route("journals.close", ":id") }}'.replace(':id', journalId);

        Swal.fire({
            title: '{{ ___("journals.close_journal") }}',
            text: '{{ ___("journals.close_journal_confirmation") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ ___("common.yes_close") }}',
            cancelButtonText: '{{ ___("common.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: closeUrl,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: response[1] || 'success',
                            title: response[2] || '{{ ___("alert.success") }}',
                            text: response[0] || '{{ ___("journals.journal_closed_successfully") }}',
                            confirmButtonText: response[3] || '{{ ___("alert.OK") }}'
                        }).then(() => {
                            window.location.href = '{{ route("journals.index") }}';
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = '{{ ___("alert.something_went_wrong") }}';
                        if (xhr.responseJSON) {
                            errorMessage = xhr.responseJSON[0] || xhr.responseJSON.message || errorMessage;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: '{{ ___("alert.oops") }}',
                            text: errorMessage
                        });
                    }
                });
            }
        });
    });

    // Open journal button handler
    $('.open-journal-btn').on('click', function() {
        const journalId = $(this).data('id');
        const openUrl = '{{ route("journals.open", ":id") }}'.replace(':id', journalId);

        Swal.fire({
            title: '{{ ___("journals.open_journal") }}',
            text: '{{ ___("journals.open_journal_confirmation") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ ___("common.yes_open") }}',
            cancelButtonText: '{{ ___("common.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: openUrl,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: response[1] || 'success',
                            title: response[2] || '{{ ___("alert.success") }}',
                            text: response[0] || '{{ ___("journals.journal_opened_successfully") }}',
                            confirmButtonText: response[3] || '{{ ___("alert.OK") }}'
                        }).then(() => {
                            window.location.href = '{{ route("journals.index") }}';
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = '{{ ___("alert.something_went_wrong") }}';
                        if (xhr.responseJSON) {
                            errorMessage = xhr.responseJSON[0] || xhr.responseJSON.message || errorMessage;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: '{{ ___("alert.oops") }}',
                            text: errorMessage
                        });
                    }
                });
            }
        });
    });

    // Delete journal button handler
    $('.delete_data').on('click', function(e) {
        e.preventDefault();
        const deleteUrl = $(this).data('href');

        Swal.fire({
            title: '{{ ___("alert.are_you_sure") }}',
            text: '{{ ___("alert.you_wont_be_able_to_revert_this") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ ___("alert.yes_delete_it") }}',
            cancelButtonText: '{{ ___("common.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: response[1] || 'success',
                            title: response[2] || '{{ ___("alert.deleted") }}',
                            text: response[0] || '{{ ___("alert.record_deleted_successfully") }}',
                            confirmButtonText: response[3] || '{{ ___("alert.OK") }}'
                        }).then(() => {
                            window.location.href = '{{ route("journals.index") }}';
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = '{{ ___("alert.something_went_wrong_please_try_again") }}';
                        if (xhr.responseJSON) {
                            errorMessage = xhr.responseJSON[0] || xhr.responseJSON.message || errorMessage;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: '{{ ___("alert.oops") }}',
                            text: errorMessage
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush

@section('style')
<style>
    .detail-item {
        margin-bottom: 15px;
    }
    .detail-label {
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 5px;
        display: block;
    }
    .detail-value {
        color: #495057;
    }
    .stats-item {
        padding: 8px 0;
        border-bottom: 1px solid #e9ecef;
    }
    .stats-item:last-child {
        border-bottom: none;
    }
    .stats-label {
        font-size: 0.875rem;
        color: #6c757d;
    }
    .stats-value {
        font-size: 0.875rem;
    }
</style>
@endsection