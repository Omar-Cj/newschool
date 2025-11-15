@extends('mainapp::layouts.backend.master')

@section('title')
    {{ @$data['title'] }}
@endsection

@section('content')
<div class="page-content">
    {{-- Breadcrumb Area Start --}}
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ ___('common.Feature Groups') }}</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="text-end">
                    <a href="{{ route('feature-groups.create') }}" class="btn btn-lg ot-btn-primary">
                        <span><i class="fa-solid fa-plus"></i></span>
                        {{ ___('common.add_new') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- Breadcrumb Area End --}}

    <div class="card ot-card">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('danger'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('danger') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered feature-groups-table" id="feature-groups-table">
                    <thead class="thead">
                        <tr>
                            <th width="50">{{ ___('common.Order') }}</th>
                            <th width="60">{{ ___('common.Icon') }}</th>
                            <th>{{ ___('common.Name') }}</th>
                            <th>{{ ___('common.Slug') }}</th>
                            <th>{{ ___('common.Description') }}</th>
                            <th width="100">{{ ___('common.Features') }}</th>
                            <th width="80">{{ ___('common.Status') }}</th>
                            <th width="120" class="text-center">{{ ___('common.Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="tbody sortable-list" id="sortable-feature-groups">
                        @forelse ($data['feature_groups'] as $group)
                        <tr data-id="{{ $group->id }}" class="sortable-item">
                            <td class="text-center drag-handle" style="cursor: move;">
                                <i class="fa-solid fa-grip-vertical"></i>
                            </td>
                            <td class="text-center">
                                @if($group->icon)
                                    <i class="{{ $group->icon }} fa-lg"></i>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $group->name }}</strong>
                            </td>
                            <td>
                                <code>{{ $group->slug }}</code>
                            </td>
                            <td>{{ Str::limit($group->description, 50) }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $group->features_count ?? 0 }}</span>
                            </td>
                            <td class="text-center">
                                @if($group->status)
                                    <span class="badge badge-success">{{ ___('common.active') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ ___('common.inactive') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="dropdown dropdown-action">
                                    <button type="button" class="btn-dropdown" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('feature-groups.edit', $group->id) }}">
                                                <i class="fa-solid fa-pen-to-square"></i> {{ ___('common.edit') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item delete-item" href="javascript:void(0);"
                                               data-url="{{ route('feature-groups.destroy', $group->id) }}">
                                                <i class="fa-solid fa-trash-can"></i> {{ ___('common.delete') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">{{ ___('common.No data available') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Sortable for drag and drop
    var sortable = new Sortable(document.getElementById('sortable-feature-groups'), {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        onEnd: function(evt) {
            var order = [];
            $('#sortable-feature-groups tr').each(function() {
                var id = $(this).data('id');
                if (id) {
                    order.push(id);
                }
            });

            // Send AJAX request to update order
            $.ajax({
                url: '{{ route("feature-groups.reorder") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order: order
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                    }
                },
                error: function(xhr) {
                    toastr.error('{{ ___("common.Failed to reorder") }}');
                    // Reload page to restore original order
                    location.reload();
                }
            });
        }
    });

    // Delete confirmation
    $('.delete-item').on('click', function() {
        var url = $(this).data('url');
        var row = $(this).closest('tr');

        Swal.fire({
            title: '{{ ___("alert.are_you_sure") }}',
            text: '{{ ___("alert.you_wont_be_able_to_revert_this") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ ___("alert.yes_delete_it") }}',
            cancelButtonText: '{{ ___("alert.cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire(
                            response[2],
                            response[0],
                            response[1]
                        );
                        if (response[1] === 'success') {
                            row.fadeOut(300, function() {
                                $(this).remove();
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(
                            '{{ ___("alert.error") }}',
                            '{{ ___("alert.something_went_wrong") }}',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>

<style>
.sortable-ghost {
    opacity: 0.4;
    background-color: #f0f0f0;
}
.drag-handle {
    cursor: move;
    user-select: none;
}
</style>
@endpush
