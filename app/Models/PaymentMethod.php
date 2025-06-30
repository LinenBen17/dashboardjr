<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'payment_method_id');
    }
    public function shipmentEntries()
    {
        return $this->hasMany(ShipmentEntry::class, 'payment_method_id');
    }
}
