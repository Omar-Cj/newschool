/**
 * DataTable - Responsive table component with sorting, filtering, and formatting
 * Provides accessible, keyboard-navigable data tables with Bootstrap styling
 */

class DataTable {
    constructor(options = {}) {
        this.options = {
            columns: [],
            data: [],
            pageSize: 50,
            sortable: true,
            searchable: true,
            responsive: true,
            onSort: null,
            onFilter: null,
            ...options
        };

        this.currentData = [...this.options.data];
        this.filteredData = [...this.options.data];
        this.sortColumn = null;
        this.sortDirection = 'asc';
        this.searchTerm = '';
        this.container = null;
    }

    /**
     * Render the data table
     * @param {HTMLElement} container - Container element
     */
    render(container) {
        this.container = container;
        this.container.innerHTML = '';

        // Create wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'data-table-wrapper';

        // Add search if enabled
        if (this.options.searchable) {
            const searchBox = this.createSearchBox();
            wrapper.appendChild(searchBox);
        }

        // Add table
        const tableWrapper = this.createTableWrapper();
        wrapper.appendChild(tableWrapper);

        // Add to container
        this.container.appendChild(wrapper);
    }

    /**
     * Create search box
     * @returns {HTMLElement}
     */
    createSearchBox() {
        const searchWrapper = document.createElement('div');
        searchWrapper.className = 'data-table-search mb-3';

        const searchGroup = document.createElement('div');
        searchGroup.className = 'input-group';
        searchGroup.innerHTML = `
            <span class="input-group-text">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </span>
            <input
                type="text"
                class="form-control"
                placeholder="Search in results..."
                aria-label="Search table data"
                id="dataTableSearch"
            >
        `;

        const searchInput = searchGroup.querySelector('input');
        searchInput.addEventListener('input', (e) => {
            this.handleSearch(e.target.value);
        });

        searchWrapper.appendChild(searchGroup);
        return searchWrapper;
    }

    /**
     * Create table wrapper with responsive container
     * @returns {HTMLElement}
     */
    createTableWrapper() {
        const wrapper = document.createElement('div');
        wrapper.className = 'table-responsive';

        const table = document.createElement('table');
        table.className = 'table table-hover table-bordered align-middle';
        table.setAttribute('role', 'table');

        // Create table head
        const thead = this.createTableHead();
        table.appendChild(thead);

        // Create table body
        const tbody = this.createTableBody();
        table.appendChild(tbody);

        wrapper.appendChild(table);
        return wrapper;
    }

    /**
     * Create table header
     * @returns {HTMLElement}
     */
    createTableHead() {
        const thead = document.createElement('thead');
        thead.className = 'table-light';

        const headerRow = document.createElement('tr');

        this.options.columns.forEach((column, index) => {
            const th = document.createElement('th');
            th.setAttribute('scope', 'col');
            th.className = column.className || '';

            // Make sortable if enabled
            if (this.options.sortable && column.sortable !== false) {
                th.className += ' sortable';
                th.style.cursor = 'pointer';
                th.setAttribute('role', 'button');
                th.setAttribute('tabindex', '0');
                th.setAttribute('aria-label', `${column.label}, sortable column`);

                // Sort indicator
                const sortIndicator = document.createElement('span');
                sortIndicator.className = 'sort-indicator ms-2';
                sortIndicator.setAttribute('aria-hidden', 'true');

                if (this.sortColumn === column.key) {
                    sortIndicator.innerHTML = this.sortDirection === 'asc' ? '▲' : '▼';
                    th.setAttribute('aria-sort', this.sortDirection === 'asc' ? 'ascending' : 'descending');
                } else {
                    sortIndicator.innerHTML = '⇅';
                    th.setAttribute('aria-sort', 'none');
                }

                th.innerHTML = column.label;
                th.appendChild(sortIndicator);

                // Add click handler
                th.addEventListener('click', () => this.handleSort(column.key));

                // Add keyboard handler
                th.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.handleSort(column.key);
                    }
                });
            } else {
                th.textContent = column.label;
            }

