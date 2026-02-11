<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'departament_id', 'status', 'prefix', 'route_id'];

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
    public function departament()
    {
        return $this->belongsTo(Departament::class, 'departament_id');
    }
    public function customers()
    {
        return $this->hasMany(Customer::class, 'town_id');
    }
    public function shipmentEntries()
    {
        return $this->hasMany(ShipmentEntry::class, 'town_id');
    }
}
