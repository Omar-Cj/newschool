<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Academic\ExamRoutineController;
use App\Http\Controllers\Examination\ExamTypeController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use App\Http\Controllers\Examination\MarksheetController;
use App\Http\Controllers\Examination\ExamAssignController;
use App\Http\Controllers\Examination\MarksGradeController;
use App\Http\Controllers\Examination\MarksRegisterController;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use App\Http\Controllers\Examination\ExaminationSettingsController;
use App\Http\Controllers\Backend\Examination\ExamEntryController;
use App\Http\Controllers\Academic\TermController;


Route::middleware(saasMiddleware())->group(function () {
    Route::group(['middleware' => ['XssSanitizer']], function () {
        Route::group(['middleware' => ['lang', 'CheckSubscription', 'FeatureCheck:examination']], function () {
            Route::group(['middleware' => ['auth.routes', 'AdminPanel']], function () {

                Route::controller(ExamTypeController::class)->prefix('exam-type')->group(function () {
                    Route::get('/',                 'index')->name('exam-type.index')->middleware('PermissionCheck:exam_type_read');
                    Route::get('/create',           'create')->name('exam-type.create')->middleware('PermissionCheck:exam_type_create');
                    Route::post('/store',           'store')->name('exam-type.store')->middleware('PermissionCheck:exam_type_create', 'DemoCheck');
                    Route::get('/edit/{id}',        'edit')->name('exam-type.edit')->middleware('PermissionCheck:exam_type_update');
                    Route::put('/update/{id}',      'update')->name('exam-type.update')->middleware('PermissionCheck:exam_type_update', 'DemoCheck');
                    Route::delete('/delete/{id}',   'delete')->name('exam-type.delete')->middleware('PermissionCheck:exam_type_delete', 'DemoCheck');
                });

                Route::controller(MarksGradeController::class)->prefix('marks-grade')->group(function () {
                    Route::get('/',                 'index')->name('marks-grade.index')->middleware('PermissionCheck:marks_grade_read');
                    Route::get('/create',           'create')->name('marks-grade.create')->middleware('PermissionCheck:marks_grade_create');
                    Route::post('/store',           'store')->name('marks-grade.store')->middleware('PermissionCheck:marks_grade_create', 'DemoCheck');
                    Route::get('/edit/{id}',        'edit')->name('marks-grade.edit')->middleware('PermissionCheck:marks_grade_update');
                    Route::put('/update/{id}',      'update')->name('marks-grade.update')->middleware('PermissionCheck:marks_grade_update', 'DemoCheck');
                    Route::delete('/delete/{id}',   'delete')->name('marks-grade.delete')->middleware('PermissionCheck:marks_grade_delete', 'DemoCheck');
                });

                Route::controller(MarksRegisterController::class)->prefix('marks-register')->group(function () {
                    Route::get('/',                 'index')->name('marks-register.index')->middleware('PermissionCheck:marks_register_read');
                    Route::any('/search',          'search')->name('marks-register.search')->middleware('PermissionCheck:marks_register_read');
                    Route::get('/create',           'create')->name('marks-register.create')->middleware('PermissionCheck:marks_register_create');
                    Route::post('/store',           'store')->name('marks-register.store')->middleware('PermissionCheck:marks_register_create', 'DemoCheck');
                    Route::get('/edit/{id}',        'edit')->name('marks-register.edit')->middleware('PermissionCheck:marks_register_update');
                    Route::put('/update/{id}',      'update')->name('marks-register.update')->middleware('PermissionCheck:marks_register_update', 'DemoCheck');
                    Route::delete('/delete/{id}',   'delete')->name('marks-register.delete')->middleware('PermissionCheck:marks_register_delete', 'DemoCheck');
                    Route::get('/show',             'show');
                    Route::post('/update-marksheet-status/{id}', 'updateMarksheetStatus')->name('marks-register.update-marksheet-status')->middleware('PermissionCheck:marks_register_update');
                });



                Route::controller(ExamAssignController::class)->prefix('exam-assign')->group(function () {
                    Route::get('/',                 'index')->name('exam-assign.index')->middleware('PermissionCheck:exam_assign_read');
                    Route::any('/search',           'search')->name('exam-assign.search')->middleware('PermissionCheck:exam_assign_read');
                    Route::get('/create',           'create')->name('exam-assign.create')->middleware('PermissionCheck:exam_assign_create');
                    Route::post('/store',           'store')->name('exam-assign.store')->middleware('PermissionCheck:exam_assign_create', 'DemoCheck');
                    Route::get('/edit/{id}',        'edit')->name('exam-assign.edit')->middleware('PermissionCheck:exam_assign_update');
                    Route::put('/update/{id}',      'update')->name('exam-assign.update')->middleware('PermissionCheck:exam_assign_update', 'DemoCheck');
                    Route::delete('/delete/{id}',   'delete')->name('exam-assign.delete')->middleware('PermissionCheck:exam_assign_delete', 'DemoCheck');
                    Route::get('/marks-distribution', 'marksDistribution');
                    Route::get('/subject-marks-distribution', 'subjectMarksDistribution');

                    Route::get('/get-sections',     'getSections');
                    Route::get('/get-subjects',     'getSubjects');
                    Route::get('/get-exam-type',    'getExamType');
                    Route::post('/check-submit',    'checkSubmit')->middleware('DemoCheck');
                    Route::get('/check-mark-register/{id}', 'checkMarkRegister');
                });

                Route::controller(ExaminationSettingsController::class)->prefix('examination-settings')->group(function () {
                    Route::get('/',                 'index')->name('examination-settings.index')->middleware('PermissionCheck:exam_assign_read');
                    Route::put('/update',           'update')->name('examination-settings.update')->middleware('PermissionCheck:exam_assign_create', 'DemoCheck');
                });

                // Exam Entry Routes
                Route::controller(ExamEntryController::class)->prefix('exam-entry')->group(function () {
                    Route::get('/',                         'index')->name('exam-entry.index')->middleware('PermissionCheck:exam_entry_read');
                    Route::get('/ajax-data',                'ajaxData')->name('exam-entry.ajax-data')->middleware('PermissionCheck:exam_entry_read');
                    Route::get('/create',                   'create')->name('exam-entry.create')->middleware('PermissionCheck:exam_entry_create');
                    Route::post('/store',                   'store')->name('exam-entry.store')->middleware('PermissionCheck:exam_entry_create', 'DemoCheck');
                    Route::get('/show/{id}',                'show')->name('exam-entry.show')->middleware('PermissionCheck:exam_entry_read');
                    Route::get('/edit/{id}',                'edit')->name('exam-entry.edit')->middleware('PermissionCheck:exam_entry_update');
                    Route::put('/update/{id}',              'update')->name('exam-entry.update')->middleware('PermissionCheck:exam_entry_update', 'DemoCheck');
                    Route::delete('/delete/{id}',           'destroy')->name('exam-entry.delete')->middleware('PermissionCheck:exam_entry_delete', 'DemoCheck');
                    Route::put('/publish/{id}',             'publish')->name('exam-entry.publish')->middleware('PermissionCheck:exam_entry_update', 'DemoCheck');
                    Route::get('/get-students',             'getStudents')->name('exam-entry.get-students');
                    Route::get('/download-template',        'downloadTemplate')->name('exam-entry.download-template');
                    Route::post('/upload-results',          'uploadResults')->name('exam-entry.upload-results')->middleware('PermissionCheck:exam_entry_create', 'DemoCheck');
                    Route::get('/get-terms',                'getTerms')->name('exam-entry.get-terms');
                    Route::get('/get-sections',             'getSections')->name('exam-entry.get-sections');
                    Route::get('/get-subjects',             'getSubjects')->name('exam-entry.get-subjects');
                    Route::put('/calculate-grades/{id}',    'calculateGrades')->name('exam-entry.calculate-grades')->middleware('PermissionCheck:exam_entry_update', 'DemoCheck');
                });

                // Terms Routes (needed for exam entry)
                Route::controller(TermController::class)->prefix('terms')->group(function () {
                    Route::get('/',                     'index')->name('terms.index');
                    Route::get('/ajax-data',            'ajaxData')->name('terms.ajax-data');
                    Route::get('/create',               'create')->name('terms.create');
                    Route::post('/store',               'store')->name('terms.store');
                    Route::get('/edit/{id}',            'edit')->name('terms.edit');
                    Route::put('/update/{id}',          'update')->name('terms.update');
                    Route::put('/close/{id}',           'close')->name('terms.close');
                    Route::put('/activate/{id}',        'activate')->name('terms.activate');
                    Route::get('/suggestions',          'suggestions')->name('terms.suggestions');
                    Route::post('/bulk-open',           'bulkOpen')->name('terms.bulk-open');
                    Route::post('/clone',               'cloneTerms')->name('terms.clone');
                    Route::get('/timeline',             'timeline')->name('terms.timeline');
                    Route::get('/statistics',           'statistics')->name('terms.statistics');
                    Route::post('/validate-dates',      'validateTermDates')->name('terms.validate-dates');
                    Route::get('/definitions',          'definitions')->name('terms.definitions');
                    Route::get('/definitions/ajax-data', 'definitionsAjaxData')->name('terms.definitions.ajax-data');
                    Route::post('/definitions/store',   'storeDefinition')->name('terms.definitions.store');
                    Route::get('/definitions/edit/{id}', 'editDefinition')->name('terms.definitions.edit');
                    Route::put('/definitions/update/{id}', 'updateDefinition')->name('terms.definitions.update');
                    Route::delete('/definitions/delete/{id}', 'deleteDefinition')->name('terms.definitions.delete');
                });
            });
        });

        Route::group(['middleware' => ['lang', 'CheckSubscription', 'FeatureCheck:routine']], function () {
            Route::group(['middleware' => ['auth.routes', 'AdminPanel']], function () {

                Route::controller(ExamRoutineController::class)->prefix('exam-routine')->group(function () {
                    Route::get('/',                 'index')->name('exam-routine.index')->middleware('PermissionCheck:exam_routine_read');
                    Route::get('/create',           'create')->name('exam-routine.create')->middleware('PermissionCheck:exam_routine_create');
                    Route::post('/store',           'store')->name('exam-routine.store')->middleware('PermissionCheck:exam_routine_create');
                    Route::get('/edit/{id}',        'edit')->name('exam-routine.edit')->middleware('PermissionCheck:exam_routine_update');
                    Route::put('/update/{id}',      'update')->name('exam-routine.update')->middleware('PermissionCheck:exam_routine_update');
                    Route::delete('/delete/{id}',   'delete')->name('exam-routine.delete')->middleware('PermissionCheck:exam_routine_delete');
                    Route::get('/add-exam-routine', 'addexamRoutine');
                    Route::get('/check-exam-routine', 'checkExamRoutine');
                });
            });
        });
    });
});
