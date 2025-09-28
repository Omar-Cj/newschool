<div class="modal-header modal-header-image">
    <h5 class="modal-title" id="statementModalLabel">
        Statement for {{ $parent->user->name }}
    </h5>
    <button type="button" class="m-0 btn-close d-flex justify-content-center align-items-center"
            data-bs-dismiss="modal" aria-label="Close">
        <i class="fa fa-times text-white" aria-hidden="true"></i>
    </button>
</div>

<div class="modal-body">
    <!-- Parent Information -->
    <div class="card bg-light mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Parent Information</h6>
            <div class="parent-info d-flex align-items-center">
                <i class="fas fa-user-tie me-2"></i>
                <span>{{ $parent->user->name }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="parent-details">
                        <div class="parent-detail-item">
                            <strong>Name:</strong> {{ $parent->user->name }}
                        </div>
                        <div class="parent-detail-item">
                            <strong>Email:</strong> {{ $parent->user->email }}
                        </div>
                        @if($parent->user->phone)
                        <div class="parent-detail-item">
                            <strong>Phone:</strong> {{ $parent->user->phone }}
                        </div>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="balance-display">
                        <small class="text-muted d-block">Total Balance</small>
                        <div class="h5 mb-0 fw-bold text-success">{{ $parent->getFormattedAvailableBalance() }}</div>
                    </div>
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
                    <select class="form-control select2" name="student_id" id="student_id">
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
                    <select class="form-control select2" name="transaction_type" id="transaction_type">
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
                    <input type="date" class="form-control ot-input" name="start_date" id="start_date"
                           value="{{ now()->subMonth()->format('Y-m-d') }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control ot-input" name="end_date" id="end_date"
                           value="{{ now()->format('Y-m-d') }}">
                </div>
            </div>

            <!-- Quick Date Range Buttons -->
            <div class="col-12">
                <div class="form-group mb-3">
                    <label class="form-label">Quick Date Ranges</label>
                    <div class="quick-date-ranges d-flex flex-wrap gap-2">
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
                        View Statement
                    </button>
                    <button type="button" class="btn btn-success" id="exportPDF">
                        Export PDF
                    </button>
                    <button type="button" class="btn btn-info" id="exportExcel">
                        Export Excel
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Balance Summary -->
    @if($parent->children->count() > 0)
    <div class="mt-4">
        <h6>Account Balances</h6>
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
        <h6>Statement Information</h6>
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

@include('backend.parent-deposits.statements.statement-modal-style')

<script>
    $(document).ready(function() {
        // Initialize Select2 dropdowns
        initializeStatementSelect2();

        // Quick date range functionality
        $('.quick-date-range').on('click', function() {
            var range = $(this).data('range');
            setQuickDateRange(range);
            
            // Update active button
            $('.quick-date-range').removeClass('active');
            $(this).addClass('active');
        });

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

    // Global function to initialize Select2 dropdowns (called from parent page)
    window.initializeStatementSelect2 = function() {
        try {
            const modalParent = $('#parentActionModal');
            const parentElement = modalParent.length > 0 ? modalParent : $('body');

            // Initialize Student dropdown
            $('#student_id').select2({
                placeholder: "All Students",
                allowClear: true,
                width: '100%',
                dropdownParent: parentElement
            });

            // Initialize Transaction Type dropdown
            $('#transaction_type').select2({
                placeholder: "All Types",
                allowClear: true,
                width: '100%',
                dropdownParent: parentElement
            });
        } catch (error) {
            console.error('Error initializing Select2 dropdowns:', error);
            // Fallback: initialize without dropdownParent
            $('#student_id, #transaction_type').select2({
                width: '100%'
            });
        }
    };

    // Set quick date range
    function setQuickDateRange(range) {
        var today = new Date();
        var startDate, endDate;

        switch(range) {
            case 'today':
                startDate = endDate = today;
                break;
            case 'week':
                startDate = new Date(today.setDate(today.getDate() - today.getDay()));
                endDate = new Date(today.setDate(today.getDate() - today.getDay() + 6));
                break;
            case 'month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                endDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'quarter':
                var quarter = Math.floor(today.getMonth() / 3);
                startDate = new Date(today.getFullYear(), quarter * 3, 1);
                endDate = new Date(today.getFullYear(), quarter * 3 + 3, 0);
                break;
            case 'year':
                startDate = new Date(today.getFullYear(), 0, 1);
                endDate = new Date(today.getFullYear(), 11, 31);
                break;
            default:
                return;
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
</script>