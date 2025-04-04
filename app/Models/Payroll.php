<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory;
    protected $fillable = ['state'];
    
    public function employees()
    {
        return $this->hasMany(Employee::class, 'id_payroll');
    }
}
