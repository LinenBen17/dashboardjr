<?php

namespace App\Filament\Resources\VacationResource\Pages;

use App\Filament\Resources\VacationResource;
use App\Models\Vacation;
use App\Models\VacationHistory;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ManageVacations extends ManageRecords
{
    protected static string $resource = VacationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->using(function (array $data, string $model): Model {
                    // Validar los datos de entrada
                    $validator = Validator::make($data, [
                        'employee_id' => 'required|integer',
                        'request_year' => 'required|integer',
                        'request_date' => 'required|date',
                        'start_date' => 'required|date',
                        'end_date' => 'required|date',
                        'days_requested' => 'required|integer',
                        'vacation_type_id' => 'required|integer',
                        'comments' => 'required|string',
                    ]);

                    $validator->after(function ($validator) use ($data) {
                        try {
                            $now = Carbon::now();
                            $employeeEntryDate = Carbon::parse(DB::table('employees')
                                ->select('entry_date')
                                ->where('id', $data['employee_id'])
                                ->value('entry_date'));

                            // Obtener el año de la solicitud
                            $year = $data['request_year'];

                            // Verificar si existe un registro en vacation_histories para el año actual
                            $existingVacationHistory = DB::table('vacation_histories')
                                ->where('employee_id', $data['employee_id'])
                                ->where('year', $year)
                                ->orderBy('created_at', 'desc')
                                ->first();

                            // Definir los periodos de cálculo
                            $startPeriod = $employeeEntryDate->copy()->setYear($year - 1);
                            $endPeriod = $employeeEntryDate->copy()->setYear((int)$year);

                            // Definir las fechas de inicio y fin
                            $start_date = Carbon::parse($data['start_date']);
                            $end_date = Carbon::parse($data['end_date']);

                            // Calcular días hábiles
                            $businessDays = $start_date->diffInDaysFiltered(function (Carbon $date) {
                                return $date->isWeekday();
                            }, $end_date);

                            // Validaciones
                            if ($year <= $employeeEntryDate->year) {
                                $validator->errors()->add('request_year', 'No puede aplicar a este año.');
                                Notification::make()
                                    ->title('Error en el campo Año de solicitud')
                                    ->body('No puede aplicar a este año.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                                throw new ValidationException($validator);
                            }

                            if (!$start_date->greaterThan($endPeriod)) {
                                Logger()->error('Start date: ' . $start_date);
                                Logger()->error('End date: ' . $endPeriod);
                                $validator->errors()->add('start_date', 'Las fechas deben ser mayores al periodo final');
                                Notification::make()
                                    ->title('Error en el campo Fecha de inicio')
                                    ->body('Las fechas deben ser mayores al periodo final.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                                throw new ValidationException($validator);
                            }

                            if (!$end_date->greaterThan($endPeriod)) {
                                $validator->errors()->add('end_date', 'Las fechas deben ser mayores al periodo final');
                                Notification::make()
                                    ->title('Error en el campo Fecha de fin')
                                    ->body('Las fechas deben ser mayores al periodo final.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                                throw new ValidationException($validator);
                            }

                            if (($businessDays + 1) > 15 || ($businessDays + 1) < 1) {
                                $validator->errors()->add('end_date', 'Ingrese un rango entre 1-15 días hábiles.');
                                Notification::make()
                                    ->title('Error en el campo Fecha de fin')
                                    ->body('Ingrese un rango entre 1-15 días hábiles.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                                throw new ValidationException($validator);
                            }

                            if ($data['days_requested'] > 15) {
                                $validator->errors()->add('days_requested', 'Ingrese un rango entre 1-15 días hábiles.');
                                Notification::make()
                                    ->title('Error en el campo Días solicitados')
                                    ->body('Ingrese un rango entre 1-15 días hábiles.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                                throw new ValidationException($validator);
                            }

                            if ($data['days_requested'] != ($businessDays + 1)) {
                                $validator->errors()->add('days_requested', 'Las fechas no coinciden con los días solicitados.');

                                Notification::make()
                                    ->body('Diferencia en fechas dadas: ' . $businessDays + 1 . '. Días solicitados: ' . $data['days_requested'])
                                    ->color('warning')
                                    ->warning()
                                    ->persistent()
                                    ->send();
                                Notification::make()
                                    ->title('Cantidad de Días no coincide')
                                    ->body('Las fechas no coinciden con los días solicitados.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                                throw new ValidationException($validator);
                            }

                            // Calcular antigüedad
                            if ($year > $now->year) {
                                $daysDifference = $startPeriod->diffInDays($now);
                            } elseif ($year == $now->year) {
                                $daysDifference = $startPeriod->diffInDays($now);
                            } else {
                                $daysDifference = $startPeriod->diffInDays($endPeriod);
                            }

                            if ($daysDifference < 150) {
                                $validator->errors()->add('request_year', 'No cumple con los 150 días de antigüedad.');
                                Notification::make()
                                    ->title('Error en el campo Año de solicitud')
                                    ->body('No cumple con los 150 días de antigüedad.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                                throw new ValidationException($validator);
                            }

                            // Validar días disponibles
                            if ($existingVacationHistory && $data['days_requested'] > $existingVacationHistory->days_remaining) {
                                $validator->errors()->add('days_requested', 'El empleado tiene ' . $existingVacationHistory->days_remaining . ' días disponibles.');
                                Notification::make()
                                    ->title('Error en el campo Días solicitados')
                                    ->body('El empleado tiene ' . $existingVacationHistory->days_remaining . ' días disponibles.')
                                    ->danger()
                                    ->persistent()
                                    ->send();
                                throw new ValidationException($validator);
                            }
                        } catch (\Exception $e) {
                            $validator->errors()->add('employee', 'Hubo un error al crear la solicitud de vacaciones.');
                            throw new ValidationException($validator);
                        }
                    });

                    // Lanzar excepción si la validación falla
                    if ($validator->fails()) {
                        throw new ValidationException($validator);
                    }

                    // Ejecutar la lógica de creación dentro de una transacción
                    return DB::transaction(function () use ($data, $model) {
                        $year = $data['request_year'];
                        $employeeEntryDate = Carbon::parse(DB::table('employees')
                            ->select('entry_date')
                            ->where('id', $data['employee_id'])
                            ->value('entry_date'));
                        $startPeriod = $employeeEntryDate->copy()->setYear($year - 1);
                        $endPeriod = $employeeEntryDate->copy()->setYear((int)$year);

                        // Verificar si existe un registro en vacation_histories
                        $existingVacationHistory = VacationHistory::where('employee_id', $data['employee_id'])
                            ->where('year', $year)
                            ->orderBy('created_at', 'desc')
                            ->first();

                        if ($existingVacationHistory) {
                            // Actualizar el registro existente
                            $existingVacationHistory->update([
                                'days_used' => $existingVacationHistory->days_used + $data['days_requested'],
                                'days_remaining' => $existingVacationHistory->days_remaining - $data['days_requested'],
                            ]);
                            $vacationHistory = $existingVacationHistory;
                        } else {
                            // Crear un nuevo registro en vacation_histories
                            $vacationHistory = VacationHistory::create([
                                'employee_id' => $data['employee_id'],
                                'year' => $year,
                                'period_start' => $startPeriod,
                                'period_end' => $endPeriod,
                                'days_allocated' => 15,
                                'days_used' => $data['days_requested'],
                                'days_remaining' => 15 - $data['days_requested'],
                            ]);
                        }

                        // Crear el registro de vacaciones
                        return $model::create([
                            'employee_id' => $data['employee_id'],
                            'request_date' => $data['request_date'],
                            'start_date' => $data['start_date'],
                            'end_date' => $data['end_date'],
                            'days_requested' => $data['days_requested'],
                            'vacation_type_id' => $data['vacation_type_id'],
                            'vacation_history_id' => $vacationHistory->id,
                            'comments' => $data['comments'],
                        ]);
                    });
                })
        ];
    }
}
