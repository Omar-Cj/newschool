<script src="{{ asset('backend/assets/js/sibling-fee-collection.js') }}"></script>

<script>
// Ensure jQuery is loaded before executing
(function($) {
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded. Please ensure jQuery is loaded before this script.');
        return;
    }
    
    // Initialize modal variables (global scope)
    let selectedFees = [];
    let totalAmount = 0;
    let payableAmount = 0;
    let currentStudentName = '';

    // Global function to populate modal with family payment data (called from parent page)
    window.populateFeeCollectionModal = function(studentId, feesData, studentInfo = null) {
        console.log('Populating family payment modal for student:', studentId);
        
        // Track student info
        currentStudentName = (studentInfo?.name || studentInfo?.student_name || '').trim();
        if (!currentStudentName) currentStudentName = '{{ ___('common.student') }}';

        // Update modal title and student name
        $('#feeCollectionModalLabel').text(`{{ ___('fees.Family Payment') }} - ${currentStudentName}`);
        $('#modal_student_id').val(studentId);
        $('#primary_student_id').val(studentId);

        // Show loading state
        $('#sibling-loading').show();
        $('#sibling-payment-interface').hide();
        $('#no-siblings-message').hide();
        $('#sibling-payment-footer').hide();
        $('#individual-payment-interface').hide();
        $('#individual-payment-footer').hide();
        
        // Remove required attributes from individual payment fields when showing family payment
        $('#payment_amount').removeAttr('required');
        $('#payment_method').removeAttr('required');
        $('#journal_id').removeAttr('required');
        $('#payment_date').removeAttr('required');

        // Load family payment data
        console.log('Checking sibling fee manager availability:', typeof window.siblingFeeManager);
        console.log('Student ID:', studentId);

        // Wait for sibling fee manager to be available
        if (!window.siblingFeeManager) {
            console.error('SiblingFeeCollectionManager not initialized! Family payment unavailable.');
            $('#sibling-loading').hide();
            $('#no-siblings-message').show();
            return;
        }

        if (window.siblingFeeManager && studentId) {
            console.log('Loading sibling data...');
            // Load sibling data directly
            window.siblingFeeManager.loadSiblingData(studentId).then(() => {
                console.log('Family payment data loaded successfully');
            }).catch(error => {
                console.error('Error loading family payment data:', error);
                // Check if this is a "no siblings" case or an actual error
                if (error.message && error.message.includes('no siblings')) {
                    // Show individual payment interface for students without siblings
                    showIndividualPaymentInterface(studentId, feesData, studentInfo);
                } else {
                    // Show no siblings message for other errors
                    $('#sibling-loading').hide();
                    $('#no-siblings-message').show();
                }
            });
        } else {
            console.error('Sibling fee manager not available, falling back to individual payment');
            // Fallback to individual payment interface
            showIndividualPaymentInterface(studentId, feesData, studentInfo);
        }
    };

    window.showIndividualPaymentInterface = function(studentId, feesData, studentInfo) {
        console.log('Showing individual payment interface for student:', studentId);
        
        // Hide loading and show individual payment interface
        $('#sibling-loading').hide();
        $('#no-siblings-message').hide();
        
        // Update modal title
        $('#feeCollectionModalLabel').text(`{{ ___('fees.Fee Collection') }} - ${currentStudentName}`);
        
        // Show individual payment interface
        $('#individual-payment-interface').show();
        $('#sibling-payment-interface').hide();
        
        // Make individual payment fields required
        $('#payment_amount').attr('required', 'required');
        $('#payment_method').attr('required', 'required');
        $('#journal_id').attr('required', 'required');
        $('#payment_date').attr('required', 'required');
        
        // Populate individual payment data
        if (feesData && feesData.fees) {
            selectedFees = Array.isArray(feesData.fees) ? feesData.fees : [];
            totalAmount = parseFloat(feesData.totalAmount || 0);
            payableAmount = parseFloat(feesData.payableAmount || 0);
            
            $('#payment_amount').val(payableAmount.toFixed(2));
            updateFeesSummary(selectedFees);
            calculateNetAmount();
        }
        
        // Show individual payment footer
        $('#individual-payment-footer').show();
        $('#sibling-payment-footer').hide();
    };
    
    $(document).ready(function() {
        // Note: SiblingFeeCollectionManager is initialized in DOMContentLoaded in sibling-fee-collection.js
        // We just verify it's available here
        if (typeof window.siblingFeeManager === 'undefined') {
            console.warn('SiblingFeeCollectionManager not initialized yet');
        } else {
            console.log('SiblingFeeManager instance available:', !!window.siblingFeeManager);
        }

    // Load journals on modal open
    $('#modalCustomizeWidth').on('show.bs.modal', function() {
        // Load journals using the sibling fee manager's method
        if (window.siblingFeeManager) {
            window.siblingFeeManager.loadSiblingJournals();
        } else {
            // Fallback to individual journals loading
            loadJournals();
        }
    });

    // Initialize Select2 dropdowns when modal is shown
    $('#modalCustomizeWidth').on('shown.bs.modal', function() {
        initializeSelect2Dropdowns();
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
        updatePartialPaymentIndicator();
    });

        // Note: Family payment UI controls (distribution buttons, payment mode, validate button)
        // are all bound by SiblingFeeCollectionManager class to avoid duplicate event handlers

        // Family payment discount handlers
        $('#family_discount_type').change(function() {
            const discountType = $(this).val();
            if (discountType) {
                $('#family_discount_amount').prop('disabled', false).prop('required', true);
                if (discountType === 'percentage') {
                    $('#family_discount_amount').attr('max', '100');
                } else {
                    $('#family_discount_amount').removeAttr('max');
                }
            } else {
                $('#family_discount_amount').prop('disabled', true).prop('required', false).val('');
            }
            calculateFamilyNetAmount();
        });

        // Real-time discount calculation
        $('#family_discount_amount').on('input', function() {
            calculateFamilyNetAmount();
        });

    // Form submission - Unified handler for both family and individual payment
    $('#siblingPaymentForm').submit(function(e) {
        e.preventDefault();
        console.log('Form submission triggered');

        // Check which interface is visible
        if ($('#sibling-payment-interface').is(':visible')) {
            // Family payment interface is active
            console.log('Family payment form submission triggered');
            
            if (!validateFamilyPaymentForm()) {
                console.log('Family payment form validation failed');
                return false;
            }

            console.log('Family payment form validation passed, calling processFamilyPayment');
            processFamilyPayment();
        } else if ($('#individual-payment-interface').is(':visible')) {
            // Individual payment interface is active
            console.log('Individual payment form submission triggered');
            
            if (!validateForm()) {
                console.log('Individual payment form validation failed');
                return false;
            }

            console.log('Individual payment form validation passed, calling processPayment');
            processPayment();
        } else {
            console.error('No payment interface is visible');
            showErrorMessage('No payment interface is active. Please refresh and try again.');
        }
    });

    // Functions
    window.loadJournals = function() {
        $.ajax({
            url: '{{ route("journals.dropdown") }}',
            method: 'GET',
            success: function(response) {
                const journalSelect = $('#journal_id');
                journalSelect.empty().append('<option value="">{{ ___("fees.select_journal") }}</option>');

                response.forEach(function(journal) {
                    journalSelect.append(`<option value="${journal.id}">${journal.text}</option>`);
                });

                // Re-initialize Select2 for journal dropdown after AJAX load
                if (journalSelect.hasClass('select2-hidden-accessible')) {
                    journalSelect.select2('destroy');
                }
                journalSelect.select2({
                    placeholder: "{{ ___('fees.select_journal') }}",
                    allowClear: false,
                    width: '100%',
                    dropdownParent: $('#modalCustomizeWidth')
                });
            },
            error: function() {
                showErrorMessage('{{ ___("fees.failed_to_load_journals") }}');
            }
        });
    }

    function initializeSelect2Dropdowns() {
        const modalParent = $('#modalCustomizeWidth');

        // Initialize Payment Method dropdown
        $('#payment_method').select2({
            placeholder: "{{ ___('fees.select_payment_method') }}",
            allowClear: false,
            width: '100%',
            dropdownParent: modalParent
        });

        // Initialize Journal dropdown (will be re-initialized after AJAX load)
        $('#journal_id').select2({
            placeholder: "{{ ___('fees.select_journal') }}",
            allowClear: false,
            width: '100%',
            dropdownParent: modalParent
        });

        // Initialize Discount Type dropdown
        $('#discount_type').select2({
            placeholder: "{{ ___('fees.No Discount') }}",
            allowClear: true,
            width: '100%',
            dropdownParent: modalParent
        });

        // Initialize Family Discount Type dropdown
        $('#family_discount_type').select2({
            placeholder: "{{ ___('fees.No Discount') }}",
            allowClear: true,
            width: '100%',
            dropdownParent: modalParent
        });
    }

    window.processPayment = function() {
        console.log('Processing individual payment...');
        const submitBtn = $('#process_payment_btn');
        const originalText = submitBtn.text();
        
        // Disable submit button and show loading
        submitBtn.prop('disabled', true).text('Processing...');
        
        // Collect form data
        const formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('student_id', $('#modal_student_id').val());
        formData.append('payment_amount', $('#payment_amount').val());
        formData.append('payment_method', $('#payment_method').val());
        formData.append('transaction_reference', $('#transaction_reference').val());
        formData.append('journal_id', $('#journal_id').val());
        formData.append('discount_type', $('#discount_type').val());
        formData.append('discount_amount', $('#discount_amount').val());
        formData.append('payment_date', $('#payment_date').val());
        formData.append('notes', $('#notes').val());
        formData.append('fees_source', 'individual_payment');
        
        // Submit payment
        $.ajax({
            url: $('#siblingPaymentForm').attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Payment processed successfully:', response);
                if (response.success) {
                    showSuccessMessage('Payment processed successfully!');
                    $('#modalCustomizeWidth').modal('hide');
                    // Refresh the page or update the UI as needed
                    if (typeof window.location !== 'undefined') {
                        window.location.reload();
                    }
                } else {
                    showErrorMessage(response.message || 'Payment processing failed');
                }
            },
            error: function(xhr, status, error) {
                console.error('Payment processing error:', error);
                let errorMessage = 'An error occurred while processing payment';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showErrorMessage(errorMessage);
            },
            complete: function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    };

    window.calculateNetAmount = function() {
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

    function calculateFamilyNetAmount() {
        const totalPayment = window.siblingFeeManager ? window.siblingFeeManager.getTotalPaymentAmount() : 0;
        const discountType = $('#family_discount_type').val();
        const discountValue = parseFloat($('#family_discount_amount').val()) || 0;
        let discountAmount = 0;

        if (discountType && discountValue > 0) {
            if (discountType === 'percentage') {
                discountAmount = (totalPayment * discountValue) / 100;
            } else {
                discountAmount = discountValue;
            }
        }

        const netAmount = totalPayment - discountAmount;

        // Update display
        $('#family-total-payment').text('{{ Setting("currency_symbol") }}' + totalPayment.toFixed(2));
        $('#family-discount-amount').text('{{ Setting("currency_symbol") }}' + discountAmount.toFixed(2));
        $('#family-net-amount').text('{{ Setting("currency_symbol") }}' + Math.max(0, netAmount).toFixed(2));
    }

    function updatePartialPaymentIndicator() {
        const paymentAmount = parseFloat($('#payment_amount').val()) || 0;
        const remainingBalance = payableAmount - paymentAmount;

        // Remove existing indicators
        $('.partial-payment-indicator').remove();

        if (paymentAmount > 0 && remainingBalance > 0) {
            // This is a partial payment
            const indicator = $(`
                <div class="partial-payment-indicator alert alert-info mt-2">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>{{ ___("fees.partial_payment") }}:</strong>
                    {{ ___("fees.remaining_balance_will_be") }} {{ Setting("currency_symbol") }}${remainingBalance.toFixed(2)}
                </div>
            `);
            $('#payment_amount').closest('.mb-3').after(indicator);
        } else if (paymentAmount >= payableAmount && payableAmount > 0) {
            // This is a full payment
            const indicator = $(`
                <div class="partial-payment-indicator alert alert-success mt-2">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>{{ ___("fees.full_payment") }}:</strong>
                    {{ ___("fees.fee_will_be_fully_paid") }}
                </div>
            `);
            $('#payment_amount').closest('.mb-3').after(indicator);
        }
    }

    function validateForm() {
        console.log('validateForm called');
        let isValid = true;

        // Clear previous errors
        $('.error-message').remove();

        // Validate payment amount
        const paymentAmount = parseFloat($('#payment_amount').val());
        console.log('Payment amount:', paymentAmount);
        console.log('Payable amount:', payableAmount);
        if (!paymentAmount || paymentAmount <= 0) {
            console.log('Payment amount validation failed');
            showFieldError('#payment_amount', '{{ ___("fees.payment_amount_required") }}');
            isValid = false;
        } else if (paymentAmount > payableAmount) {
            console.log('Payment amount exceeds total');
            showFieldError('#payment_amount', '{{ ___("fees.payment_amount_exceeds_total") }}');
            isValid = false;
        }

        // Validate payment method (dropdown)
        const selectedPaymentMethod = $('#payment_method').val();
        console.log('Selected payment method:', selectedPaymentMethod);
        if (!selectedPaymentMethod) {
            console.log('Payment method validation failed');
            showFieldError('#payment_method', '{{ ___("fees.payment_method_required") }}');
            isValid = false;
        }

        // Validate journal selection
        const selectedJournal = $('#journal_id').val();
        console.log('Selected journal:', selectedJournal);
        if (!selectedJournal) {
            console.log('Journal validation failed');
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

        console.log('Validation result:', isValid);
        return isValid;
    }

    function validateFamilyPaymentForm() {
        console.log('Validating family payment form...');
        let isValid = true;

        // Clear previous errors
        $('.error-message').remove();
        $('.is-invalid').removeClass('is-invalid');

        // Debug: Check payment mode
        const paymentMode = $('input[name="payment_mode_radio"]:checked').val();
        console.log('Payment mode:', paymentMode);

        // Validate payment method (only for direct payments)
        if (paymentMode === 'direct') {
            const paymentMethod = $('#family_payment_method').val();
            console.log('Payment method value:', paymentMethod);
            if (!paymentMethod) {
                console.log('Payment method validation failed - no value');
                showFieldError('#family_payment_method', '{{ ___("fees.payment_method_required") }}');
                isValid = false;
            }
        }

        // Validate journal
        const journalId = $('#family_journal_id').val();
        console.log('Journal ID value:', journalId);
        if (!journalId) {
            console.log('Journal validation failed - no value');
            showFieldError('#family_journal_id', '{{ ___("fees.journal_required") }}');
            isValid = false;
        }


        // Validate that at least one sibling has payment amount
        let hasPayment = false;
        let paymentCount = 0;
        $('.payment-amount-input').each(function() {
            const amount = parseFloat($(this).val()) || 0;
            if (amount > 0) {
                hasPayment = true;
                paymentCount++;
            }
        });

        console.log('Payment inputs found:', $('.payment-amount-input').length);
        console.log('Payments with amount > 0:', paymentCount);
        console.log('Has payment:', hasPayment);

        if (!hasPayment) {
            console.log('Payment amount validation failed - no payments');
            showErrorMessage('{{ ___("fees.at_least_one_payment_required") }}');
            isValid = false;
        }

        // Validate discount
        const discountType = $('#family_discount_type').val();
        const discountValue = parseFloat($('#family_discount_amount').val()) || 0;
        if (discountType && discountValue > 0) {
            if (discountType === 'percentage' && discountValue > 100) {
                showFieldError('#family_discount_amount', '{{ ___("fees.discount_percentage_invalid") }}');
                isValid = false;
            }
        }

        console.log('Family payment validation result:', isValid);
        return isValid;
    }

    function showFieldError(selector, message) {
        const field = $(selector);
        const errorDiv = $('<div class="error-message"></div>').text(message);
        field.closest('.mb-3').append(errorDiv);
        field.addClass('is-invalid');
    }

    function processFamilyPayment() {
        console.log('processFamilyPayment function called');

        // Check validation state from class instance
        if (window.siblingFeeManager && !window.siblingFeeManager.validationState.isValid) {
            console.log('Validation state check failed');
            showErrorMessage('{{ ___("fees.please_validate_payment_first") }}');
            return;
        }

        const submitBtn = $('#process_sibling_payment_btn');
        const originalText = submitBtn.html();

        console.log('Submit button found:', submitBtn.length > 0);
        console.log('Form element found:', $('#siblingPaymentForm').length > 0);

        // Show loading state
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>{{ ___("fees.processing") }}');

        // Use the class method to collect payment data in correct format
        if (window.siblingFeeManager) {
            const paymentData = window.siblingFeeManager.collectPaymentData();
            console.log('Payment data collected:', paymentData);

            $.ajax({
                url: $('#siblingPaymentForm').attr('action'),
                method: 'POST',
                data: JSON.stringify(paymentData),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
            success: function(response) {
                if (!response || !response.success) {
                    showErrorMessage(response?.message || '{{ ___("fees.payment_failed") }}');
                    return;
                }

                // Enhanced success message for partial payments
                let successMessage = response.message;
                if (response.is_partial_payment) {
                    successMessage += `<br><small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        {{ ___("fees.remaining_balance") }}: {{ Setting("currency_symbol") }}${response.remaining_balance || 0}
                    </small>`;
                }
                showSuccessMessage(successMessage);

                const paymentId = response.payment_id;
                const paymentDetails = response.payment_details || {};

                selectedFees = [];
                totalAmount = 0;
                payableAmount = 0;

                // Close the family payment modal
                $('#modalCustomizeWidth').modal('hide');

                // Check if direct print URL is available (new improved flow)
                if (response.direct_print_url) {
                    // Open receipt directly in new window for immediate printing
                    window.open(response.direct_print_url, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');

                    // Refresh the page after a short delay to reflect updated balances
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else if (response.receipt_options && response.receipt_options.html) {
                    // Fallback: Show receipt options modal (for backward compatibility)
                    showReceiptOptionsModal(response.receipt_options.html, response.receipt_options.meta);
                } else if (paymentId) {
                    // Fallback to existing receipt options flow
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
        } else {
            console.error('SiblingFeeManager not available');
            showErrorMessage('Family payment system not available');
            submitBtn.prop('disabled', false).html(originalText);
        }
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


    window.updateFeesSummary = function(fees) {
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
                        let paymentStatusHtml = '';

                        // Add partial payment status if available
                        if (fee.partial_payment_info) {
                            const info = fee.partial_payment_info;
                            const paidAmount = parseFloat(info.paid_amount || 0);
                            const balanceAmount = parseFloat(info.balance_amount || 0);

                            if (info.payment_status === 'partial') {
                                paymentStatusHtml = `
                                    <div class="fee-payment-status text-warning small">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ ___("fees.partial") }}: {{ Setting('currency_symbol') }}${paidAmount.toFixed(2)} {{ ___("fees.paid") }}
                                        <br>{{ ___("fees.balance") }}: {{ Setting('currency_symbol') }}${balanceAmount.toFixed(2)}
                                    </div>
                                `;
                            } else if (info.payment_status === 'paid') {
                                paymentStatusHtml = `
                                    <div class="fee-payment-status text-success small">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ ___("fees.fully_paid") }}
                                    </div>
                                `;
                            }
                        }

                        itemsEl.append(`
                            <div class="fee-item">
                                <div class="fee-item-name">
                                    ${fee.name}
                                    ${paymentStatusHtml}
                                </div>
                                <div class="fee-item-amount">{{ Setting('currency_symbol') }}${safeAmount}</div>
                            </div>
                        `);
                    });
                });
            } else {
                // Flat list fallback
                fees.forEach(function(fee) {
                    const safeAmount = parseFloat(fee.amount || 0).toFixed(2);
                    let paymentStatusHtml = '';

                    // Add partial payment status if available
                    if (fee.partial_payment_info) {
                        const info = fee.partial_payment_info;
                        const paidAmount = parseFloat(info.paid_amount || 0);
                        const balanceAmount = parseFloat(info.balance_amount || 0);

                        if (info.payment_status === 'partial') {
                            paymentStatusHtml = `
                                <div class="fee-payment-status text-warning small">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ ___("fees.partial") }}: {{ Setting('currency_symbol') }}${paidAmount.toFixed(2)} {{ ___("fees.paid") }}
                                    <br>{{ ___("fees.balance") }}: {{ Setting('currency_symbol') }}${balanceAmount.toFixed(2)}
                                </div>
                            `;
                        } else if (info.payment_status === 'paid') {
                            paymentStatusHtml = `
                                <div class="fee-payment-status text-success small">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ ___("fees.fully_paid") }}
                                </div>
                            `;
                        }
                    }

                    summaryContainer.append(`
                        <div class="fee-item">
                            <div class="fee-item-name">
                                ${fee.name}
                                ${paymentStatusHtml}
                            </div>
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

    // Function to show receipt options modal from AJAX response
    function showReceiptOptionsModal(htmlContent, metaData) {
        // Remove any existing receipt options modal
        $('#receiptOptionsModal').remove();

        // Create modal container
        const modalContainer = $('<div>').html(htmlContent);
        $('body').append(modalContainer);

        // Initialize the modal
        const receiptModal = new bootstrap.Modal(document.getElementById('receiptOptionsModal'), {
            backdrop: 'static',
            keyboard: true
        });

        // Show the modal
        receiptModal.show();

        // Handle modal events
        $('#receiptOptionsModal').on('hidden.bs.modal', function () {
            // Clean up modal element
            $(this).remove();
            // Refresh the page to reflect updated balances
            window.location.reload();
        });

        // Handle print receipt functionality
        window.printReceipt = function(paymentId) {
            const printUrl = `{{ route('fees.receipt.individual', '__PAYMENT_ID__') }}`.replace('__PAYMENT_ID__', paymentId) + '?print=1';
            window.open(printUrl, '_blank', 'width=800,height=600,scrollbars=yes');
        };

        // Handle email receipt functionality
        window.emailReceipt = function(paymentId) {
            // Placeholder for email functionality
            alert('{{ ___("fees.email_feature_coming_soon") ?? "Email feature coming soon" }}');
        };

        // Handle collect another payment functionality
        window.collectAnotherPayment = function() {
            receiptModal.hide();
            // Reopen the fee collection modal
            setTimeout(() => {
                $('#modalCustomizeWidth').modal('show');
            }, 300);
        };

        // Handle group receipt generation
        window.generateGroupReceipt = function() {
            // This would be implemented if needed for partial payments
            alert('{{ ___("fees.group_receipt_not_available") ?? "Group receipts not available for partial payments" }}');
        };

        console.log('Receipt options modal displayed successfully', metaData);
    }
    });
})(jQuery);
</script>

<!-- Sibling Fee Collection JavaScript -->
<script>
if (typeof window.siblingFeeCollectionLoaded === 'undefined') {
    window.siblingFeeCollectionLoaded = true;
    // Load the script dynamically
    var script = document.createElement('script');
    script.src = '{{ asset("backend/assets/js/sibling-fee-collection.js") }}?v=' + Date.now();
    script.async = false;
    document.head.appendChild(script);
}
</script>
