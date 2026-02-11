<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePayrolls extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'payroll_id', 'active'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }
}
