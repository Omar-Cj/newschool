{{-- Receipt Options Modal --}}
<div class="modal fade" id="receiptOptionsModal" tabindex="-1" aria-labelledby="receiptOptionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h4 class="modal-title" id="receiptOptionsModalLabel">
                    <i class="fa-solid fa-check-circle me-2"></i>{{ ___('fees.payment_successful') }}
                </h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- Success Message --}}
                <div class="alert alert-success d-flex align-items-center mb-4">
                    <i class="fa-solid fa-check-circle me-3 fa-2x"></i>
                    <div>
                        <h5 class="mb-1">{{ ___('fees.payment_processed_successfully') }}</h5>
                        <p class="mb-0">{{ ___('fees.payment_confirmation_message') }}</p>
                    </div>
                </div>

                {{-- Payment Summary --}}
                <div class="card border-primary mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fa-solid fa-receipt me-2"></i>{{ ___('fees.payment_summary') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ ___('student_info.student_name') }}:</strong><br>
                                   {{ $payment->student->first_name }} {{ $payment->student->last_name }}</p>
                                <p><strong>{{ ___('student_info.admission_no') }}:</strong><br>
                                   {{ $payment->student->admission_no }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ ___('fees.payment_date') }}:</strong><br>
                                   {{ dateFormat($payment->date) }}</p>
                                <p><strong>{{ ___('fees.amount_paid') }}:</strong><br>
                                   <span class="h5 text-success">{{ Setting('currency_symbol') }} {{ number_format($payment->amount, 2) }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Receipt Options --}}
                <div class="receipt-options">
                    <h5 class="mb-3"><i class="fa-solid fa-download me-2"></i>{{ ___('fees.receipt_options') }}</h5>
                    
                    <div class="row g-3">
                        {{-- Individual Receipt --}}
                        <div class="col-md-6">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fa-solid fa-file-pdf fa-3x text-info"></i>
                                    </div>
                                    <h6 class="card-title">{{ ___('fees.individual_receipt') }}</h6>
                                    <p class="card-text small">{{ ___('fees.individual_receipt_description') }}</p>
                                    <a href="{{ route('fees.receipt.individual', $payment->id) }}" 
                                       class="btn btn-info btn-sm w-100" target="_blank">
                                        <i class="fa-solid fa-download me-2"></i>{{ ___('fees.download_receipt') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Student Summary Receipt --}}
                        <div class="col-md-6">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fa-solid fa-file-lines fa-3x text-warning"></i>
                                    </div>
                                    <h6 class="card-title">{{ ___('fees.student_summary') }}</h6>
                                    <p class="card-text small">{{ ___('fees.student_summary_description') }}</p>
                                    <a href="{{ route('fees.receipt.student-summary', $payment->student_id) }}" 
                                       class="btn btn-warning btn-sm w-100" target="_blank">
                                        <i class="fa-solid fa-download me-2"></i>{{ ___('fees.download_summary') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Options --}}
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h6><i class="fa-solid fa-lightbulb me-2"></i>{{ ___('fees.additional_options') }}</h6>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-primary btn-sm w-100" 
                                            onclick="printReceipt({{ $payment->id }})">
                                        <i class="fa-solid fa-print me-2"></i>{{ ___('fees.print_receipt') }}
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-success btn-sm w-100" 
                                            onclick="emailReceipt({{ $payment->id }})">
                                        <i class="fa-solid fa-envelope me-2"></i>{{ ___('fees.email_receipt') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Group Receipt Option (if multiple payments today) --}}
                    <div class="mt-3" id="group-receipt-option" style="display: none;">
                        <div class="alert alert-warning">
                            <h6><i class="fa-solid fa-users me-2"></i>{{ ___('fees.group_receipt_available') }}</h6>
                            <p class="small mb-2">{{ ___('fees.group_receipt_description') }}</p>
                            <button type="button" class="btn btn-warning btn-sm" onclick="generateGroupReceipt()">
                                <i class="fa-solid fa-download me-2"></i>{{ ___('fees.generate_group_receipt') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times me-2"></i>{{ ___('common.close') }}
                </button>
                <button type="button" class="btn btn-primary" onclick="collectAnotherPayment()">
                    <i class="fa-solid fa-plus me-2"></i>{{ ___('fees.collect_another_payment') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Show receipt options modal after payment success
function showReceiptOptions(paymentId) {
    // Check if there are multiple payments today for group receipt option
    checkGroupReceiptAvailability();
    
    $('#receiptOptionsModal').modal('show');
}

// Check if group receipt is available
function checkGroupReceiptAvailability() {
    $.get('{{ route("fees.receipt.check-group-availability") }}')
        .done(function(response) {
            if (response.has_multiple_payments) {
                $('#group-receipt-option').show();
            }
        });
}

// Print receipt function
function printReceipt(paymentId) {
    const printWindow = window.open(
        '{{ route("fees.receipt.individual", ":id") }}'.replace(':id', paymentId),
        '_blank',
        'width=800,height=600'
    );
    
    printWindow.onload = function() {
        printWindow.print();
    };
}

// Email receipt function
function emailReceipt(paymentId) {
    // Show email form or send directly
    $.post('{{ route("fees.receipt.email") }}', {
        payment_id: paymentId,
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        if (response.success) {
            showAlert('{{ ___("fees.receipt_emailed_successfully") }}', 'success');
        } else {
            showAlert(response.message, 'error');
        }
    })
    .fail(function() {
        showAlert('{{ ___("fees.email_failed") }}', 'error');
    });
}

// Generate group receipt
function generateGroupReceipt() {
    $.get('{{ route("fees.receipt.today-payments") }}')
        .done(function(response) {
            if (response.payment_ids && response.payment_ids.length > 0) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("fees.receipt.group") }}';
                form.target = '_blank';
                
                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                // Add payment IDs
                response.payment_ids.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'payment_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });
                
                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            }
        });
}

// Collect another payment
function collectAnotherPayment() {
    $('#receiptOptionsModal').modal('hide');
    // Reload the fee collection page or reset form
    window.location.reload();
}

// Alert function (you can customize this based on your alert system)
function showAlert(message, type) {
    // Implementation depends on your alert system (toastr, SweetAlert, etc.)
    alert(message);
}
</script>
