<?php

use App\Http\Controllers\MarkSheetApprovalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Report\AccountController;
use App\Http\Controllers\Report\DueFeesController;
use App\Http\Controllers\Report\MarksheetController;
use App\Http\Controllers\Report\MeritListController;
use App\Http\Controllers\Report\ExamRoutineController;
use App\Http\Controllers\Report\ClassRoutineController;
use App\Http\Controllers\Report\ProgressCardController;
use App\Http\Controllers\Report\ProgressListController;
use App\Http\Controllers\Report\StudentReportController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Report\FeesCollectionController;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;


Route::middleware(saasMiddleware())->group(function () {
    Route::group(['middleware' => ['XssSanitizer']], function () {
        Route::group(['middleware' => ['lang', 'CheckSubscription', 'FeatureCheck:report']], function () {
            Route::group(['middleware' => ['auth.routes', 'AdminPanel']], function () {

                Route::controller(MarksheetController::class)->prefix('report-marksheet')->group(function () {
                    Route::get('/', 'index')->name('report-marksheet.index')->middleware('PermissionCheck:report_marksheet_read');
                    Route::get('/search', 'search')->name('marksheet.search')->middleware('PermissionCheck:report_marksheet_read');
                    Route::get('/get-students', 'getStudents');
                    Route::get('/get-terms/{session}', 'getTerms');
                    Route::get('/pdf-generate/{id}/{type}/{class}/{section}/{session}/{term}', 'generatePDF')->name('report-marksheet.pdf-generate');
                });

                Route::controller(MeritListController::class)->prefix('report-merit-list')->group(function () {
                    Route::get('/', 'index')->name('report-merit-list.index')->middleware('PermissionCheck:report_merit_list_read');
                    Route::any('/search', 'search')->name('merit-list.search')->middleware('PermissionCheck:report_merit_list_read');
                    Route::get('/pdf-generate/{type}/{class}/{section}', 'generatePDF')->name('report-merit-list.pdf-generate');
                });

                Route::controller(ProgressCardController::class)->prefix('report-progress-card')->group(function () {
                    Route::get('/', 'index')->name('report-progress-card.index')->middleware('PermissionCheck:report_progress_card_read');
                    Route::post('/search', 'search')->name('report-progress-card.search');
                    Route::get('/get-students', 'getStudents');
                    Route::get('/get-terms/{sessionId}', 'getTerms')->name('report-progress-card.get-terms');
                    Route::get('/pdf-generate/{session}/{term}/{class}/{section}/{student}', 'generatePDF')->name('report-progress-card.pdf-generate');
                });

                Route::controller(DueFeesController::class)->prefix('report-due-fees')->group(function () {
                    Route::get('/', 'index')->name('report-due-fees.index')->middleware('PermissionCheck:report_due_fees_read');
                    Route::any('/search', 'search')->name('due-fees.search')->middleware('PermissionCheck:report_due_fees_read');
                    Route::post('/pdf-generate', 'generatePDF')->name('report-due-fees.pdf-generate');
                });

                Route::controller(FeesCollectionController::class)->prefix('report-fees-collection')->group(function () {
                    Route::get('/', 'index')->name('report-fees-collection.index')->middleware('PermissionCheck:report_fees_collection_read');
                    Route::any('/search', 'search')->name('fees-collection.search')->middleware('PermissionCheck:report_fees_collection_read');
                    Route::get('/pdf-generate/{class}/{section}/{dates}', 'generatePDF')->name('report-fees-collection.pdf-generate');
                });

                Route::controller(AccountController::class)->prefix('report-account')->group(function () {
                    Route::get('/', 'index')->name('report-account.index')->middleware('PermissionCheck:report_account_read');
                    Route::any('/search', 'search')->name('account.search')->middleware('PermissionCheck:report_account_read');
                    Route::get('/get-account-types', 'getAccountTypes');
                    Route::post('/pdf-generate', 'generatePDF')->name('report-account.pdf-generate');
                });

                Route::controller(AttendanceController::class)->prefix('report-attendance')->group(function () {
                    Route::get('/report', 'report')->name('report-attendance.report')->middleware('PermissionCheck:report_attendance_read');
                    Route::any('/report-search', 'reportSearch')->name('report-attendance.report-search')->middleware('PermissionCheck:report_attendance_read');
                    Route::post('/pdf-generate', 'generatePDF')->name('report-attendance.pdf-generate');
                });

                Route::controller(ClassRoutineController::class)->prefix('report-class-routine')->group(function () {
                    Route::get('/', 'index')->name('report-class-routine.index')->middleware('PermissionCheck:report_class_routine_read');
                    Route::post('/search', 'search')->name('report-class-routine.search')->middleware('PermissionCheck:report_class_routine_read');
                    Route::get('/pdf-generate/{class}/{section}', 'generatePDF')->name('report-class-routine.pdf-generate');
                });

                Route::controller(ExamRoutineController::class)->prefix('report-exam-routine')->group(function () {
                    Route::get('/', 'index')->name('report-exam-routine.index')->middleware('PermissionCheck:report_exam_routine_read');
                    Route::post('/search', 'search')->name('report-exam-routine.search')->middleware('PermissionCheck:report_exam_routine_read');
                    Route::get('/pdf-generate/{class}/{section}/{type}', 'generatePDF')->name('report-exam-routine.pdf-generate');
                });

                Route::controller(StudentReportController::class)->prefix('report-student')->group(function () {
                    Route::get('/', 'index')->name('report-student.index')->middleware('PermissionCheck:student_reports_read');
                    Route::get('/search-student-list', 'searchStudentList')->name('report-student.search-student-list')->middleware('PermissionCheck:student_reports_read');
                    Route::get('/pdf-student-list', 'generateStudentListPDF')->name('report-student.pdf-student-list')->middleware('PermissionCheck:student_reports_read');
                    Route::get('/search-student-registration', 'searchStudentRegistration')->name('report-student.search-student-registration')->middleware('PermissionCheck:student_reports_read');
                    Route::get('/pdf-student-registration', 'generateStudentRegistrationPDF')->name('report-student.pdf-student-registration')->middleware('PermissionCheck:student_reports_read');
                    Route::get('/search-guardian-list', 'searchGuardianList')->name('report-student.search-guardian-list')->middleware('PermissionCheck:student_reports_read');
                    Route::get('/pdf-guardian-list', 'generateGuardianListPDF')->name('report-student.pdf-guardian-list')->middleware('PermissionCheck:student_reports_read');
                });

                Route::controller(\App\Http\Controllers\Report\BillingReportController::class)->prefix('report-billing')->group(function () {
                    Route::get('/', 'index')->name('report-billing.index')->middleware('PermissionCheck:billing_reports_read');
                    // Future routes will be added here for each report type
                });

                Route::controller(\App\Http\Controllers\Report\ExaminationReportController::class)->prefix('report-examination')->group(function () {
                    Route::get('/', 'index')->name('report-examination.index')->middleware('PermissionCheck:report_marksheet_read');
                    Route::get('/search-marksheet', 'searchMarksheet')->name('report-examination.search-marksheet')->middleware('PermissionCheck:report_marksheet_read');
                    Route::post('/search-progress-card', 'searchProgressCard')->name('report-examination.search-progress-card')->middleware('PermissionCheck:report_progress_card_read');
                    Route::get('/get-students', 'getStudents')->name('report-examination.get-students');
                    Route::get('/get-terms/{session}', 'getTerms')->name('report-examination.get-terms');
                });

                Route::controller(MarkSheetApprovalController::class)->prefix('report-marksheet')->group(function () {
                    Route::post('/', 'approveOrReject')->name('report-marksheet.approve-or-reject');
                });

            });
        });
    });
});


