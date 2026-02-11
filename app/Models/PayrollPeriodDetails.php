<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPeriodDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_period_id',
        'employee_id',
        'salary_base',
        'bonus_of_law',
        'incentive_bonus',
        'igss',
        'isr',
        'total_bonuses',
        'total_discounts',
        'phone_discount',
        'total_pay',
    ];
}
