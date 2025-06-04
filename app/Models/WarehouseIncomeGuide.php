<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseIncomeGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_income_id',
        'guide_number',
        'pieces',
        'mother_guide',
        'child_guide',
        'scanned_at',
    ];

    public function warehouseIncome()
    {
        return $this->belongsTo(WarehouseIncomes::class);
    }
}
