/**
 * Sibling Fee Collection Management
 * Handles family payment functionality with sibling consolidation
 */

// Prevent duplicate class declaration
if (typeof window.SiblingFeeCollectionManager === 'undefined') {

class SiblingFeeCollectionManager {
    constructor() {
        this.siblings = [];
        this.availableDeposit = 0;
        this.totalOutstanding = 0;
        this.paymentMode = 'direct';
        this.isLoaded = false;
        this.familyLinkChecked = false;
        this.validationState = {
            isValid: false,
            errors: []
        };

        // Bind methods
        this.init = this.init.bind(this);
        this.loadSiblingData = this.loadSiblingData.bind(this);
        this.handlePaymentModeChange = this.handlePaymentModeChange.bind(this);
        this.handlePaymentAmountChange = this.handlePaymentAmountChange.bind(this);
        this.calculateDistribution = this.calculateDistribution.bind(this);
        this.validatePayment = this.validatePayment.bind(this);
        this.processPayment = this.processPayment.bind(this);
    }

    /**
     * Initialize the sibling fee collection manager
     */
    init() {
        this.bindEvents();
        this.setupPaymentModeToggle();
        console.log('SiblingFeeCollectionManager initialized');
    }

    /**
     * Bind all event listeners
     */
    bindEvents() {
        console.log('Binding events for modal-in-modal approach...');

        // Family payment link click event
        const familyPaymentLink = document.getElementById('family-payment-link');
        if (familyPaymentLink) {
            familyPaymentLink.addEventListener('click', () => {
                console.log('Family payment link clicked');
                this.openFamilyPaymentModal();
            });
            console.log('Family payment link event listener bound successfully');
        } else {
            console.error('Family payment link element not found!');
        }

        // Family payment modal show event
        const familyModal = document.getElementById('familyPaymentModal');
        if (familyModal) {
            familyModal.addEventListener('shown.bs.modal', () => {
                console.log('Family payment modal shown event triggered');
                this.onFamilyModalShown();
            });
            console.log('Family payment modal event listener bound successfully');
        } else {
            console.error('Family payment modal element not found!');
        }

        // Main fee collection modal show event - check for siblings to show/hide family link
        const mainModal = document.getElementById('feeCollectionModalWidth') ||
                         document.querySelector('#modalCustomizeWidth .modal-content');
        if (mainModal) {
            // Listen for when the modal content is populated
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList' || mutation.type === 'attributes') {
                        const studentId = document.getElementById('modal_student_id')?.value;
                        if (studentId && !this.familyLinkChecked) {
                            this.familyLinkChecked = true;
                            this.checkAndUpdateFamilyLink(studentId);
                        }
                    }
                });
            });
            observer.observe(mainModal, { childList: true, subtree: true, attributes: true });
            console.log('Main modal mutation observer set up for family link visibility');
        }

        // Payment mode radio buttons
        document.querySelectorAll('input[name="payment_mode_radio"]').forEach(radio => {
            radio.addEventListener('change', this.handlePaymentModeChange);
        });

        // Distribution buttons
        document.getElementById('equal-distribution-btn')?.addEventListener('click', () => {
            this.calculateDistribution('equal');
        });

        document.getElementById('proportional-distribution-btn')?.addEventListener('click', () => {
            this.calculateDistribution('proportional');
        });

        document.getElementById('clear-distribution-btn')?.addEventListener('click', () => {
            this.clearDistribution();
        });

        // Validation and submission
        document.getElementById('validate_sibling_payment_btn')?.addEventListener('click', () => {
            this.validatePayment();
        });

        document.getElementById('siblingPaymentForm')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.processPayment();
        });
    }

    /**
     * Open family payment modal
     */
    async openFamilyPaymentModal() {
        console.log('Opening family payment modal');

        const studentId = document.getElementById('modal_student_id')?.value;
        const studentName = document.getElementById('summary-student-name')?.textContent;

        if (!studentId) {
            console.error('No student ID found');
            this.showError('Student information not available. Please close and reopen the fee collection.');
            return;
        }

        // Update family modal title
        const familyStudentName = document.getElementById('family-student-name');
        if (familyStudentName && studentName) {
            familyStudentName.textContent = studentName;
        }

        // Set primary student ID for the family payment
        const primaryStudentInput = document.getElementById('primary_student_id');
        if (primaryStudentInput) {
            primaryStudentInput.value = studentId;
        }

        // Show the family payment modal
        const familyModal = new bootstrap.Modal(document.getElementById('familyPaymentModal'));
        familyModal.show();
    }

    /**
     * Handle when family payment modal is shown
     */
    async onFamilyModalShown() {
        console.log('Family payment modal shown event triggered');
        console.log('isLoaded:', this.isLoaded);

        // Setup payment mode toggle behavior
        this.setupPaymentModeToggle();

        // Always load journals when modal opens
        this.loadSiblingJournals();

        if (!this.isLoaded) {
            const studentId = document.getElementById('primary_student_id')?.value ||
                           document.getElementById('modal_student_id')?.value;
            console.log('Student ID:', studentId);

            if (studentId) {
                console.log('Loading sibling data for student:', studentId);
                await this.loadSiblingData(studentId);
            } else {
                console.error('No student ID found');
            }
        } else {
            console.log('Sibling data already loaded, skipping');
        }
    }

    /**
     * Load sibling fee data for the student
     */
    async loadSiblingData(studentId) {
        const loadingEl = document.getElementById('sibling-loading');
        const interfaceEl = document.getElementById('sibling-payment-interface');
        const noSiblingsEl = document.getElementById('no-siblings-message');
        const footerEl = document.getElementById('sibling-payment-footer');

        try {
            // Show loading state
            loadingEl.style.display = 'block';
            interfaceEl.style.display = 'none';
            noSiblingsEl.style.display = 'none';
            footerEl.style.display = 'none';

            // Fetch sibling data
            const response = await fetch(`/~omar/schooltemplate/public/index.php/fees/siblings/${studentId}/data`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                credentials: 'same-origin'
            });

            const data = await response.json();
            console.log('Sibling data received:', data);

            if (!data.success) {
                console.log('Sibling data load failed:', data.message);
                if (data.show_individual_only) {
                    noSiblingsEl.style.display = 'block';
                    this.updateFamilyPaymentLink();
                } else {
                    throw new Error(data.message || 'Failed to load sibling data');
                }
                return;
            }

            // Store data
            this.siblings = data.data.siblings || [];
            this.availableDeposit = data.data.available_deposit || 0;
            this.totalOutstanding = data.data.total_family_outstanding || 0;
            
            console.log('Siblings loaded:', this.siblings.length, 'siblings');
            console.log('Available deposit:', this.availableDeposit);
            console.log('Total outstanding:', this.totalOutstanding);

            // Update UI
            this.updateFamilySummary(data.data);
            this.renderSiblingTable();
            this.updateSiblingsCount();
            this.updateFamilyPaymentLink();
            this.setupPaymentModeState();

            // Show interface
            console.log('Showing interface elements...');
            console.log('interfaceEl:', interfaceEl);
            console.log('footerEl:', footerEl);
            
            if (interfaceEl) {
                interfaceEl.style.display = 'block';
                console.log('Interface element shown');
                
                // Check computed styles
                const computedStyle = window.getComputedStyle(interfaceEl);
                console.log('Interface element computed display:', computedStyle.display);
                console.log('Interface element computed visibility:', computedStyle.visibility);
                console.log('Interface element computed height:', computedStyle.height);
            } else {
                console.error('Interface element not found!');
            }
            
            if (footerEl) {
                footerEl.style.display = 'flex';
                console.log('Footer element shown');
            } else {
                console.error('Footer element not found!');
            }
            
            this.isLoaded = true;
            console.log('Sibling data loaded successfully - interface ready');

        } catch (error) {
            console.error('Error loading sibling data:', error);
            this.showError('Failed to load sibling fee data. Please try again.');
            noSiblingsEl.style.display = 'block';
        } finally {
            loadingEl.style.display = 'none';
        }
    }

    /**
     * Load journals for family payment modal
     * Mirrors the individual payment modal approach for branch-specific journals
     */
    loadSiblingJournals() {
        console.log('Loading journals for family payment modal...');

        // Make AJAX call to same route as individual modal for consistency
        fetch('/~omar/schooltemplate/public/index.php/admin/journals-dropdown', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(journals => {
            console.log('Journals received:', journals);

            const journalSelect = document.getElementById('sibling_journal_id');
            if (!journalSelect) {
                console.error('Sibling journal select element not found!');
                return;
            }

            // Clear existing options and add default
            journalSelect.innerHTML = '<option value="">Select Journal</option>';

            // Populate with received journals
            journals.forEach(journal => {
                const option = document.createElement('option');
                option.value = journal.id;
                option.textContent = journal.text;
                journalSelect.appendChild(option);
            });

            // Initialize/Re-initialize Select2 for journal dropdown
            this.initializeSiblingSelect2();

            console.log('Family payment journals loaded successfully');
        })
        .catch(error => {
            console.error('Error loading journals:', error);
            this.showError('Failed to load journals. Please try again.');
        });
    }

    /**
     * Initialize Select2 dropdowns for family payment modal
     */
    initializeSiblingSelect2() {
        const modalParent = document.getElementById('familyPaymentModal');

        // Initialize journal dropdown
        const journalSelect = document.getElementById('sibling_journal_id');
        if (journalSelect) {
            // Destroy existing Select2 if it exists
            if (journalSelect.classList.contains('select2-hidden-accessible')) {
                $(journalSelect).select2('destroy');
            }

            // Initialize new Select2
            $(journalSelect).select2({
                placeholder: "Select Journal",
                allowClear: false,
                width: '100%',
                dropdownParent: $(modalParent)
            });
        }

        // Initialize payment method dropdown
        const paymentMethodSelect = document.getElementById('sibling_payment_method');
        if (paymentMethodSelect) {
            // Destroy existing Select2 if it exists
            if (paymentMethodSelect.classList.contains('select2-hidden-accessible')) {
                $(paymentMethodSelect).select2('destroy');
            }

            // Initialize new Select2
            $(paymentMethodSelect).select2({
                placeholder: "Select Payment Method",
                allowClear: false,
                width: '100%',
                dropdownParent: $(modalParent)
            });
        }
    }

    /**
     * Update family summary display
     */
    updateFamilySummary(data) {
        console.log('Updating family summary with data:', data);
        
        try {
            // Update family totals
            const totalOutstandingEl = document.getElementById('family-total-outstanding');
            const availableDepositEl = document.getElementById('family-available-deposit');
            const parentNameEl = document.getElementById('parent-name');
            
            if (totalOutstandingEl) {
                totalOutstandingEl.textContent = data.formatted_total_outstanding || '$0.00';
            }
            
            if (availableDepositEl) {
                availableDepositEl.textContent = data.formatted_deposit || '$0.00';
            }
            
            if (parentNameEl) {
                parentNameEl.textContent = data.parent_info?.name || 'Unknown Parent';
            }

            // Update deposit info alert
            const depositAlert = document.getElementById('deposit-info-alert');
            const depositBalanceAmount = document.getElementById('deposit-balance-amount');
            const depositBalanceStatus = document.getElementById('deposit-balance-status');

            if (depositBalanceAmount) {
                depositBalanceAmount.textContent = data.formatted_deposit || '$0.00';
            }

            if (depositBalanceStatus) {
                if (data.can_pay_fully_with_deposit) {
                    depositBalanceStatus.innerHTML = '<span class="badge bg-success">Sufficient</span>';
                } else {
                    depositBalanceStatus.innerHTML = '<span class="badge bg-warning">Partial</span>';
                }
            }
            
            console.log('Family summary updated successfully');
            
        } catch (error) {
            console.error('Error updating family summary:', error);
        }
    }

    /**
     * Render the sibling table
     */
    renderSiblingTable() {
        console.log('Rendering sibling table with', this.siblings.length, 'siblings');
        
        const tbody = document.getElementById('sibling-fees-tbody');
        if (!tbody) {
            console.error('sibling-fees-tbody element not found');
            return;
        }
        
        tbody.innerHTML = '';

        if (!this.siblings || this.siblings.length === 0) {
            console.log('No siblings to render');
            return;
        }

        this.siblings.forEach((sibling, index) => {
            console.log('Creating row for sibling:', sibling.name, 'at index', index);
            const row = this.createSiblingRow(sibling, index);
            if (row) {
                tbody.appendChild(row);
                console.log('Row appended to tbody for:', sibling.name);
            } else {
                console.error('Failed to create row for sibling:', sibling);
            }
        });

        // Check if rows were actually added
        const rows = tbody.querySelectorAll('tr');
        console.log('Total rows in tbody after rendering:', rows.length);
        console.log('Tbody innerHTML length:', tbody.innerHTML.length);
        
        // Verify table was rendered successfully
        const table = document.getElementById('sibling-fees-table');
        if (table) {
            console.log('Sibling table rendered with', this.siblings.length, 'rows');
        } else {
            console.error('Sibling table element not found after rendering');
        }

        this.updateTotals();
        console.log('Sibling table rendered successfully');
    }

    /**
     * Create a sibling table row
     */
    createSiblingRow(sibling, index) {
        try {
            console.log('Creating row for sibling:', sibling);
            
            // Validate required fields
            if (!sibling.name) {
                console.error('Sibling name is missing:', sibling);
                return null;
            }
            
            if (!sibling.id) {
                console.error('Sibling ID is missing:', sibling);
                return null;
            }
            
            if (typeof sibling.total_outstanding !== 'number') {
                console.error('Sibling total_outstanding is not a number:', sibling);
                return null;
            }

            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="ps-3">
                    <div>
                        <div class="fw-semibold">${sibling.name || 'Unknown Student'}</div>
                        <small class="text-muted">ID: ${sibling.admission_no || 'N/A'}</small>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge bg-light text-dark">${sibling.class_section || 'N/A'}</span>
                </td>
                <td class="text-end">
                    <div class="fw-semibold">${this.formatCurrency(sibling.total_outstanding || 0)}</div>
                    <small class="text-muted">${(sibling.outstanding_fees || []).length} fee(s)</small>
                </td>
                <td class="text-end">
                    <div class="input-group input-group-sm" style="max-width: 150px; margin-left: auto;">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control payment-amount-input text-end"
                               data-sibling-index="${index}"
                               data-student-id="${sibling.id}"
                               data-max-amount="${sibling.total_outstanding || 0}"
                               min="0" max="${sibling.total_outstanding || 0}" step="0.01" value="0.00"
                               placeholder="0.00">
                    </div>
                    <div class="invalid-feedback" style="display: none;"></div>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary"
                            onclick="window.siblingFeeManager.showFeeDetails(${index})">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            `;

            // Bind payment amount change event
            const paymentInput = row.querySelector('.payment-amount-input');
            if (paymentInput) {
                paymentInput.addEventListener('input', this.handlePaymentAmountChange);
                paymentInput.addEventListener('blur', this.validateIndividualPayment.bind(this));
            } else {
                console.error('Payment input not found in row for sibling:', sibling);
            }

            console.log('Row created successfully for sibling:', sibling.name);
            return row;
            
        } catch (error) {
            console.error('Error creating sibling row:', error, 'for sibling:', sibling);
            return null;
        }
    }

    /**
     * Handle payment mode change
     */
    handlePaymentModeChange(event) {
        this.paymentMode = event.target.value;
        document.getElementById('sibling_payment_mode').value = this.paymentMode;

        // Get elements
        const paymentMethodConfig = document.getElementById('payment-method-config');
        const paymentMethodSelect = document.getElementById('sibling_payment_method');
        const journalConfig = document.getElementById('journal-config');
        const journalSelect = document.getElementById('sibling_journal_id');
        const depositAlert = document.getElementById('deposit-info-alert');

        if (this.paymentMode === 'direct') {
            // Show payment method field and make it required
            paymentMethodConfig.style.display = 'block';
            paymentMethodSelect.setAttribute('required', 'required');

            // Keep journal visible and required
            journalConfig.style.display = 'block';
            journalSelect.setAttribute('required', 'required');

            // Hide deposit info
            depositAlert.style.display = 'none';
        } else {
            // Hide payment method field and remove required attribute
            paymentMethodConfig.style.display = 'none';
            paymentMethodSelect.removeAttribute('required');

            // Keep journal visible and required (journal is always needed)
            journalConfig.style.display = 'block';
            journalSelect.setAttribute('required', 'required');

            // Show deposit info
            depositAlert.style.display = 'block';
        }

        this.updatePaymentCalculations();
    }

    /**
     * Handle payment amount change
     */
    handlePaymentAmountChange(event) {
        const input = event.target;
        const siblingIndex = parseInt(input.dataset.siblingIndex);
        const amount = parseFloat(input.value) || 0;

        // Update sibling data
        if (this.siblings[siblingIndex]) {
            this.siblings[siblingIndex].suggested_payment = amount;
        }

        this.updateTotals();
        this.updatePaymentCalculations();
        this.validateIndividualPayment(event);
    }

    /**
     * Validate individual payment amount
     */
    validateIndividualPayment(event) {
        const input = event.target;
        const amount = parseFloat(input.value) || 0;
        const maxAmount = parseFloat(input.dataset.maxAmount);
        const feedback = input.parentElement.parentElement.querySelector('.invalid-feedback');

        let isValid = true;
        let errorMessage = '';

        if (amount < 0) {
            isValid = false;
            errorMessage = 'Amount cannot be negative';
        } else if (amount > maxAmount + 50) { // Allow small overpayment tolerance
            isValid = false;
            errorMessage = `Amount exceeds outstanding fee (${this.formatCurrency(maxAmount)})`;
        }

        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            feedback.style.display = 'none';
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            feedback.textContent = errorMessage;
            feedback.style.display = 'block';
        }

        return isValid;
    }

    /**
     * Calculate payment distribution
     */
    async calculateDistribution(method) {
        const totalPayment = this.getTotalPaymentAmount();

        if (totalPayment <= 0) {
            // If no total set, use total outstanding
            const totalOutstanding = this.siblings.reduce((sum, sibling) => sum + sibling.total_outstanding, 0);
            this.distributeAmount(totalOutstanding, method);
            return;
        }

        try {
            const response = await fetch('/~omar/schooltemplate/public/index.php/fees/siblings/calculate-distribution', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    sibling_ids: this.siblings.map(s => s.id),
                    total_amount: totalPayment,
                    distribution_method: method
                })
            });

            const data = await response.json();

            if (data.success) {
                this.applyDistribution(data.data.distribution);
            } else {
                throw new Error(data.message || 'Distribution calculation failed');
            }

        } catch (error) {
            console.error('Error calculating distribution:', error);
            // Fallback to local calculation
            this.distributeAmount(totalPayment, method);
        }
    }

    /**
     * Local distribution calculation fallback
     */
    distributeAmount(totalAmount, method) {
        let distribution = [];

        switch (method) {
            case 'equal':
                const equalAmount = totalAmount / this.siblings.length;
                distribution = this.siblings.map(sibling => ({
                    student_id: sibling.id,
                    suggested_payment: Math.min(equalAmount, sibling.total_outstanding)
                }));
                break;

            case 'proportional':
                const totalOutstanding = this.siblings.reduce((sum, s) => sum + s.total_outstanding, 0);
                distribution = this.siblings.map(sibling => ({
                    student_id: sibling.id,
                    suggested_payment: totalOutstanding > 0
                        ? Math.min(totalAmount * (sibling.total_outstanding / totalOutstanding), sibling.total_outstanding)
                        : 0
                }));
                break;
        }

        this.applyDistribution(distribution);
    }

    /**
     * Apply distribution to the UI
     */
    applyDistribution(distribution) {
        distribution.forEach(item => {
            const input = document.querySelector(`input[data-student-id="${item.student_id}"]`);
            if (input) {
                input.value = item.suggested_payment.toFixed(2);
                input.dispatchEvent(new Event('input'));
            }
        });
    }

    /**
     * Clear all payment amounts
     */
    clearDistribution() {
        document.querySelectorAll('.payment-amount-input').forEach(input => {
            input.value = '0.00';
            input.dispatchEvent(new Event('input'));
        });
    }

    /**
     * Update totals display
     */
    updateTotals() {
        const totalOutstanding = this.siblings.reduce((sum, sibling) => sum + sibling.total_outstanding, 0);
        const totalPayment = this.getTotalPaymentAmount();

        document.getElementById('total-outstanding-amount').textContent = this.formatCurrency(totalOutstanding);
        document.getElementById('total-payment-amount').textContent = this.formatCurrency(totalPayment);
        document.getElementById('family-total-payment').textContent = this.formatCurrency(totalPayment);
        document.getElementById('family-remaining-balance').textContent = this.formatCurrency(totalOutstanding - totalPayment);
    }

    /**
     * Update payment calculations based on mode
     */
    updatePaymentCalculations() {
        const totalPayment = this.getTotalPaymentAmount();
        let depositUsed = 0;
        let cashRequired = totalPayment;

        if (this.paymentMode === 'deposit') {
            depositUsed = Math.min(this.availableDeposit, totalPayment);
            cashRequired = totalPayment - depositUsed;
        }

        // Update summary
        document.getElementById('summary-total-payment').textContent = this.formatCurrency(totalPayment);
        document.getElementById('summary-deposit-used').textContent = this.formatCurrency(depositUsed);
        document.getElementById('summary-cash-required').textContent = this.formatCurrency(cashRequired);
        document.getElementById('summary-students-count').textContent = this.getActiveStudentsCount();
    }

    /**
     * Get total payment amount from inputs
     */
    getTotalPaymentAmount() {
        return Array.from(document.querySelectorAll('.payment-amount-input'))
            .reduce((sum, input) => sum + (parseFloat(input.value) || 0), 0);
    }

    /**
     * Get count of students with payment amount > 0
     */
    getActiveStudentsCount() {
        return Array.from(document.querySelectorAll('.payment-amount-input'))
            .filter(input => (parseFloat(input.value) || 0) > 0).length;
    }

    /**
     * Validate the entire payment
     */
    async validatePayment() {
        const validateBtn = document.getElementById('validate_sibling_payment_btn');
        const processBtn = document.getElementById('process_sibling_payment_btn');

        try {
            validateBtn.disabled = true;
            validateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Validating...';

            const paymentData = this.collectPaymentData();

            const response = await fetch('/~omar/schooltemplate/public/index.php/fees/siblings/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                credentials: 'same-origin',
                body: JSON.stringify(paymentData)
            });

            const data = await response.json();

            if (data.success && data.data.valid) {
                this.validationState.isValid = true;
                this.validationState.errors = [];
                processBtn.disabled = false;
                this.showSuccess('Payment validation successful!');
            } else {
                this.validationState.isValid = false;
                this.validationState.errors = data.data.errors || [data.message];
                processBtn.disabled = true;
                this.showError('Payment validation failed: ' + this.validationState.errors.join(', '));
            }

        } catch (error) {
            console.error('Validation error:', error);
            this.validationState.isValid = false;
            this.showError('Validation failed. Please check your payment details.');
            processBtn.disabled = true;
        } finally {
            validateBtn.disabled = false;
            validateBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Validate Payment';
        }
    }

    /**
     * Process the sibling payment
     */
    async processPayment() {
        if (!this.validationState.isValid) {
            this.showError('Please validate the payment first.');
            return;
        }

        const processBtn = document.getElementById('process_sibling_payment_btn');

        try {
            processBtn.disabled = true;
            processBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

            const paymentData = this.collectPaymentData();

            const response = await fetch('/~omar/schooltemplate/public/index.php/fees/siblings/process', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                credentials: 'same-origin',
                body: JSON.stringify(paymentData)
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccess('Family payment processed successfully!');
                this.showPaymentResults(data);

                // Close family payment modal after a delay
                setTimeout(() => {
                    const familyModal = bootstrap.Modal.getInstance(document.getElementById('familyPaymentModal'));
                    familyModal?.hide();

                    // Reset loaded state for next time
                    this.isLoaded = false;
                    this.siblings = [];

                    // Reload the page to refresh fee data
                    if (typeof refreshFeesData === 'function') {
                        refreshFeesData();
                    } else {
                        location.reload();
                    }
                }, 3000);

            } else {
                this.showError('Payment processing failed: ' + data.message);
            }

        } catch (error) {
            console.error('Payment processing error:', error);
            this.showError('An error occurred while processing the payment. Please try again.');
        } finally {
            processBtn.disabled = false;
            processBtn.innerHTML = '<i class="fas fa-credit-card me-2"></i>Process Family Payment';
        }
    }

    /**
     * Collect payment data for submission
     */
    collectPaymentData() {
        const siblingPayments = [];

        document.querySelectorAll('.payment-amount-input').forEach(input => {
            const amount = parseFloat(input.value) || 0;
            if (amount > 0) {
                const studentId = parseInt(input.dataset.studentId);
                const siblingIndex = parseInt(input.dataset.siblingIndex);
                const sibling = this.siblings[siblingIndex];

                siblingPayments.push({
                    student_id: studentId,
                    amount: amount,
                    fee_ids: sibling.outstanding_fees.map(fee => fee.id)
                });
            }
        });

        const paymentData = {
            payment_mode: this.paymentMode,
            sibling_payments: siblingPayments,
            payment_date: document.getElementById('sibling_payment_date').value,
            payment_notes: document.getElementById('sibling_payment_notes').value
        };

        if (this.paymentMode === 'direct') {
            paymentData.payment_method = document.getElementById('sibling_payment_method').value;
            paymentData.journal_id = document.getElementById('sibling_journal_id').value;
        }

        return paymentData;
    }

    /**
     * Show fee details for a sibling
     */
    showFeeDetails(siblingIndex) {
        const sibling = this.siblings[siblingIndex];
        if (!sibling) return;

        const modalTitle = document.getElementById('siblingFeeDetailModalLabel');
        const modalBody = document.getElementById('siblingFeeDetailBody');

        modalTitle.textContent = `Fee Details - ${sibling.name}`;

        let html = `
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Fee Type</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Due Date</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        sibling.outstanding_fees.forEach(fee => {
            const statusBadge = fee.is_overdue
                ? '<span class="badge bg-danger">Overdue</span>'
                : '<span class="badge bg-warning">Pending</span>';

            html += `
                <tr>
                    <td>${fee.fee_name}</td>
                    <td class="text-end">${this.formatCurrency(fee.amount)}</td>
                    <td class="text-center">${fee.due_date || 'N/A'}</td>
                    <td class="text-center">${statusBadge}</td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold">
                            <td>Total Outstanding</td>
                            <td class="text-end">${this.formatCurrency(sibling.total_outstanding)}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        `;

        modalBody.innerHTML = html;

        const modal = new bootstrap.Modal(document.getElementById('siblingFeeDetailModal'));
        modal.show();
    }

    /**
     * Update siblings count display
     */
    updateSiblingsCount() {
        console.log('Updating siblings count:', this.siblings?.length || 0);
        
        try {
            const countBadge = document.getElementById('siblings-count');
            const countBadge2 = document.getElementById('siblings-count-badge');

            const siblingsCount = this.siblings?.length || 0;

            if (countBadge) {
                countBadge.textContent = siblingsCount;
                countBadge.style.display = siblingsCount > 0 ? 'inline' : 'none';
            }

            if (countBadge2) {
                countBadge2.textContent = siblingsCount;
            }
            
            console.log('Siblings count updated to:', siblingsCount);
            
        } catch (error) {
            console.error('Error updating siblings count:', error);
        }
    }

    /**
     * Setup payment mode state
     */
    setupPaymentModeState() {
        // Enable/disable deposit option based on availability
        const depositRadio = document.getElementById('deposit_payment_mode');
        const depositLabel = document.querySelector('label[for="deposit_payment_mode"]');

        if (this.availableDeposit <= 0) {
            depositRadio.disabled = true;
            depositLabel.classList.add('text-muted');
            depositLabel.innerHTML += ' <small>(No deposit available)</small>';
        }
    }

    /**
     * Setup payment mode toggle behavior
     */
    setupPaymentModeToggle() {
        // Initial state
        this.handlePaymentModeChange({ target: { value: 'direct' } });
    }

    /**
     * Check for siblings and update family payment link visibility
     */
    async checkAndUpdateFamilyLink(studentId) {
        console.log('Checking siblings for family link visibility, student ID:', studentId);

        try {
            const response = await fetch(`/~omar/schooltemplate/public/index.php/fees/siblings/${studentId}/data`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                credentials: 'same-origin'
            });

            const data = await response.json();
            console.log('Siblings check response:', data);

            if (data.success && data.data.siblings && data.data.siblings.length > 0) {
                // Store minimal sibling info for link display
                this.siblings = data.data.siblings;
                this.updateFamilyPaymentLink();
            } else {
                console.log('No siblings found, keeping family link hidden');
                const familyLink = document.getElementById('family-payment-link');
                if (familyLink) {
                    familyLink.style.display = 'none';
                }
            }
        } catch (error) {
            console.error('Error checking siblings:', error);
            // Hide link on error
            const familyLink = document.getElementById('family-payment-link');
            if (familyLink) {
                familyLink.style.display = 'none';
            }
        }
    }

    /**
     * Update family payment link visibility and count
     */
    updateFamilyPaymentLink() {
        const familyLink = document.getElementById('family-payment-link');
        const familyCount = document.getElementById('family-siblings-count');
        const familyLinkText = document.getElementById('family-link-text');

        if (!familyLink || !familyCount) {
            console.error('Family payment link elements not found');
            return;
        }

        const siblingsCount = this.siblings?.length || 0;

        if (siblingsCount > 0) {
            familyCount.textContent = siblingsCount;
            familyLink.style.display = 'inline-block';

            // Update link text to be more descriptive
            if (familyLinkText) {
                const studentName = document.getElementById('summary-student-name')?.textContent || 'Student';
                familyLinkText.textContent = `Pay for ${studentName} + Family`;
            }

            console.log('Family payment link shown with', siblingsCount, 'siblings');
        } else {
            familyLink.style.display = 'none';
            console.log('Family payment link hidden - no siblings found');
        }
    }

    /**
     * Show payment results
     */
    showPaymentResults(data) {
        let message = `Payment Summary:\n`;
        message += `- Total Processed: ${this.formatCurrency(data.summary.total_processed)}\n`;
        message += `- Students Paid: ${data.summary.successful_payments}\n`;

        if (data.summary.total_deposit_used > 0) {
            message += `- From Deposit: ${this.formatCurrency(data.summary.total_deposit_used)}\n`;
        }
        if (data.summary.total_cash_payment > 0) {
            message += `- Cash Payment: ${this.formatCurrency(data.summary.total_cash_payment)}\n`;
        }

        alert(message);
    }

    /**
     * Utility methods
     */
    formatCurrency(amount) {
        return '$' + parseFloat(amount || 0).toFixed(2);
    }

    showSuccess(message) {
        // You can integrate with your existing notification system
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else {
            alert('Success: ' + message);
        }
    }

    showError(message) {
        // You can integrate with your existing notification system
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else {
            alert('Error: ' + message);
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('SiblingFeeCollectionManager: DOM ready, checking for modals...');

    // Only initialize if we're on a page with the fee collection modal or family payment modal
    const mainModal = document.getElementById('feeCollectionModalWidth');
    const familyModal = document.getElementById('familyPaymentModal');

    if (mainModal || familyModal) {
        console.log('SiblingFeeCollectionManager: Fee collection modals found, initializing...');
        window.siblingFeeManager = new SiblingFeeCollectionManager();
        window.siblingFeeManager.init();
        console.log('SiblingFeeCollectionManager: Initialized successfully');
    } else {
        console.log('SiblingFeeCollectionManager: Fee collection modals not found, skipping initialization');
    }
});

// Export for global access
window.SiblingFeeCollectionManager = SiblingFeeCollectionManager;

} // End of duplicate prevention check