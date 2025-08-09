<?php

namespace App\Http\Controllers;

use App\Models\ShipmentEntry;
use App\Models\Town;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
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

        $municipios = Town::query()
            ->whereHas(
                'agency.departament',
                fn($q) =>
                $q->where('prefix', $prefix)
            )
            ->orderBy('name')
            ->pluck('name', 'id');

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
}
