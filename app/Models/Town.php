<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'departament_id', 'status', 'prefix'];

    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }
    public function employees()
    {
        return $this->hasMany(Employee::class, 'town_id');
    }
}
