<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPeriods extends Model
{
    use HasFactory;

    protected $fillable = ['payroll_id', 'period_start', 'period_end', 'period_number', 'year', 'status', 'closed_at'];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }
}
