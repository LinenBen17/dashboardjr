<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class VacationRepository
{
    public function getEmployeeEntryDate(int $employeeId): ?string
    {
        return DB::table('employees')
            ->where('id', $employeeId)
            ->value('entry_date');
    }

    public function getVacationHistory(int $employeeId, int $year)
    {
        return DB::table('vacation_histories')
            ->where('employee_id', $employeeId)
            ->where('year', $year)
            ->first();
    }

    public function updateVacationHistory(int $historyId, array $data): bool
    {
        return DB::table('vacation_histories')
            ->where('id', $historyId)
            ->update($data);
    }

    public function createVacationHistory(array $data)
    {
        return DB::table('vacation_histories')->insertGetId($data);
    }

    public function createVacation(array $data)
    {
        return DB::table('vacations')->insertGetId($data);
    }
}
