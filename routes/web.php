<?php

use App\Http\Controllers\LoanController;
use App\Http\Controllers\VacationController;
use App\Http\Controllers\WarehouseIncomesController;
use App\Http\Controllers\WarehouseOutgosController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/app');
});


Route::get('/vacations/{id}', VacationController::class)->name('vacation_format');
Route::get('/loans/{id}', LoanController::class)->name('loan_format');

Route::get('/manifest-incomes/{id}', WarehouseIncomesController::class)->name('manifest_income_format');
Route::get('/manifest-outgos/{id}', WarehouseOutgosController::class)->name('manifest_outgo_format');
