<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Route;
use App\Models\WarehouseIncomeGuide;
use App\Models\WarehouseIncomes;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WarehouseIncomesController extends Controller
{
    public function __invoke($id)
    {
        $manifest_income = WarehouseIncomes::find($id);
        $person_scans = Employee::where('id', $manifest_income->person_scans)->first();
        $route = Route::where('id', $manifest_income->route_id)->first();
        $guides = WarehouseIncomeGuide::where('warehouse_income_id', $id)
            ->orderBy('scanned_at', 'asc')
            ->get();

        $motherGuides = WarehouseIncomeGuide::where('warehouse_income_id', $id)
            ->whereNotNull('mother_guide')
            ->pluck('mother_guide')
            ->toArray();

        $childGuides = WarehouseIncomeGuide::where('warehouse_income_id', $id)
            ->whereNotNull('child_guide')
            ->pluck('child_guide')
            ->toArray();

        // Get hour of the first and last guide
        $hora_inicio = Carbon::parse($guides->first()->scanned_at)
            ->format('H:i:s');
        $hora_fin = Carbon::parse($manifest_income->created_at)
            ->format('H:i:s');


        return view('filament.resources.warehouse_incomes.manifest_incomes', compact('manifest_income', 'route', 'guides', 'motherGuides', 'childGuides', 'hora_inicio', 'hora_fin', 'person_scans'));
    }
}
