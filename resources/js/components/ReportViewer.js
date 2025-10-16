/**
 * ReportViewer - Main component for rendering report results
 * Handles different report types (tabular, chart, summary)
 */

import DataTable from './DataTable.js';

class ReportViewer {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        if (!this.container) {
            throw new Error(`Container with ID '${containerId}' not found`);
        }

        this.options = {
            showPagination: true,
            showExport: true,
            pageSize: 50,
            ...options
        };

        this.currentData = null;
        this.currentReportType = null;
        this.dataTable = null;
    }

    /**
     * Main rendering function - routes to appropriate renderer based on report type
     * @param {Object} data - API response data
     * @param {String} reportType - Type of report (tabular, chart, summary)
     */
    render(data, reportType = 'tabular') {
        this.currentData = data;
        this.currentReportType = reportType;

        // Clear previous content
        this.container.innerHTML = '';

        // Handle error state
        if (!data.success) {
            this.renderError(data.message || 'Failed to generate report');
            return;
        }

        // Handle empty results
        if (!data.results || data.results.length === 0) {
            this.renderEmptyState();
            return;
        }

        // Route to appropriate renderer
        switch (reportType) {
            case 'tabular':
                this.renderTable(data);
                break;
            case 'chart':
                this.renderChart(data);
                break;
            case 'summary':
                this.renderSummary(data);
                break;
            default:
                this.renderTable(data);
        }
    }

    /**
     * Render results as a responsive data table
     * @param {Object} data - Report data with results and columns
     */
    renderTable(data) {
        const { results, columns, meta, report } = data;

        // Create wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'report-viewer-wrapper';

        // Add report header
        const header = this.createReportHeader(report);
        wrapper.appendChild(header);

        // Initialize DataTable component
        this.dataTable = new DataTable({
            columns: columns,
            data: results,
            pageSize: this.options.pageSize,
            sortable: true,
            searchable: true,
            onSort: (sortedData) => this.handleSort(sortedData),
            onFilter: (filteredData) => this.handleFilter(filteredData)
        });

        // Render table
        const tableContainer = document.createElement('div');
        tableContainer.className = 'table-container mb-4';
        this.dataTable.render(tableContainer);
        wrapper.appendChild(tableContainer);

        // Add pagination if enabled
        if (this.options.showPagination && meta) {
            const pagination = this.renderPagination(meta);
            wrapper.appendChild(pagination);
        }

        // Summary tables hidden in web view
        // They will appear only in print and PDF exports via print-wrapper.blade.php and pdf/template.blade.php
        // if (data.data && data.data.summary) {
        //     const summarySection = this.renderSummaryTables(data.data.summary);
        //     if (summarySection) {
        //         wrapper.appendChild(summarySection);
        //     }
        // }

        // Add to container
        this.container.appendChild(wrapper);
    }

    /**
     * Create report header with title and metadata
     * @param {Object} report - Report information
     * @returns {HTMLElement}
     */
    createReportHeader(report) {
        const header = document.createElement('div');
        header.className = 'report-header d-flex justify-content-between align-items-center mb-4';
        header.innerHTML = `
            <div>
                <h3 class="mb-1">${this.escapeHtml(report.name)}</h3>
                ${report.description ? `<p class="text-muted mb-0">${this.escapeHtml(report.description)}</p>` : ''}
            </div>
            <div class="report-meta text-end">
                <small class="text-muted d-block">Generated: ${this.formatDateTime(new Date())}</small>
                ${report.filters ? `<small class="text-muted d-block">Filters Applied: ${this.formatFilters(report.filters)}</small>` : ''}
            </div>
        `;
        return header;
    }

    /**
     * Render pagination controls
     * @param {Object} meta - Pagination metadata
     * @returns {HTMLElement}
     */
    renderPagination(meta) {
        const paginationWrapper = document.createElement('div');
        paginationWrapper.className = 'pagination-wrapper d-flex justify-content-between align-items-center';

        // Results info
        const info = document.createElement('div');
        info.className = 'pagination-info text-muted';
        const startRecord = ((meta.page - 1) * meta.per_page) + 1;
        const endRecord = Math.min(meta.page * meta.per_page, meta.total_rows);
        info.innerHTML = `Showing ${startRecord} to ${endRecord} of ${meta.total_rows} records`;

        // Pagination controls
        const controls = document.createElement('nav');
        controls.setAttribute('aria-label', 'Report pagination');

        const totalPages = Math.ceil(meta.total_rows / meta.per_page);
        const currentPage = meta.page;

        let paginationHTML = '<ul class="pagination mb-0">';

        // Previous button
        paginationHTML += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `;

        // Page numbers (show max 5 pages)
        const maxPages = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
        let endPage = Math.min(totalPages, startPage + maxPages - 1);

        if (endPage - startPage < maxPages - 1) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }

        // First page
        if (startPage > 1) {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (startPage > 2) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        // Last page
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
        }

        // Next button
        paginationHTML += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `;

        paginationHTML += '</ul>';
        controls.innerHTML = paginationHTML;

        // Add event listeners
        controls.querySelectorAll('a.page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(link.dataset.page);
                if (page && page !== currentPage) {
                    this.handlePageChange(page);
                }
            });
        });

        paginationWrapper.appendChild(info);
        paginationWrapper.appendChild(controls);

        return paginationWrapper;
    }

    /**
     * Apply formatting based on column type
     * @param {*} value - Value to format
     * @param {String} type - Data type (currency, date, number, percentage, string)
     * @returns {String} Formatted value
     */
    applyFormatting(value, type) {
        // Handle null/undefined
        if (value === null || value === undefined || value === '') {
            return '<span class="text-muted">—</span>';
        }

        switch (type) {
            case 'currency':
                return this.formatCurrency(value);

            case 'date':
                return this.formatDate(value);

            case 'datetime':
                return this.formatDateTime(value);

            case 'number':
                return this.formatNumber(value);

            case 'percentage':
                return this.formatPercentage(value);

            case 'boolean':
                return this.formatBoolean(value);

            case 'status':
                return this.formatStatus(value);

            default:
                return this.escapeHtml(String(value));
        }
    }

    /**
     * Format currency values
     */
    formatCurrency(value) {
        const num = parseFloat(value);
        if (isNaN(num)) return value;

        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            minimumFractionDigits: 2
        }).format(num);
    }

    /**
     * Format date values
     */
    formatDate(value) {
        const date = new Date(value);
        if (isNaN(date.getTime())) return value;

        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }).format(date);
    }

    /**
     * Format datetime values
     */
    formatDateTime(value) {
        const date = new Date(value);
        if (isNaN(date.getTime())) return value;

        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(date);
    }

    /**
     * Format number values
     */
    formatNumber(value) {
        const num = parseFloat(value);
        if (isNaN(num)) return value;

        return new Intl.NumberFormat('en-US').format(num);
    }

    /**
     * Format percentage values
     */
    formatPercentage(value) {
        const num = parseFloat(value);
        if (isNaN(num)) return value;

        return `${num.toFixed(2)}%`;
    }

    /**
     * Format boolean values
     */
    formatBoolean(value) {
        if (typeof value === 'boolean') {
            return value
                ? '<span class="badge bg-success">Yes</span>'
                : '<span class="badge bg-secondary">No</span>';
        }
        return value;
    }

    /**
     * Format status values with badges
     */
    formatStatus(value) {
        const statusMap = {
            'paid': 'success',
            'pending': 'warning',
            'overdue': 'danger',
            'active': 'success',
            'inactive': 'secondary',
            'present': 'success',
            'absent': 'danger',
            'late': 'warning'
        };

        const status = String(value).toLowerCase();
        const badgeClass = statusMap[status] || 'secondary';

        return `<span class="badge bg-${badgeClass}">${this.escapeHtml(value)}</span>`;
    }

    /**
     * Render empty state message
     */
    renderEmptyState() {
        this.container.innerHTML = `
            <div class="empty-state text-center py-5">
                <svg class="mb-3" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="12" y1="18" x2="12" y2="12"></line>
                    <line x1="9" y1="15" x2="15" y2="15"></line>
                </svg>
                <h4 class="text-muted">No Results Found</h4>
                <p class="text-muted mb-0">No data matches the selected criteria. Try adjusting your filters.</p>
            </div>
        `;
    }

    /**
     * Render error state
     */
    renderError(message) {
        this.container.innerHTML = `
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <svg class="me-2" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div>
                    <strong>Error</strong>
                    <p class="mb-0">${this.escapeHtml(message)}</p>
                </div>
            </div>
        `;
    }

    /**
     * Show loading state
     */
    showLoading() {
        this.container.innerHTML = `
            <div class="loading-state text-center py-5">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="text-muted">Generating Report...</h5>
                <p class="text-muted mb-0">Please wait while we fetch your data.</p>
            </div>
        `;
    }

    /**
     * Render chart (placeholder for future chart implementation)
     */
    renderChart(data) {
        // TODO: Implement chart rendering using Chart.js or similar
        this.container.innerHTML = `
            <div class="alert alert-info">
                Chart rendering coming soon. For now, view data in table format.
            </div>
        `;
    }

    /**
     * Render summary view
     */
    renderSummary(data) {
        const { results, report } = data;

        const wrapper = document.createElement('div');
        wrapper.className = 'summary-view';

        // Add header
        wrapper.appendChild(this.createReportHeader(report));

        // Create summary cards
        const summaryGrid = document.createElement('div');
        summaryGrid.className = 'row g-3';

        results.forEach(item => {
            const card = document.createElement('div');
            card.className = 'col-md-6 col-lg-4';
            card.innerHTML = `
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">${this.escapeHtml(item.label || item.name)}</h5>
                        <h2 class="mb-0">${this.applyFormatting(item.value, item.type || 'number')}</h2>
                    </div>
                </div>
            `;
            summaryGrid.appendChild(card);
        });

        wrapper.appendChild(summaryGrid);
        this.container.appendChild(wrapper);
    }

    /**
     * Event handlers
     */
    handlePageChange(page) {
        if (this.options.onPageChange) {
            this.options.onPageChange(page);
        }
    }

    handleSort(sortedData) {
        // Re-render with sorted data
        if (this.dataTable) {
            this.dataTable.updateData(sortedData);
        }
    }

    handleFilter(filteredData) {
        // Re-render with filtered data
        if (this.dataTable) {
            this.dataTable.updateData(filteredData);
        }
    }

    /**
     * Utility functions
     */
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    formatFilters(filters) {
        return Object.entries(filters)
            .map(([key, value]) => `${key}: ${value}`)
            .join(', ');
    }

    /**
     * Render summary tables for Fee Generation & Collection and other report types
     * @param {Object} summary - Summary data structure
     * @returns {HTMLElement|null}
     */
    renderSummaryTables(summary) {
        if (!summary || !summary.type) {
            return null;
        }

        const container = document.createElement('div');
        container.className = 'summary-container mt-4 mb-4';

        // Handle Fee Generation & Collection Summary (three-column layout)
        if (summary.type === 'fee_generation_collection' && summary.sections) {
            const title = document.createElement('h5');
            title.className = 'mb-3 text-center fw-bold';
            title.textContent = 'Summary Report';
            container.appendChild(title);

            const row = document.createElement('div');
            row.className = 'row';

            summary.sections.forEach(section => {
                const col = document.createElement('div');
                col.className = 'col-md-4 mb-3';

                const card = document.createElement('div');
                card.className = 'card border';

                const cardHeader = document.createElement('div');
                cardHeader.className = 'card-header bg-light text-center py-2';
                cardHeader.innerHTML = `<h6 class="mb-0 fw-bold">${this.escapeHtml(section.title)}</h6>`;
                card.appendChild(cardHeader);

                const cardBody = document.createElement('div');
                cardBody.className = 'card-body p-0';

                const table = document.createElement('table');
                table.className = 'table table-bordered mb-0';

                const tbody = document.createElement('tbody');
                section.rows.forEach(row => {
                    const tr = document.createElement('tr');
                    if (row.is_total) {
                        tr.className = 'table-active fw-bold';
                    }
                    tr.innerHTML = `
                        <td class="py-2 px-3" style="width: 60%;">${this.escapeHtml(row.label)}</td>
                        <td class="text-end py-2 px-3" style="width: 40%;">$${this.formatNumber(row.value)}</td>
                    `;
                    tbody.appendChild(tr);
                });
                table.appendChild(tbody);
                cardBody.appendChild(table);
                card.appendChild(cardBody);

                col.appendChild(card);
                row.appendChild(col);
            });

            container.appendChild(row);
            return container;
        }

        // Handle Financial Summary (Paid Students, Fee Generation)
        if (summary.type === 'financial' && summary.rows) {
            const title = document.createElement('h5');
            title.className = 'mb-3';
            title.textContent = 'Financial Summary';
            container.appendChild(title);

            const row = document.createElement('div');
            row.className = 'row';
            const col = document.createElement('div');
            col.className = 'col-md-6 offset-md-6';

            const table = document.createElement('table');
            table.className = 'table table-bordered';
            const tbody = document.createElement('tbody');

            summary.rows.forEach(row => {
                const isGrandTotal = row.metric === 'Grand Total' || row.metric === 'Total Invoices';
                const tr = document.createElement('tr');
                if (isGrandTotal) {
                    tr.className = 'table-success fw-bold';
                }
                tr.innerHTML = `
                    <th class="text-end py-2 px-3" style="width: 50%;">${this.escapeHtml(row.metric)}:</th>
                    <td class="text-end py-2 px-3" style="width: 50%;">$${this.formatNumber(row.value)}</td>
                `;
                tbody.appendChild(tr);
            });

            table.appendChild(tbody);
            col.appendChild(table);
            row.appendChild(col);
            container.appendChild(row);
            return container;
        }

        // Handle Exam Gradebook Summary
        if (summary.type === 'exam_gradebook' && summary.rows) {
            const title = document.createElement('h5');
            title.className = 'mb-3';
            title.textContent = 'Summary - Total Marks by Exam';
            container.appendChild(title);

            const row = document.createElement('div');
            row.className = 'row';
            const col = document.createElement('div');
            col.className = 'col-md-6 offset-md-6';

            const table = document.createElement('table');
            table.className = 'table table-bordered';

            const thead = document.createElement('thead');
            thead.className = 'table-light';
            thead.innerHTML = `
                <tr>
                    <th>Exam Name</th>
                    <th class="text-end">Total Marks</th>
                </tr>
            `;
            table.appendChild(thead);

            const tbody = document.createElement('tbody');
            summary.rows.forEach(row => {
                const isTotalRow = row.exam_name === 'Total All Exams';
                const tr = document.createElement('tr');
                if (isTotalRow) {
                    tr.className = 'table-success fw-bold';
                }
                tr.innerHTML = `
                    <td>${this.escapeHtml(row.exam_name || '-')}</td>
                    <td class="text-end">${this.formatNumber(row.total_marks || 0)}</td>
                `;
                tbody.appendChild(tr);
            });
            table.appendChild(tbody);

            col.appendChild(table);
            row.appendChild(col);
            container.appendChild(row);
            return container;
        }

        return null;
    }

    /**
     * Public API
     */
    destroy() {
        if (this.dataTable) {
            this.dataTable.destroy();
        }
        this.container.innerHTML = '';
        this.currentData = null;
        this.currentReportType = null;
    }

    getData() {
        return this.currentData;
    }

    getReportType() {
        return this.currentReportType;
    }
}

export default ReportViewer;
