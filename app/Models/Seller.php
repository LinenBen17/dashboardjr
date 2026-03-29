<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'alias', 'code'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
