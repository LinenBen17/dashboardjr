<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateBenefit extends Model
{
    use HasFactory;
    
    protected $fillable = ['benefit_id', 'date_from', 'date_to'];

    public function reports()
    {
        return $this->hasMany(Report::class, 'date_benefit_id');
    }
}
