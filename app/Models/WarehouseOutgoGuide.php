<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseOutgoGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_outgo_id',
        'guide_number',
        'pieces',
        'mother_guide',
        'child_guide',
        'scanned_at',
    ];

    public function warehouseOutgo()
    {
        return $this->belongsTo(WarehouseOutgo::class, 'warehouse_outgo_id');
    }
}
