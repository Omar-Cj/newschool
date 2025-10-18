<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DynamicReportController;

/*
|--------------------------------------------------------------------------
| Report Routes
|--------------------------------------------------------------------------
|
| Routes for dynamic report generation and export functionality
|
*/

Route::middleware(['auth'])->prefix('reports')->name('reports.')->group(function () {

    // Export endpoints (with branch access control)
    Route::post('/{reportId}/export', [DynamicReportController::class, 'export'])->name('export')->middleware('branch.access');
    Route::get('/download-export', [DynamicReportController::class, 'downloadExport'])->name('download-export');
    Route::post('/quick-export', [DynamicReportController::class, 'quickExport'])->name('quick-export')->middleware('branch.access');

    // Utility endpoints
    Route::get('/export-options', [DynamicReportController::class, 'getExportOptions'])->name('export-options');

    // Admin endpoints
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/cleanup-exports', [DynamicReportController::class, 'cleanupExports'])->name('cleanup-exports');
    });
});
