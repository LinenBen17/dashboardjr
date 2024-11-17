<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    public function agencies()
    {
        return $this->belongsTo(Agency::class, 'id_agency');
    }
}
