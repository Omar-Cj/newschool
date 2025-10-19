<?php

use Illuminate\Support\Facades\Route;
use Modules\Journals\Http\Controllers\JournalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::resource('journals', JournalController::class);
    Route::post('journals/{id}/close', [JournalController::class, 'close'])->name('journals.close');
    Route::post('journals/{id}/open', [JournalController::class, 'open'])->name('journals.open');
    Route::get('journals-dropdown', [JournalController::class, 'getJournalsDropdown'])->name('journals.dropdown');
    Route::get('journals/{id}/details', [JournalController::class, 'getJournalDetails'])->name('journals.details');
});
