<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPayroll extends Model
{
    use HasFactory;

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function districts()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
