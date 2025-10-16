@extends('layouts.app')

@section('title', 'Report - ' . ($report->name ?? 'View Report'))

@section('content')
<div class="container-fluid py-4">
    {{-- Page Header --}}
    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">{{ $report->name ?? 'Report Results' }}</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $report->name ?? 'View' }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Filters Summary (if any) --}}
    @if(isset($filters) && count($filters) > 0)
    <div class="row mb-3">
        <div class="col">
            <div class="card">
                <div class="card-body py-2">
                    <div class="d-flex align-items-center">
                        <strong class="me-3">Filters Applied:</strong>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($filters as $key => $value)
                                <span class="badge bg-light text-dark">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}: <strong>{{ $value }}</strong>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Results Section --}}
    <div class="row">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body">
                    @include('reports.partials.results', [
                        'reportId' => $report->id ?? null,
                        'reportData' => $reportData ?? null
                    ])
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Tables Section - Hidden in web view, shown only in print/PDF --}}
    {{-- Summary is automatically included in print-wrapper.blade.php and pdf/template.blade.php --}}

    {{-- Additional Actions --}}
    <div class="row mt-4 d-print-none">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Report Actions</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline-primary" onclick="window.location.reload()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                <polyline points="23 4 23 10 17 10"></polyline>
                                <polyline points="1 20 1 14 7 14"></polyline>
                                <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                            </svg>
                            Refresh Data
                        </button>

                        @if(isset($report->id))
                        <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-outline-secondary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                            </svg>
                            Modify Filters
                        </a>
                        @endif

                        <button type="button" class="btn btn-outline-info" onclick="window.toggleFullScreen()">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                            </svg>
                            Full Screen
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Full screen toggle utility
    window.toggleFullScreen = function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.error('Error attempting to enable full-screen mode:', err);
            });
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    };

    // Keyboard shortcut for refresh (F5 or Ctrl+R handled by browser)
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + P for print
        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
            e.preventDefault();
            window.print();
        }
        // Ctrl/Cmd + E for export menu
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            const exportBtn = document.querySelector('.export-buttons .dropdown-toggle');
            if (exportBtn) exportBtn.click();
        }
    });

    // Update generated date attributes for print
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.querySelector('.report-results-container');
        if (container) {
            const now = new Date();
            container.setAttribute('data-generated', now.toLocaleDateString());
            container.setAttribute('data-time', now.toLocaleTimeString());
            container.setAttribute('data-report-name', '{{ $report->name ?? "Report" }}');
        }
    });
</script>
@endpush
@endsection
