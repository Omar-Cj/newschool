/**
 * Cash Transfer Create Form JavaScript
 * Handles journal selection, balance display, validation, and form submission
 */

(function ($) {
    'use strict';

    // Configuration from Blade template
    const config = window.cashTransferCreateConfig || {};

    // Debug: Log configuration
    console.log('🎨 [CREATE] Config Loaded:', config);
    console.log('🎨 [CREATE] Journals API URL:', config.journalsApiUrl);

    // Store selected journal data
    let selectedJournal = null;

    /**
     * Initialize the page
     */
    function init() {
        loadJournals();
        bindEventHandlers();
    }

    /**
     * Load active journals for selection
     */
    function loadJournals() {
        console.log('📚 [CREATE-JOURNALS] Loading journals...');
        console.log('📚 [CREATE-JOURNALS] API URL:', config.journalsApiUrl);

        $.ajax({
            url: config.journalsApiUrl,
            method: 'GET',
            data: { status: 'active' },
            success: function (response) {
                console.log('✅ [CREATE-JOURNALS] AJAX Success:', response);

                if (response.status && response.data) {
                    console.log('📚 [CREATE-JOURNALS] Found journals:', response.data.length);
                    populateJournalDropdown(response.data);
                    console.log('✅ [CREATE-JOURNALS] Dropdown populated');
                } else {
                    console.warn('⚠️ [CREATE-JOURNALS] Invalid response format:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ [CREATE-JOURNALS] AJAX Error:', {
                    url: config.journalsApiUrl,
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    statusCode: xhr.status
                });
                showAlert('error', config.translations.error);
            }
        });
    }

    /**
     * Populate journal dropdown
     */
    function populateJournalDropdown(journals) {
        const $select = $('#journal_id');
        $select.find('option:not(:first)').remove();

        journals.forEach(function (journal) {
            $select.append(
                $('<option></option>')
                    .val(journal.id)
                    .text(journal.name || `Journal #${journal.id}`)
                    .data('journal', journal)
            );
        });

        // Initialize Select2 if available
        if ($.fn.select2) {
            $select.select2({
                placeholder: config.translations.selectJournalFirst,
                allowClear: true
            });
        }
    }

    /**
     * Bind event handlers
     */
    function bindEventHandlers() {
        // Journal selection change
        $('#journal_id').on('change', function () {
            const journalId = $(this).val();

            if (journalId) {
                const option = $(this).find('option:selected');
                selectedJournal = option.data('journal');
                loadJournalDetails(journalId);
            } else {
                resetJournalInfo();
            }
        });

        // Amount input validation
        $('#amount').on('input', function () {
            validateAmount();
        });

        // Form submission
        $('#create-transfer-form').on('submit', function (e) {
            e.preventDefault();

            if (validateForm()) {
                submitForm();
            }
        });
    }

    /**
     * Load journal details and update preview
     */
    function loadJournalDetails(journalId) {
        $.ajax({
            url: `${config.journalsApiUrl}/${journalId}`,
            method: 'GET',
            success: function (response) {
                if (response.status && response.data) {
                    selectedJournal = response.data;
                    updateJournalInfo(response.data);
                    updatePreviewCards(response.data);
                }
            },
            error: function () {
                showAlert('error', 'Failed to load journal details');
                resetJournalInfo();
            }
        });
    }

    /**
     * Update journal information display
     */
    function updateJournalInfo(journal) {
        const remainingBalance = journal.remaining_balance || 0;
        const symbol = config.currencySymbol || '$';

        $('#display-remaining-balance').val(`${formatNumber(remainingBalance)}`);

        // Update progress bar if available
        if (journal.progress_percentage !== undefined) {
            const percentage = Math.round(journal.progress_percentage);
            $('#progress-bar-section').show();
            $('#journal-progress-bar')
                .css('width', `${percentage}%`)
                .attr('aria-valuenow', percentage);
            $('#progress-text').text(`${percentage}%`);

            // Update progress bar color based on percentage
            const progressBar = $('#journal-progress-bar');
            progressBar.removeClass('bg-success bg-warning bg-danger');

            if (percentage >= 75) {
                progressBar.addClass('bg-success');
            } else if (percentage >= 50) {
                progressBar.addClass('bg-warning');
            } else {
                progressBar.addClass('bg-danger');
            }
        }
    }

    /**
     * Update preview statistics cards
     */
    function updatePreviewCards(journal) {
        const symbol = config.currencySymbol || '$';

        $('#preview-receipt-cash').text(`${symbol}${formatNumber(journal.receipt_cash || 0)}`);
        $('#preview-previous-transfer').text(`${symbol}${formatNumber(journal.transferred_amount || 0)}`);
        $('#preview-deposit').text(`${symbol}${formatNumber(journal.deposit_amount || 0)}`);
        $('#preview-remaining-balance').text(`${symbol}${formatNumber(journal.remaining_balance || 0)}`);
    }

    /**
     * Reset journal information
     */
    function resetJournalInfo() {
        selectedJournal = null;
        $('#display-remaining-balance').val('-');
        $('#progress-bar-section').hide();
        $('#amount-error').hide();

        // Reset preview cards
        $('#preview-receipt-cash').text('-');
        $('#preview-previous-transfer').text('-');
        $('#preview-deposit').text('-');
        $('#preview-remaining-balance').text('-');
    }

    /**
     * Validate amount against remaining balance
     */
    function validateAmount() {
        const amount = parseFloat($('#amount').val()) || 0;
        const errorDiv = $('#amount-error');

        if (!selectedJournal) {
            errorDiv.text(config.translations.selectJournalFirst).show();
            return false;
        }

        const remainingBalance = selectedJournal.remaining_balance || 0;

        if (amount > remainingBalance) {
            errorDiv.text(config.translations.amountExceedsBalance).show();
            $('#amount').addClass('is-invalid');
            return false;
        } else {
            errorDiv.hide();
            $('#amount').removeClass('is-invalid');
            return true;
        }
    }

    /**
     * Validate entire form
     */
    function validateForm() {
        let isValid = true;

        // Check journal selection
        if (!$('#journal_id').val()) {
            $('#journal_id').addClass('is-invalid');
            isValid = false;
        } else {
            $('#journal_id').removeClass('is-invalid');
        }

        // Check amount
        const amount = parseFloat($('#amount').val());
        if (!amount || amount <= 0) {
            $('#amount').addClass('is-invalid');
            isValid = false;
        } else if (!validateAmount()) {
            isValid = false;
        } else {
            $('#amount').removeClass('is-invalid');
        }

        return isValid;
    }

    /**
     * Submit form via AJAX
     */
    function submitForm() {
        const formData = {
            journal_id: $('#journal_id').val(),
            amount: parseFloat($('#amount').val()),
            notes: $('#notes').val() || ''
        };

        const submitBtn = $('#submit-btn');
        const originalText = submitBtn.html();

        // Disable submit button and show loading state
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: config.createApiUrl,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            success: function (response) {
                if (response.status) {
                    showAlert('success', config.translations.success);

                    // Redirect after short delay
                    setTimeout(function () {
                        window.location.href = config.indexUrl;
                    }, 1500);
                } else {
                    showAlert('error', response.message || config.translations.error);
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function (xhr) {
                let errorMessage = config.translations.error;

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON.errors) {
                        // Handle validation errors
                        const errors = xhr.responseJSON.errors;
                        const errorList = Object.values(errors).flat();
                        errorMessage = errorList.join('<br>');
                    }
                }

                showAlert('error', errorMessage);
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    }

    /**
     * Show alert using SweetAlert2 or fallback
     */
    function showAlert(type, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Success' : 'Error',
                html: message,
                timer: type === 'success' ? 2000 : 5000,
                showConfirmButton: type !== 'success'
            });
        } else {
            alert(message);
        }
    }

    /**
     * Format number with thousand separators
     */
    function formatNumber(number) {
        return parseFloat(number).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    // Initialize on document ready
    $(document).ready(function () {
        init();
    });

})(jQuery);
