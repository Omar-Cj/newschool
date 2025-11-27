@extends('mainapp::layouts.backend.master')
@section('title')
    {{ @$data['title'] }}
@endsection
@section('content')
    <div class="page-content">

        {{-- bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('mainapp_common.home') }}</a></li>
                        <li class="breadcrumb-item">{{ ___('mainapp_subscriptions.Subscription List') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        {{-- Quick Filters Start --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('subscription.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">{{ ___('mainapp_common.Quick Filters') }}</label>
                            <select name="filter" class="form-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>{{ ___('mainapp_common.All Subscriptions') }}</option>
                                <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>{{ ___('mainapp_common.Active') }}</option>
                                <option value="expiring" {{ request('filter') == 'expiring' ? 'selected' : '' }}>{{ ___('mainapp_common.Expiring Soon') }} (30 days)</option>
                                <option value="grace" {{ request('filter') == 'grace' ? 'selected' : '' }}>{{ ___('mainapp_common.In Grace Period') }}</option>
                                <option value="expired" {{ request('filter') == 'expired' ? 'selected' : '' }}>{{ ___('mainapp_common.Expired') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ ___('mainapp_schools.School') }}</label>
                            <select name="school_id" class="form-select" onchange="this.form.submit()">
                                <option value="">{{ ___('mainapp_common.All Schools') }}</option>
                                @if(isset($data['schools']))
                                    @foreach($data['schools'] as $school)
                                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                            {{ $school->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ ___('mainapp_subscriptions.Package') }}</label>
                            <select name="package_id" class="form-select" onchange="this.form.submit()">
                                <option value="">{{ ___('mainapp_common.All Packages') }}</option>
                                @if(isset($data['packages']))
                                    @foreach($data['packages'] as $package)
                                        <option value="{{ $package->id }}" {{ request('package_id') == $package->id ? 'selected' : '' }}>
                                            {{ $package->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">{{ ___('mainapp_common.Filter') }}</button>
                            <a href="{{ route('subscription.index') }}" class="btn btn-secondary">{{ ___('mainapp_common.Reset') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        {{-- Quick Filters End --}}

        <!--  table content start -->
        <div class="table-content table-basic mt-20">
            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('mainapp_subscriptions.Subscription List') }}</h4>
                    {{-- Add button hidden: subscriptions are auto-created during school creation --}}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered subscription-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('mainapp_common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('mainapp_schools.School') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Package') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Branches') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Total Price') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Purchase Date') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Date of Expire') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Grace Period') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Trx ID') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.status') }}</th>
                                    <th class="purchase">{{ ___('mainapp_subscriptions.Payment status') }}</th>
                                    <th class="action">{{ ___('mainapp_common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @forelse ($data['subscriptions'] as $key => $row)
                                @php
                                    $expiryDate = $row->expiry_date ? \Carbon\Carbon::parse($row->expiry_date) : null;
                                    $graceExpiryDate = $row->grace_expiry_date ? \Carbon\Carbon::parse($row->grace_expiry_date) : null;
                                    $now = \Carbon\Carbon::now();
                                    // Cast to integer to avoid decimal display
                                    $daysUntilExpiry = $expiryDate ? (int)$now->diffInDays($expiryDate, false) : null;
                                    $hoursUntilExpiry = $expiryDate ? $now->diffInHours($expiryDate, false) : null;
                                    $isExpiring = $daysUntilExpiry !== null && $daysUntilExpiry <= 30 && $daysUntilExpiry > 0;
                                    $isInGracePeriod = $expiryDate && $graceExpiryDate && $now->greaterThan($expiryDate) && $now->lessThanOrEqualTo($graceExpiryDate);
                                    $isExpired = $graceExpiryDate && $now->greaterThan($graceExpiryDate);

                                    $rowClass = '';
                                    if ($isExpired) {
                                        $rowClass = 'table-danger';
                                    } elseif ($isInGracePeriod) {
                                        $rowClass = 'table-warning';
                                    } elseif ($isExpiring) {
                                        $rowClass = 'table-info';
                                    }
                                @endphp
                                <tr id="row_{{ $row->id }}" class="{{ $rowClass }}">
                                    <td class="serial">{{ $data['subscriptions']->firstItem() + $key }}</td>
                                    <td title="{{ @$row->school->email }}">
                                        {{ Str::limit(@$row->school->name, 30) ?? ___('mainapp_common.N/A') }}
                                        @if($isExpiring || $isInGracePeriod || $isExpired)
                                            <br>
                                            <small class="text-{{ $isExpired ? 'danger' : ($isInGracePeriod ? 'warning' : 'info') }}">
                                                <i class="las la-exclamation-circle"></i>
                                                @if($isExpired)
                                                    {{ ___('mainapp_common.Expired') }}
                                                @elseif($isInGracePeriod)
                                                    {{ ___('mainapp_common.In Grace Period') }}
                                                @else
                                                    {{ ___('mainapp_common.Expiring Soon') }}
                                                @endif
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $row->package->name ?? ___('mainapp_common.N/A') }}</td>
                                    <td>
                                        <span class="badge badge-basic-info-text">
                                            {{ $row->branch_count ?? 1 }} {{ ___('mainapp_common.Branches') }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($row->total_price ?? $row->price, 2) }}</strong>
                                        @if(isset($row->branch_count) && $row->branch_count > 1)
                                            <br><small class="text-muted">${{ number_format($row->price, 2) }} × {{ $row->branch_count }}</small>
                                        @endif
                                    </td>
                                    <td>{{ dateFormat(@$row->created_at) }}</td>
                                    <td>
                                        {{ $row->expiry_date ? dateFormat(@$row->expiry_date) : ___('mainapp_subscriptions.Lifetime') }}
                                        @if($daysUntilExpiry !== null && $daysUntilExpiry >= 0)
                                            <br>
                                            @if($daysUntilExpiry > 0)
                                                <small class="text-{{ $daysUntilExpiry <= 7 ? 'danger' : ($daysUntilExpiry <= 15 ? 'warning' : 'info') }}">
                                                    {{ $daysUntilExpiry }} {{ $daysUntilExpiry == 1 ? ___('mainapp_common.day left') : ___('mainapp_common.days left') }}
                                                </small>
                                            @elseif($hoursUntilExpiry > 0)
                                                <small class="text-danger">
                                                    <i class="las la-clock"></i> {{ $hoursUntilExpiry }} {{ $hoursUntilExpiry == 1 ? ___('mainapp_common.hour left') : ___('mainapp_common.hours left') }}
                                                </small>
                                            @else
                                                <small class="text-danger">
                                                    <i class="las la-exclamation-triangle"></i> {{ ___('mainapp_common.Expiring Soon') }}
                                                </small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($graceExpiryDate)
                                            {{ dateFormat($graceExpiryDate) }}
                                            @if($isInGracePeriod)
                                                @php
                                                    $graceDaysLeft = (int)$now->diffInDays($graceExpiryDate);
                                                    $graceHoursLeft = $now->diffInHours($graceExpiryDate);
                                                @endphp
                                                <br>
                                                <small class="text-warning">
                                                    <i class="las la-clock"></i>
                                                    @if($graceDaysLeft > 0)
                                                        {{ $graceDaysLeft }} {{ $graceDaysLeft == 1 ? ___('mainapp_common.day left') : ___('mainapp_common.days left') }}
                                                    @else
                                                        {{ $graceHoursLeft }} {{ $graceHoursLeft == 1 ? ___('mainapp_common.hour left') : ___('mainapp_common.hours left') }}
                                                    @endif
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">{{ ___('mainapp_common.N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $row->trx_id }}</td>
                                    <td>
                                        @if ($row->status == App\Enums\SubscriptionStatus::APPROVED)
                                            <span class="badge-basic-success-text">{{ ___('mainapp_subscriptions.Approved') }}</span>
                                        @elseif ($row->status == App\Enums\SubscriptionStatus::REJECT)
                                            <span class="badge-basic-danger-text">{{ ___('mainapp_subscriptions.Reject') }}</span>
                                        @else
                                            <span class="badge-basic-warning-text">{{ ___('mainapp_subscriptions.Pending') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row->payment_status == 1)
                                            <span class="badge-basic-success-text">{{ ___('mainapp_subscriptions.Paid') }}</span>
                                        @else
                                            <span class="badge-basic-danger-text">{{ ___('mainapp_subscriptions.Unpaid') }}</span>
                                        @endif
                                    </td>

                                    <td class="action">
                                        <div class="dropdown dropdown-action">
                                                <button type="button" class="btn-dropdown" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis"></i>
                                                </button>

                                                <ul class="dropdown-menu dropdown-menu-end ">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('subscription.edit', $row->id) }}"><span
                                                                class="icon mr-8"><i
                                                                    class="fa-solid fa-edit"></i></span>
                                                            {{ ___('mainapp_common.edit') }}</a>
                                                    </li>
                                                    @if(isset($row->school_id))
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('subscription-payments.history', $row->school_id) }}">
                                                            <span class="icon mr-8"><i class="fa-solid fa-history"></i></span>
                                                            {{ ___('mainapp_common.Payment History') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('subscription-payments.create', $row->school_id) }}">
                                                            <span class="icon mr-8"><i class="fa-solid fa-money-bill"></i></span>
                                                            {{ ___('mainapp_common.Record Payment') }}
                                                        </a>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="100%" class="text-center gray-color">
                                        <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary" width="100">
                                        <p class="mb-0 text-center">{{ ___('mainapp_common.no_data_available') }}</p>
                                        <p class="mb-0 text-center text-secondary font-size-90">
                                            {{ ___('mainapp_common.please_add_new_entity_regarding_this_table') }}</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!--  table end -->
                    <!--  pagination start -->

                        <div class="ot-pagination pagination-content d-flex justify-content-end align-content-center py-3">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination justify-content-between">
                                    {!!$data['subscriptions']->appends(request()->query())->links() !!}
                                </ul>
                            </nav>
                        </div>

                    <!--  pagination end -->
                </div>
            </div>
        </div>
        <!--  table content end -->

    </div>
@endsection

@push('script')
    @include('backend.partials.delete-ajax')
@endpush
