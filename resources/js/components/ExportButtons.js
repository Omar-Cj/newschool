/**
 * ExportButtons - Handle data export functionality (Excel, PDF, CSV)
 * Manages export requests, file downloads, and progress indication
 */

class ExportButtons {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            throw new Error(`Container with ID '${containerId}' not found`);
        }

        this.options = {
            reportId: null,
            formats: ['excel', 'pdf', 'csv'],
            apiEndpoint: '/api/reports/export',
            onExportStart: null,
            onExportComplete: null,
            onExportError: null,
            ...options
        };

        this.isExporting = false;
        this.currentFormat = null;
        this.exportData = null;

        this.render();
    }

    /**
     * Render export buttons
     */
    render() {
        this.container.innerHTML = '';

        const buttonGroup = document.createElement('div');
        buttonGroup.className = 'btn-group export-buttons';
        buttonGroup.setAttribute('role', 'group');
        buttonGroup.setAttribute('aria-label', 'Export options');

        // Export dropdown button
        const dropdownHTML = `
            <button
                type="button"
                class="btn btn-outline-primary dropdown-toggle"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                aria-label="Export report"
                ${this.isExporting ? 'disabled' : ''}
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                ${this.isExporting ? 'Exporting...' : 'Export'}
            </button>
            <ul class="dropdown-menu">
                ${this.renderFormatOptions()}
            </ul>
        `;

        buttonGroup.innerHTML = dropdownHTML;

        // Add event listeners
        this.attachEventListeners(buttonGroup);

        this.container.appendChild(buttonGroup);
    }

    /**
     * Render format options in dropdown
     * @returns {String} HTML string
     */
    renderFormatOptions() {
        const formatConfig = {
            excel: {
                label: 'Excel (.xlsx)',
                icon: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <path d="M9 15l6-6"></path>
                    <path d="M15 15l-6-6"></path>
                </svg>`,
                description: 'Spreadsheet with formulas'
            },
            pdf: {
                label: 'PDF Document',
                icon: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>`,
                description: 'Formatted print-ready document'
            },
            csv: {
                label: 'CSV File',
                icon: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>`,
                description: 'Raw data for import'
            }
        };

        return this.options.formats
            .map(format => {
                const config = formatConfig[format];
                if (!config) return '';

                return `
                    <li>
                        <a class="dropdown-item export-format" href="#" data-format="${format}">
                            <div class="d-flex align-items-start">
                                <div class="me-2">${config.icon}</div>
                                <div>
                                    <div class="fw-semibold">${config.label}</div>
                                    <small class="text-muted">${config.description}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                `;
            })
            .join('');
    }

    /**
     * Attach event listeners to export buttons
     * @param {HTMLElement} buttonGroup
     */
    attachEventListeners(buttonGroup) {
        const formatLinks = buttonGroup.querySelectorAll('.export-format');

        formatLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const format = link.dataset.format;
                this.handleExport(format);
            });
        });
    }

    /**
     * Handle export request
     * @param {String} format - Export format (excel, pdf, csv)
     */
    async handleExport(format) {
        if (this.isExporting) {
            return;
        }

        this.isExporting = true;
        this.currentFormat = format;
        this.updateButtonState();

        // Callback
        if (this.options.onExportStart) {
            this.options.onExportStart(format);
        }

        try {
            // Build export URL
            const url = this.buildExportUrl(format);

            // Make export request
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(this.exportData || {})
            });

            if (!response.ok) {
                throw new Error(`Export failed with status: ${response.status}`);
            }

            // Check if response is JSON (error) or blob (file)
            const contentType = response.headers.get('content-type');

            if (contentType && contentType.includes('application/json')) {
                const errorData = await response.json();
                throw new Error(errorData.message || 'Export failed');
            }

            // Handle file download
            const blob = await response.blob();
            const filename = this.getFilenameFromResponse(response) || `report.${this.getFileExtension(format)}`;

            this.downloadFile(blob, filename);

            // Success callback
            if (this.options.onExportComplete) {
                this.options.onExportComplete(format, filename);
            }

            this.showSuccessMessage(format);

        } catch (error) {
            console.error('Export error:', error);

            // Error callback
            if (this.options.onExportError) {
                this.options.onExportError(error, format);
            }

            this.showErrorMessage(error.message);

        } finally {
            this.isExporting = false;
            this.currentFormat = null;
            this.updateButtonState();
        }
    }

    /**
     * Build export URL with parameters
     * @param {String} format
     * @returns {String}
     */
    buildExportUrl(format) {
        const baseUrl = this.options.apiEndpoint;
        const params = new URLSearchParams({
            format: format,
            report_id: this.options.reportId || '',
        });

        return `${baseUrl}?${params.toString()}`;
    }

    /**
     * Get file extension for format
     * @param {String} format
     * @returns {String}
     */
    getFileExtension(format) {
        const extensions = {
            excel: 'xlsx',
            pdf: 'pdf',
            csv: 'csv'
        };
        return extensions[format] || 'dat';
    }

    /**
     * Extract filename from response headers
     * @param {Response} response
     * @returns {String|null}
     */
    getFilenameFromResponse(response) {
        const disposition = response.headers.get('content-disposition');
        if (!disposition) return null;

        const filenameMatch = disposition.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
        if (filenameMatch && filenameMatch[1]) {
            return filenameMatch[1].replace(/['"]/g, '');
        }

        return null;
    }

    /**
     * Download file blob
     * @param {Blob} blob
     * @param {String} filename
     */
    downloadFile(blob, filename) {
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        link.style.display = 'none';

        document.body.appendChild(link);
        link.click();

        // Cleanup
        setTimeout(() => {
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        }, 100);
    }

    /**
     * Update button state (disabled/enabled)
     */
    updateButtonState() {
        const button = this.container.querySelector('.dropdown-toggle');
        if (button) {
            button.disabled = this.isExporting;
            button.innerHTML = this.isExporting
                ? `
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Exporting...
                `
                : `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Export
                `;
        }
    }

    /**
     * Show success message
     * @param {String} format
     */
    showSuccessMessage(format) {
        this.showToast('success', `Report exported successfully as ${format.toUpperCase()}`);
    }

    /**
     * Show error message
     * @param {String} message
     */
    showErrorMessage(message) {
        this.showToast('error', `Export failed: ${message}`);
    }

    /**
     * Show toast notification
     * @param {String} type - 'success' or 'error'
     * @param {String} message
     */
    showToast(type, message) {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('export-toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'export-toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        // Create toast
        const toastId = `toast-${Date.now()}`;
        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
        const icon = type === 'success'
            ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>'
            : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';

        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center">
                        ${icon}
                        <span class="ms-2">${message}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        // Show toast
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        toast.show();

        // Remove toast element after hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    /**
     * Set export data (filters, parameters, etc.)
     * @param {Object} data
     */
    setExportData(data) {
        this.exportData = data;
    }

    /**
     * Set report ID
     * @param {Number} reportId
     */
    setReportId(reportId) {
        this.options.reportId = reportId;
    }

    /**
     * Enable/disable export buttons
     * @param {Boolean} enabled
     */
    setEnabled(enabled) {
        const button = this.container.querySelector('.dropdown-toggle');
        if (button) {
            button.disabled = !enabled;
        }
    }

    /**
     * Public API
     */
    destroy() {
        this.container.innerHTML = '';
        this.exportData = null;
    }

    getState() {
        return {
            isExporting: this.isExporting,
            currentFormat: this.currentFormat
        };
    }
}

export default ExportButtons;
