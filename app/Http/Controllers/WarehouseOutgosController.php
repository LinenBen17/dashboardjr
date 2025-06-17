<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Route;
use App\Models\WarehouseOutgo;
use App\Models\WarehouseOutgoGuide;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WarehouseOutgosController extends Controller
{
    public function __invoke($id)
    {
        $manifest_outgo = WarehouseOutgo::find($id);
        $person_scans = Employee::where('id', $manifest_outgo->person_scans)->first();
        $route = Route::where('id', $manifest_outgo->route_id)->first();
        $guides = WarehouseOutgoGuide::where('warehouse_outgo_id', $id)
            ->orderBy('scanned_at', 'asc')
            ->get();

        $motherGuides = WarehouseOutgoGuide::where('warehouse_outgo_id', $id)
            ->whereNotNull('mother_guide')
            ->pluck('mother_guide')
            ->toArray();

        $childGuides = WarehouseOutgoGuide::where('warehouse_outgo_id', $id)
            ->whereNotNull('child_guide')
            ->pluck('child_guide')
            ->toArray();

        // Get hour of the first and last guide
        $hora_inicio = Carbon::parse($guides->first()->scanned_at)
            ->format('H:i:s');
        $hora_fin = Carbon::parse($manifest_outgo->created_at)
            ->format('H:i:s');


        return view('filament.resources.warehouse_outgos.manifest_outgos', compact('manifest_outgo', 'route', 'guides', 'motherGuides', 'childGuides', 'hora_inicio', 'hora_fin', 'person_scans'));
    }
}
