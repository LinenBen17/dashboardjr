<?php

declare(strict_types=1);

namespace App\MoonShine\Controllers;

use App\Models\Vacation;
use App\Models\VacationHistory;
use Carbon\Carbon;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use MoonShine\MoonShineRequest;
use MoonShine\Http\Controllers\MoonShineController;
use Symfony\Component\HttpFoundation\Response;

final class VacationController extends MoonShineController
{
    public function store(MoonShineRequest $request): Response
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer',
            'request_year' => 'required|integer',
            'request_date' => 'required|date',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'days_requested' => 'required|integer',
            'vacation_type_id' => 'required|integer',
            'comments' => 'required|string',
        ]);

        $validator->after(function ($validator) use ($request) {
            try {
                $now = Carbon::now();
                $employeeEntryDate = Carbon::parse(DB::table('employees')
                    ->select('entry_date')
                    ->where('id', $request->employee_id)
                    ->value('entry_date'));

                $vacation_histories = null; // Inicializar la variable 

                // Obtener el año de la solicitud
                $year = $request->request_year;

                // Verificar si existe un registro en vacation_histories para el año actual
                $existingVacationHistory = DB::table('vacation_histories')
                    ->where('employee_id', $request->employee_id)
                    ->where('year', $year)
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Definir los periodos de cálculo
                $startPeriod = $employeeEntryDate->copy()->setYear($year - 1);
                $endPeriod = $employeeEntryDate->copy()->setYear((int)$year);

                // Definir las fechas de inicio y fin
                $start_date = Carbon::parse($request->start_date);
                $end_date = Carbon::parse($request->end_date);

                // Calcular días hábiles
                $businessDays = $start_date->diffInDaysFiltered(function (Carbon $date) {
                    return $date->isWeekday(); // Cuenta solo si es un día hábil (no sábado ni domingo)
                }, $end_date);

                // Validar si el año ingresado es válido
                if ($year <= $employeeEntryDate->year) {
                    $this->toast('No puede aplicar a este año.', 'error');
                    $validator->errors()->add('request_year', 'No puede aplicar a este año.');
                }

                // Validar si la fecha de inicio es menor a la fecha de fin
                if (!$start_date->greaterThan($endPeriod)) {
                    $this->toast('Las fechas deben ser mayores al periodo final', 'error');
                    $validator->errors()->add('start_date', 'Las fechas deben ser mayores al periodo final');
                }

                // Validar si la fecha de fin es menor a la fecha de fin
                if (!$end_date->greaterThan($endPeriod)) {
                    $this->toast('Las fechas deben ser mayores al periodo final', 'error');
                    $validator->errors()->add('end_date', 'Las fechas deben ser mayores al periodo final');
                }

                // Validar si el rango de días solicitados es mayor a 15
                if (($businessDays + 1) > 15 || ($businessDays + 1) < 1) {
                    $this->toast('Ingrese un rango entre 1-15 días.', 'error');
                    $validator->errors()->add('end_date', 'Ingrese un rango entre 1-15 días hábiles.');
                }

                // Validar si los días solicitados son mayores a 15
                if ($request->days_requested > 15) {
                    $this->toast('Ingrese un rango entre 1-15 días hábiles.', 'error');
                    $validator->errors()->add('request_date', 'Ingrese un rango entre 1-15 días hábiles.');
                }

                // Calcular la antigüedad en días dependiendo del año
                if ($year > $now->year) {
                    $daysDifference = $startPeriod->diffInDays($now);
                } elseif ($year == $now->year) {
                    $daysDifference = $startPeriod->diffInDays($now);
                } else {
                    $daysDifference = $startPeriod->diffInDays($endPeriod);
                }

                // Validar antigüedad mínima de 150 días
                if ($daysDifference < 150) {
                    $this->toast('No cumple con los 150 días de antigüedad.', 'error');
                    $validator->errors()->add('request_year', 'No cumple con los 150 días de antigüedad.');
                }

                //validar si ya existe un registro para este año
                if ($existingVacationHistory) {
                    // Obtener el valor del campo days_remaining
                    $daysRemaining = $existingVacationHistory->days_remaining;
                    // Validar si los días solicitados son mayores a los días restantes
                    if ($request->days_requested > $daysRemaining) {
                        $this->toast('No puede solicitar más días de los que tiene disponibles.', 'error');
                        $validator->errors()->add('days_requested', 'El empleado tiene ' . $daysRemaining . ' días disponibles.');
                    } else {
                        // Actualizar el registro de vacation_histories
                        DB::table('vacation_histories')
                            ->where('id', $existingVacationHistory->id)
                            ->update([
                                'days_used' => $existingVacationHistory->days_used + $request->days_requested,
                                'days_remaining' => $existingVacationHistory->days_remaining - $request->days_requested,
                            ]);

                        $vacation_histories = VacationHistory::find($existingVacationHistory->id);
                    }
                } else {
                    // Crear el registro de vacation_histories
                    $vacation_histories = VacationHistory::create([
                        'employee_id' => $request->employee_id,
                        'year' => $year,
                        'period_start' => $startPeriod,
                        'period_end' => $endPeriod,
                        'days_allocated' => 15,
                        'days_used' => $request->days_requested,
                        'days_remaining' => 15 - $request->days_requested,
                    ]);
                }

                $vacation_histories_id = $vacation_histories->id;

                // Crear el registro de vacaciones
                $vacation = Vacation::create([
                    'employee_id' => $request->employee_id,
                    'request_date' => $request->request_date,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'days_requested' => $request->days_requested,
                    'vacation_type_id' => $request->vacation_type_id,
                    'vacation_history_id' => $vacation_histories_id,
                    'comments' => $request->comments,
                ]);

                $vacation_id = $vacation->id;
            } catch (\Exception $e) {
                $validator->errors()->add('employee', 'Hubo un error al crear la solicitud de vacaciones.');
            }
        });

        if ($validator->fails()) {
            $this->toast('Verifica que todos los campos estén correctamente llenos.', 'error');
            return back()->withErrors($validator)->withInput();
        }

        $this->toast('Permiso de vacaciones creado correctamente.', 'success');

        return back();
    }
    public function delete(MoonShineRequest $request, $id): Response
    {
        try {
            $vacation = Vacation::find($id);
            $vacation_history = VacationHistory::find($vacation->vacation_history_id);

            $vacation_history->days_used = $vacation_history->days_used - $vacation->days_requested;
            $vacation_history->days_remaining = $vacation_history->days_remaining + $vacation->days_requested;

            $vacation_history->save();
            $vacation->delete();

            $this->toast('Permiso de vacaciones eliminado correctamente.', 'success');
        } catch (\Throwable $th) {
            $this->toast('Hubo un error al eliminar el permiso de vacaciones.', 'error');
        }

        return back();
    }
    public function getVacationFormat(MoonShineRequest $request, $id): Response
    {
        // Unir datos de tabla vacations y vacations_histories, y de employees para obtener datos en base el id
        try {
            $vacation = DB::table('vacations')
                ->join('vacation_histories', 'vacations.vacation_history_id', '=', 'vacation_histories.id')
                ->join('employees', 'vacations.employee_id', '=', 'employees.id')
                ->join('charges', 'employees.id_charge', '=', 'charges.id')
                ->select('vacations.*', 'vacation_histories.year', 'employees.name', 'employees.last_name', 'employees.entry_date', 'charges.name as charge')
                ->where('vacations.id', $id)
                ->first();

            Logger($vacation);
        } catch (\Throwable $th) {
            Logger($th);
            //throw $th;
        }

        return response()->view('vacations.vacation_format', [
            'id' => $vacation->id,
            'charge' => $vacation->charge,
            'employee_name' => $vacation->name . ' ' . $vacation->last_name,
            'employee_entry_date' => Carbon::parse($vacation->entry_date)->format('d/m/Y'),
            'request_date' => $vacation->request_date,
            'start_date' => Carbon::parse($vacation->start_date)->format('d/m/Y'),
            'end_date' => Carbon::parse($vacation->end_date)->format('d/m/Y'),
            'days_requested' => $vacation->days_requested,
            'comments' => $vacation->comments,
            'year' => $vacation->year,
        ]);
    }
}
