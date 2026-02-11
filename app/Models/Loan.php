<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_payroll_id',
        'start_date',
        'amount_loan',
        'no_share',
        'amount_share',
        'comments',
    ];

    public function employeePayroll()
    {
        return $this->belongsTo(EmployeePayrolls::class, 'employee_payroll_id');
    }
    public function installments()
    {
        return $this->hasMany(Installments::class, 'loan_id');
    }
}
