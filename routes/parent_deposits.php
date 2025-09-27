<?php

use App\Http\Controllers\ParentDeposit\ParentDepositController;
use App\Http\Controllers\ParentDeposit\ParentStatementController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware(saasMiddleware())->group(function () {
    Route::group(['middleware' => ['XssSanitizer']], function () {
        Route::group(['middleware' => ['lang', 'CheckSubscription']], function () {
            Route::group(['middleware' => ['auth.routes']], function () {

                // Parent Deposit Routes
                Route::controller(ParentDepositController::class)->prefix('parent-deposits')->group(function () {
                    Route::get('/', 'index')->name('parent-deposits.index')->middleware('permission:parent_deposit_view');
                    Route::get('/deposit-modal', 'depositModal')->name('parent-deposits.deposit-modal')->middleware('permission:parent_deposit_create');
                    Route::post('/store', 'store')->name('parent-deposits.store')->middleware('permission:parent_deposit_create');
                    Route::get('/{deposit}', 'show')->name('parent-deposits.show')->middleware('permission:parent_deposit_view');
                    Route::get('/balance/get', 'getBalance')->name('parent-deposits.get-balance')->middleware('permission:parent_deposit_view');
                    Route::get('/children/get', 'getChildren')->name('parent-deposits.get-children')->middleware('permission:parent_deposit_view');
                    Route::post('/payment/process', 'processLocalPayment')->name('parent-deposits.process-payment')->middleware('permission:parent_deposit_create');
                    Route::post('/balance/transfer', 'transferBalance')->name('parent-deposits.transfer-balance')->middleware('permission:parent_deposit_create');
                    Route::delete('/{deposit}', 'destroy')->name('parent-deposits.destroy')->middleware('permission:parent_deposit_delete');
                    Route::any('/search', 'search')->name('parent-deposits.search')->middleware('permission:parent_deposit_view');
                });

                // Parent Statement Routes
                Route::controller(ParentStatementController::class)->prefix('parent-statements')->group(function () {
                    Route::get('/', 'index')->name('parent-statements.index')->middleware('permission:parent_statement_view');
                    Route::get('/{parent}', 'show')->name('parent-statements.show')->middleware('permission:parent_statement_view');
                    Route::any('/search', 'search')->name('parent-statements.search')->middleware('permission:parent_statement_view');
                    Route::get('/export/statement', 'export')->name('parent-statements.export')->middleware('permission:parent_statement_export');
                    Route::get('/modal/statement', 'statementModal')->name('parent-statements.statement-modal')->middleware('permission:parent_statement_view');
                    Route::get('/ajax/transaction-details', 'getTransactionDetails')->name('parent-statements.transaction-details')->middleware('permission:parent_statement_view');
                    Route::get('/ajax/balance-trend', 'getBalanceTrend')->name('parent-statements.balance-trend')->middleware('permission:parent_statement_view');
                    Route::get('/ajax/monthly-summary', 'getMonthlySummary')->name('parent-statements.monthly-summary')->middleware('permission:parent_statement_view');
                    Route::get('/ajax/summary-statistics', 'getSummaryStatistics')->name('parent-statements.summary-statistics')->middleware('permission:parent_statement_view');
                });

            });
        });
    });
});