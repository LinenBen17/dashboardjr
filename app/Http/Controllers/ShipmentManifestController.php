<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Carbon\Carbon;
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

    public function searchRouteByAgency(Request $request)
    {
        $agency_id = $request->get('agency_id');
        $routes = DB::table('routes')
            ->where('agency_id', $agency_id)
            ->pluck('prefix', 'id')
            ->toArray();
        return response()->json($routes);
    }

    public function searchManifestByOriginAndRoute(Request $request)
    {
        $agency_origin_id = $request->get('agency_origin_id');
        $agency_destination_id = $request->get('agency_destination_id');
        $route_id = $request->get('route_id');
        $manifest_date = Carbon::createFromFormat('d/m/Y', $request->manifest_date)
            ->format('Y-m-d');

        $manifests = DB::table('shipment_manifests')
            ->where('agency_origin_id', $agency_origin_id)
            ->where('agency_destination_id', $agency_destination_id)
            ->where('route_id', $route_id)
            ->whereDate('date', $manifest_date)
            ->value('manifest_code');

        return response()->json($manifests);
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
        $agency_origin_id = $request->get('agency_origin_id');
        $agency_destination_id = $request->get('agency_destination_id');
        $route_id = $request->get('route_id');
        $rawDate = $request->get('manifest_date');

        try {
            if (str_contains($rawDate, '/')) {
                // formato: 30/03/2026
                $date = Carbon::createFromFormat('d/m/Y', $rawDate);
            } else {
                // formato: 2026-04-07
                $date = Carbon::parse($rawDate);
            }

            $date = $date->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Formato de fecha inválido'
            ], 400);
        }

        // Obtener los IDs de towns que pertenecen a la ruta
        $town_ids = DB::table('towns')
            ->where('route_id', $route_id)
            ->pluck('id');

        $departament_origin_prefix = DB::table('agencies as a')
            ->join('departaments as d', 'a.departament_id', '=', 'd.id')
            ->where('a.id', $agency_origin_id)
            ->value('d.prefix');

        // Obtener las guías que corresponden a esos towns
        $guides = DB::table('shipment_entries')
            ->whereIn('town_id', $town_ids)
            ->whereDate('date_guide', $date)
            ->where('prefix_origin', $departament_origin_prefix)
            ->join('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
            ->select('shipment_entries.*', 'payment_methods.name as payment_method_name')
            ->orderBy('shipment_entries.mother', 'asc')
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
