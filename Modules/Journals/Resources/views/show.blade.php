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
                        @if (hasPermission('journal_update'))
                            <a href="{{ route('journals.edit', $data['journal']->id) }}"
                               class="btn btn-lg ot-btn-primary btn-right-icon radius-md">
                                <span><i class="fa-solid fa-pen-to-square"></i></span>
                                <span class="">{{ ___('common.edit') }}</span>
                            </a>
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
                                                {{ currency() }}{{ number_format($data['journal']->feesCollects()->sum('paid_amount'), 2) }}
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
                                        @if (hasPermission('journal_update'))
                                            <a href="{{ route('journals.edit', $data['journal']->id) }}"
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-pen-to-square me-1"></i>
                                                {{ ___('common.edit_journal') }}
                                            </a>
                                        @endif

                                        @if (hasPermission('fees_collection_read'))
                                            <a href="{{ route('fees.collect.index') }}?journal_id={{ $data['journal']->id }}"
                                               class="btn btn-outline-success btn-sm">
                                                <i class="fa-solid fa-money-bill-wave me-1"></i>
                                                {{ ___('journals.view_collections') }}
                                            </a>
                                        @endif

                                        @if (hasPermission('journal_delete'))
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
                                        <a href="{{ route('fees.collect.index') }}?journal_id={{ $data['journal']->id }}"
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
                                                    <td>{{ currency() }}{{ number_format($collection->paid_amount, 2) }}</td>
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
                </div>
            </div>
        </div>
    </div>

    @include('backend.partials.delete-ajax')
@endsection

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