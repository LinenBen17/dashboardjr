<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentEntry extends Model
{
    use HasFactory;
    protected $fillable = [
        'mother',
        'mother_guide_id',
        'sender_code',
        'sender_name',
        'sender_address',
        'sender_phone',
        'receiver_code',
        'receiver_name',
        'receiver_address',
        'receiver_phone',
        'prefix_origin',
        'prefix_destination',
        'town_id',
        'product_id',
        'product_description',
        'pieces',
        'unit_price',
        'sender_total',
        'receiver_total',
        'total',
        'date_guide',
        'payment_method_id',
        'no_manifest'
    ];

    public function towns()
    {
        return $this->belongsTo(Town::class, 'town_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
