<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentManifest extends Model
{
    use HasFactory;

    protected $fillable = [
        'manifest_code',
        'date',
        'route_id',
        'agency_origin_id',
        'agency_destination_id',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }
    public function agencyOrigin()
    {
        return $this->belongsTo(Agency::class, 'agency_origin_id');
    }
    public function agencyDestination()
    {
        return $this->belongsTo(Agency::class, 'agency_destination_id');
    }
}
