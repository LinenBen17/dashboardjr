<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Log\Logger;

use function Laravel\Prompts\error;

class VacationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function vacation($fechaIngreso, $anio)
    {
        $now = Carbon::now();
        $employeeEntryDate = Carbon::parse($fechaIngreso);

        // Definir los periodos de cálculo
        $startPeriod = $employeeEntryDate->copy()->setYear($anio - 1);
        $endPeriod = $employeeEntryDate->copy()->setYear($anio);

        // Validar si el año ingresado es válido
        if ($anio <= $employeeEntryDate->year) {
            return "No puede aplicar a este año. Fecha de ingreso: $employeeEntryDate";
        }

        // Calcular la antigüedad en días dependiendo del año
        if ($anio > $now->year) {
            $daysDifference = $startPeriod->diffInDays($now);
        } elseif ($anio == $now->year) {
            $daysDifference = $startPeriod->diffInDays($now);
        } else {
            $daysDifference = $startPeriod->diffInDays($endPeriod);
        }

        // Validar antigüedad mínima de 150 días
        if ($daysDifference < 150) {
            return "No cumple con los 150 días de antigüedad. Inicio del periodo: $startPeriod. Días calculados: $daysDifference";
        }

        return "Cumple con los 150 días de antigüedad. Inicio del periodo: $startPeriod. Días calculados: $daysDifference";
    }


    public function run(): void
    {
        Logger($this->vacation("2020-11-11", 2025));
    }
}
