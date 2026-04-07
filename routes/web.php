<?php

use App\Http\Controllers\LoanController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ShipmentDeliveryController;
use App\Http\Controllers\ShipmentEntryController;
use App\Http\Controllers\ShipmentManifestController;
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

Route::get('/shipment-entries/{id}/printMother', [ShipmentEntryController::class, 'printMother'])->name('shipment_entry.printMother');
Route::get('/shipment-entries/buscar-municipios', [ShipmentEntryController::class, 'searchTown'])->name('buscar_municipios');
Route::get('/shipment-entries/buscar-unico-municipio', [ShipmentEntryController::class, 'searchOnlyTown'])->name('buscar_unico_municipio');
Route::get('/shipment-entries/buscar-producto', [ShipmentEntryController::class, 'searchProductDetail'])->name('buscar_producto');
Route::get('/shipment-entries/buscar-ruta', [ShipmentEntryController::class, 'searchRoute'])->name('buscar_ruta');
Route::get('/shipment-entries/buscar-cliente', [ShipmentEntryController::class, 'getCustomerData'])->name('buscar_cliente');
Route::get('/shipment-entries/listar-clientes', [ShipmentEntryController::class, 'getCustomers'])->name('listar_clientes');
Route::get('/shipment-entries/buscar-guia', [ShipmentEntryController::class, 'getGuideData'])->name('buscar_guia');
Route::put('/shipment-entries/modificar-guia/{id}', [ShipmentEntryController::class, 'modifyGuideData'])->name('modificar_guia');

Route::get('/shipment-manifest/buscar-agencia', [ShipmentManifestController::class, 'searchAgencyByRoute'])->name('buscar_agencia');
Route::get('/shipment_manifest/buscar-ruta-por-agencia', [ShipmentManifestController::class, 'searchRouteByAgency'])->name('buscar_ruta_por_agencia');
Route::get('/shipment-manifest/buscar-manifiesto-por-origen-y-ruta', [ShipmentManifestController::class, 'searchManifestByOriginAndRoute'])->name('buscar_manifiesto_por_origen_y_ruta');
Route::get('/shipment-manifest/crear-manifiesto', [ShipmentManifestController::class, 'newManifestByRoute'])->name('crear_manifiesto');
Route::get('/shipment-manifest/obtener-guia-manifestadas', [ShipmentManifestController::class, 'getManifestGuides'])->name('obtener_guias_manifestadas');
Route::get('/shipment_manifest/{manifest_code}/printManifestGuides', [ShipmentManifestController::class, 'printManifestGuides'])->name('imprimir_manifiesto_entrega');

Route::get('/shipment-delivery/obtener-datos-entrega', [ShipmentDeliveryController::class, 'getDataDelivery'])->name('obtener_datos_entrega');

Route::get('/payrolls/{id}/printPayroll', [PayrollController::class, 'generatePayrollReport'])->name('payroll.printPayroll');
Route::get('/payrolls/benefit-payslips', [PayrollController::class, 'generatePayslipsReport'])->name('payroll.generatePayslipsReport');
