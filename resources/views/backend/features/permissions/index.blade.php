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
                    <li class="breadcrumb-item active" aria-current="page">{{ ___('common.Permission Features') }}</li>
                </ol>
            </div>
            <div class="col-sm-6">
                <div class="text-end">
                    <a href="{{ route('permission-features.create') }}" class="btn btn-lg ot-btn-primary">
                        <span><i class="fa-solid fa-plus"></i></span>
                        {{ ___('common.add_new') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    {{-- Breadcrumb Area End --}}

    <div class="card ot-card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('permission-features.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="group_filter" class="form-label">{{ ___('common.Filter by Group') }}</label>
                    <select class="form-control ot-input" name="group_id" id="group_filter" onchange="this.form.submit()">
                        <option value="">{{ ___('common.All Groups') }}</option>
                        @foreach($data['feature_groups'] as $group)
                            <option value="{{ $group->id }}" {{ $data['selected_group'] == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

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

            @if(isset($data['permission_features']) && $data['permission_features']->isNotEmpty())
                @foreach($data['permission_features'] as $groupName => $features)
                    <div class="feature-group-section mb-4">
                        <h5 class="mb-3">
                            <i class="fa-solid fa-layer-group"></i>
                            {{ $groupName }}
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead">
                                    <tr>
                                        <th>{{ ___('common.Name') }}</th>
                                        <th>{{ ___('common.Permission') }}</th>
                                        <th>{{ ___('common.Description') }}</th>
                                        <th width="80">{{ ___('common.Premium') }}</th>
                                        <th width="80">{{ ___('common.Status') }}</th>
                                        <th width="120" class="text-center">{{ ___('common.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="tbody">
                                    @foreach($features as $feature)
                                    <tr>
                                        <td><strong>{{ $feature->name ?: $feature->permission->attribute }}</strong></td>
                                        <td><code>{{ $feature->permission->attribute }}</code></td>
                                        <td>{{ Str::limit($feature->description, 60) }}</td>
                                        <td class="text-center">
                                            @if($feature->is_premium)
                                                <span class="badge badge-warning">
                                                    <i class="fa-solid fa-star"></i> Premium
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">Standard</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($feature->status)
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
                                                        <a class="dropdown-item" href="{{ route('permission-features.edit', $feature->id) }}">
                                                            <i class="fa-solid fa-pen-to-square"></i> {{ ___('common.edit') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item delete-item" href="javascript:void(0);"
                                                           data-url="{{ route('permission-features.destroy', $feature->id) }}">
                                                            <i class="fa-solid fa-trash-can"></i> {{ ___('common.delete') }}
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
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">{{ ___('common.No permission features found') }}</p>
                    <a href="{{ route('permission-features.create') }}" class="btn ot-btn-primary mt-3">
                        <i class="fa-solid fa-plus"></i> {{ ___('common.Create First Feature') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
$(document).ready(function() {
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
.feature-group-section {
    border-left: 4px solid #007bff;
    padding-left: 15px;
}

.feature-group-section h5 {
    color: #007bff;
    font-weight: 600;
}

.badge-warning {
    background-color: #ffc107;
    color: #000;
}
</style>
@endpush
