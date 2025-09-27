@extends('backend.master')
@section('title')
    {{ @$data['title'] }}
@endsection
@section('content')
    <div class="page-content">

        {{-- bradecrumb Area S t a r t --}}
        <div class="page-header">
            <div class="row">
                <div class="col-sm-6">
                    <h4 class="bradecrumb-title mb-1">{{ $data['title'] }}</h1>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                        <li class="breadcrumb-item">{{ $data['title'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
        {{-- bradecrumb Area E n d --}}


        <div class="col-12">
            <form action="{{ route('parent.search') }}" method="post" id="marksheed" enctype="multipart/form-data">
                @csrf
                <div class="card ot-card mb-24 position-relative z_1">
                    <div class="card-header d-flex align-items-center gap-4 flex-wrap">
                        <h3 class="mb-0">{{ ___('common.Filtering') }}</h3>

                        <div class="card_header_right d-flex align-items-center gap-3 flex-fill justify-content-end flex-wrap">

                            <div class="single_large_selectBox">
                                <input class="form-control ot-input"
                                    name="keyword" list="datalistOptions" id="exampleDataList"
                                    placeholder="{{ ___('student_info.enter_keyword') }}"
                                    value="{{ old('keyword', @$data['request']->keyword) }}">
                            </div>

                            <button class="btn btn-lg ot-btn-primary" type="submit">
                                {{___('common.Search')}}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>



        <!--  table content start -->
        <div class="table-content table-basic mt-20">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $data['title'] }}</h4>
                    @if (hasPermission('parent_create'))
                        <a href="{{ route('parent.create') }}" class="btn btn-lg ot-btn-primary">
                            <span><i class="fa-solid fa-plus"></i> </span>
                            <span class="">{{ ___('common.add') }}</span>
                        </a>
                    @endif
                </div>
                @if (@$data['parents'])
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered role-table">
                            <thead class="thead">
                                <tr>
                                    <th class="serial">{{ ___('common.sr_no') }}</th>
                                    <th class="purchase">{{ ___('common.name') }}</th>
                                    <th class="purchase">{{ ___('common.phone') }}</th>
                                    <th class="purchase">{{ ___('common.email') }}</th>
                                    <th class="purchase">{{ ___('common.Place of work') }}</th>
                                    <th class="purchase">{{ ___('common.Position') }}</th>
                                    <th class="purchase">{{ ___('common.address') }}</th>
                                    <th class="purchase">{{ ___('common.status') }}</th>
                                    <th class="purchase">{{ ___('common.balance') }}</th>
                                    @if (hasPermission('parent_update') || hasPermission('parent_delete') || hasPermission('parent_deposit_create') || hasPermission('parent_statement_view'))
                                        <th class="action">{{ ___('common.action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @forelse ($data['parents'] as $key => $row)
                                <tr id="row_{{ $row->id }}">
                                    <td class="serial">{{ ++$key }}</td>
                                    <td>{{ @$row->user->name }}</td>
                                    <td>{{ @$row->user->phone }}</td>
                                    <td>{{ @$row->user->email }}</td>
                                    <td>{{ @$row->guardian_place_of_work }}</td>
                                    <td>{{ @$row->guardian_position }}</td>
                                    <td>{{ @$row->guardian_address }}</td>
                                    <td>
                                        @if ($row->status == App\Enums\Status::ACTIVE)
                                            <span class="badge-basic-success-text">{{ ___('common.active') }}</span>
                                        @else
                                            <span class="badge-basic-danger-text">{{ ___('common.inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-success font-weight-bold">{{ @$row->getFormattedAvailableBalance() }}</span>
                                        @if(@$row->hasAvailableBalance())
                                            <small class="text-muted d-block">Available</small>
                                        @else
                                            <small class="text-muted d-block">No Balance</small>
                                        @endif
                                    </td>
                                    @if (hasPermission('parent_update') || hasPermission('parent_delete') || hasPermission('parent_deposit_create') || hasPermission('parent_statement_view'))
                                        <td class="action">
                                            <div class="dropdown dropdown-action">
                                                <button type="button" class="btn-dropdown" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fa-solid fa-ellipsis"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end ">
                                                    @if (hasPermission('parent_deposit_create'))
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                onclick="openDepositModal({{ $row->id }})">
                                                                <span class="icon mr-8"><i class="fa-solid fa-plus-circle text-success"></i></span>
                                                                <span>{{ ___('common.deposit') }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if (hasPermission('parent_statement_view'))
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                onclick="openStatementModal({{ $row->id }})">
                                                                <span class="icon mr-8"><i class="fa-solid fa-file-lines text-info"></i></span>
                                                                <span>{{ ___('common.statement') }}</span>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if (hasPermission('parent_update'))
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('parent.edit', $row->id) }}"><span
                                                                    class="icon mr-8"><i
                                                                        class="fa-solid fa-pen-to-square"></i></span>
                                                                {{ ___('common.edit') }}</a>
                                                        </li>
                                                    @endif
                                                    @if (hasPermission('parent_delete'))
                                                        <li>
                                                            <a class="dropdown-item" href="javascript:void(0);"
                                                                onclick="delete_row('parent/delete', {{ $row->id }})">
                                                                <span class="icon mr-8"><i
                                                                        class="fa-solid fa-trash-can"></i></span>
                                                                <span>{{ ___('common.delete') }}</span>
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
                                    <td colspan="100%" class="text-center gray-color">
                                        <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary" width="100">
                                        <p class="mb-0 text-center">{{ ___('common.no_data_available') }}</p>
                                        <p class="mb-0 text-center text-secondary font-size-90">
                                            {{ ___('common.please_add_new_entity_regarding_this_table') }}</p>
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
                                    {!!$data['parents']->appends(\Request::capture()->except('page'))->links() !!}
                                </ul>
                            </nav>
                        </div>

                    <!--  pagination end -->
                </div>
                @else
                <div class="text-center gray-color p-5">
                    <img src="{{ asset('images/no_data.svg') }}" alt="" class="mb-primary" width="100">
                    <p class="mb-0 text-center">{{ ___('common.no_data_available') }}</p>
                    <p class="mb-0 text-center text-secondary font-size-90">
                        {{ ___('common.please_add_new_entity_regarding_this_table') }}</p>
                </div>
                @endif
            </div>
        </div>
        <!--  table content end -->

    </div>
@endsection

@push('script')
    @include('backend.partials.delete-ajax')

    <!-- Deposit and Statement Modal Container -->
    <div class="modal fade" id="parentActionModal" tabindex="-1" aria-labelledby="parentActionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div id="modalContent">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to open deposit modal
        function openDepositModal(parentId) {
            $.ajax({
                url: "{{ route('parent-deposits.deposit-modal') }}",
                method: 'GET',
                data: { 
                    parent_id: parentId,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#modalContent').html('<div class="modal-body text-center"><div class="spinner-border" role="status"></div><p class="mt-2">Loading...</p></div>');
                    $('#parentActionModal').modal('show');
                },
                success: function(response) {
                    if (response.success) {
                        $('#modalContent').html(response.html);
                        initializeDepositForm();
                    } else {
                        showError(response.message || 'Failed to load deposit form');
                    }
                },
                error: function(xhr) {
                    console.log('AJAX Error:', xhr);
                    console.log('Response:', xhr.responseText);
                    showError('Error loading deposit form: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        }

        // Function to open statement modal
        function openStatementModal(parentId) {
            $.ajax({
                url: "{{ route('parent-statements.statement-modal') }}",
                method: 'GET',
                data: { parent_id: parentId },
                beforeSend: function() {
                    $('#modalContent').html('<div class="modal-body text-center"><div class="spinner-border" role="status"></div><p class="mt-2">Loading...</p></div>');
                    $('#parentActionModal').modal('show');
                },
                success: function(response) {
                    if (response.success) {
                        $('#modalContent').html(response.html);
                        initializeStatementForm();
                    } else {
                        showError(response.message || 'Failed to load statement form');
                    }
                },
                error: function(xhr) {
                    showError('Error loading statement form: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        }

        // Initialize deposit form functionality
        function initializeDepositForm() {
            // Student selection change handler
            $('#student_id').on('change', function() {
                var studentId = $(this).val();
                updateBalanceDisplay(studentId);
            });

            // Payment method change handler
            $('#payment_method').on('change', function() {
                var method = $(this).val();
                if (method === '3' || method === '4') { // Zaad or Edahab
                    $('#transaction_reference').prop('required', true);
                    $('#transaction_reference_group').show();
                } else {
                    $('#transaction_reference').prop('required', false);
                    $('#transaction_reference_group').hide();
                }
            });

            // Form submission
            $('#depositForm').on('submit', function(e) {
                e.preventDefault();
                submitDepositForm();
            });
        }

        // Initialize statement form functionality
        function initializeStatementForm() {
            // Date range handlers
            $('#start_date, #end_date').on('change', function() {
                validateDateRange();
            });

            // Quick date range buttons
            $('.quick-date-range').on('click', function() {
                var range = $(this).data('range');
                setQuickDateRange(range);
            });
        }

        // Submit deposit form
        function submitDepositForm() {
            var form = $('#depositForm');
            var formData = form.serialize();

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                beforeSend: function() {
                    $('#submitDeposit').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
                },
                success: function(response) {
                    if (response.success) {
                        $('#parentActionModal').modal('hide');
                        showSuccess(response.message);
                        // Refresh the page to show updated balance
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showError(response.message || 'Failed to create deposit');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        displayValidationErrors(xhr.responseJSON.errors);
                    } else {
                        showError('Error creating deposit: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    }
                },
                complete: function() {
                    $('#submitDeposit').prop('disabled', false).html('Create Deposit');
                }
            });
        }

        // Update balance display when student changes
        function updateBalanceDisplay(studentId) {
            var parentId = $('#parent_guardian_id').val();

            $.ajax({
                url: "{{ route('parent-deposits.get-balance') }}",
                method: 'GET',
                data: {
                    parent_id: parentId,
                    student_id: studentId || null
                },
                success: function(response) {
                    if (response.success) {
                        $('#current_balance').text(response.data.formatted_balance);
                        $('#balance_info').show();
                    }
                },
                error: function() {
                    $('#balance_info').hide();
                }
            });
        }

        // Validate date range
        function validateDateRange() {
            var startDate = new Date($('#start_date').val());
            var endDate = new Date($('#end_date').val());

            if (startDate && endDate && startDate > endDate) {
                showError('End date must be after start date');
                $('#end_date').val('');
            }
        }

        // Set quick date range
        function setQuickDateRange(range) {
            var today = new Date();
            var startDate, endDate = today;

            switch(range) {
                case 'today':
                    startDate = today;
                    break;
                case 'week':
                    startDate = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                    break;
                case 'month':
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    break;
                case 'quarter':
                    var quarter = Math.floor(today.getMonth() / 3);
                    startDate = new Date(today.getFullYear(), quarter * 3, 1);
                    break;
                case 'year':
                    startDate = new Date(today.getFullYear(), 0, 1);
                    break;
            }

            $('#start_date').val(formatDate(startDate));
            $('#end_date').val(formatDate(endDate));
        }

        // Format date for input
        function formatDate(date) {
            return date.getFullYear() + '-' +
                   String(date.getMonth() + 1).padStart(2, '0') + '-' +
                   String(date.getDate()).padStart(2, '0');
        }

        // Display validation errors
        function displayValidationErrors(errors) {
            $('.invalid-feedback').remove();
            $('.is-invalid').removeClass('is-invalid');

            $.each(errors, function(field, messages) {
                var input = $('#' + field);
                input.addClass('is-invalid');
                input.after('<div class="invalid-feedback">' + messages[0] + '</div>');
            });
        }

        // Show success message
        function showSuccess(message) {
            toastr.success(message);
        }

        // Show error message
        function showError(message) {
            toastr.error(message);
        }
    </script>
@endpush
