<?php

namespace App\Http\Controllers;

use App\Models\ShipmentEntry;
use App\Models\Town;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ShipmentEntryController extends Controller
{
    public function printMother(ShipmentEntry $shipment)
    {
        return view('filament.resources.shipment_entries.print-mother', compact('shipment'));
    }

    public function searchTown(Request $request)
    {
        $prefix = $request->get('prefix');

        // clave de cache única por cada prefix
        $cacheKey = "municipios_prefix_$prefix";

        $municipios = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($prefix) {
            return Town::query()
                ->join('routes', 'towns.route_id', '=', 'routes.id')
                ->join('agencies', 'routes.agency_id', '=', 'agencies.id')
                ->join('departaments', 'agencies.departament_id', '=', 'departaments.id')
                ->where('departaments.prefix', $prefix)
                ->orderBy('towns.name')
                ->pluck('towns.name', 'towns.id');
        });


        return response()->json($municipios);
    }

    public function searchProductDetail(Request $request)
    {
        $idProduct = $request->get('code');

        $productDescription = DB::table('products')
            ->where('id', $idProduct)
            ->value('name');

        $productPrice = DB::table('products')
            ->where('id', $idProduct)
            ->value('price');

        return response()->json([
            'description' => $productDescription,
            'price' => $productPrice,
        ]);
    }

    public function searchRoute(Request $request)
    {
        //En la tabla town hay un campo route_id, obtener el nombre de la ruta
        $town_id = $request->get('town_id');

        $routeId = DB::table('towns')
            ->where('id', $town_id)
            ->value('route_id');

        $route = DB::table('routes')
            ->where('id', $routeId)
            ->value('prefix');

        return response()->json($route);
    }

    public function getCustomerData(Request $request)
    {
        $customer_code = $request->get('code');

        $customer_data = DB::table('customers')
            ->where('code', 'LIKE', '%-' . $customer_code)
            ->select('name', 'address', 'phone')
            ->first();

        return response()->json($customer_data);
    }

    public function getCustomers(Request $request)
    {
        $customers = DB::table('customers')
            ->select('code', 'name', 'address', 'phone')
            ->get();

        Logger($customers);

        return response()->json($customers);
    }

    public function getGuideData(Request $request)
    {
        $guide = $request->get('guide');

        // Obtener datos generales de la guía
        $guide_data = DB::table('shipment_entries')
            ->where('mother', $guide)
            ->join('payment_methods', 'shipment_entries.payment_method_id', '=', 'payment_methods.id')
            ->join('users', 'shipment_entries.created_by', '=', 'users.id')
            ->join('departaments', DB::raw("JSON_UNQUOTE(JSON_EXTRACT(users.custom_fields, '$.departament_id'))"), '=', 'departaments.id')
            ->select(
                'shipment_entries.*',
                'payment_methods.name as payment_method', // Traemos la forma de pago
                'users.name as created_by', // Traemos el nombre del usuario que creó la guía
                'departaments.name as departament_name' // Traemos el nombre del departamento
            )
            ->first();

        // Tracking de la guía
        // Ingresos a Bodega de la guía
        $guide_incomes = DB::table('warehouse_income_guides')
            ->where('guide_number', $guide)
            ->join('warehouse_incomes', 'warehouse_income_guides.warehouse_income_id', '=', 'warehouse_incomes.id')
            ->join('warehouses', 'warehouse_incomes.warehouse_id', '=', 'warehouses.id')
            ->join('routes', 'warehouse_incomes.route_id', '=', 'routes.id')
            ->join('users', 'warehouse_incomes.person_scans', '=', 'users.id')
            ->select(
                'warehouse_income_guides.*',
                'warehouse_incomes.manifest_code',
                'users.name as user_name', // Traemos el nombre del usuario que escaneó
                'warehouses.name as warehouse_name', // Traemos el nombre de la bodega
                'routes.name as route_name', // Traemos el nombre de la ruta
                'routes.plates as route_plate' // Traemos la placa de la ruta
            )
            ->orderBy('warehouse_income_guides.scanned_at', 'asc')
            ->get();

        // Salidas de bodega de la guía
        $guide_outgos = DB::table('warehouse_outgo_guides')
            ->where('guide_number', $guide)
            ->join('warehouse_outgos', 'warehouse_outgo_guides.warehouse_outgo_id', '=', 'warehouse_outgos.id')
            ->join('warehouses as destination', 'warehouse_outgos.warehouse_id', '=', 'destination.id')
            ->join('warehouses as origin', 'warehouse_outgos.origin_warehouse_id', '=', 'origin.id')
            ->join('routes', 'warehouse_outgos.route_id', '=', 'routes.id')
            ->join('users', 'warehouse_outgos.person_scans', '=', 'users.id')
            ->select(
                'warehouse_outgo_guides.*',
                'warehouse_outgos.manifest_code',
                'users.name as user_name',
                'destination.name as destination_name', // nombre de bodega destino
                'origin.name as origin_name',           // nombre de bodega origen
                'routes.name as route_name',
                'routes.plates as route_plate'
            )
            ->orderBy('warehouse_outgo_guides.scanned_at', 'asc')
            ->get();


        // Formatear solo la fecha de date_guide
        if ($guide_data) {
            $guide_data->date_guide = \Carbon\Carbon::parse($guide_data->date_guide)->format('d/m/Y');
        }
        return response()->json([
            'guide_data' => $guide_data,
            'guide_incomes' => $guide_incomes,
            'guide_outgos' => $guide_outgos,
        ]);
    }
}
