<?php

namespace App\Services;

use Carbon\Carbon;
use App\Repositories\VacationRepository;
use Exception;

class VacationService
{
    private VacationRepository $vacationRepository;

    public function __construct(VacationRepository $vacationRepository)
    {
        $this->vacationRepository = $vacationRepository;
    }

    public function validateBusinessRules(array $data): void
    {
        $employeeEntryDate = Carbon::parse(
            $this->vacationRepository->getEmployeeEntryDate($data['employee_id'])
        );

        $year = $data['request_year'];
        $now = Carbon::now();

        $start_date = Carbon::parse($data['start_date']);
        $end_date = Carbon::parse($data['end_date']);

        $businessDays = $start_date->diffInDaysFiltered(fn($date) => $date->isWeekday(), $end_date);

        if ($year <= $employeeEntryDate->year) {
            throw new Exception('No puede aplicar a este año.');
        }

        if (!$start_date->greaterThan($employeeEntryDate->copy()->setYear($year))) {
            throw new Exception('Las fechas deben ser mayores al periodo final.');
        }

        if (($businessDays + 1) > 15 || ($businessDays + 1) < 1) {
            throw new Exception('Ingrese un rango entre 1-15 días hábiles.');
        }

        $daysDifference = $year > $now->year
            ? $employeeEntryDate->diffInDays($now)
            : $employeeEntryDate->diffInDays($employeeEntryDate->copy()->setYear($year));

        if ($daysDifference < 150) {
            throw new Exception('No cumple con los 150 días de antigüedad.');
        }
    }

    public function handleVacationHistory(array $data)
    {
        $existingVacationHistory = $this->vacationRepository->getVacationHistory(
            $data['employee_id'],
            $data['request_year']
        );

        if ($existingVacationHistory) {
            if ($data['days_requested'] > $existingVacationHistory->days_remaining) {
                throw new Exception(
                    'El empleado tiene solo ' . $existingVacationHistory->days_remaining . ' días disponibles.'
                );
            }

            $this->vacationRepository->updateVacationHistory($existingVacationHistory->id, [
                'days_used' => $existingVacationHistory->days_used + $data['days_requested'],
                'days_remaining' => $existingVacationHistory->days_remaining - $data['days_requested'],
            ]);

            return $existingVacationHistory;
        } else {
            return $this->vacationRepository->createVacationHistory([
                'employee_id' => $data['employee_id'],
                'year' => $data['request_year'],
                'period_start' => Carbon::now()->startOfYear(),
                'period_end' => Carbon::now()->endOfYear(),
                'days_allocated' => 15,
                'days_used' => $data['days_requested'],
                'days_remaining' => 15 - $data['days_requested'],
            ]);
        }
    }

    public function createVacationRecord(array $data, $vacationHistoryId)
    {
        return $this->vacationRepository->createVacation([
            'employee_id' => $data['employee_id'],
            'request_date' => $data['request_date'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'days_requested' => $data['days_requested'],
            'vacation_type_id' => $data['vacation_type_id'],
            'vacation_history_id' => $vacationHistoryId,
            'comments' => $data['comments'],
        ]);
    }
}
