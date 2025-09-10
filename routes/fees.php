<?php

use App\Http\Controllers\FeesDiscountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Fees\FeesTypeController;
use App\Http\Controllers\Fees\FeesGroupController;
use App\Http\Controllers\Fees\FeesAssignController;
use App\Http\Controllers\Fees\FeesMasterController;
use App\Http\Controllers\Fees\FeesCollectController;
use App\Http\Controllers\Fees\FeesGenerationController;
use App\Http\Controllers\Fees\ReceiptController;
use App\Http\Controllers\Fees\StudentServiceController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;


Route::middleware(saasMiddleware())->group(function () {
    Route::group(['middleware' => ['XssSanitizer']], function () {
        Route::group(['middleware' => ['lang', 'CheckSubscription', 'FeatureCheck:fees']], function () {
            // auth routes
            Route::group(['middleware' => ['auth.routes', 'AdminPanel']], function () {
                Route::controller(FeesGroupController::class)->prefix('fees-group')->group(function () {
                    Route::get('/',                 'index')->name('fees-group.index')->middleware('PermissionCheck:fees_group_read');
                    Route::get('/create',           'create')->name('fees-group.create')->middleware('PermissionCheck:fees_group_create');
                    Route::post('/store',           'store')->name('fees-group.store')->middleware('PermissionCheck:fees_group_create', 'DemoCheck');
                    Route::get('/edit/{id}',        'edit')->name('fees-group.edit')->middleware('PermissionCheck:fees_group_update');
                    Route::put('/update/{id}',      'update')->name('fees-group.update')->middleware('PermissionCheck:fees_group_update', 'DemoCheck');
                    Route::delete('/delete/{id}',   'delete')->name('fees-group.delete')->middleware('PermissionCheck:fees_group_delete', 'DemoCheck');
                });

                Route::controller(FeesTypeController::class)->prefix('fees-type')->group(function () {
                    Route::get('/',                 'index')->name('fees-type.index')->middleware('PermissionCheck:fees_type_read');
                    Route::get('/create',           'create')->name('fees-type.create')->middleware('PermissionCheck:fees_type_create');
                    Route::post('/store',           'store')->name('fees-type.store')->middleware('PermissionCheck:fees_type_create', 'DemoCheck');
                    Route::get('/edit/{id}',        'edit')->name('fees-type.edit')->middleware('PermissionCheck:fees_type_update');
                    Route::put('/update/{id}',      'update')->name('fees-type.update')->middleware('PermissionCheck:fees_type_update', 'DemoCheck');
                    Route::delete('/delete/{id}',   'delete')->name('fees-type.delete')->middleware('PermissionCheck:fees_type_delete', 'DemoCheck');
                });

                Route::controller(FeesMasterController::class)->prefix('fees-master')->group(function () {
                    Route::get('/',                 'index')->name('fees-master.index')->middleware('PermissionCheck:fees_master_read');
                    Route::get('/create',           'create')->name('fees-master.create')->middleware('PermissionCheck:fees_master_create');
                    Route::post('/store',           'store')->name('fees-master.store')->middleware('PermissionCheck:fees_master_create', 'DemoCheck');
                    Route::get('/edit/{id}',        'edit')->name('fees-master.edit')->middleware('PermissionCheck:fees_master_update');
                    Route::put('/update/{id}',      'update')->name('fees-master.update')->middleware('PermissionCheck:fees_master_update', 'DemoCheck');
                    Route::delete('/delete/{id}',   'delete')->name('fees-master.delete')->middleware('PermissionCheck:fees_master_delete', 'DemoCheck');
                    Route::get('/get-all-type',     'getAllTypes');
                });

                Route::controller(FeesAssignController::class)->prefix('fees-assign')->group(function () {
                    Route::get('/',                 'index')->name('fees-assign.index')->middleware('PermissionCheck:fees_assign_read');
                    Route::get('/create',           'create')->name('fees-assign.create')->middleware('PermissionCheck:fees_assign_create');
                    Route::post('/store',           'store')->name('fees-assign.store')->middleware('PermissionCheck:fees_assign_create', 'DemoCheck');
                    Route::get('/edit/{id}',        'edit')->name('fees-assign.edit')->middleware('PermissionCheck:fees_assign_update');
                    Route::put('/update/{id}',      'update')->name('fees-assign.update')->middleware('PermissionCheck:fees_assign_update', 'DemoCheck');
                    Route::delete('/delete/{id}',   'delete')->name('fees-assign.delete')->middleware('PermissionCheck:fees_assign_delete', 'DemoCheck');
                    Route::get('/show',              'show');

                    Route::get('/get-all-type',     'getAllTypes');

                    Route::get('/get-fees-assign-students',  'getFeesAssignStudents');
                    Route::get('/import', 'import')->name('fees-assign.import')->middleware('PermissionCheck:fees_assign_create');
                    Route::post('/import-submit', 'importSubmit')->name('fees-assign.importSubmit')->middleware('PermissionCheck:fees_assign_create');
                    Route::get('/sample-download',          'sampleDownload')->name('fees-assign.sampleDownload')->middleware('PermissionCheck:fees_assign_create');
                });

                Route::controller(FeesCollectController::class)->prefix('fees-collect')->group(function () {
                    Route::get('/',                 'index')->name('fees-collect.index')->middleware('PermissionCheck:fees_collect_read');
                    Route::get('/create',           'create')->name('fees-collect.create')->middleware('PermissionCheck:fees_collect_create');
                    Route::post('/store',           'store')->name('fees-collect.store')->middleware('PermissionCheck:fees_collect_create', 'DemoCheck');
                    Route::get('/edit/{id}',        'edit')->name('fees-collect.edit')->middleware('PermissionCheck:fees_collect_update');
                    Route::put('/update/{id}',      'update')->name('fees-collect.update')->middleware('PermissionCheck:fees_collect_update', 'DemoCheck');
                    Route::delete('/delete/{id}',   'delete')->name('fees-collect.delete')->middleware('PermissionCheck:fees_collect_delete', 'DemoCheck');
                    Route::get('/collect/{id}',     'collect')->name('fees-collect.collect')->middleware('PermissionCheck:fees_collect_update');


                    Route::any('/search', 'getFeesCollectStudents')->name('fees-collect-search');
                    Route::get('/fees-show', 'feesShow')->middleware('PermissionCheck:fees_collect_update');
                });

                Route::controller(FeesDiscountController::class)->prefix('fees-discount')->group(function () {
                    Route::get('/',                 'index')->name('fees-discount.index')->middleware('PermissionCheck:siblings_discount');
                    Route::post('/store',           'store')->name('fees-discount.store')->middleware('PermissionCheck:siblings_discount');
                    Route::post('/early-payment-discount', 'storeEarlyPaymentDiscount')->name('fees-discount.early-payment-discount')->middleware('PermissionCheck:early_payment_discount');
                    Route::post('/toggle-applicable', 'toggleApplicable')->name('fees-discount.toggle-applicable');
                    Route::post('/early-payment-toggle', 'toggleEarlyPayment')->name('fees-discount.early-payment-toggle');
                });

                Route::controller(FeesGenerationController::class)->prefix('fees-generation')->group(function () {
                    Route::get('/',                     'index')->name('fees-generation.index')->middleware('PermissionCheck:fees_generate_read');
                    Route::get('/create',               'create')->name('fees-generation.create')->middleware('PermissionCheck:fees_generate_create');
                    Route::get('/history',              'history')->name('fees-generation.history')->middleware('PermissionCheck:fees_generate_read');
                    Route::get('/show/{id}',            'show')->name('fees-generation.show')->middleware('PermissionCheck:fees_generate_read');
                    
                    // AJAX endpoints
                    Route::post('/preview',             'preview')->name('fees-generation.preview')->middleware('PermissionCheck:fees_generate_create');
                    Route::post('/generate',            'generate')->name('fees-generation.generate')->middleware('PermissionCheck:fees_generate_create', 'DemoCheck');
                    Route::get('/status/{batchId}',     'status')->name('fees-generation.status')->middleware('PermissionCheck:fees_generate_read');
                    Route::post('/cancel/{id}',         'cancel')->name('fees-generation.cancel')->middleware('PermissionCheck:fees_generate_delete', 'DemoCheck');
                    
                    // Helper endpoints
                    Route::get('/get-sections',         'getSections');
                    Route::get('/get-student-count',    'getStudentCount');
                    
                    // Service Manager endpoints
                    Route::get('/system-status',        'getSystemStatus')->name('fees-generation.system-status');
                    Route::post('/switch-system',       'switchSystem')->name('fees-generation.switch-system')->middleware('PermissionCheck:fees_generate_create', 'DemoCheck');
                    Route::post('/preview-managed',     'generatePreviewWithManager')->name('fees-generation.preview-managed')->middleware('PermissionCheck:fees_generate_create');
                    Route::post('/generate-managed',    'generateFeesWithManager')->name('fees-generation.generate-managed')->middleware('PermissionCheck:fees_generate_create', 'DemoCheck');
                });

                // Receipt Generation Routes
                Route::controller(ReceiptController::class)->prefix('fees/receipt')->group(function () {
                    // Individual receipt generation
                    Route::get('/individual/{paymentId}',              'generateIndividualReceipt')->name('fees.receipt.individual')->middleware('PermissionCheck:fees_collect_read');
                    
                    // Student summary receipt (all payments for a student)
                    Route::get('/student-summary/{studentId}',         'generateStudentSummaryReceipt')->name('fees.receipt.student-summary')->middleware('PermissionCheck:fees_collect_read');
                    
                    // Group receipt for multiple payments
                    Route::post('/group',                               'generateGroupReceipt')->name('fees.receipt.group')->middleware('PermissionCheck:fees_collect_read');
                    
                    // Daily collection receipt for collector
                    Route::get('/daily-collection',                    'generateDailyCollectionReceipt')->name('fees.receipt.daily-collection')->middleware('PermissionCheck:fees_collect_read');
                    
                    // Receipt options modal
                    Route::get('/options/{paymentId}',                 'showReceiptOptions')->name('fees.receipt.options')->middleware('PermissionCheck:fees_collect_read');
                    
                    // Helper endpoints
                    Route::get('/check-group-availability',            'checkGroupReceiptAvailability')->name('fees.receipt.check-group-availability');
                    Route::get('/today-payments',                      'getTodayPayments')->name('fees.receipt.today-payments');
                    Route::post('/email',                              'emailReceipt')->name('fees.receipt.email')->middleware('PermissionCheck:fees_collect_read');
                });

                // Enhanced Fee Processing System - Service Management Routes
                Route::controller(StudentServiceController::class)->prefix('student-services')->group(function () {
                    // Service Management Dashboard
                    Route::get('/',                                     'dashboard')->name('student-services.dashboard')->middleware('PermissionCheck:fees_assign_read');
                    Route::get('/dashboard-stats',                     'getDashboardStats')->name('student-services.dashboard-stats');
                    Route::get('/services-overview',                   'getServicesOverview')->name('student-services.services-overview');
                    Route::get('/recent-activities',                   'getRecentActivities')->name('student-services.recent-activities');
                    Route::get('/academic-level-stats',                'getAcademicLevelStats')->name('student-services.academic-level-stats');
                    Route::get('/search',                              'searchStudentServices')->name('student-services.search');
                    Route::get('/export-report',                       'exportServiceReport')->name('student-services.export-report');
                    
                    // Student service information
                    Route::get('/available',                            'getAvailableServicesForRegistration')->name('student-services.available-registration');
                    Route::get('/registration-services',               'getServicesForRegistration')->name('student-services.registration-services');
                    Route::get('/student/{student}/available',         'getAvailableServices')->name('student-services.available')->middleware('PermissionCheck:fees_assign_read');
                    Route::get('/student/{student}/subscriptions',     'getStudentServices')->name('student-services.subscriptions')->middleware('PermissionCheck:fees_assign_read');
                    
                    // Service subscription management
                    Route::post('/subscribe',                           'subscribe')->name('student-services.subscribe')->middleware('PermissionCheck:fees_assign_create', 'DemoCheck');
                    Route::post('/student/{student}/auto-mandatory',   'autoSubscribeMandatory')->name('student-services.auto-mandatory')->middleware('PermissionCheck:fees_assign_create', 'DemoCheck');
                    Route::delete('/service/{service}/unsubscribe',    'unsubscribe')->name('student-services.unsubscribe')->middleware('PermissionCheck:fees_assign_delete', 'DemoCheck');
                    
                    // Discount management
                    Route::post('/service/{service}/discount',         'applyDiscount')->name('student-services.apply-discount')->middleware('PermissionCheck:fees_assign_update', 'DemoCheck');
                    Route::delete('/service/{service}/discount',       'removeDiscount')->name('student-services.remove-discount')->middleware('PermissionCheck:fees_assign_update', 'DemoCheck');
                    
                    // Bulk operations
                    Route::post('/bulk-subscribe',                     'bulkSubscribe')->name('student-services.bulk-subscribe')->middleware('PermissionCheck:fees_assign_create', 'DemoCheck');
                    Route::post('/bulk-discount',                      'bulkApplyDiscount')->name('student-services.bulk-discount')->middleware('PermissionCheck:fees_assign_update', 'DemoCheck');
                    
                    // Fee generation preview
                    Route::post('/preview',                             'generatePreview')->name('student-services.preview')->middleware('PermissionCheck:fees_generate_read');
                });
            });
        });
    });
});


