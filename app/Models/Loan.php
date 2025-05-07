<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'start_date',
        'amount_loan',
        'no_share',
        'amount_share',
        'comments',
    ];

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function installments()
    {
        return $this->hasMany(Installments::class, 'loan_id');
    }
}
