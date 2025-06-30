<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
    ];

    public function shipmentEntries()
    {
        return $this->hasMany(ShipmentEntry::class, 'product_id');
    }

    public function shipmentEntryChilds()
    {
        return $this->hasMany(ShipmentEntryChild::class, 'product_id');
    }
}
