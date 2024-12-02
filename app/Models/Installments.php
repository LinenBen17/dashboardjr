<?php

namespace App\Models;

use App\Enums\StatusEnum;
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
    
    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
}
