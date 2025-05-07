<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'regular_salaries',
        'bonus_of_law',
        'incentive_bonus',
        'percentage_igss',
        'percentage_isr',
        'phone_discount',
        'district_id'
    ];

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function districts()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
