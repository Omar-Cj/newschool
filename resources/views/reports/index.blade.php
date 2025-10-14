@extends('backend.master')

@section('title')
    {{ ___('reports.dynamic_reports') }}
@endsection

@section('css')
<style>
    .report-category-tabs {
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 1.5rem;
    }

    .report-category-tabs .nav-link {
        color: #6c757d;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .report-category-tabs .nav-link:hover {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
    }

    .report-category-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
        background: transparent;
    }

    .dynamic-form-container {
        min-height: 200px;
        transition: opacity 0.3s ease;
    }

    .dynamic-form-container.loading {
        opacity: 0.5;
        pointer-events: none;
    }

    .parameter-field {
        margin-bottom: 1rem;
    }

    .parameter-field label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }

    .parameter-field label .required-indicator {
        color: #dc3545;
        margin-left: 0.25rem;
    }

    .parameter-field .form-control,
    .parameter-field .form-select {
        border-radius: 0.375rem;
        border: 1px solid #dee2e6;
        padding: 0.625rem 0.875rem;
        font-size: 0.9375rem;
    }

    .parameter-field .form-control:focus,
    .parameter-field .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
    }

    .parameter-field .invalid-feedback {
        display: block;
        margin-top: 0.25rem;
    }

    .parameter-field.has-error .form-control,
    .parameter-field.has-error .form-select {
        border-color: #dc3545;
    }

    .dependent-field {
        position: relative;
    }

    .dependent-field .spinner-border {
        position: absolute;
        right: 2.5rem;
        top: 50%;
        transform: translateY(-50%);
        width: 1rem;
        height: 1rem;
    }

    .results-container {
        margin-top: 2rem;
    }

    .results-actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .export-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 3rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        border-radius: 0.5rem;
    }

    .report-info-badge {
        background: #f8f9fa;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        color: #495057;
    }
</style>
@endsection

@section('content')
<div class="page-content">

    {{-- Breadcrumb Area Start --}}
    <div class="page-header">
        <div class="row">
            <div class="col-sm-6">
                <h4 class="bradecrumb-title mb-1">{{ ___('reports.dynamic_reports') }}</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ ___('common.home') }}</a></li>
                    <li class="breadcrumb-item">{{ ___('reports.reports') }}</li>
                    <li class="breadcrumb-item active">{{ ___('reports.dynamic_reports') }}</li>
                </ol>
            </div>
        </div>
    </div>
    {{-- Breadcrumb Area End --}}

    {{-- Report Categories Navigation --}}
    <div class="row">
        <div class="col-12">
            <div class="card ot-card mb-24">
                <div class="card-body">
                    <ul class="nav report-category-tabs" id="reportCategoryTabs" role="tablist">
                        <!-- Categories will be dynamically loaded here -->
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Selection and Form --}}
    <div class="row">
        <div class="col-12">
            <div class="card ot-card mb-24 position-relative">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <h3 class="mb-0">{{ ___('reports.select_report') }}</h3>
                    <div class="card_header_right d-flex align-items-center gap-3 flex-wrap">
                        <div class="single_large_selectBox" style="min-width: 300px;">
                            <select id="reportSelector" class="form-select">
                                <option value="">{{ ___('reports.select_a_report') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Report Description --}}
                    <div id="reportDescription" class="report-info-badge mb-3" style="display: none;">
                        <i class="bi bi-info-circle me-2"></i>
                        <span id="reportDescriptionText"></span>
                    </div>

                    {{-- Dynamic Form Container --}}
                    <div id="dynamicFormContainer" class="dynamic-form-container">
                        <div class="empty-state">
                            <i class="bi bi-file-earmark-text"></i>
                            <p class="mb-0">{{ ___('reports.select_report_to_begin') }}</p>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div id="formActions" class="d-flex gap-2 mt-4" style="display: none !important;">
                        <button type="button" id="generateReportBtn" class="btn btn-lg ot-btn-primary">
                            <i class="bi bi-play-circle me-2"></i>
                            {{ ___('reports.generate_report') }}
                        </button>
                        <button type="button" id="resetFormBtn" class="btn btn-lg btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            {{ ___('common.reset') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Results Container --}}
    <div class="row" id="resultsSection" style="display: none;">
        <div class="col-12">
            <div class="card ot-card mb-24 position-relative">
                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <h3 class="mb-0">{{ ___('reports.report_results') }}</h3>
                    <div class="results-actions">
                        <div class="export-buttons">
                            <button type="button" class="btn btn-sm btn-success export-btn" data-format="excel">
                                <i class="bi bi-file-earmark-excel me-1"></i>
                                {{ ___('reports.export_excel') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-danger export-btn" data-format="pdf">
                                <i class="bi bi-file-earmark-pdf me-1"></i>
                                {{ ___('reports.export_pdf') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-info export-btn" data-format="csv">
                                <i class="bi bi-file-earmark-text me-1"></i>
                                {{ ___('reports.export_csv') }}
                            </button>
                            <button type="button" id="printReportBtn" class="btn btn-sm btn-secondary">
                                <i class="bi bi-printer me-1"></i>
                                {{ ___('reports.print') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="reportResultsContainer" class="table-responsive">
                        <!-- Results will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('script')
<script>
    // Global configuration
    window.ReportConfig = {
        apiBaseUrl: '{{ url('/api/teacher/reports') }}',
        csrfToken: '{{ csrf_token() }}',
        translations: {
            loading: '{{ ___('common.loading') }}',
            error: '{{ ___('common.error') }}',
            success: '{{ ___('common.success') }}',
            validationError: '{{ ___('common.validation_error') }}',
            requiredField: '{{ ___('common.this_field_is_required') }}',
            invalidFormat: '{{ ___('common.invalid_format') }}',
            pleaseWait: '{{ ___('common.please_wait') }}',
            noDataFound: '{{ ___('common.no_data_found') }}',
            selectParentFirst: '{{ ___('reports.select_parent_field_first') }}'
        }
    };
</script>

{{-- Load JavaScript modules --}}
<script type="module" src="{{ asset('js/services/ReportApiService.js') }}"></script>
<script type="module" src="{{ asset('js/utils/FormValidation.js') }}"></script>
<script type="module" src="{{ asset('js/components/DependencyHandler.js') }}"></script>
<script type="module" src="{{ asset('js/components/DynamicReportForm.js') }}"></script>

{{-- Main initialization script --}}
<script type="module">
    import { DynamicReportForm } from '{{ asset('js/components/DynamicReportForm.js') }}';

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the dynamic report form
        const reportForm = new DynamicReportForm({
            categoryTabsSelector: '#reportCategoryTabs',
            reportSelectorId: 'reportSelector',
            formContainerId: 'dynamicFormContainer',
            formActionsId: 'formActions',
            resultsSectionId: 'resultsSection',
            resultsContainerId: 'reportResultsContainer',
            generateBtnId: 'generateReportBtn',
            resetBtnId: 'resetFormBtn',
            reportDescriptionId: 'reportDescription',
            reportDescriptionTextId: 'reportDescriptionText'
        });

        // Load initial data
        reportForm.initialize();
    });
</script>
@endpush
