<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouses extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'departament_id', 'prefix'];

    public function departament()
    {
        return $this->belongsTo(Departament::class, 'departament_id');
    }
    public function warehouseIncomes()
    {
        return $this->hasMany(WarehouseIncomes::class);
    }
}
