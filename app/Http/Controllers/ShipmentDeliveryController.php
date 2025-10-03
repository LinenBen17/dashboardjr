<?php

namespace App\Http\Controllers;

use App\Models\ShipmentDelivery;
use Illuminate\Http\Request;

class ShipmentDeliveryController extends Controller
{
    public function getDataDelivery(Request $request)
    {
        $guideNumber = $request->get('guide');

        $shipment_delivery = ShipmentDelivery::where('shipment_entry_id', function ($query) use ($guideNumber) {
            $query->select('id')
                ->from('shipment_entries')
                ->where('mother', $guideNumber)
                ->limit(1);
        })->first();

        return response()->json($shipment_delivery);
    }
}
