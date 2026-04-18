<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class Route extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'prefix', 'employee_id', 'agency_id', 'plates'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function agency()
    {
        return $this->belongsTo(Agency::class);
    }
    public function warehouseIncomes()
    {
        return $this->hasMany(WarehouseIncomes::class);
    }
    public function warehouseOutgos()
    {
        return $this->hasMany(WarehouseOutgo::class);
    }
}
