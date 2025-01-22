<?php

use App\Http\Controllers\DateBenefit as ControllerDateBenefit;
use App\MoonShine\Controllers\Report as ControllerReport;
use App\MoonShine\Controllers\LoanController;
use App\MoonShine\Controllers\VacationController as ControllerVacation;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::post('/vacations', [ControllerVacation::class, 'store'])->name('vacations.store');
Route::get('/vacations/{id}', [ControllerVacation::class, 'getVacationFormat'])->name('vacations.vacation_format');
Route::delete('/vacations/{id}', [ControllerVacation::class, 'delete'])->name('vacations.delete');

Route::post('/bono14', [ControllerDateBenefit::class, 'store'])->name('storeBono14');

Route::post('/reports/payroll', [ControllerReport::class, 'getPayroll'])->name('reports.payroll');
Route::post('/reports/payslips', [ControllerReport::class, 'getPayslips'])->name('reports.payslips');
Route::post('/reports/benefit_payroll', [ControllerReport::class, 'getBenefitPayroll'])->name('reports.benefit_payroll');
Route::post('/reports/benefit_payslips', [ControllerReport::class, 'getBenefitPayslips'])->name('reports.benefit_payslips');

Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
Route::get('/loans/{id}', [LoanController::class, 'getLoanFormat'])->name('loans.loan_format');
Route::post('/loans/{id}', [LoanController::class, 'delete'])->name('loans.delete');
