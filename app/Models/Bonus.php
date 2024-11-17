<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    
}
