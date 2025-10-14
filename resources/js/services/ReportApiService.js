/**
 * Report API Service
 * Handles all API communications for the dynamic report system
 */

export class ReportApiService {
    constructor() {
        this.baseUrl = window.ReportConfig?.apiBaseUrl || '/api/reports';
        this.csrfToken = window.ReportConfig?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content;
    }

    /**
     * Fetch all reports grouped by category
     * @returns {Promise<Object>} Reports data with categories
     */
    async fetchReports() {
        try {
            const response = await fetch(`${this.baseUrl}`, {
                method: 'GET',
                headers: this._getHeaders()
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching reports:', error);
            throw new Error('Failed to load reports. Please try again.');
        }
    }

    /**
     * Fetch parameters for a specific report
     * @param {number} reportId - The report ID
     * @returns {Promise<Object>} Report parameters and metadata
     */
    async fetchParameters(reportId) {
        try {
            const response = await fetch(`${this.baseUrl}/${reportId}/parameters`, {
                method: 'GET',
                headers: this._getHeaders()
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error fetching parameters:', error);
            throw new Error('Failed to load report parameters. Please try again.');
        }
    }

    /**
     * Fetch dependent values for cascading dropdowns
     * @param {number} parameterId - The parameter ID
     * @param {string|number} parentValue - The parent parameter value
     * @returns {Promise<Array>} Array of dependent values
     */
    async fetchDependentValues(parameterId, parentValue) {
        console.log('🔄 fetchDependentValues called', {
            parameterId,
            parentValue,
            parentValueType: typeof parentValue
        });

        try {
            const url = `${this.baseUrl}/parameters/${parameterId}/dependent-values?parent_value=${encodeURIComponent(parentValue)}`;
            console.log('🔄 Fetching from URL:', url);

            const response = await fetch(url, {
                method: 'GET',
                headers: this._getHeaders()
            });

            console.log('🔄 Response status:', response.status, response.statusText);

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                console.error('❌ API error response:', errorData);
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log('✅ Dependent values received:', data);
            console.log('✅ Values count:', data.values?.length || 0);
            console.log('✅ Sample values:', data.values?.slice(0, 3));

            return data.values || [];
        } catch (error) {
            console.error('❌ Error fetching dependent values:', error);
            console.error('❌ Stack trace:', error.stack);
            throw new Error('Failed to load dependent options. Please try again.');
        }
    }

    /**
     * Execute report with provided parameters
     * @param {number} reportId - The report ID
     * @param {Object} formData - Form data with parameter values
     * @returns {Promise<Object>} Report execution results
     */
    async executeReport(reportId, formData) {
        try {
            const response = await fetch(`${this.baseUrl}/${reportId}/execute`, {
                method: 'POST',
                headers: this._getHeaders(),
                body: JSON.stringify({ parameters: formData })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error executing report:', error);
            throw error;
        }
    }

    /**
     * Export report in specified format
     * @param {number} reportId - The report ID
     * @param {string} format - Export format (excel, pdf, csv)
     * @param {Object} formData - Form data with parameter values
     * @returns {Promise<Blob>} File blob for download
     */
    async exportReport(reportId, format, formData) {
        try {
            const response = await fetch(`${this.baseUrl}/${reportId}/export/${format}`, {
                method: 'POST',
                headers: this._getHeaders(),
                body: JSON.stringify({ parameters: formData })
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            // Get filename from Content-Disposition header
            const contentDisposition = response.headers.get('Content-Disposition');
            let filename = `report_${Date.now()}.${format}`;

            if (contentDisposition) {
                const filenameMatch = contentDisposition.match(/filename="?(.+)"?/);
                if (filenameMatch && filenameMatch[1]) {
                    filename = filenameMatch[1];
                }
            }

            const blob = await response.blob();
            return { blob, filename };
        } catch (error) {
            console.error('Error exporting report:', error);
            throw error;
        }
    }

    /**
     * Print report - returns HTML for browser printing
     * @param {number} reportId - The report ID
     * @param {Object} formData - Form data with parameter values
     * @returns {Promise<string>} HTML content for printing
     */
    async printReport(reportId, formData) {
        try {
            const response = await fetch(`${this.baseUrl}/${reportId}/print`, {
                method: 'POST',
                headers: this._getHeaders(),
                body: JSON.stringify({ parameters: formData }),
                credentials: 'same-origin' // Send session cookies for authentication
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            // Return HTML content
            const htmlContent = await response.text();
            return htmlContent;
        } catch (error) {
            console.error('Error printing report:', error);
            throw error;
        }
    }

    /**
     * Get common headers for API requests
     * @private
     * @returns {Object} Request headers
     */
    _getHeaders() {
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': this.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        };
    }

    /**
     * Download blob as file
     * @param {Blob} blob - File blob
     * @param {string} filename - Filename for download
     */
    downloadFile(blob, filename) {
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    }
}

// Export singleton instance
export default new ReportApiService();
