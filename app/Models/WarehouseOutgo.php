<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseOutgo extends Model
{
    use HasFactory;

    protected $fillable = [
        'manifest_code',
        'left_date',
        'warehouse_id',
        'origin_warehouse_id',
        'route_id',
        'driver',
        'person_scans',
        'total_pieces',
        'total_guides',
    ];

    public function warehouses()
    {
        return $this->belongsTo(Warehouses::class, 'warehouse_id');
    }
    public function routes()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }
    public function guides()
    {
        return $this->hasMany(WarehouseOutgoGuide::class, 'warehouse_outgo_id');
    }
    public function person_scans()
    {
        return $this->belongsTo(Employee::class, 'person_scans');
    }
}
