<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Transportation\BusController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;


Route::middleware(saasMiddleware())->group(function () {
    Route::group(['middleware' => ['XssSanitizer']], function () {
        Route::group(['middleware' => ['lang', 'CheckSubscription']], function () {
            // auth routes
            Route::group(['middleware' => ['auth.routes', 'AdminPanel']], function () {
                Route::controller(BusController::class)->prefix('bus')->group(function () {
                    Route::get('/',                 'index')->name('bus.index')->middleware('PermissionCheck:bus_read');
                    Route::get('/ajax-data',        'ajaxBusData')->name('bus.ajaxData')->middleware('PermissionCheck:bus_read');
                    Route::get('/create',           'create')->name('bus.create')->middleware('PermissionCheck:bus_create');
                    Route::post('/store',           'store')->name('bus.store')->middleware('PermissionCheck:bus_create', 'DemoCheck');
                    Route::get('/edit/{id}',        'edit')->name('bus.edit')->middleware('PermissionCheck:bus_update');
                    Route::put('/update/{id}',      'update')->name('bus.update')->middleware('PermissionCheck:bus_update', 'DemoCheck');
                    Route::delete('/delete/{id}',   'delete')->name('bus.delete')->middleware('PermissionCheck:bus_delete', 'DemoCheck');
                });
            });
        });
    });
});
