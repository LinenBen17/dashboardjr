<?php

namespace App\Observers;

use App\Models\DetailPayroll;
use App\Models\District;

class DistrictObserver
{
    /**
     * Handle the District "created" event.
     */
    public function created(District $district): void
    {
        //
    }

    public function saved(District $district)
    {
        // Solo actualizar si el año del registro coincide con el año actual
        $currentYear = now()->year;

        if ((intval($district->year) == intval($currentYear)) && $district->name == 'CE1') {
            $salary = District::where('name', 'CE1')
                ->where('year', $currentYear)
                ->value('salary');
            $district = District::where('year', now()->year)
                ->where('name', 'CE1')
                ->first();

            // Actualizar todos los salarios regulares en la tabla `detail_payrolls` para el año actual con Circunscripcion 1
            DetailPayroll::query()
                ->where('district_id', $district->id)
                ->update(['regular_salaries' => $salary]);
        }elseif ((intval($district->year) == intval($currentYear)) && $district->name == 'CE2') {
            $salary = District::where('name', 'CE2')
                ->where('year', $currentYear)
                ->value('salary');
            $district = District::where('year', now()->year)
                ->where('name', 'CE2')
                ->first();

            DetailPayroll::query()
                ->where('district_id', $district->id)
                ->update(['regular_salaries' => $salary]);
        }
    }

    /**
     * Handle the District "updated" event.
     */
    public function updated(District $district): void
    {
        //
    }

    /**
     * Handle the District "deleted" event.
     */
    public function deleted(District $district): void
    {
        //
    }

    /**
     * Handle the District "restored" event.
     */
    public function restored(District $district): void
    {
        //
    }

    /**
     * Handle the District "force deleted" event.
     */
    public function forceDeleted(District $district): void
    {
        //
    }
}
