<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'departament_id', 'short'];

    public function departament()
    {
        return $this->belongsTo(Departament::class, 'departament_id');
    }
    public function town()
    {
        return $this->hasMany(Town::class, 'agency_id');
    }

    /* public function employees()
    {
        return $this->hasMany(Employee::class, 'id_agency');
    }
    public function reports()
    {
        return $this->hasMany(Report::class, 'id_agency');
    } */
}
