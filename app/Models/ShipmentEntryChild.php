<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentEntryChild extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_entry_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function products()
    {
        return $this->belongsTo(Product::class);
    }
}
