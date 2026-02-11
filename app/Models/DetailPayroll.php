<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPayroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_payroll_id',
        'regular_salaries',
        'bonus_of_law',
        'incentive_bonus',
        'percentage_igss',
        'percentage_isr',
        'phone_discount',
    ];

    public function employeePayroll()
    {
        return $this->belongsTo(EmployeePayrolls::class, 'employee_payroll_id');
    }
    public function districts()
    {
        return $this->belongsTo(District::class, 'district_id');
    }
}