            headerRow.appendChild(th);
        });

        thead.appendChild(headerRow);
        return thead;
    }

    /**
     * Create table body
     * @returns {HTMLElement}
     */
    createTableBody() {
        const tbody = document.createElement('tbody');

        if (this.filteredData.length === 0) {
            const emptyRow = document.createElement('tr');
            const emptyCell = document.createElement('td');
            emptyCell.setAttribute('colspan', this.options.columns.length);
            emptyCell.className = 'text-center text-muted py-4';
            emptyCell.innerHTML = `
                <svg class="mb-2" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div>No matching records found</div>
            `;
            emptyRow.appendChild(emptyCell);
            tbody.appendChild(emptyRow);
            return tbody;
        }

        this.filteredData.forEach((row, rowIndex) => {
            const tr = document.createElement('tr');
            tr.setAttribute('role', 'row');

            this.options.columns.forEach((column) => {
                const td = document.createElement('td');
                td.setAttribute('role', 'cell');
                td.className = column.cellClassName || '';

                const value = row[column.key];
                const formattedValue = this.formatCellValue(value, column.type, column.format);

                td.innerHTML = formattedValue;
                tr.appendChild(td);
            });

            tbody.appendChild(tr);
        });

        return tbody;
    }

    /**
     * Format cell value based on type
     * @param {*} value - Cell value
     * @param {String} type - Data type
     * @param {Function} customFormat - Custom formatter function
     * @returns {String}
     */
    formatCellValue(value, type, customFormat) {
        // Handle null/undefined
        if (value === null || value === undefined || value === '') {
            return '<span class="text-muted">—</span>';
        }

        // Use custom formatter if provided
        if (customFormat && typeof customFormat === 'function') {
            return customFormat(value);
        }

        // Apply type-based formatting
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

            case 'badge':
                return this.formatBadge(value);

            default:
                return this.escapeHtml(String(value));
        }
    }

    /**
     * Formatting helper functions
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

    formatDate(value) {
        const date = new Date(value);
        if (isNaN(date.getTime())) return value;

        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }).format(date);
    }

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

    formatNumber(value) {
        const num = parseFloat(value);
        if (isNaN(num)) return value;

        return new Intl.NumberFormat('en-US').format(num);
    }

    formatPercentage(value) {
        const num = parseFloat(value);
        if (isNaN(num)) return value;

        return `<span class="badge bg-info">${num.toFixed(2)}%</span>`;
    }

    formatBoolean(value) {
        if (typeof value === 'boolean' || value === 'true' || value === 'false') {
            const boolVal = value === true || value === 'true';
            return boolVal
                ? '<span class="badge bg-success">Yes</span>'
                : '<span class="badge bg-secondary">No</span>';
        }
        return value;
    }

    formatStatus(value) {
        const statusMap = {
            'paid': 'success',
            'pending': 'warning',
            'overdue': 'danger',
            'active': 'success',
            'inactive': 'secondary',
            'present': 'success',
            'absent': 'danger',
            'late': 'warning',
            'excused': 'info'
        };

        const status = String(value).toLowerCase();
        const badgeClass = statusMap[status] || 'secondary';

        return `<span class="badge bg-${badgeClass}">${this.escapeHtml(value)}</span>`;
    }

    formatBadge(value) {
        return `<span class="badge bg-primary">${this.escapeHtml(value)}</span>`;
    }

    /**
     * Handle sorting
     * @param {String} columnKey - Column to sort by
     */
    handleSort(columnKey) {
        // Toggle sort direction if same column
        if (this.sortColumn === columnKey) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = columnKey;
            this.sortDirection = 'asc';
        }

        // Perform sort
        this.filteredData.sort((a, b) => {
            let aVal = a[columnKey];
            let bVal = b[columnKey];

            // Handle null/undefined
            if (aVal === null || aVal === undefined) return 1;
            if (bVal === null || bVal === undefined) return -1;

            // Convert to comparable values
            if (typeof aVal === 'string') {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }

            // Compare
            let comparison = 0;
            if (aVal > bVal) comparison = 1;
            if (aVal < bVal) comparison = -1;

            return this.sortDirection === 'asc' ? comparison : -comparison;
        });

        // Re-render
        this.render(this.container);

        // Callback
        if (this.options.onSort) {
            this.options.onSort(this.filteredData);
        }
    }

    /**
     * Handle search/filter
     * @param {String} searchTerm - Search query
     */
    handleSearch(searchTerm) {
        this.searchTerm = searchTerm.toLowerCase();

        if (!this.searchTerm) {
            this.filteredData = [...this.currentData];
        } else {
            this.filteredData = this.currentData.filter(row => {
                return this.options.columns.some(column => {
                    const value = row[column.key];
                    if (value === null || value === undefined) return false;

                    return String(value).toLowerCase().includes(this.searchTerm);
                });
            });
        }

        // Re-render
        this.render(this.container);

        // Callback
        if (this.options.onFilter) {
            this.options.onFilter(this.filteredData);
        }
    }

    /**
     * Update table data
     * @param {Array} newData - New data array
     */
    updateData(newData) {
        this.currentData = [...newData];
        this.filteredData = [...newData];

        // Reapply search if active
        if (this.searchTerm) {
            this.handleSearch(this.searchTerm);
        } else {
            this.render(this.container);
        }
    }

    /**
     * Utility: Escape HTML
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

    /**
     * Public API
     */
    destroy() {
        if (this.container) {
            this.container.innerHTML = '';
        }
        this.currentData = [];
        this.filteredData = [];
    }

    getData() {
        return this.filteredData;
    }

    getColumns() {
        return this.options.columns;
    }
}

export default DataTable;
