<script>
$(document).ready(function() {
    // Initialize modal variables
    let selectedFees = [];
    let totalAmount = 0;
    let payableAmount = 0;
    let currentStudentName = '';

    // Load journals on modal open
    $('#modalCustomizeWidth').on('show.bs.modal', function() {
        loadJournals();
    });

    // Payment method change handler
    $('input[name="payment_method"]').change(function() {
        const method = $(this).val();
        if (method === 'zaad' || method === 'edahab') {
            $('#transaction_reference_field').show();
            $('#transaction_reference').prop('required', true);
        } else {
            $('#transaction_reference_field').hide();
            $('#transaction_reference').prop('required', false).val('');
        }
    });

    // Discount type change handler
    $('#discount_type').change(function() {
        const discountType = $(this).val();
        if (discountType) {
            $('#discount_amount').prop('disabled', false).prop('required', true);
            if (discountType === 'percentage') {
                $('#discount_amount').attr('max', '100');
            } else {
                $('#discount_amount').removeAttr('max');
            }
        } else {
            $('#discount_amount').prop('disabled', true).prop('required', false).val('');
        }
        calculateNetAmount();
    });

    // Real-time calculation
    $('#payment_amount, #discount_amount').on('input', function() {
        calculateNetAmount();
    });

    // Pay full amount button
    $('#pay_full_amount').click(function() {
        $('#payment_amount').val(payableAmount.toFixed(2));
        calculateNetAmount();
    });

    // Form submission
    $('#feeCollectionForm').submit(function(e) {
        e.preventDefault();

        if (!validateForm()) {
            return false;
        }

        processPayment();
    });

    // Functions
    function loadJournals() {
        $.ajax({
            url: '{{ route("journals.dropdown") }}',
            method: 'GET',
            success: function(response) {
                const journalSelect = $('#journal_id');
                journalSelect.empty().append('<option value="">{{ ___("fees.select_journal") }}</option>');

                response.forEach(function(journal) {
                    journalSelect.append(`<option value="${journal.id}">${journal.text}</option>`);
                });
            },
            error: function() {
                showErrorMessage('{{ ___("fees.failed_to_load_journals") }}');
            }
        });
    }

    function calculateNetAmount() {
        const paymentAmount = parseFloat($('#payment_amount').val()) || 0;
        const discountType = $('#discount_type').val();
        const discountValue = parseFloat($('#discount_amount').val()) || 0;
        let discountAmount = 0;

        if (discountType && discountValue > 0) {
            if (discountType === 'percentage') {
                discountAmount = (paymentAmount * discountValue) / 100;
            } else {
                discountAmount = discountValue;
            }
        }

        const netAmount = paymentAmount - discountAmount;

        // Update display
        $('#display_payment_amount').text('{{ Setting("currency_symbol") }}' + paymentAmount.toFixed(2));
        $('#display_discount_amount').text('{{ Setting("currency_symbol") }}' + discountAmount.toFixed(2));
        $('#display_net_amount').text('{{ Setting("currency_symbol") }}' + Math.max(0, netAmount).toFixed(2));
    }

    function validateForm() {
        let isValid = true;

        // Clear previous errors
        $('.error-message').remove();

        // Validate payment amount
        const paymentAmount = parseFloat($('#payment_amount').val());
        if (!paymentAmount || paymentAmount <= 0) {
            showFieldError('#payment_amount', '{{ ___("fees.payment_amount_required") }}');
            isValid = false;
        } else if (paymentAmount > payableAmount) {
            showFieldError('#payment_amount', '{{ ___("fees.payment_amount_exceeds_total") }}');
            isValid = false;
        }

        // Validate payment method
        if (!$('input[name="payment_method"]:checked').val()) {
            showFieldError('input[name="payment_method"]', '{{ ___("fees.payment_method_required") }}');
            isValid = false;
        }

        // Validate transaction reference for digital payments
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        if ((paymentMethod === 'zaad' || paymentMethod === 'edahab') && !$('#transaction_reference').val().trim()) {
            showFieldError('#transaction_reference', '{{ ___("fees.transaction_reference_required") }}');
            isValid = false;
        }

        // Validate journal selection
        if (!$('#journal_id').val()) {
            showFieldError('#journal_id', '{{ ___("fees.journal_required") }}');
            isValid = false;
        }

        // Validate discount
        const discountType = $('#discount_type').val();
        const discountValue = parseFloat($('#discount_amount').val()) || 0;
        if (discountType && discountValue > 0) {
            if (discountType === 'percentage' && discountValue > 100) {
                showFieldError('#discount_amount', '{{ ___("fees.discount_percentage_invalid") }}');
                isValid = false;
            }
        }

        return isValid;
    }

    function showFieldError(selector, message) {
        const field = $(selector);
        const errorDiv = $('<div class="error-message"></div>').text(message);
        field.closest('.mb-3').append(errorDiv);
        field.addClass('is-invalid');
    }

    function processPayment() {
        const submitBtn = $('#process_payment_btn');
        const originalText = submitBtn.html();

        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>{{ ___("fees.processing") }}');

        // Prepare form data
        const formData = new FormData($('#feeCollectionForm')[0]);

        // Add selected fees data
        formData.append('fees_assign_childrens', JSON.stringify(selectedFees));
        formData.append('total_amount', totalAmount);
        formData.append('payable_amount', payableAmount);

        $.ajax({
            url: $('#feeCollectionForm').attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showSuccessMessage(response.message);

                    // Close modal after 2 seconds
                    setTimeout(function() {
                        $('#modalCustomizeWidth').modal('hide');
                        // Refresh the page or update the fee list
                        location.reload();
                    }, 2000);
                } else {
                    showErrorMessage(response.message || '{{ ___("fees.payment_failed") }}');
                }
            },
            error: function(xhr) {
                let errorMessage = '{{ ___("fees.payment_failed") }}';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }

                showErrorMessage(errorMessage);
            },
            complete: function() {
                // Restore button state
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    function showErrorMessage(message) {
        const alertDiv = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('.modal-body').prepend(alertDiv);

        // Auto dismiss after 5 seconds
        setTimeout(function() {
            $('.alert-danger').fadeOut();
        }, 5000);
    }

    function showSuccessMessage(message) {
        const alertDiv = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('.modal-body').prepend(alertDiv);
    }

    // Global function to populate modal with selected fees (called from parent page)
    window.populateFeeCollectionModal = function(studentId, feesData, studentInfo = null) {
        // Extract fees and amounts
        selectedFees = Array.isArray(feesData.fees) ? feesData.fees : [];
        totalAmount = parseFloat(feesData.totalAmount || 0);
        payableAmount = parseFloat(feesData.payableAmount || 0);

        // Track student info
        currentStudentName = (studentInfo?.name || studentInfo?.student_name || '').trim();
        if (!currentStudentName) currentStudentName = '{{ ___('common.student') }}';

        // Populate base fields
        $('#modal_student_id').val(studentId);
        $('#payment_amount').val(payableAmount.toFixed(2));
        $('#modal_fees_assign_childrens').val(JSON.stringify(selectedFees));

        // Update labels in header and summary
        $('#feeCollectionModalLabel').text(`{{ ___('fees.Fee Collection') }} - ${currentStudentName}`);
        $('#summary-student-name').text(currentStudentName);

        // Update summary display and calculations
        updateFeesSummary(selectedFees);
        calculateNetAmount();
    };

    function updateFeesSummary(fees) {
        const summaryContainer = $('#selected-fees-summary');
        summaryContainer.empty();

        if (!fees || fees.length === 0) {
            summaryContainer.append('<div class="text-muted">{{ ___('fees.no_outstanding_fees') }}</div>');
        } else {
            fees.forEach(function(fee) {
                const safeAmount = parseFloat(fee.amount || 0).toFixed(2);
                const feeItem = `
                    <div class="fee-item">
                        <div class="fee-item-name">${fee.name}</div>
                        <div class="fee-item-amount">{{ Setting('currency_symbol') }}${safeAmount}</div>
                    </div>
                `;
                summaryContainer.append(feeItem);
            });
        }

        $('#total-amount').text('{{ Setting("currency_symbol") }}' + totalAmount.toFixed(2));
        $('#payable-amount').text('{{ Setting("currency_symbol") }}' + payableAmount.toFixed(2));
        $('#summary-outstanding-amount').text('{{ Setting("currency_symbol") }}' + payableAmount.toFixed(2));
    }
});
</script>
