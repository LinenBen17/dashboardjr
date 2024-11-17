<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'departament_id', 'status', 'prefix'];

    public function departaments()
    {
        return $this->belongsTo(Departament::class, 'departament_id');
    }
    public function employees()
    {
        return $this->hasMany(Employee::class, 'town_id');
    }
    protected static function booted()
    {
        static::saving(function ($town) {
            // Obtener el departamento seleccionado
            $departament = $town->departaments;

            if ($departament) {
                // Obtener el prefix del departamento seleccionado
                $town->prefix = $departament->prefix;
            }
        });
    }
}
