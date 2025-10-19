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
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- breadcrumb Area End --}}

        <div class="col-12">
            <form action="{{ route('journals.index') }}" method="get" id="journalFilterForm" enctype="multipart/form-data">
                <div class="card ot-card mb-24 position-relative z_1">
                    <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                        <h3 class="mb-0">{{ ___('common.Filtering') }}</h3>

                        <div class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">
                            <!-- Search Box -->
                            <div class="table_searchBox">
                                <div class="search-input">
                                    <input class="form-control" type="text" placeholder="{{ ___('common.search') }}"
                                           name="search" value="{{ request('search') }}">
                                    <span class="icon"><i class="las la-search"></i></span>
                                </div>
                            </div>

                            <!-- Status Filter -->
                            <div class="single_large_selectBox">
                                <select class="nice-select niceSelect bordered_style wide" name="status">
                                    <option value="">{{ ___('common.select_status') }}</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                        {{ ___('common.active') }}
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                        {{ ___('common.inactive') }}
                                    </option>
                                </select>
                            </div>

                            <!-- Branch Filter -->
                            <div class="single_large_selectBox">
                                <select class="nice-select niceSelect bordered_style wide" name="branch_id">
                                    <option value="">{{ ___('journals.select_branch') }}</option>
                                    @foreach($data['branches'] as $branch)
                                        <option value="{{ $branch['id'] }}" {{ request('branch_id') == $branch['id'] ? 'selected' : '' }}>
                                            {{ $branch['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filter Button -->
                            <button type="submit" class="btn btn-outline-primary btn-default btn-squared radius-md">
                                {{ ___('common.filter') }}
                            </button>

                            <!-- Reset Button -->
                            <a href="{{ route('journals.index') }}" class="btn btn-outline-secondary btn-default btn-squared radius-md">
                                {{ ___('common.reset') }}
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-12">
            <div class="card ot-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                    <h3 class="card-title">{{ $data['title'] }}</h3>
                    @if (hasPermission('journal_create'))
                        <a href="{{ route('journals.create') }}" class="btn btn-lg ot-btn-primary btn-right-icon radius-md">
                            <span><i class="fa-solid fa-plus"></i> </span>
                            <span class="">{{ ___('journals.add_journal') }}</span>
                        </a>
                    @endif
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered role-table" id="journal-table">
                            <thead class="thead-light">
                                <tr>
                                    <th class="serial">#</th>
                                    <th>{{ ___('journals.name') }}</th>
                                    <th>{{ ___('journals.branch') }}</th>
                                    <th>{{ ___('journals.description') }}</th>
                                    <th>{{ ___('common.created_at') }}</th>
                                    @if (hasPermission('journal_update') || hasPermission('journal_read') || hasPermission('journal_delete'))
                                        <th>{{ ___('common.action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @forelse ($data['journals'] as $key => $journal)
                                    <tr>
                                        <td class="serial">{{ $data['journals']->firstItem() + $key }}</td>
                                        <td>
                                            <div class="user-card">
                                                <h6 class="text-primary">{{ $journal->name }}</h6>
                                            </div>
                                        </td>
                                        <td>{{ is_object($journal->branch) ? $journal->branch->name : $journal->branch }}</td>
                                        <td>
                                            <span class="text-muted">
                                                {{ Str::limit($journal->description, 50) }}
                                            </span>
                                        </td>
                                        <td>{{ dateFormat($journal->created_at) }}</td>
                                        @if (hasPermission('journal_update') || hasPermission('journal_read') || hasPermission('journal_delete'))
                                            <td>
                                                <div class="dropdown dropdown-action">
                                                    <button type="button" class="btn-dropdown" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fa-solid fa-ellipsis"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @if (hasPermission('journal_read'))
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('journals.show', $journal->id) }}">
                                                                    <span class="icon mr-12"><i class="fa-solid fa-eye"></i></span>
                                                                    {{ ___('common.view') }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if (hasPermission('journal_update'))
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('journals.edit', $journal->id) }}">
                                                                    <span class="icon mr-12"><i class="fa-solid fa-pen-to-square"></i></span>
                                                                    {{ ___('common.edit') }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if (hasPermission('journal_update') && $journal->status == 'active')
                                                            <li>
                                                                <a class="dropdown-item close-journal" href="javascript:void(0);"
                                                                   data-id="{{ $journal->id }}"
                                                                   data-name="{{ $journal->name }}">
                                                                    <span class="icon mr-12"><i class="fa-solid fa-lock"></i></span>
                                                                    {{ ___('journals.close_journal') }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if (auth()->user()->role_id === 1 && $journal->status == 'inactive')
                                                            <li>
                                                                <a class="dropdown-item open-journal" href="javascript:void(0);"
                                                                   data-id="{{ $journal->id }}"
                                                                   data-name="{{ $journal->name }}">
                                                                    <span class="icon mr-12"><i class="fa-solid fa-lock-open"></i></span>
                                                                    {{ ___('journals.open_journal') }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                        @if (hasPermission('journal_delete'))
                                                            <li>
                                                                <a class="dropdown-item delete_data" href="javascript:void(0);"
                                                                   data-href="{{ route('journals.destroy', $journal->id) }}">
                                                                    <span class="icon mr-12"><i class="fa-solid fa-trash-can"></i></span>
                                                                    {{ ___('common.delete') }}
                                                                </a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ hasPermission('journal_update') || hasPermission('journal_read') || hasPermission('journal_delete') ? '6' : '5' }}"
                                            class="text-center">{{ ___('common.no_data_available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($data['journals']->hasPages())
                        <div class="row">
                            <div class="col-12">
                                <div class="pagination-container">
                                    {{ $data['journals']->links() }}
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
    $('.close-journal').on('click', function() {
        const journalId = $(this).data('id');
        const journalName = $(this).data('name');
        const closeUrl = '{{ route("journals.close", ":id") }}'.replace(':id', journalId);

        Swal.fire({
            title: '{{ ___("journals.close_journal") }}',
            text: '{{ ___("journals.close_journal_confirmation") }}' + ' "' + journalName + '"?',
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
                            location.reload();
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
    $('.open-journal').on('click', function() {
        const journalId = $(this).data('id');
        const journalName = $(this).data('name');
        const openUrl = '{{ route("journals.open", ":id") }}'.replace(':id', journalId);

        Swal.fire({
            title: '{{ ___("journals.open_journal") }}',
            text: '{{ ___("journals.open_journal_confirmation") }}' + ' "' + journalName + '"?',
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
                            location.reload();
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
                            location.reload();
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