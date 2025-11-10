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
                        <li class="breadcrumb-item">{{ ___('common.Permission Features') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <!--  table content start -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('common.Permission Features') }}</h4>
                    <a href="{{ route('permission-features.create') }}" class="btn btn-lg ot-btn-primary">
                        <span><i class="fa-solid fa-plus"></i> </span>
                        <span class="">{{ ___('mainapp_common.add') }}</span>
                    </a>
                </div>
                <div class="card-body">
                    @forelse ($data['grouped_features'] as $group)
                        <div class="feature-group-section mb-4">
                            <h5 class="border-bottom pb-2 mb-3">
                                @if($group['group']->icon)
                                    <i class="{{ $group['group']->icon }} text-primary mr-2"></i>
                                @endif
                                {{ $group['group']->name }}
                                <span class="badge bg-info ms-2">{{ $group['features']->count() }}</span>
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead class="thead">
                                        <tr>
                                            <th class="serial">{{ ___('mainapp_common.sr_no') }}</th>
                                            <th class="purchase">{{ ___('mainapp_common.name') }}</th>
                                            <th class="purchase">{{ ___('common.Permission') }}</th>
                                            <th class="purchase">{{ ___('common.Description') }}</th>
                                            <th class="purchase text-center">{{ ___('common.Premium') }}</th>
                                            <th class="purchase text-center">{{ ___('common.Position') }}</th>
                                            <th class="purchase text-center">{{ ___('mainapp_common.status') }}</th>
                                            <th class="action">{{ ___('mainapp_common.action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="tbody">
                                        @foreach ($group['features'] as $key => $feature)
                                        <tr id="row_{{ $feature->id }}">
                                            <td class="serial">{{ ++$key }}</td>
                                            <td>{{ $feature->name }}</td>
                                            <td>
                                                @if($feature->permission)
                                                    <code>{{ $feature->permission->attribute }}</code>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($feature->description, 40) }}</td>
                                            <td class="text-center">
                                                @if($feature->is_premium)
                                                    <span class="badge bg-warning">{{ ___('common.Premium') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ___('common.Standard') }}</span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $feature->position }}</td>
                                            <td class="text-center">
                                                @if ($feature->status == 1)
                                                    <span class="badge-basic-success-text">{{ ___('mainapp_common.active') }}</span>
                                                @else
                                                    <span class="badge-basic-danger-text">{{ ___('mainapp_common.inactive') }}</span>
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
                                                                href="{{ route('permission-features.edit', $feature->id) }}"><span
                                                                    class="icon mr-8"><i
                                                                        class="fa-solid fa-pen-to-square"></i></span>
                                                                {{ ___('mainapp_common.edit') }}</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                onclick="delete_row('permission-features/delete', {{ $feature->id }})">
                                                                <span class="icon mr-8"><i
                                                                        class="fa-solid fa-trash-can"></i></span>
                                                                <span>{{ ___('mainapp_common.delete') }}</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="text-center gray-color py-5">
                            <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary" width="100">
                            <p class="mb-0 text-center">{{ ___('mainapp_common.no_data_available') }}</p>
                            <p class="mb-0 text-center text-secondary font-size-90">
                                {{ ___('mainapp_common.please_add_new_entity_regarding_this_table') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <!--  table content end -->

    </div>
@endsection

@push('script')
    @include('backend.partials.delete-ajax')
@endpush
