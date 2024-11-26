<?php

use App\Http\Controllers\DateBenefit as ControllersDateBenefit;
use App\MoonShine\Controllers\Report as ControllerReport;
use App\MoonShine\Controllers\LoanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::post('/bono14', [ControllersDateBenefit::class, 'store'])->name('storeBono14');

Route::post('/reports', ControllerReport::class)->name('reports');
Route::post('/loans', LoanController::class)->name('loan');