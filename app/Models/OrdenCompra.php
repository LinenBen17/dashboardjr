<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenCompra extends Model
{
    //
    protected $casts = [
    'fecha' => 'date',
    'items' => 'array', // JSON → array
    'total_general' => 'decimal:2',
];
}
