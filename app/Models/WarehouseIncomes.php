<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseIncomes extends Model
{
    use HasFactory;
    protected $fillable = [
        'manifest_code',
        'arrived_date',
        'warehouse_id',
        'route_id',
        'driver',
        'person_scans',
        'total_pieces',
        'total_guides'
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
        return $this->hasMany(WarehouseIncomeGuide::class, 'warehouse_income_id');
    }

    public function person_scans()
    {
        return $this->belongsTo(Employee::class, 'person_scans');
    }
}
