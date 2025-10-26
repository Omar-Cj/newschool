/**
 * Cash Transfers Management JavaScript
 * Handles DataTable initialization, filters, modals, and AJAX operations
 */

(function ($) {
    'use strict';

    // Configuration from Blade template
    const config = window.cashTransferConfig || {};

    // DataTable instance
    let dataTable = null;

    // Payment method names
    const paymentMethods = {
        1: config.translations?.cash || 'Cash',
        2: config.translations?.stripe || 'Stripe',
        3: config.translations?.zaad || 'Zaad',
        4: config.translations?.edahab || 'Edahab',
        5: config.translations?.paypal || 'PayPal'
    };

    /**
     * Initialize the page
     */
    function init() {
        // loadStatistics(); // Removed - statistics cards no longer displayed
        loadJournalsForFilter();
        initializeDataTable();
        bindEventHandlers();
    }

    /**
     * Load statistics for dashboard cards
     */
    function loadStatistics() {
        $.ajax({
            url: config.statisticsUrl,
            method: 'GET',
            success: function (response) {
                if (response.status) {
                    updateStatisticsCards(response.data);
                }
            },
            error: function () {
                // Show default values or error state
                $('#stat-receipt-cash').text('N/A');
                $('#stat-previous-transfer').text('N/A');
                $('#stat-deposit').text('N/A');
                $('#stat-total-amount').text('N/A');
            }
        });
    }

    /**
     * Update statistics cards with data
     */
    function updateStatisticsCards(data) {
        const symbol = config.currencySymbol || '$';

        $('#stat-receipt-cash').html(`${symbol}${formatNumber(data.receipt_cash || 0)}`);
        $('#stat-previous-transfer').html(`${symbol}${formatNumber(data.previous_transfer || 0)}`);
        $('#stat-deposit').html(`${symbol}${formatNumber(data.deposit || 0)}`);
        $('#stat-total-amount').html(`${symbol}${formatNumber(data.total_amount || 0)}`);
    }

    /**
     * Load journals for filter dropdown
     */
    function loadJournalsForFilter() {
        const journalsUrl = config.journalsUrl || '/api/journals';


        $.ajax({
            url: journalsUrl,
            method: 'GET',
            data: {}, // Load all journals, not just active ones
            success: function (response) {

                if (response.status && response.data) {

                    const $select = $('#filter-journal');
                    $select.find('option:not(:first)').remove();

                    response.data.forEach(function (journal) {
                        $select.append(
                            $('<option></option>')
                                .val(journal.id)
                                .text(journal.name || `Journal #${journal.id}`)
                        );
                    });


                    // Initialize Select2 if available
                    if ($.fn.select2) {
                        $select.select2({
                            placeholder: 'Select Journal',
                            allowClear: true
                        });
                    }
                } else {
                }
            },
            error: function(xhr, status, error) {
                // Error loading journals - silent fail
                // Could optionally show an alert if needed
            }
        });
    }

    /**
     * Initialize DataTable
     */
    function initializeDataTable() {
        dataTable = $('#cash-transfers-table').DataTable({
            processing: true,
            serverSide: true,
            dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                 '<"row"<"col-sm-12"tr>>' +
                 '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
            ajax: {
                url: config.apiBaseUrl,
                data: function (d) {

                    // Add filter parameters
                    d.journal_id = $('#filter-journal').val();
                    d.status = $('#filter-status').val();
                    d.date_from = $('#filter-date-from').val();
                    d.date_to = $('#filter-date-to').val();
                },
                error: function(xhr, error, thrown) {
                    // Try to parse error response
                    try {
                        const errorData = JSON.parse(xhr.responseText);
                        if (errorData && errorData.message) {
                            showAlert('error', errorData.message);
                        }
                    } catch (e) {
                        showAlert('error', 'Failed to load data');
                    }
                }
            },
            columns: [
                {
                    data: null,
                    name: 'serial',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    render: function (data) {
                        return data ? new Date(data).toLocaleDateString() : '-';
                    }
                },
                {
                    data: 'transferred_by',
                    name: 'transferred_by.name',
                    render: function (data, type, row) {
                        return data ? data.name : '-';
                    }
                },
                {
                    data: 'journal',
                    name: 'journal.name',
                    render: function (data, type, row) {
                        return data ? data.name : '-';
                    }
                },
                {
                    data: 'amount',
                    name: 'amount',
                    render: function (data) {
                        return `${config.currencySymbol}${formatNumber(data)}`;
                    }
                },
                {
                    data: 'approved_by',
                    name: 'approved_by.name',
                    render: function (data, type, row) {
                        return data ? data.name : '-';
                    }
                },
                {
                    data: 'approved_at',
                    name: 'approved_at',
                    render: function (data) {
                        return data ? new Date(data).toLocaleDateString() : '-';
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function (data) {
                        return getStatusBadge(data);
                    }
                },
                {
                    data: null,
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return generateActionButtons(row);
                    }
                }
            ],
            order: [[1, 'desc']],
            pageLength: 10,
            language: {
                emptyTable: config.translations?.noData || 'No transfers found',
                processing: config.translations?.loading || 'Loading...'
            }
        });

    }

    /**
     * Generate status badge HTML
     */
    function getStatusBadge(status) {
        const badges = {
            pending: '<span class="badge bg-warning">Pending</span>',
            approved: '<span class="badge bg-success">Approved</span>',
            rejected: '<span class="badge bg-danger">Rejected</span>'
        };
        return badges[status] || status;
    }

    /**
     * Generate action buttons based on permissions and status
     */
    function generateActionButtons(row) {

        let buttons = '<div class="dropdown dropdown-action">';
        buttons += '<button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">';
        buttons += '<i class="fa-solid fa-ellipsis"></i></button>';
        buttons += '<ul class="dropdown-menu dropdown-menu-end">';

        // View button (always visible)
        buttons += `<li><a href="#" class="dropdown-item view-transfer" data-id="${row.id}">`;
        buttons += `<span class="icon mr-8"><i class="fa-solid fa-eye"></i></span> ${config.translations.view}</a></li>`;

        // Super admin actions for pending transfers
        if (config.isSuperAdmin && row.status === 'pending') {

            if (config.canApprove) {
                buttons += `<li><a href="#" class="dropdown-item approve-transfer" data-id="${row.id}">`;
                buttons += `<span class="icon mr-8"><i class="fa-solid fa-check text-success"></i></span> ${config.translations.approve}</a></li>`;
            }

            if (config.canReject) {
                buttons += `<li><a href="#" class="dropdown-item reject-transfer" data-id="${row.id}">`;
                buttons += `<span class="icon mr-8"><i class="fa-solid fa-times text-danger"></i></span> ${config.translations.reject}</a></li>`;
            }

            if (config.canDelete) {
                buttons += `<li><a href="#" class="dropdown-item delete-transfer" data-id="${row.id}">`;
                buttons += `<span class="icon mr-8"><i class="fa-solid fa-trash-can text-danger"></i></span> ${config.translations.delete}</a></li>`;
            }
        } else {
        }

        buttons += '</ul></div>';

        return buttons;
    }

    /**
     * Bind event handlers
     */
    function bindEventHandlers() {

        try {
            // Filter form submission
            $('#filters-form').on('submit', function (e) {
                e.preventDefault();
                dataTable.ajax.reload();
            });

            // Reset filters
            $('#reset-filters').on('click', function () {
                $('#filters-form')[0].reset();
                if ($.fn.select2) {
                    $('#filter-journal').val('').trigger('change');
                }
                dataTable.ajax.reload();
            });

            // =================================================================
            // CRITICAL FIX: Use NATIVE event listeners in CAPTURE PHASE
            // This intercepts clicks BEFORE Bootstrap 5's dropdown handles them
            // Bootstrap's dropdown calls stopImmediatePropagation() which blocks
            // jQuery's delegated handlers (which use bubble phase)
            // =================================================================

            const table = document.getElementById('cash-transfers-table');

            if (!table) {
            } else {

                // View transfer details - CAPTURE PHASE
                table.addEventListener('click', function (e) {
                    const target = e.target.closest('.view-transfer');
                    if (target) {
                        e.preventDefault();
                        e.stopImmediatePropagation(); // Prevent Bootstrap from handling
                        const id = target.getAttribute('data-id');
                        showTransferDetails(parseInt(id));
                    }
                }, true); // ⚡ CRITICAL: `true` = capture phase (fires BEFORE Bootstrap)

                // Approve transfer - CAPTURE PHASE
                table.addEventListener('click', function (e) {
                    const target = e.target.closest('.approve-transfer');
                    if (target) {
                        e.preventDefault();
                        e.stopImmediatePropagation(); // Prevent Bootstrap from handling
                        const id = target.getAttribute('data-id');
                        showApproveModal(parseInt(id));
                    }
                }, true); // ⚡ CRITICAL: capture phase

                // Reject transfer - CAPTURE PHASE
                table.addEventListener('click', function (e) {
                    const target = e.target.closest('.reject-transfer');
                    if (target) {
                        e.preventDefault();
                        e.stopImmediatePropagation(); // Prevent Bootstrap from handling
                        const id = target.getAttribute('data-id');
                        showRejectModal(parseInt(id));
                    }
                }, true); // ⚡ CRITICAL: capture phase

                // Delete transfer - CAPTURE PHASE
                table.addEventListener('click', function (e) {
                    const target = e.target.closest('.delete-transfer');
                    if (target) {
                        e.preventDefault();
                        e.stopImmediatePropagation(); // Prevent Bootstrap from handling
                        const id = target.getAttribute('data-id');
                        deleteTransfer(parseInt(id));
                    }
                }, true); // ⚡ CRITICAL: capture phase

            }

            // Confirm approve button
            $('#confirm-approve-btn').on('click', function () {
                const id = $('#approve-transfer-id').val();
                approveTransfer(id);
            });

            // Confirm reject form submission
            $('#reject-transfer-form').on('submit', function (e) {
                e.preventDefault();
                const id = $('#reject-transfer-id').val();
                const reason = $('#rejection-reason').val();
                rejectTransfer(id, reason);
            });

        } catch (error) {
        }
    }

    /**
     * Show transfer details in modal
     */
    function showTransferDetails(id) {

        // Check if Bootstrap is available
        if (typeof window.bootstrap === 'undefined') {
            alert('Error: Bootstrap library not loaded. Please refresh the page.');
            return;
        }

        const modalElement = document.getElementById('viewTransferModal');

        if (!modalElement) {
            alert('Error: Modal element not found. Please contact support.');
            return;
        }

        const content = $('#transfer-details-content');

        // Show loading state
        content.html(`
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                <p class="mt-3">${config.translations.loading}</p>
            </div>
        `);

        // Bootstrap 5 API with error handling
        try {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } catch (error) {
            alert('Error opening modal: ' + error.message);
            return;
        }

        // Fetch transfer details
        $.ajax({
            url: config.showUrl.replace(':id', id),
            method: 'GET',
            success: function (response) {
                if (response.status && response.data) {
                    renderTransferDetails(response.data);
                }
            },
            error: function (xhr, status, error) {
                content.html('<div class="alert alert-danger">Failed to load transfer details.</div>');
            }
        });
    }

    /**
     * Render transfer details in modal
     */
    function renderTransferDetails(transfer) {
        // Properly access template element content
        const templateElement = document.getElementById('transfer-details-template');
        const templateContent = templateElement.content.cloneNode(true);
        const templateHtml = templateContent.querySelector('div').outerHTML;
        $('#transfer-details-content').html(templateHtml);

        // Populate fields
        $('#detail-journal').text(transfer.journal?.name || '-');
        $('#detail-amount').text(`${config.currencySymbol}${formatNumber(transfer.amount)}`);
        $('#detail-status').html(getStatusBadge(transfer.status));
        $('#detail-transferred-by').text(transfer.transferred_by?.name || '-');
        $('#detail-date-transferred').text(transfer.created_at ? new Date(transfer.created_at).toLocaleDateString() : '-');
        $('#detail-approved-by').text(transfer.approved_by?.name || '-');
        $('#detail-date-approved').text(transfer.approved_at ? new Date(transfer.approved_at).toLocaleDateString() : '-');
        $('#detail-notes').text(transfer.notes || 'No notes provided');

        // Show rejection reason if rejected
        if (transfer.status === 'rejected' && transfer.rejection_reason) {
            $('#rejection-reason-row').show();
            $('#detail-rejection-reason').text(transfer.rejection_reason);
        }

        // Render payment method breakdown
        renderPaymentMethodBreakdown(transfer.payment_method_breakdown || {});
    }

    /**
     * Render payment method breakdown table
     */
    function renderPaymentMethodBreakdown(breakdown) {
        const tbody = $('#payment-methods-table');
        tbody.empty();

        if (Object.keys(breakdown).length === 0) {
            tbody.html('<tr><td colspan="2" class="text-center">No payment data available</td></tr>');
            return;
        }

        for (const [methodId, amount] of Object.entries(breakdown)) {
            const methodName = paymentMethods[methodId] || `Method ${methodId}`;
            tbody.append(`
                <tr>
                    <td>${methodName}</td>
                    <td class="text-end">${config.currencySymbol}${formatNumber(amount)}</td>
                </tr>
            `);
        }
    }

    /**
     * Show approve confirmation modal
     */
    function showApproveModal(id) {

        if (typeof window.bootstrap === 'undefined') {
            alert('Error: Bootstrap library not loaded. Please refresh the page.');
            return;
        }

        const modalElement = document.getElementById('approveTransferModal');
        if (!modalElement) {
            return;
        }

        $('#approve-transfer-id').val(id);

        try {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } catch (error) {
            alert('Error opening approve modal: ' + error.message);
        }
    }

    /**
     * Show reject modal
     */
    function showRejectModal(id) {

        if (typeof window.bootstrap === 'undefined') {
            alert('Error: Bootstrap library not loaded. Please refresh the page.');
            return;
        }

        const modalElement = document.getElementById('rejectTransferModal');
        if (!modalElement) {
            return;
        }

        $('#reject-transfer-id').val(id);
        $('#rejection-reason').val('');

        try {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } catch (error) {
            alert('Error opening reject modal: ' + error.message);
        }
    }

    /**
     * Approve transfer
     */
    function approveTransfer(id) {

        const btn = $('#confirm-approve-btn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        const url = config.approveUrl.replace(':id', id);

        $.ajax({
            url: url,
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                // Bootstrap 5 API - hide modal
                const modalElement = document.getElementById('approveTransferModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();

                showAlert('success', response.message || 'Transfer approved successfully');

                dataTable.ajax.reload();
                // loadStatistics(); // Removed - statistics cards no longer displayed
            },
            error: function (xhr) {
                const message = xhr.responseJSON?.message || 'Failed to approve transfer';
                showAlert('error', message);
            },
            complete: function () {
                btn.prop('disabled', false).html(`<i class="fa-solid fa-check"></i> ${config.translations.approve}`);
            }
        });
    }

    /**
     * Reject transfer
     */
    function rejectTransfer(id, reason) {
        const btn = $('#confirm-reject-btn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.ajax({
            url: config.rejectUrl.replace(':id', id),
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { reason: reason },
            success: function (response) {
                // Bootstrap 5 API - hide modal
                const modalElement = document.getElementById('rejectTransferModal');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();

                showAlert('success', response.message || 'Transfer rejected successfully');
                dataTable.ajax.reload();
                // loadStatistics(); // Removed - statistics cards no longer displayed
            },
            error: function (xhr) {
                const message = xhr.responseJSON?.message || 'Failed to reject transfer';
                showAlert('error', message);
            },
            complete: function () {
                btn.prop('disabled', false).html(`<i class="fa-solid fa-times"></i> ${config.translations.reject}`);
            }
        });
    }

    /**
     * Delete transfer
     */
    function deleteTransfer(id) {
        if (!confirm(config.translations.confirmDelete)) {
            return;
        }

        $.ajax({
            url: config.deleteUrl.replace(':id', id),
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                showAlert('success', response.message || 'Transfer deleted successfully');
                dataTable.ajax.reload();
                // loadStatistics(); // Removed - statistics cards no longer displayed
            },
            error: function (xhr) {
                const message = xhr.responseJSON?.message || 'Failed to delete transfer';
                showAlert('error', message);
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
                text: message,
                timer: 3000,
                showConfirmButton: false
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
        if (typeof window.bootstrap !== 'undefined') {
        }

        init();
    });

})(jQuery);
