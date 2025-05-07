<?php

use App\Http\Controllers\LoanController;
use App\Http\Controllers\VacationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/vacations/{id}', VacationController::class)->name('vacation_format');
Route::get('/loans/{id}', LoanController::class)->name('loan_format');
