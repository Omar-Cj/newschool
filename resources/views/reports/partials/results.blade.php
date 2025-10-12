{{--
    Report Results Display Partial
    Handles the display of report results, loading states, and error handling
--}}

<div class="report-results-container">
    {{-- Export Buttons --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Report Results</h4>
            <p class="text-muted mb-0 small">
                <span id="resultCount">Loading...</span>
            </p>
        </div>
        <div id="exportButtons" class="export-buttons-container"></div>
    </div>

    {{-- Results Display Area --}}
    <div id="reportResultsDisplay" class="results-display">
        {{-- Loading State (Initial) --}}
        <div id="loadingState" class="loading-state text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading report results...</span>
            </div>
            <h5 class="text-muted mb-2">Generating Report</h5>
            <p class="text-muted mb-0">Please wait while we fetch your data...</p>
            <div class="progress mt-3" style="max-width: 300px; margin: 0 auto;">
                <div class="progress-bar progress-bar-striped progress-bar-animated"
                     role="progressbar"
                     aria-valuenow="100"
                     aria-valuemin="0"
                     aria-valuemax="100"
                     style="width: 100%">
                </div>
            </div>
        </div>

        {{-- Empty State Template (Hidden by default) --}}
        <div id="emptyState" class="empty-state text-center py-5" style="display: none;">
            <svg class="mb-3 text-muted" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="12" y1="18" x2="12" y2="12"></line>
                <line x1="9" y1="15" x2="15" y2="15"></line>
            </svg>
            <h4 class="text-muted mb-2">No Results Found</h4>
            <p class="text-muted mb-3">No data matches the selected criteria.</p>
            <button type="button" class="btn btn-outline-primary" onclick="window.location.reload()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                Refresh
            </button>
        </div>

        {{-- Error State Template (Hidden by default) --}}
        <div id="errorState" class="error-state" style="display: none;">
            <div class="alert alert-danger d-flex align-items-start" role="alert">
                <svg class="me-3 flex-shrink-0" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-2">Error Loading Report</h5>
                    <p class="mb-2" id="errorMessage">An unexpected error occurred while generating the report.</p>
                    <hr>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-danger" onclick="window.location.reload()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <polyline points="1 20 1 14 7 14"></polyline>
                                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                            </svg>
                            Retry
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="history.back()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                <line x1="19" y1="12" x2="5" y2="12"></line>
                                <polyline points="12 19 5 12 12 5"></polyline>
                            </svg>
                            Go Back
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Results Container (Initially empty, populated by JavaScript) --}}
        <div id="reportTableContainer" class="report-table-container" style="display: none;"></div>
    </div>

    {{-- Print Button --}}
    <div class="print-actions mt-4 d-print-none" id="printActions" style="display: none;">
        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            Print Report
        </button>
    </div>
</div>

{{-- Print Styles --}}
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reports-print.css') }}" media="print">
@endpush

{{-- Scripts --}}
@push('scripts')
<script type="module">
    import ReportViewer from '{{ asset('js/components/ReportViewer.js') }}';
    import ExportButtons from '{{ asset('js/components/ExportButtons.js') }}';

    // Initialize components when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Report Viewer
        const reportViewer = new ReportViewer('reportTableContainer', {
            showPagination: true,
            showExport: true,
            pageSize: 50,
            onPageChange: (page) => {
                console.log('Page changed to:', page);
                // Implement server-side pagination if needed
            }
        });

        // Initialize Export Buttons
        const exportButtons = new ExportButtons('exportButtons', {
            reportId: {{ $reportId ?? 'null' }},
            formats: ['excel', 'pdf', 'csv'],
            apiEndpoint: '{{ route('reports.export') }}',
            onExportStart: (format) => {
                console.log('Export started:', format);
            },
            onExportComplete: (format, filename) => {
                console.log('Export completed:', format, filename);
            },
            onExportError: (error, format) => {
                console.error('Export error:', error, format);
            }
        });

        // Expose to window for external access
        window.reportViewer = reportViewer;
        window.exportButtons = exportButtons;

        // Auto-load report if data is available
        @if(isset($reportData))
            loadReportData({!! json_encode($reportData) !!});
        @endif
    });

    /**
     * Load and display report data
     * @param {Object} data - Report data from API
     */
    function loadReportData(data) {
        const loadingState = document.getElementById('loadingState');
        const emptyState = document.getElementById('emptyState');
        const errorState = document.getElementById('errorState');
        const tableContainer = document.getElementById('reportTableContainer');
        const printActions = document.getElementById('printActions');
        const resultCount = document.getElementById('resultCount');

        // Hide loading
        if (loadingState) loadingState.style.display = 'none';

        // Check for errors
        if (!data.success) {
            if (errorState) {
                errorState.style.display = 'block';
                const errorMessage = errorState.querySelector('#errorMessage');
                if (errorMessage) {
                    errorMessage.textContent = data.message || 'An unexpected error occurred.';
                }
            }
            return;
        }

        // Check for empty results
        if (!data.results || data.results.length === 0) {
            if (emptyState) emptyState.style.display = 'block';
            return;
        }

        // Display results
        if (tableContainer) {
            tableContainer.style.display = 'block';
            window.reportViewer.render(data, data.report?.type || 'tabular');
        }

        // Update result count
        if (resultCount && data.meta) {
            resultCount.textContent = `${data.meta.total_rows} records found`;
        }

        // Show print button
        if (printActions) printActions.style.display = 'block';

        // Set export data
        if (window.exportButtons) {
            window.exportButtons.setExportData({
                report_id: data.report?.id,
                filters: data.report?.filters
            });
        }
    }

    // Expose loadReportData globally
    window.loadReportData = loadReportData;
</script>
@endpush

{{-- Accessibility enhancements --}}
<div class="visually-hidden" role="status" aria-live="polite" aria-atomic="true" id="reportStatus">
    Loading report...
</div>
