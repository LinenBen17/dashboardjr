<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_payroll_id',
        'type',
        'date',
        'amount',
        'comments',
    ];

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function employeePayroll()
    {
        return $this->belongsTo(EmployeePayrolls::class);
    }
}
