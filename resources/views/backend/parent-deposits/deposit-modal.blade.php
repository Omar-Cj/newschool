@include('backend.parent-deposits.deposit-modal-style')

<div class="modal-header modal-header-image">
    <h5 class="modal-title" id="depositModalLabel">
        <i class="fa-solid fa-piggy-bank me-2"></i>
        Create Deposit for {{ $parent->user->name }}
    </h5>
    <button type="button" class="m-0 btn-close d-flex justify-content-center align-items-center"
            data-bs-dismiss="modal" aria-label="Close">
        <i class="fa fa-times text-white" aria-hidden="true"></i>
    </button>
</div>

<form id="depositForm" action="{{ $formRoute }}" method="POST">
    @csrf
    <div class="modal-body p-4">
        <input type="hidden" name="parent_guardian_id" id="parent_guardian_id" value="{{ $parent->id }}">

        <!-- Parent Information Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fa-solid fa-user me-2"></i>Parent Information
                        </h6>
                        <div class="parent-info d-flex align-items-center">
                            <i class="fas fa-user-tie me-2"></i>
                            <span>{{ $parent->user->name }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="parent-details">
                                    <div class="parent-name">
                                        <strong>{{ $parent->user->name }}</strong>
                                    </div>
                                    <div class="parent-email text-muted">
                                        <i class="fa-solid fa-envelope me-1"></i>
                                        {{ $parent->user->email }}
                                    </div>
                                    @if($parent->user->phone)
                                    <div class="parent-phone text-muted">
                                        <i class="fa-solid fa-phone me-1"></i>
                                        {{ $parent->user->phone }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 text-end">
                                <div id="balance_info" class="balance-display" style="display: none;">
                                    <div class="balance-label">Current Balance</div>
                                    <div class="balance-amount" id="current_balance">$0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deposit Form Fields -->
        <div class="row">
            <!-- Student Selection -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="student_id" class="form-label">
                        Student
                    </label>
                    <select class="form-control select2" name="student_id" id="student_id">
                        <option value="">General Deposit</option>
                        @foreach($parent->children as $child)
                            <option value="{{ $child->id }}">
                                {{ $child->full_name }} - {{ $child->class?->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text">
                        Select a specific student or leave blank for general deposit
                    </small>
                </div>
            </div>

            <!-- Amount -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="amount" class="form-label">
                        Amount <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-primary text-white">
                            {{ Setting('currency_symbol') }}
                        </span>
                        <input type="number" class="form-control ot-input" name="amount" id="amount"
                               step="0.01" min="0.01" required placeholder="0.00">
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="payment_method" class="form-label">
                        Payment Method <span class="text-danger">*</span>
                    </label>
                    <select class="form-control select2" name="payment_method" id="payment_method" required>
                        <option value="">Select Payment Method</option>
                        <option value="1">Cash</option>
                        <option value="3">Zaad</option>
                        <option value="4">Edahab</option>
                    </select>
                </div>
            </div>

            <!-- Journal -->
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="journal_id" class="form-label">
                        Journal
                    </label>
                    <select class="form-control select2" name="journal_id" id="journal_id">
                        <option value="">Select Journal</option>
                        <!-- Will be populated via AJAX if needed -->
                    </select>
                </div>
            </div>

            <!-- Deposit Reason -->
            <div class="col-12 mb-3">
                <div class="form-group">
                    <label for="deposit_reason" class="form-label">
                        Deposit Reason
                    </label>
                    <textarea class="form-control ot-input" name="deposit_reason" id="deposit_reason"
                              rows="3" placeholder="Optional reason for this deposit..."></textarea>
                </div>
            </div>

            <!-- Quick Amount Buttons -->
            <div class="col-12 mb-4">
                <div class="form-group">
                    <label class="form-label">
                        Quick Amount Selection
                    </label>
                    <div class="quick-amounts d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary quick-amount" data-amount="50">
                            $50
                        </button>
                        <button type="button" class="btn btn-outline-primary quick-amount" data-amount="100">
                            $100
                        </button>
                        <button type="button" class="btn btn-outline-primary quick-amount" data-amount="200">
                            $200
                        </button>
                        <button type="button" class="btn btn-outline-primary quick-amount" data-amount="500">
                            $500
                        </button>
                        <button type="button" class="btn btn-outline-primary quick-amount" data-amount="1000">
                            $1,000
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Information -->
        <div class="alert alert-info border-0 shadow-sm">
            <div class="d-flex align-items-start">
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-2">Payment Method Information</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="payment-method-info">
                                <strong>Cash:</strong> Payment collected in person
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="payment-method-info">
                                <strong>Zaad:</strong> Mobile money transfer
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="payment-method-info">
                                <strong>Edahab:</strong> Mobile money transfer
                            </div>
                        </div>
                    </div>
                    <hr class="my-2">
                    <small class="text-muted">
                        No transaction reference required for any payment method.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light border-top">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            Cancel
        </button>
        <button type="submit" class="btn btn-success ot-btn-primary" id="submitDeposit">
            Create Deposit
        </button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Quick amount button functionality
        $('.quick-amount').on('click', function() {
            var amount = $(this).data('amount');
            $('#amount').val(amount);
            $('.quick-amount').removeClass('active');
            $(this).addClass('active');
        });

        // Initialize with general balance
        updateBalanceDisplay(null);

        // Load journals if needed
        loadJournals();
    });

    // Global function to initialize Select2 dropdowns (called from parent page)
    window.initializeDepositSelect2 = function() {
        // Initialize Select2 dropdowns with a small delay to ensure modal is ready
        setTimeout(function() {
            initializeSelect2Dropdowns();
        }, 100);
    };

    // Initialize Select2 dropdowns
    function initializeSelect2Dropdowns() {
        try {
            const modalParent = $('#parentActionModal');
            
            // Check if modal parent exists
            if (modalParent.length === 0) {
                console.warn('Modal parent not found, using body as fallback');
                var parentElement = $('body');
            } else {
                var parentElement = modalParent;
            }

            // Initialize Student dropdown
            $('#student_id').select2({
                placeholder: "General Deposit",
                allowClear: true,
                width: '100%',
                dropdownParent: parentElement
            });

            // Initialize Payment Method dropdown
            $('#payment_method').select2({
                placeholder: "Select Payment Method",
                allowClear: false,
                width: '100%',
                dropdownParent: parentElement
            });

            // Initialize Journal dropdown (will be re-initialized after AJAX load)
            $('#journal_id').select2({
                placeholder: "Select Journal",
                allowClear: false,
                width: '100%',
                dropdownParent: parentElement
            });
        } catch (error) {
            console.error('Error initializing Select2 dropdowns:', error);
            // Fallback: initialize without dropdownParent
            $('#student_id, #payment_method, #journal_id').select2({
                width: '100%'
            });
        }
    }

    // Load available journals from system (filtered by current branch)
    function loadJournals() {
        $.ajax({
            url: '{{ route("parent-deposits.get-journals") }}',
            type: 'GET',
            data: {
                branch_id: {{ activeBranch() }}
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    var journalSelect = $('#journal_id');
                    journalSelect.empty();
                    journalSelect.append('<option value="">Select Journal</option>');

                    $.each(response.data, function(index, journal) {
                        var optionText = journal.name;
                        if (journal.description) {
                            optionText += ' - ' + journal.description;
                        }
                        journalSelect.append('<option value="' + journal.id + '">' + optionText + '</option>');
                    });

                    // Re-initialize Select2 for journal dropdown after AJAX load
                    try {
                        if (journalSelect.hasClass('select2-hidden-accessible')) {
                            journalSelect.select2('destroy');
                        }
                        
                        const modalParent = $('#parentActionModal');
                        const parentElement = modalParent.length > 0 ? modalParent : $('body');
                        
                        journalSelect.select2({
                            placeholder: "Select Journal",
                            allowClear: false,
                            width: '100%',
                            dropdownParent: parentElement
                        });
                    } catch (error) {
                        console.error('Error re-initializing journal Select2:', error);
                        // Fallback: initialize without dropdownParent
                        journalSelect.select2({
                            width: '100%'
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.log('Error loading journals:', error);
                // Show user-friendly message
                $('#journal_id').append('<option value="">Error loading journals</option>');
            }
        });
    }
</script>