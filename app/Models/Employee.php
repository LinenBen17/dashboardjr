<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    public function agencies()
    {
        return $this->belongsTo(Agency::class, 'id_agency');
    }

    public function payrolls()
    {
        return $this->belongsTo(Payroll::class, 'id_payroll');
    }

    public function charges()
    {
        return $this->belongsTo(Charge::class, 'id_charge');
    }
    public function towns()
    {
        return $this->belongsTo(Town::class, 'town_id');
    }
    public function departaments()
    {
        return $this->belongsTo(Departament::class, 'departament_id');
    }
    public function civilStatus()
    {
        return $this->belongsTo(CivilStatus::class, 'civil_status_id');
    }
    public function genders()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }
    public function detailPayrolls()
    {
        return $this->hasOne(DetailPayroll::class, 'employee_id');
    }
    public function bonuses()
    {
        return $this->hasMany(Bonus::class, 'employee_id');
    }
    public function discounts()
    {
        return $this->hasMany(Discount::class, 'employee_id');
    }
    public function loans()
    {
        return $this->hasMany(Loan::class, 'employee_id');
    }
    public function vacations()
    {
        return $this->hasMany(Vacation::class, 'employee_id');
    }
    public function vacationHistories()
    {
        return $this->hasMany(VacationHistory::class, 'employee_id');
    }
}
