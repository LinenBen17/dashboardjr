<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShipmentManifestController extends Controller
{
    public function searchAgencyByRoute(Request $request)
    {
        //En la tabla town hay un campo route_id, obtener el nombre de la ruta
        $route_id = $request->get('route_id');

        $route = DB::table('routes')
            ->where('id', $route_id)
            ->value('agency_id');

        $agency = Agency::where('id', $route)
            ->pluck('name', 'id')
            ->toArray();

        return response()->json($agency);
    }

    public function newManifestByRoute(Request $request)
    {
        $route_id = $request->get('route_id');
        $date = $request->get('date');

        $route_name = DB::table('routes')
            ->where('id', $route_id)
            ->value('prefix');

        $manifest_repeat = DB::table('shipment_manifests')
            ->where('route_id', $route_id)
            ->whereDate('date', $date)
            ->select('manifest_code')
            ->first();

        //Ultimo manifiesto creado para esa ruta
        $last_manifest = DB::table('shipment_manifests')
            ->where('route_id', $route_id)
            ->select('manifest_code')
            ->orderBy('id', 'desc')
            ->first();

        if ($manifest_repeat) {
            $newManifestCode = $last_manifest->manifest_code;
        } elseif ($last_manifest && $last_manifest->manifest_code) {
            $last_code_number = explode('-', $last_manifest->manifest_code)[1];
            $newManifestCode = $route_name . '-' . ($last_code_number + 1);
        } else {
            $newManifestCode = $route_name . '-1';
        }

        return response()->json([
            'manifest_code' => $newManifestCode,
            'manifest_repeat' => $manifest_repeat ? true : false,
        ]);
    }

    public function getManifestGuides(Request $request)
    {
        $route_id = $request->get('route_id');
        $date = $request->get('date');

        // Obtener los IDs de towns que pertenecen a la ruta
        $town_ids = DB::table('towns')
            ->where('route_id', $route_id)
            ->pluck('id');

        // Obtener las guías que corresponden a esos towns
        $guides = DB::table('shipment_entries')
            ->whereIn('town_id', $town_ids)
            ->whereDate('date_guide', $date)
            ->join('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
            ->select('shipment_entries.*', 'payment_methods.name as payment_method_name')
            ->get();

        return response()->json($guides);
    }

    public function printManifestGuides($manifest_code)
    {
        $totalGuiasPorCobrar = 0;
        $totalGuiasPrepago = 0;
        $totalPiezas = 0;
        $totalContado = 0;
        $totalPorCobrar = 0;
        $totalCredito = 0;
        $totalPrepago = 0;
        $montoTotal = 0;

        $manifest_data = DB::table('shipment_manifests')
            ->where('manifest_code', $manifest_code)
            ->first();

        if (!$manifest_data) {
            abort(404, 'Manifiesto no encontrado');
        }

        $manifest_date = $manifest_data->date;

        $agency_origin_name = DB::table('agencies')
            ->where('id', $manifest_data->agency_origin_id)
            ->first();

        $agency_destination_name = DB::table('agencies')
            ->where('id', $manifest_data->agency_destination_id)
            ->first();

        $route = DB::table('routes')
            ->where('id', $manifest_data->route_id)
            ->first();

        $plates = $route->plates;

        $driver = DB::table('employees')
            ->where('id', $route->employee_id)
            ->first();

        $motherGuides = DB::table('shipment_entries')
            ->where('shipment_manifest_id', $manifest_data->id)
            ->join('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
            ->select('shipment_entries.*', 'payment_methods.name as payment_method_name')
            ->get();

        $totalGuias = count($motherGuides);

        foreach ($motherGuides as $guide) {
            $totalPiezas += intval($guide->pieces);
            $montoTotal += floatval($guide->total);

            if (strtoupper($guide->payment_method_name) == 'CONTADO') {
                $totalContado += floatval($guide->total);
            }
            if (strtoupper($guide->payment_method_name) == 'POR COBRAR') {
                $totalPorCobrar += floatval($guide->total);
                $totalGuiasPorCobrar += 1;
            }
            if (strtoupper($guide->payment_method_name) == 'CREDITO') {
                $totalCredito += floatval($guide->total);
            }
            if (strtoupper($guide->payment_method_name) == 'PREPAGO') {
                $totalPrepago += floatval($guide->total);
                $totalGuiasPrepago += 1;
            }
        }

        return view('filament.resources.shipment-manifest-resource.pages.shipment_manifest_print', [
            'manifest_code' => $manifest_data->manifest_code,
            'date' => $manifest_data->date,
            'agency_origin_name' => $agency_origin_name->name,
            'agency_destination_name' => $agency_destination_name->name,
            'route_name' => $route->prefix,
            'plates' => $plates,
            'driver_name' => ($driver != null) ? $driver->name . ' ' . $driver->last_name : 'N/A',
            'motherGuides' => $motherGuides,
            'totalGuias' => $totalGuias,
            'totalPiezas' => $totalPiezas,
            'totalContado' => $totalContado,
            'totalPorCobrar' => $totalPorCobrar,
            'totalCredito' => $totalCredito,
            'totalPrepago' => $totalPrepago,
            'montoTotal' => $montoTotal,
            'totalGuiasPorCobrar' => $totalGuiasPorCobrar,
            'totalGuiasPrepago' => $totalGuiasPrepago,
        ]);
    }
}
