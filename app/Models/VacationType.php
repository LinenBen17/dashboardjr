<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VacationType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function vacations()
    {
        return $this->hasMany(Vacation::class, 'vacation_type_id');
    }
}
