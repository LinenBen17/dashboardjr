<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'id_charge');
    }
}
