<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installments extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'no_installment',
        'amount',
        'status',
        'billing_date',
    ];
}
