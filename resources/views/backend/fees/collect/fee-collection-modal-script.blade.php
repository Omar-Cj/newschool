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
                if (!response || !response.success) {
                    showErrorMessage(response?.message || '{{ ___("fees.payment_failed") }}');
                    return;
                }

                showSuccessMessage(response.message);

                const paymentId = response.payment_id;
                const paymentDetails = response.payment_details || {};

                selectedFees = [];
                totalAmount = 0;
                payableAmount = 0;

                // Close the collection modal to reveal the options modal
                $('#modalCustomizeWidth').modal('hide');

                if (paymentId) {
                    // Attempt to open print window immediately
                    if (window.ReceiptActions) {
                        window.ReceiptActions.printReceipt(paymentId);

                        window.ReceiptActions.loadOptionsModal(paymentId, {
                            onShown: function () {
                                // Optionally, we could inject extra details here if needed
                            }
                        }).then(function(modal) {
                            if (!modal) {
                                return;
                            }

                            // Refresh the page when the modal is closed to reflect updated balances
                            modal.on('hidden.bs.modal', function () {
                                window.location.reload();
                            });
                        });
                    } else {
                        window.open(`{{ route('fees.receipt.individual', '__PAYMENT_ID__') }}`.replace('__PAYMENT_ID__', paymentId) + '?print=1', '_blank');
                        window.location.href = `{{ route('fees.receipt.options', '__PAYMENT_ID__') }}`.replace('__PAYMENT_ID__', paymentId);
                    }
                } else {
                    // Fallback for unexpected missing payment id
                    window.location.reload();
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
        // Identify source and extract display items
        const source = feesData.source || 'service_based';
        const displayFees = Array.isArray(feesData.fees) ? feesData.fees : [];

        // Persist only legacy items for submission (service-based is display-only; backend handles via fees_source)
        selectedFees = source === 'legacy' ? displayFees : [];
        totalAmount = parseFloat(feesData.totalAmount || 0);
        payableAmount = parseFloat(feesData.payableAmount || 0);

        // Track student info
        currentStudentName = (studentInfo?.name || studentInfo?.student_name || '').trim();
        if (!currentStudentName) currentStudentName = '{{ ___('common.student') }}';

        // Populate base fields
        $('#modal_student_id').val(studentId);
        $('#payment_amount').val(payableAmount.toFixed(2));
        $('#modal_fees_assign_childrens').val(JSON.stringify(selectedFees));
        $('#fees_source').val(source);

        // Update labels in header and summary
        $('#feeCollectionModalLabel').text(`{{ ___('fees.Fee Collection') }} - ${currentStudentName}`);
        $('#summary-student-name').text(currentStudentName);

        // Update summary display and calculations (use display fees, not submission list)
        updateFeesSummary(displayFees);
        calculateNetAmount();
    };

    function updateFeesSummary(fees) {
        const summaryContainer = $('#selected-fees-summary');
        summaryContainer.empty();

        if (!fees || !fees.length) {
            summaryContainer.append('<div class="text-muted">No outstanding fees</div>');
        } else {
            const hasPeriods = fees.some(function(f){ return !!f.billing_period; });
            if (hasPeriods) {
                // Group by billing period (YYYY-MM) with collapsible sections
                const groups = {};
                fees.forEach(function(fee){
                    const p = fee.billing_period || 'unknown';
                    groups[p] = groups[p] || [];
                    groups[p].push(fee);
                });

                Object.keys(groups).sort().forEach(function(period, idx){
                    const pretty = formatBillingPeriod(period);
                    const groupId = `fee-period-${(period || 'unknown').replace(/[^a-zA-Z0-9]/g, '-')}-${idx}`;
                    const groupEl = $(`
                        <div class="fee-period-group">
                            <div class="fee-period-heading d-flex justify-content-between align-items-center collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#${groupId}" aria-expanded="false" aria-controls="${groupId}">
                                <span class="d-flex align-items-center">
                                    <i class="fas fa-chevron-down fee-period-caret me-2"></i>
                                    <span>${pretty}</span>
                                </span>
                                <span class="text-muted small">${groups[period].length} item(s)</span>
                            </div>
                            <div id="${groupId}" class="collapse"></div>
                        </div>
                    `);
                    summaryContainer.append(groupEl);

                    const itemsEl = groupEl.find(`#${groupId}`);
                    groups[period].forEach(function(fee){
                        const safeAmount = parseFloat(fee.amount || 0).toFixed(2);
                        itemsEl.append(`
                            <div class="fee-item">
                                <div class="fee-item-name">${fee.name}</div>
                                <div class="fee-item-amount">{{ Setting('currency_symbol') }}${safeAmount}</div>
                            </div>
                        `);
                    });
                });
            } else {
                // Flat list fallback
                fees.forEach(function(fee) {
                    const safeAmount = parseFloat(fee.amount || 0).toFixed(2);
                    summaryContainer.append(`
                        <div class="fee-item">
                            <div class="fee-item-name">${fee.name}</div>
                            <div class="fee-item-amount">{{ Setting('currency_symbol') }}${safeAmount}</div>
                        </div>
                    `);
                });
            }
        }

        // Preserve totals + outstanding
        $('#total-amount').text('{{ Setting("currency_symbol") }}' + totalAmount.toFixed(2));
        $('#payable-amount').text('{{ Setting("currency_symbol") }}' + payableAmount.toFixed(2));
        $('#summary-outstanding-amount').text('{{ Setting("currency_symbol") }}' + payableAmount.toFixed(2));
    }

    // Helper to format YYYY-MM to localized "Mon YYYY"
    function formatBillingPeriod(period) {
        if (!period || period.length < 7) return period || '—';
        const parts = period.split('-');
        const y = parseInt(parts[0]);
        const m = parseInt(parts[1]);
        const d = new Date(y, m - 1, 1);
        return isNaN(d.getTime()) ? period : d.toLocaleString(undefined, { month: 'short', year: 'numeric' });
    }
});
</script>
