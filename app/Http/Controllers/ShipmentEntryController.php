<?php

namespace App\Http\Controllers;

use App\Models\ShipmentEntry;
use Illuminate\Http\Request;

class ShipmentEntryController extends Controller
{
    public function printMother(ShipmentEntry $shipment)
    {
        return view('filament.resources.shipment_entries.print-mother', compact('shipment'));
    }
}
