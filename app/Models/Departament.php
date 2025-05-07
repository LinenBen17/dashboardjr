<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departament extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'status', 'prefix'];

    public function agencies()
    {
        return $this->hasMany(Agency::class, 'departament_id');
    }
    public function employees()
    {
        return $this->hasMany(Employee::class, 'departament_id');
    }
    public function warehouses()
    {
        return $this->hasMany(Warehouses::class, 'departament_id');
    }
}
