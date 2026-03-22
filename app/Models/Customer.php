<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'address',
        'phone',
        'email',
        'contact_name',
        'contact_phone',
        'payment_method_id',
        'departament_id',
        'town_id',
        'prefix_origin',
        'employee_id',
    ];

    public function paymentMethods()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
    public function departaments()
    {
        return $this->belongsTo(Departament::class, 'departament_id');
    }
    public function towns()
    {
        return $this->belongsTo(Town::class, 'town_id');
    }
    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function shipment_entries()
    {
        return $this->hasMany(ShipmentEntry::class, 'customer_id');
    }

    public function customerSpecialRates()
    {
        return $this->hasMany(CustomerSpecialRates::class, 'customer_id');
    }

    
}
