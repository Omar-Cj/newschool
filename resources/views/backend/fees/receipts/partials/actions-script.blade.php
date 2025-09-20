<script>
(function (window, $) {
    if (window.ReceiptActions) {
        return;
    }

    if (!$) {
        console.error('jQuery is required for ReceiptActions.');
        return;
    }

    const routes = {
        individual: @json(route('fees.receipt.individual', ['paymentId' => '__PAYMENT_ID__'])),
        options: @json(route('fees.receipt.options', ['paymentId' => '__PAYMENT_ID__'])),
        checkGroup: @json(route('fees.receipt.check-group-availability')),
        todayPayments: @json(route('fees.receipt.today-payments')),
        email: @json(route('fees.receipt.email')),
        group: @json(route('fees.receipt.group')),
        backToCollection: @json(route('fees-collect.index'))
    };

    function buildUrl(template, id) {
        if (!template) {
            return '';
        }

        return template.replace('__PAYMENT_ID__', id);
    }

    function showAlert(message, type = 'info') {
        if (window.Toast && typeof window.Toast.fire === 'function') {
            window.Toast.fire({
                icon: type === 'error' ? 'error' : type,
                title: message
            });
            return;
        }

        alert(message);
    }

    function openPrintWindow(paymentId) {
        const url = buildUrl(routes.individual, paymentId) + '?print=1';
        const printWindow = window.open(url, '_blank', 'width=900,height=700,scrollbars=yes,resizable=yes');

        if (!printWindow) {
            if (window.confirm('Popup blocker may be preventing the print window from opening. Would you like to open the receipt in the current tab instead?')) {
                window.location.href = url;
            } else {
                showAlert('Please allow popups for this site to use the print feature.', 'warning');
            }
            return null;
        }

        try {
            printWindow.focus();
            printWindow.addEventListener('load', function () {
                try {
                    printWindow.print();
                } catch (err) {
                    console.warn('Automatic print trigger failed.', err);
                }
            });
        } catch (err) {
            console.debug('Unable to focus print window.', err);
        }

        setTimeout(function () {
            try {
                if (printWindow.closed) {
                    return;
                }

                if (printWindow.document && printWindow.document.body && printWindow.document.body.innerHTML.trim() === '') {
                    showAlert('Print preview failed to load. Please download the receipt instead.', 'error');
                }
            } catch (err) {
                console.debug('Unable to inspect print window contents.', err);
            }
        }, 2000);

        return printWindow;
    }

    function printReceipt(paymentId) {
        return openPrintWindow(paymentId);
    }

    function checkGroupAvailability() {
        return $.get(routes.checkGroup)
            .done(function (response) {
                const hasMultiple = response && response.has_multiple_payments;
                const groupOption = $('#group-receipt-option');

                if (!groupOption.length) {
                    return;
                }

                if (hasMultiple) {
                    groupOption.show();
                } else {
                    groupOption.hide();
                }
            })
            .fail(function () {
                console.warn('Unable to verify group receipt availability.');
            });
    }

    function generateGroupReceipt() {
        $.get(routes.todayPayments)
            .done(function (response) {
                if (!response || !Array.isArray(response.payment_ids) || response.payment_ids.length === 0) {
                    showAlert('{{ ___('fees.no_payments_found_for_date') }}', 'info');
                    return;
                }

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = routes.group;
                form.target = '_blank';

                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                if (csrfToken) {
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);
                }

                response.payment_ids.forEach(function (id) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'payment_ids[]';
                    input.value = id;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
                document.body.removeChild(form);
            })
            .fail(function () {
                showAlert('Unable to generate group receipt at this time.', 'error');
            });
    }

    function emailReceipt(paymentId) {
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        return $.post(routes.email, {
            payment_id: paymentId,
            _token: csrfToken
        }).done(function (response) {
            if (response && response.success) {
                showAlert(response.message || '{{ ___('fees.receipt_emailed_successfully') }}', 'success');
            } else {
                showAlert((response && response.message) || '{{ ___('fees.email_feature_coming_soon') }}', 'info');
            }
        }).fail(function () {
            showAlert('{{ ___('fees.email_failed') }}', 'error');
        });
    }

    function collectAnotherPayment() {
        const modal = $('#receiptOptionsModal');
        if (modal.length) {
            modal.modal('hide');
        }

        if (routes.backToCollection) {
            window.location.href = routes.backToCollection;
        } else {
            window.location.reload();
        }
    }

    function loadOptionsModal(paymentId, options = {}) {
        const url = buildUrl(routes.options, paymentId);

        return new Promise(function (resolve, reject) {
            $.ajax({
                url: url,
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).done(function (response) {
                const html = response && response.html ? response.html : response;

                if (!html) {
                    reject(new Error('Empty modal content received.'));
                    return;
                }

                $('#receiptOptionsModal').remove();
                $('body').append(html);

                const modal = $('#receiptOptionsModal');
                modal.modal('show');

                checkGroupAvailability();

                if (options.onShown && typeof options.onShown === 'function') {
                    modal.on('shown.bs.modal', options.onShown);
                }

                resolve(modal);
            }).fail(function (error) {
                console.warn('Unable to load receipt options modal via AJAX.', error);
                window.location.href = url;
                reject(error);
            });
        });
    }

    window.ReceiptActions = {
        routes,
        buildUrl,
        openPrintWindow,
        printReceipt,
        loadOptionsModal,
        checkGroupAvailability,
        generateGroupReceipt,
        emailReceipt,
        collectAnotherPayment,
        showAlert
    };

    window.printReceipt = function (paymentId) {
        return window.ReceiptActions.printReceipt(paymentId);
    };

    window.emailReceipt = function (paymentId) {
        return window.ReceiptActions.emailReceipt(paymentId);
    };

    window.generateGroupReceipt = function () {
        return window.ReceiptActions.generateGroupReceipt();
    };

    window.collectAnotherPayment = function () {
        return window.ReceiptActions.collectAnotherPayment();
    };

    window.showAlert = function (message, type) {
        return window.ReceiptActions.showAlert(message, type);
    };
})(window, window.jQuery);
</script>
