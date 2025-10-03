<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_entry_id',
        'received_by_name',
        'received_by_document',
        'signed',
        'status',
        'observations',
        'place_photo_path',
        'signed_receipt_path',
        'signature_path',
        'delivered_at',
        'created_by',
    ];
}
