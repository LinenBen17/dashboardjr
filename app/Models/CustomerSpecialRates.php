<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerSpecialRates extends Model
{
    protected $fillable = [
        'customer_id',
        'product_id',
        'special_price',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
