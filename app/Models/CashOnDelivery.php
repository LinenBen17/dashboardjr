<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashOnDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_entry_id',
        'no_pce',
        'amount',
        'pieces',
        'shipment_price',
        'shipment_paid_by',
        'include_commission',
        'commission_rate',
        'commission_amount',
        'total_receiver',
        'total_sender',
        'per_piece_receiver',
        'per_piece_sender',
    ];
}
