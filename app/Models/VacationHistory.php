<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacationHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'year',
        'period_start',
        'period_end',
        'days_allocated',
        'days_used',
        'days_remaining',
    ];

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function vacations()
    {
        return $this->hasMany(Vacation::class, 'vacation_history_id');
    }
}
