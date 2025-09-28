<div class="modal-header">
    <h5 class="modal-title" id="depositModalLabel">
        <i class="fa-solid fa-plus-circle text-success me-2"></i>
        Create Deposit for {{ $parent->user->name }}
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form id="depositForm" action="{{ $formRoute }}" method="POST">
    @csrf
    <div class="modal-body">
        <input type="hidden" name="parent_guardian_id" id="parent_guardian_id" value="{{ $parent->id }}">

        <div class="row">
            <!-- Parent Information -->
            <div class="col-12 mb-3">
                <div class="card border-info">
                    <div class="card-body py-2">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <strong>Parent:</strong> {{ $parent->user->name }}<br>
                                <small class="text-muted">{{ $parent->user->email }}</small>
                            </div>
                            <div class="col-md-6 text-end">
                                <div id="balance_info" style="display: none;">
                                    <strong>Current Balance:</strong>
                                    <span id="current_balance" class="text-success">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Selection -->
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="student_id" class="form-label">Student (Optional)</label>
                    <select class="form-select" name="student_id" id="student_id">
                        <option value="">General Deposit</option>
                        @foreach($parent->children as $child)
                            <option value="{{ $child->id }}">
                                {{ $child->full_name }} - {{ $child->class?->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-text text-muted">
                        Select a specific student or leave blank for general deposit
                    </small>
                </div>
            </div>

            <!-- Amount -->
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="amount" class="form-label">
                        Amount <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" name="amount" id="amount"
                               step="0.01" min="0.01" required placeholder="0.00">
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="payment_method" class="form-label">
                        Payment Method <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" name="payment_method" id="payment_method" required>
                        <option value="">Select Payment Method</option>
                        <option value="1">
                            <i class="fa-solid fa-money-bill"></i> Cash
                        </option>
                        <option value="3">
                            <i class="fa-solid fa-mobile-alt"></i> Zaad
                        </option>
                        <option value="4">
                            <i class="fa-solid fa-mobile-alt"></i> Edahab
                        </option>
                    </select>
                </div>
            </div>


            <!-- Journal (Optional) -->
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label for="journal_id" class="form-label">Journal (Optional)</label>
                    <select class="form-select" name="journal_id" id="journal_id">
                        <option value="">Select Journal</option>
                        <!-- Will be populated via AJAX if needed -->
                    </select>
                </div>
            </div>

            <!-- Deposit Reason -->
            <div class="col-12">
                <div class="form-group mb-3">
                    <label for="deposit_reason" class="form-label">Deposit Reason</label>
                    <textarea class="form-control" name="deposit_reason" id="deposit_reason"
                              rows="3" placeholder="Optional reason for this deposit..."></textarea>
                </div>
            </div>

            <!-- Quick Amount Buttons -->
            <div class="col-12">
                <div class="form-group mb-3">
                    <label class="form-label">Quick Amount Selection</label>
                    <div class="btn-group-sm d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary quick-amount" data-amount="50">$50</button>
                        <button type="button" class="btn btn-outline-primary quick-amount" data-amount="100">$100</button>
                        <button type="button" class="btn btn-outline-primary quick-amount" data-amount="200">$200</button>
                        <button type="button" class="btn btn-outline-primary quick-amount" data-amount="500">$500</button>
                        <button type="button" class="btn btn-outline-primary quick-amount" data-amount="1000">$1,000</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Method Information -->
        <div class="alert alert-info">
            <h6><i class="fa-solid fa-info-circle me-2"></i>Payment Method Information</h6>
            <ul class="mb-0">
                <li><strong>Cash:</strong> Payment collected in person</li>
                <li><strong>Zaad:</strong> Mobile money transfer via Zaad service</li>
                <li><strong>Edahab:</strong> Mobile money transfer via Edahab service</li>
            </ul>
            <small class="text-muted mt-2 d-block">
                <i class="fa-solid fa-info me-1"></i>
                No transaction reference required for any payment method.
            </small>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success" id="submitDeposit">
            <i class="fa-solid fa-plus-circle me-2"></i>Create Deposit
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

    // Load available journals from system
    function loadJournals() {
        $.ajax({
            url: '{{ route("parent-deposits.get-journals") }}',
            type: 'GET',
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