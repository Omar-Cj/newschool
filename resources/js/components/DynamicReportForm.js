/**
 * Dynamic Report Form Component
 * Main component for rendering and managing dynamic report forms
 */

import ReportApiService from '../services/ReportApiService.js';
import FormValidation from '../utils/FormValidation.js';
import DependencyHandler from './DependencyHandler.js';

export class DynamicReportForm {
    constructor(config) {
        this.config = config;
        this.apiService = ReportApiService;
        this.validator = FormValidation;
        this.dependencyHandler = DependencyHandler;
        this.translations = window.ReportConfig?.translations || {};

        this.currentReport = null;
        this.currentParameters = [];
        this.formData = {};

        this.initializeElements();
        this.attachEventListeners();
    }

    /**
     * Initialize DOM elements
     */
    initializeElements() {
        this.elements = {
            categoryTabs: document.querySelector(this.config.categoryTabsSelector),
            reportSelector: document.getElementById(this.config.reportSelectorId),
            formContainer: document.getElementById(this.config.formContainerId),
            formActions: document.getElementById(this.config.formActionsId),
            resultsSection: document.getElementById(this.config.resultsSectionId),
            resultsContainer: document.getElementById(this.config.resultsContainerId),
            generateBtn: document.getElementById(this.config.generateBtnId),
            resetBtn: document.getElementById(this.config.resetBtnId),
            reportDescription: document.getElementById(this.config.reportDescriptionId),
            reportDescriptionText: document.getElementById(this.config.reportDescriptionTextId),
            exportButtons: document.querySelectorAll('.export-btn')
        };
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        // Report selector change
        if (this.elements.reportSelector) {
            this.elements.reportSelector.addEventListener('change', (e) => {
                this.handleReportSelection(e.target.value);
            });
        }

        // Generate report button
        if (this.elements.generateBtn) {
            this.elements.generateBtn.addEventListener('click', () => {
                this.handleGenerateReport();
            });
        }

        // Reset form button
        if (this.elements.resetBtn) {
            this.elements.resetBtn.addEventListener('click', () => {
                this.resetForm();
            });
        }

        // Export buttons
        this.elements.exportButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const format = e.currentTarget.dataset.format;
                this.handleExportReport(format);
            });
        });
    }

    /**
     * Initialize the component - load initial data
     */
    async initialize() {
        try {
            this.showLoading(this.elements.categoryTabs, true);

            const reportsData = await this.apiService.fetchReports();

            // Handle wrapped response - API may return {success, message, data: {...}}
            const actualData = reportsData?.data || reportsData;

            if (actualData && actualData.categories) {
                this.renderCategoryTabs(actualData.categories);
            } else {
                console.error('No categories found in response');
            }

        } catch (error) {
            console.error('Initialization error:', error);
            this.showError('Failed to load reports. Please refresh the page.');
        } finally {
            this.showLoading(this.elements.categoryTabs, false);
        }
    }

    /**
     * Render category tabs
     * @param {Array} categories - Report categories
     */
    renderCategoryTabs(categories) {
        if (!this.elements.categoryTabs) return;

        const tabsHtml = categories.map((category, index) => `
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link ${index === 0 ? 'active' : ''}"
                    id="category-tab-${category.id}"
                    data-bs-toggle="tab"
                    data-bs-target="#category-${category.id}"
                    data-category-id="${category.id}"
                    type="button"
                    role="tab"
                    aria-controls="category-${category.id}"
                    aria-selected="${index === 0}"
                >
                    ${this.escapeHtml(category.name)}
                </button>
            </li>
        `).join('');

        this.elements.categoryTabs.innerHTML = tabsHtml;

        // Attach category tab listeners
        this.elements.categoryTabs.querySelectorAll('[data-category-id]').forEach(tab => {
            tab.addEventListener('click', (e) => {
                const categoryId = e.target.dataset.categoryId;
                this.handleCategoryChange(categoryId);
            });
        });

        // Load first category reports
        if (categories.length > 0) {
            this.handleCategoryChange(categories[0].id);
        }
    }

    /**
     * Handle category tab change
     * @param {number} categoryId - Selected category ID
     */
    async handleCategoryChange(categoryId) {
        try {
            this.showLoading(this.elements.reportSelector, true);

            const reportsData = await this.apiService.fetchReports();
            // Handle wrapped response
            const actualData = reportsData?.data || reportsData;
            const category = actualData.categories.find(cat => cat.id == categoryId);

            if (category && category.reports) {
                this.populateReportSelector(category.reports);
            }

        } catch (error) {
            console.error('Category change error:', error);
            this.showError('Failed to load reports for this category.');
        } finally {
            this.showLoading(this.elements.reportSelector, false);
        }
    }

    /**
     * Populate report selector dropdown
     * @param {Array} reports - Array of reports
     */
    populateReportSelector(reports) {
        if (!this.elements.reportSelector) return;

        // Clear and add placeholder
        this.elements.reportSelector.innerHTML = `
            <option value="">${this.translations.selectAReport || 'Select a report'}</option>
        `;

        // Add report options
        reports.forEach(report => {
            const option = document.createElement('option');
            option.value = report.id;
            option.textContent = report.name;
            option.dataset.description = report.description || '';
            this.elements.reportSelector.appendChild(option);
        });

        // Reset form
        this.resetForm();
    }

    /**
     * Handle report selection
     * @param {number} reportId - Selected report ID
     */
    async handleReportSelection(reportId) {
        if (!reportId) {
            this.resetForm();
            return;
        }

        try {
            this.showFormLoading(true);

            // Fetch report parameters
            const response = await this.apiService.fetchParameters(reportId);

            // Handle wrapped response - backend returns {success, message, data: {report, parameters}}
            const data = response?.data || response;

            this.currentReport = data.report;
            this.currentParameters = data.parameters || [];

            // Show report description
            if (this.currentReport.description) {
                this.elements.reportDescriptionText.textContent = this.currentReport.description;
                this.elements.reportDescription.style.display = 'block';
            } else {
                this.elements.reportDescription.style.display = 'none';
            }

            // Render form
            this.renderForm(this.currentParameters);

            // Show form actions
            this.elements.formActions.style.display = 'flex';

            // Hide results
            this.elements.resultsSection.style.display = 'none';

        } catch (error) {
            console.error('Report selection error:', error);
            this.showError('Failed to load report parameters. Please try again.');
        } finally {
            this.showFormLoading(false);
        }
    }

    /**
     * Render dynamic form
     * @param {Array} parameters - Array of parameter objects
     */
    renderForm(parameters) {
        if (!this.elements.formContainer) return;

        if (parameters.length === 0) {
            this.elements.formContainer.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    This report has no parameters. Click "Generate Report" to run it.
                </div>
            `;
            return;
        }

        // Create form element
        const form = document.createElement('form');
        form.id = 'dynamicReportForm';
        form.className = 'dynamic-report-form';

        // Register dependencies first
        this.dependencyHandler.registerDependencies(parameters);

        // Render parameters in a grid layout
        const parametersHtml = parameters.map(param => this.renderParameter(param)).join('');

        form.innerHTML = `
            <div class="row g-3">
                ${parametersHtml}
            </div>
        `;

        this.elements.formContainer.innerHTML = '';
        this.elements.formContainer.appendChild(form);

        // Attach dependency listeners
        this.dependencyHandler.attachDependencyListeners(form);

        // Attach blur validation
        parameters.forEach(param => {
            const field = form.querySelector(`[data-parameter-name="${param.name}"]`);
            if (field) {
                this.validator.attachBlurValidation(field, param.is_required, param.type);
            }
        });
    }

    /**
     * Render individual parameter
     * @param {Object} param - Parameter object
     * @returns {string} HTML string for parameter
     */
    renderParameter(param) {
        const colClass = param.type === 'textarea' ? 'col-12' : 'col-md-6';
        const isDependent = param.depends_on && param.parent_id;
        const disabled = isDependent ? 'disabled' : '';

        return `
            <div class="${colClass}">
                <div class="parameter-field" data-parameter-id="${param.id}">
                    <label for="param_${param.name}" class="form-label">
                        ${this.escapeHtml(param.label)}
                        ${param.is_required ? '<span class="required-indicator">*</span>' : ''}
                    </label>
                    ${this.renderParameterInput(param, disabled)}
                    ${param.description ? `<small class="form-text text-muted">${this.escapeHtml(param.description)}</small>` : ''}
                </div>
            </div>
        `;
    }

    /**
     * Render parameter input based on type
     * @param {Object} param - Parameter object
     * @param {string} disabled - Disabled attribute
     * @returns {string} HTML string for input
     */
    renderParameterInput(param, disabled) {
        const baseAttrs = `
            id="param_${param.name}"
            name="${param.name}"
            data-parameter-name="${param.name}"
            data-parameter-id="${param.id}"
            data-parameter-type="${param.type}"
            data-required="${param.is_required ? 'true' : 'false'}"
            ${disabled}
        `;

        switch (param.type) {
            case 'date':
                return this.renderDatePicker(param, baseAttrs);

            case 'text':
            case 'email':
                return this.renderTextInput(param, baseAttrs);

            case 'number':
                return this.renderNumberInput(param, baseAttrs);

            case 'textarea':
                return this.renderTextArea(param, baseAttrs);

            case 'select':
                return this.renderDropdown(param, baseAttrs);

            case 'multiselect':
                return this.renderMultiSelect(param, baseAttrs);

            case 'checkbox':
                return this.renderCheckbox(param, baseAttrs);

            default:
                return this.renderTextInput(param, baseAttrs);
        }
    }

    /**
     * Render date picker input
     * @param {Object} param - Parameter object
     * @param {string} baseAttrs - Base attributes
     * @returns {string} HTML string
     */
    renderDatePicker(param, baseAttrs) {
        const defaultValue = param.default_value || '';
        return `
            <input
                type="date"
                class="form-control"
                value="${this.escapeHtml(defaultValue)}"
                placeholder="${this.escapeHtml(param.placeholder || param.label)}"
                ${baseAttrs}
            />
        `;
    }

    /**
     * Render text input
     * @param {Object} param - Parameter object
     * @param {string} baseAttrs - Base attributes
     * @returns {string} HTML string
     */
    renderTextInput(param, baseAttrs) {
        const defaultValue = param.default_value || '';
        const type = param.type === 'email' ? 'email' : 'text';

        return `
            <input
                type="${type}"
                class="form-control"
                value="${this.escapeHtml(defaultValue)}"
                placeholder="${this.escapeHtml(param.placeholder || param.label)}"
                ${baseAttrs}
            />
        `;
    }

    /**
     * Render number input
     * @param {Object} param - Parameter object
     * @param {string} baseAttrs - Base attributes
     * @returns {string} HTML string
     */
    renderNumberInput(param, baseAttrs) {
        const defaultValue = param.default_value || '';
        const min = param.min_value !== undefined ? `min="${param.min_value}"` : '';
        const max = param.max_value !== undefined ? `max="${param.max_value}"` : '';
        const step = param.step || 'any';

        return `
            <input
                type="number"
                class="form-control"
                value="${this.escapeHtml(defaultValue)}"
                placeholder="${this.escapeHtml(param.placeholder || param.label)}"
                step="${step}"
                ${min}
                ${max}
                ${baseAttrs}
            />
        `;
    }

    /**
     * Render textarea
     * @param {Object} param - Parameter object
     * @param {string} baseAttrs - Base attributes
     * @returns {string} HTML string
     */
    renderTextArea(param, baseAttrs) {
        const defaultValue = param.default_value || '';

        return `
            <textarea
                class="form-control"
                rows="${param.rows || 3}"
                placeholder="${this.escapeHtml(param.placeholder || param.label)}"
                ${baseAttrs}
            >${this.escapeHtml(defaultValue)}</textarea>
        `;
    }

    /**
     * Render dropdown select
     * @param {Object} param - Parameter object
     * @param {string} baseAttrs - Base attributes
     * @returns {string} HTML string
     */
    renderDropdown(param, baseAttrs) {
        const options = param.values || [];
        const placeholder = param.placeholder || `Select ${param.label}`;

        const optionsHtml = options.map(option => {
            const selected = option.value == param.default_value ? 'selected' : '';
            return `<option value="${this.escapeHtml(option.value)}" ${selected}>${this.escapeHtml(option.label)}</option>`;
        }).join('');

        return `
            <select class="form-select" ${baseAttrs}>
                <option value="">${this.escapeHtml(placeholder)}</option>
                ${optionsHtml}
            </select>
        `;
    }

    /**
     * Render multi-select dropdown
     * @param {Object} param - Parameter object
     * @param {string} baseAttrs - Base attributes
     * @returns {string} HTML string
     */
    renderMultiSelect(param, baseAttrs) {
        const options = param.values || [];
        const defaultValues = Array.isArray(param.default_value) ? param.default_value : [];

        const optionsHtml = options.map(option => {
            const selected = defaultValues.includes(option.value) ? 'selected' : '';
            return `<option value="${this.escapeHtml(option.value)}" ${selected}>${this.escapeHtml(option.label)}</option>`;
        }).join('');

        return `
            <select class="form-select" multiple size="${Math.min(options.length, 5)}" ${baseAttrs}>
                ${optionsHtml}
            </select>
            <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple</small>
        `;
    }

    /**
     * Render checkbox input
     * @param {Object} param - Parameter object
     * @param {string} baseAttrs - Base attributes
     * @returns {string} HTML string
     */
    renderCheckbox(param, baseAttrs) {
        const checked = param.default_value == '1' || param.default_value === true ? 'checked' : '';

        return `
            <div class="form-check">
                <input
                    type="checkbox"
                    class="form-check-input"
                    value="1"
                    ${checked}
                    ${baseAttrs}
                />
                <label class="form-check-label" for="param_${param.name}">
                    ${param.description || 'Enable this option'}
                </label>
            </div>
        `;
    }

    /**
     * Handle generate report button click
     */
    async handleGenerateReport() {
        const form = document.getElementById('dynamicReportForm');

        if (form) {
            // Validate form
            const validationResult = this.validator.validateForm(form);

            if (!validationResult.isValid) {
                this.showValidationErrors(validationResult.errors);
                return;
            }

            // Collect form data
            this.formData = this.collectFormData(form);
        } else {
            this.formData = {};
        }

        try {
            // Show loading state
            this.setButtonLoading(this.elements.generateBtn, true);

            // Execute report
            const results = await this.apiService.executeReport(this.currentReport.id, this.formData);

            // Display results
            this.displayResults(results);

            // Show results section
            this.elements.resultsSection.style.display = 'block';

            // Scroll to results
            this.elements.resultsSection.scrollIntoView({ behavior: 'smooth' });

            // Show success message
            this.showSuccess('Report generated successfully!');

        } catch (error) {
            console.error('❌ Generate report error:', error);
            console.error('📍 Error details:', {
                message: error.message,
                stack: error.stack,
                reportId: this.currentReport?.id,
                formData: this.formData
            });

            // Provide more specific error message
            let errorMessage = 'Failed to generate report. Please try again.';
            if (error.response?.data?.message) {
                errorMessage = error.response.data.message;
            } else if (error.message) {
                errorMessage = error.message;
            }

            this.showError(errorMessage);
        } finally {
            this.setButtonLoading(this.elements.generateBtn, false);
        }
    }

    /**
     * Collect form data
     * @param {HTMLFormElement} form - Form element
     * @returns {Object} Form data object
     */
    collectFormData(form) {
        const formData = {};
        const fields = form.querySelectorAll('[data-parameter-name]');

        fields.forEach(field => {
            const paramName = field.dataset.parameterName;
            formData[paramName] = this.validator.getFieldValue(field);
        });

        return formData;
    }

    /**
     * Display report results
     * @param {Object} results - Report results data
     */
    displayResults(results) {
        if (!this.elements.resultsContainer) return;

        try {
            // Handle wrapped response from backend
            // API returns: { success, message, data: { success, report, data, meta } }
            const innerData = results?.data || results;

            // Extract report type and data with safe navigation
            const reportType = innerData?.report?.type || results?.report?.type || 'tabular';
            const reportData = innerData?.data || results?.data;
            const reportMeta = innerData?.meta || results?.meta;

            // Validate we have data to display
            if (!reportData) {
                console.error('❌ No report data received:', results);
                this.showError('No data returned from report. Please check your parameters.');
                this.elements.resultsContainer.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>No Data:</strong> The report executed successfully but returned no data.
                    </div>
                `;
                return;
            }

            // Validate columns and rows exist
            if (!reportData.columns || !reportData.rows) {
                console.error('❌ Invalid report data structure:', reportData);
                this.showError('Invalid report data structure received.');
                this.elements.resultsContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Invalid Data Structure:</strong> Expected data with columns and rows properties.
                        <br><small>Please check the console for detailed error information.</small>
                    </div>
                `;
                return;
            }

            // Route to appropriate rendering method based on report type
            let renderedHtml = '';

            switch (reportType.toLowerCase()) {
                case 'tabular':
                    console.log('🗂️ Rendering tabular report');
                    renderedHtml = this.renderTabularReport(reportData);
                    break;

                case 'summary':
                    console.log('📊 Rendering summary report');
                    renderedHtml = this.renderSummaryReport(reportData);
                    break;

                case 'chart':
                    console.log('📈 Rendering chart report');
                    renderedHtml = this.renderChartReport(reportData);
                    break;

                case 'custom':
                    console.log('🔧 Rendering custom report');
                    renderedHtml = this.renderCustomReport(reportData);
                    break;

                default:
                    console.warn(`⚠️ Unknown report type: ${reportType}, falling back to custom rendering`);
                    renderedHtml = this.renderCustomReport(reportData);
            }

            // Display the rendered HTML
            this.elements.resultsContainer.innerHTML = renderedHtml;

            // Add meta information if available
            if (reportMeta) {
                this.appendMetaInformation(reportMeta);
            }

            console.log('✅ Report rendered successfully');

        } catch (error) {
            console.error('❌ Error in displayResults:', error);
            console.error('📍 Error stack:', error.stack);
            console.error('📍 Results object:', results);

            this.elements.resultsContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error displaying results:</strong> ${this.escapeHtml(error.message)}
                    <br><small>Please check the console for more details.</small>
                </div>
            `;
        }
    }

    /**
     * Render tabular report (columns and rows structure)
     * @param {Object} data - Report data with {columns, rows}
     * @returns {string} HTML string
     */
    renderTabularReport(data) {
        console.log('🗂️ renderTabularReport called with:', data);

        // Validate tabular structure
        if (!data || typeof data !== 'object') {
            console.error('❌ Invalid tabular data structure:', data);
            return `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Invalid Data:</strong> Expected tabular data structure with columns and rows.
                </div>
            `;
        }

        const rows = data?.rows;
        const columns = data?.columns;

        console.log('📋 Tabular rows:', rows);
        console.log('🔧 Tabular columns:', columns);

        // Validate rows
        if (!rows || !Array.isArray(rows)) {
            console.error('❌ Invalid rows data:', rows);
            return `
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle me-2"></i>
                    ${this.translations.noDataFound || 'No data found'}
                </div>
            `;
        }

        if (rows.length === 0) {
            return `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    ${this.translations.noDataFound || 'No data found'}
                </div>
            `;
        }

        // Validate columns
        if (!columns || !Array.isArray(columns)) {
            console.warn('⚠️ No columns provided, will auto-generate from data');
            // Auto-generate columns from first row
            return this.generateResultsTable(rows, []);
        }

        // Use existing table generation method
        return this.generateResultsTable(rows, columns);
    }

    /**
     * Render summary report (array of metric objects)
     * @param {Array} data - Array of {metric, value, formatted}
     * @returns {string} HTML string
     */
    renderSummaryReport(data) {
        console.log('📊 renderSummaryReport called with:', data);

        // Validate data is an array
        if (!Array.isArray(data)) {
            console.error('❌ Invalid summary data, expected array:', data);
            return `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Invalid Data:</strong> Expected summary data as array of metrics.
                </div>
            `;
        }

        if (data.length === 0) {
            return `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    No summary metrics available.
                </div>
            `;
        }

        // Render as key-value cards
        const cardsHtml = data.map((item, index) => {
            const metric = item?.metric || item?.label || `Metric ${index + 1}`;
            const value = item?.formatted || item?.value || 'N/A';
            const description = item?.description || '';

            return `
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">${this.escapeHtml(metric)}</h6>
                            <h3 class="card-title mb-0">${this.escapeHtml(value)}</h3>
                            ${description ? `<p class="card-text mt-2"><small>${this.escapeHtml(description)}</small></p>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }).join('');

        return `
            <div class="summary-report">
                <div class="row">
                    ${cardsHtml}
                </div>
                <div class="mt-3 text-muted">
                    <small><i class="bi bi-info-circle me-1"></i>Showing ${data.length} metric(s)</small>
                </div>
            </div>
        `;
    }

    /**
     * Render chart report (placeholder for future implementation)
     * @param {Object} data - Chart data structure
     * @returns {string} HTML string
     */
    renderChartReport(data) {
        console.log('📈 renderChartReport called with:', data);

        // For now, show a placeholder message and display data in table format
        let tableHtml = '';

        // Try to display the data in a meaningful way
        if (Array.isArray(data)) {
            tableHtml = this.renderCustomReport(data);
        } else if (data && typeof data === 'object') {
            // Convert object to key-value display
            const items = Object.entries(data).map(([key, value]) => ({
                metric: key,
                value: JSON.stringify(value)
            }));
            tableHtml = this.renderSummaryReport(items);
        }

        return `
            <div class="alert alert-info mb-3">
                <i class="bi bi-bar-chart me-2"></i>
                <strong>Chart Visualization:</strong> Chart rendering is coming soon.
                For now, here's the raw data:
            </div>
            ${tableHtml}
        `;
    }

    /**
     * Render custom report (auto-detect structure)
     * @param {*} data - Custom data structure
     * @returns {string} HTML string
     */
    renderCustomReport(data) {
        console.log('🔧 renderCustomReport called with:', data);
        console.log('🔍 Data type:', typeof data);
        console.log('🔍 Is Array:', Array.isArray(data));

        // Handle array of objects (most common custom format)
        if (Array.isArray(data)) {
            if (data.length === 0) {
                return `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        No data returned by the custom report.
                    </div>
                `;
            }

            // Check if array contains objects
            if (typeof data[0] === 'object' && data[0] !== null) {
                console.log('✅ Array of objects detected, auto-generating table');

                // Auto-generate columns from first row
                const firstRow = data[0];
                const columns = Object.keys(firstRow).map(key => ({
                    field: key,
                    label: key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ')
                }));

                console.log('🔧 Auto-generated columns:', columns);

                return this.generateResultsTable(data, columns);
            } else {
                // Array of primitives - display as single column
                console.log('📋 Array of primitives detected');
                const columns = [{ field: 'value', label: 'Value' }];
                const rows = data.map(item => ({ value: item }));
                return this.generateResultsTable(rows, columns);
            }
        }

        // Handle single object
        if (data && typeof data === 'object') {
            console.log('📊 Single object detected, converting to key-value display');
            const items = Object.entries(data).map(([key, value]) => ({
                metric: key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' '),
                value: typeof value === 'object' ? JSON.stringify(value) : value
            }));
            return this.renderSummaryReport(items);
        }

        // Fallback to JSON display
        console.warn('⚠️ Unknown data structure, displaying as JSON');
        return `
            <div class="alert alert-warning mb-3">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Custom Format:</strong> Unable to auto-detect data structure. Displaying raw data:
            </div>
            <pre class="bg-light p-3 rounded"><code>${this.escapeHtml(JSON.stringify(data, null, 2))}</code></pre>
        `;
    }

    /**
     * Append meta information to results
     * @param {Object} meta - Meta information
     */
    appendMetaInformation(meta) {
        if (!meta || !this.elements.resultsContainer) return;

        const metaHtml = `
            <div class="report-meta mt-3 p-3 bg-light rounded">
                <div class="row g-2">
                    ${meta.total_records !== undefined ? `
                        <div class="col-auto">
                            <small class="text-muted">
                                <i class="bi bi-table me-1"></i>
                                <strong>Records:</strong> ${meta.total_records}
                            </small>
                        </div>
                    ` : ''}
                    ${meta.execution_time_ms !== undefined ? `
                        <div class="col-auto">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                <strong>Execution Time:</strong> ${meta.execution_time_ms}ms
                            </small>
                        </div>
                    ` : ''}
                    ${meta.generated_at ? `
                        <div class="col-auto">
                            <small class="text-muted">
                                <i class="bi bi-calendar me-1"></i>
                                <strong>Generated:</strong> ${meta.generated_at}
                            </small>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;

        this.elements.resultsContainer.insertAdjacentHTML('beforeend', metaHtml);
    }

    /**
     * Generate HTML table from results
     * @param {Array} data - Results data
     * @param {Array} columns - Column definitions
     * @returns {string} HTML table string
     */
    generateResultsTable(data, columns) {
        console.log('🔧 generateResultsTable called with:', {
            dataLength: data?.length,
            columnsLength: columns?.length,
            dataType: Array.isArray(data) ? 'array' : typeof data,
            columnsType: Array.isArray(columns) ? 'array' : typeof columns
        });

        // Guard clause: validate inputs
        if (!data || !Array.isArray(data)) {
            console.error('❌ Invalid data parameter:', data);
            return `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Invalid data format:</strong> Expected an array of rows, received ${typeof data}
                </div>
            `;
        }

        if (!columns || !Array.isArray(columns)) {
            console.error('❌ Invalid columns parameter:', columns);
            return `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Invalid columns format:</strong> Expected an array of column definitions, received ${typeof columns}
                </div>
            `;
        }

        // Handle empty data
        if (data.length === 0) {
            console.log('ℹ️ Empty data array');
            return `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    ${this.translations.noDataFound || 'No data found'}
                </div>
            `;
        }

        try {
            // If no columns defined, auto-generate from first row
            let effectiveColumns = columns;
            if (columns.length === 0 && data.length > 0) {
                console.log('⚠️ No columns defined, auto-generating from first row');
                const firstRow = data[0] ?? {};
                effectiveColumns = Object.keys(firstRow).map(key => ({
                    field: key,
                    name: key,
                    label: key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ')
                }));
                console.log('🔧 Auto-generated columns:', effectiveColumns);
            }

            // Generate table headers
            const headers = effectiveColumns.map(col => {
                const label = col?.label ?? col?.name ?? col?.field ?? 'Unknown';
                return `<th scope="col">${this.escapeHtml(label)}</th>`;
            }).join('');

            // Generate table rows
            const rows = data.map((row, rowIndex) => {
                try {
                    const cells = effectiveColumns.map((col, colIndex) => {
                        try {
                            // Backend uses 'field' property, fallback to 'name' for compatibility
                            const fieldName = col?.field ?? col?.name ?? '';
                            const value = row?.[fieldName] ?? '';
                            return `<td>${this.escapeHtml(value)}</td>`;
                        } catch (cellError) {
                            console.error(`❌ Error generating cell [${rowIndex}][${colIndex}]:`, cellError);
                            return `<td class="text-danger"><small>Error</small></td>`;
                        }
                    }).join('');
                    return `<tr>${cells}</tr>`;
                } catch (rowError) {
                    console.error(`❌ Error generating row ${rowIndex}:`, rowError);
                    return `<tr><td colspan="${effectiveColumns.length}" class="text-danger">Error rendering row</td></tr>`;
                }
            }).join('');

            console.log(`✅ Table generated successfully: ${data.length} rows, ${effectiveColumns.length} columns`);

            return `
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="table-light">
                            <tr>${headers}</tr>
                        </thead>
                        <tbody>
                            ${rows}
                        </tbody>
                    </table>
                    <div class="mt-2 text-muted">
                        <small><i class="bi bi-info-circle me-1"></i>Showing ${data.length} record(s)</small>
                    </div>
                </div>
            `;

        } catch (error) {
            console.error('❌ Critical error in generateResultsTable:', error);
            console.error('📍 Error context:', { data, columns });

            return `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Error generating table:</strong> ${this.escapeHtml(error.message)}
                    <br><small>Please check the console for detailed error information.</small>
                </div>
            `;
        }
    }

    /**
     * Handle export report
     * @param {string} format - Export format (excel, pdf, csv)
     */
    async handleExportReport(format) {
        if (!this.currentReport || !this.formData) {
            this.showError('Please generate the report first.');
            return;
        }

        try {
            // Find the clicked button
            const btn = Array.from(this.elements.exportButtons)
                .find(b => b.dataset.format === format);

            if (btn) {
                this.setButtonLoading(btn, true);
            }

            // Export report
            const { blob, filename } = await this.apiService.exportReport(
                this.currentReport.id,
                format,
                this.formData
            );

            // Download file
            this.apiService.downloadFile(blob, filename);

            this.showSuccess(`Report exported as ${format.toUpperCase()} successfully!`);

        } catch (error) {
            console.error('Export error:', error);
            this.showError(error.message || 'Failed to export report. Please try again.');
        } finally {
            const btn = Array.from(this.elements.exportButtons)
                .find(b => b.dataset.format === format);

            if (btn) {
                this.setButtonLoading(btn, false);
            }
        }
    }

    /**
     * Reset form to initial state
     */
    resetForm() {
        // Clear form container
        this.elements.formContainer.innerHTML = `
            <div class="empty-state">
                <i class="bi bi-file-earmark-text"></i>
                <p class="mb-0">${this.translations.selectReportToBegin || 'Select a report to begin'}</p>
            </div>
        `;

        // Hide elements
        this.elements.formActions.style.display = 'none';
        this.elements.resultsSection.style.display = 'none';
        this.elements.reportDescription.style.display = 'none';

        // Clear state
        this.currentReport = null;
        this.currentParameters = [];
        this.formData = {};

        // Clear dependencies
        this.dependencyHandler.clear();
    }

    /**
     * Show form loading state
     * @param {boolean} isLoading - Loading state
     */
    showFormLoading(isLoading) {
        if (!this.elements.formContainer) return;

        if (isLoading) {
            this.elements.formContainer.classList.add('loading');
        } else {
            this.elements.formContainer.classList.remove('loading');
        }
    }

    /**
     * Set button loading state
     * @param {HTMLButtonElement} button - Button element
     * @param {boolean} isLoading - Loading state
     */
    setButtonLoading(button, isLoading) {
        if (!button) return;

        if (isLoading) {
            button.disabled = true;
            button.dataset.originalHtml = button.innerHTML;
            button.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                ${this.translations.pleaseWait || 'Please wait...'}
            `;
        } else {
            button.disabled = false;
            if (button.dataset.originalHtml) {
                button.innerHTML = button.dataset.originalHtml;
                delete button.dataset.originalHtml;
            }
        }
    }

    /**
     * Show loading overlay
     * @param {HTMLElement} element - Element to show loading on
     * @param {boolean} isLoading - Loading state
     */
    showLoading(element, isLoading) {
        if (!element) return;

        const existingOverlay = element.querySelector('.loading-overlay');

        if (isLoading && !existingOverlay) {
            const overlay = document.createElement('div');
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            `;
            element.style.position = 'relative';
            element.appendChild(overlay);
        } else if (!isLoading && existingOverlay) {
            existingOverlay.remove();
        }
    }

    /**
     * Show validation errors
     * @param {Object} errors - Validation errors
     */
    showValidationErrors(errors) {
        const errorMessage = Object.values(errors).join('<br>');
        this.showError(errorMessage);
    }

    /**
     * Show error message using toastr
     * @param {string} message - Error message
     */
    showError(message) {
        if (typeof toastr !== 'undefined') {
            toastr.error(message, this.translations.error || 'Error');
        } else {
            alert(message);
        }
    }

    /**
     * Show success message using toastr
     * @param {string} message - Success message
     */
    showSuccess(message) {
        if (typeof toastr !== 'undefined') {
            toastr.success(message, this.translations.success || 'Success');
        } else {
            alert(message);
        }
    }

    /**
     * Escape HTML to prevent XSS
     * @param {string} text - Text to escape
     * @returns {string} Escaped text
     */
    escapeHtml(text) {
        if (text === null || text === undefined) return '';

        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return String(text).replace(/[&<>"']/g, (m) => map[m]);
    }
}

// Export for use in other modules
export default DynamicReportForm;
