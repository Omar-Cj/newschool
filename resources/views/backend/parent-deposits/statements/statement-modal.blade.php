<div class="modal-header">
    <h5 class="modal-title" id="statementModalLabel">
        <i class="fa-solid fa-file-lines text-info me-2"></i>
        Statement for {{ $parent->user->name }}
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <!-- Parent Information -->
    <div class="card border-info mb-3">
        <div class="card-body py-2">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <strong>Parent:</strong> {{ $parent->user->name }}<br>
                    <small class="text-muted">{{ $parent->user->email }} • {{ $parent->user->phone }}</small>
                </div>
                <div class="col-md-4 text-end">
                    <strong>Total Balance:</strong>
                    <span class="text-success">{{ $parent->getFormattedAvailableBalance() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statement Options Form -->
    <form id="statementForm" action="{{ $statementRoute }}" method="GET">
        <div class="row">
            <!-- Student Selection -->
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="student_id" class="form-label">Student</label>
                    <select class="form-select" name="student_id" id="student_id">
                        <option value="">All Students</option>
                        @foreach($parent->children as $child)
                            <option value="{{ $child->id }}">
                                {{ $child->full_name }} - {{ $child->class?->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Transaction Type Filter -->
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="transaction_type" class="form-label">Transaction Type</label>
                    <select class="form-select" name="transaction_type" id="transaction_type">
                        <option value="">All Types</option>
                        <option value="deposit">Deposits</option>
                        <option value="withdrawal">Withdrawals</option>
                        <option value="allocation">Fee Allocations</option>
                        <option value="refund">Refunds</option>
                    </select>
                </div>
            </div>

            <!-- Date Range -->
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" id="start_date"
                           value="{{ now()->subMonth()->format('Y-m-d') }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" id="end_date"
                           value="{{ now()->format('Y-m-d') }}">
                </div>
            </div>

            <!-- Quick Date Range Buttons -->
            <div class="col-12">
                <div class="form-group mb-3">
                    <label class="form-label">Quick Date Ranges</label>
                    <div class="btn-group-sm d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary quick-date-range" data-range="today">Today</button>
                        <button type="button" class="btn btn-outline-primary quick-date-range" data-range="week">This Week</button>
                        <button type="button" class="btn btn-outline-primary quick-date-range active" data-range="month">This Month</button>
                        <button type="button" class="btn btn-outline-primary quick-date-range" data-range="quarter">This Quarter</button>
                        <button type="button" class="btn btn-outline-primary quick-date-range" data-range="year">This Year</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statement Actions -->
        <div class="row">
            <div class="col-12">
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <button type="submit" class="btn btn-primary" id="viewStatement">
                        <i class="fa-solid fa-eye me-2"></i>View Statement
                    </button>
                    <button type="button" class="btn btn-success" id="exportPDF">
                        <i class="fa-solid fa-file-pdf me-2"></i>Export PDF
                    </button>
                    <button type="button" class="btn btn-info" id="exportExcel">
                        <i class="fa-solid fa-file-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Balance Summary -->
    @if($parent->children->count() > 0)
    <div class="mt-4">
        <h6><i class="fa-solid fa-wallet me-2"></i>Account Balances</h6>
        <div class="row">
            <!-- General Account -->
            <div class="col-md-6 mb-2">
                <div class="card border-success">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>General Account</strong>
                                <small class="text-muted d-block">Available for any student</small>
                            </div>
                            <div class="text-end">
                                <span class="text-success font-weight-bold">{{ $parent->getFormattedAvailableBalance() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student-Specific Accounts -->
            @foreach($parent->children as $child)
                @if($parent->getAvailableBalance($child) > 0)
                <div class="col-md-6 mb-2">
                    <div class="card border-info">
                        <div class="card-body py-2 px-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $child->full_name }}</strong>
                                    <small class="text-muted d-block">{{ $child->class?->name ?? 'N/A' }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="text-info font-weight-bold">{{ $parent->getFormattedAvailableBalance($child) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Information Alert -->
    <div class="alert alert-info mt-3">
        <h6><i class="fa-solid fa-info-circle me-2"></i>Statement Information</h6>
        <ul class="mb-0">
            <li><strong>View Statement:</strong> Opens detailed statement in new window</li>
            <li><strong>Export PDF:</strong> Downloads printable PDF statement</li>
            <li><strong>Export Excel:</strong> Downloads Excel file for data analysis</li>
        </ul>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>

<script>
    $(document).ready(function() {
        // Quick date range functionality is handled in the main script

        // Export PDF functionality
        $('#exportPDF').on('click', function() {
            var formData = $('#statementForm').serialize();
            var exportUrl = "{{ $exportRoute }}";

            // Add parent ID and format
            formData += '&parent_id={{ $parent->id }}&format=pdf';

            // Create a temporary form for file download
            var tempForm = $('<form>', {
                method: 'GET',
                action: exportUrl
            });

            // Add form data as hidden inputs
            $.each(formData.split('&'), function(i, field) {
                var fieldData = field.split('=');
                if (fieldData[0] && fieldData[1]) {
                    tempForm.append($('<input>', {
                        type: 'hidden',
                        name: decodeURIComponent(fieldData[0]),
                        value: decodeURIComponent(fieldData[1])
                    }));
                }
            });

            // Submit form and remove it
            tempForm.appendTo('body').submit().remove();
        });

        // Export Excel functionality
        $('#exportExcel').on('click', function() {
            var formData = $('#statementForm').serialize();
            var exportUrl = "{{ $exportRoute }}";

            // Add parent ID and format
            formData += '&parent_id={{ $parent->id }}&format=excel';

            // Create a temporary form for file download
            var tempForm = $('<form>', {
                method: 'GET',
                action: exportUrl
            });

            // Add form data as hidden inputs
            $.each(formData.split('&'), function(i, field) {
                var fieldData = field.split('=');
                if (fieldData[0] && fieldData[1]) {
                    tempForm.append($('<input>', {
                        type: 'hidden',
                        name: decodeURIComponent(fieldData[0]),
                        value: decodeURIComponent(fieldData[1])
                    }));
                }
            });

            // Submit form and remove it
            tempForm.appendTo('body').submit().remove();
        });

        // View statement functionality
        $('#statementForm').on('submit', function(e) {
            e.preventDefault();

            var formData = $(this).serialize();
            var statementUrl = $(this).attr('action');

            // Open statement in new window
            var newWindow = window.open(statementUrl + '?' + formData, '_blank');
            if (newWindow) {
                // Close modal after opening statement
                $('#parentActionModal').modal('hide');
            } else {
                showError('Please allow pop-ups to view the statement');
            }
        });
    });
</script>