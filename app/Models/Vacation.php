<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'request_date',
        'start_date',
        'end_date',
        'days_requested',
        'vacation_type_id',
        'vacation_history_id',
        'comments',
    ];

    public function employees()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function vacationType()
    {
        return $this->belongsTo(VacationType::class, 'vacation_type_id');
    }

    public function vacationHistory()
    {
        return $this->belongsTo(VacationHistory::class, 'vacation_history_id');
    }
}
