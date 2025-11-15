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
                        <li class="breadcrumb-item">{{ ___('common.Feature Groups') }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}

        <!--  table content start -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ ___('common.Feature Groups') }}</h4>
                    <a href="{{ route('feature-groups.create') }}" class="btn btn-lg ot-btn-primary">
                        <span><i class="fa-solid fa-plus"></i> </span>
                        <span class="">{{ ___('mainapp_common.add') }}</span>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered feature-groups-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('mainapp_common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.Icon') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.name') }}</th>
                                    <th class="purchase">{{ ___('common.Slug') }}</th>
                                    <th class="purchase">{{ ___('common.Description') }}</th>
                                    <th class="purchase">{{ ___('common.Features Count') }}</th>
                                    <th class="purchase">{{ ___('common.Position') }}</th>
                                    <th class="purchase">{{ ___('mainapp_common.status') }}</th>
                                    <th class="action">{{ ___('mainapp_common.action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @forelse ($data['feature_groups'] as $key => $row)
                                <tr id="row_{{ $row->id }}">
                                    <td class="serial">{{ ++$key }}</td>
                                    <td class="text-center">
                                        @if($row->icon)
                                            <i class="{{ $row->icon }} text-primary" style="font-size: 1.5rem;"></i>
                                        @else
                                            <i class="las la-layer-group text-muted"></i>
                                        @endif
                                    </td>
                                    <td>{{ $row->name }}</td>
                                    <td><code>{{ $row->slug }}</code></td>
                                    <td>{{ Str::limit($row->description, 50) }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $row->permission_features_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">{{ $row->position }}</td>
                                    <td>
                                        @if ($row->status == 1)
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
                                                        href="{{ route('feature-groups.edit', $row->id) }}"><span
                                                            class="icon mr-8"><i
                                                                class="fa-solid fa-pen-to-square"></i></span>
                                                        {{ ___('mainapp_common.edit') }}</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="delete_row('feature-groups/delete', {{ $row->id }})">
                                                        <span class="icon mr-8"><i
                                                                class="fa-solid fa-trash-can"></i></span>
                                                        <span>{{ ___('mainapp_common.delete') }}</span>
                                                    </a>
                                                </li>
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
                </div>
            </div>
        </div>
        <!--  table content end -->

    </div>
@endsection

@push('script')
    @include('backend.partials.delete-ajax')
@endpush
