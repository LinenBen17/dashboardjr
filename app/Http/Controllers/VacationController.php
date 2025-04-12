<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;

class VacationController extends Controller
{
    public function __invoke($id)
    {
        $vacation = DB::table('vacations')
            ->join('vacation_histories', 'vacations.vacation_history_id', '=', 'vacation_histories.id')
            ->join('employees', 'vacations.employee_id', '=', 'employees.id')
            ->join('charges', 'employees.id_charge', '=', 'charges.id')
            ->select('vacations.*', 'vacation_histories.year', 'employees.id as employee_id', 'employees.name', 'employees.last_name', 'employees.entry_date', 'charges.name as charge')
            ->where('vacations.id', $id)
            ->first();

        return response()->view('filament.resources.vacations.vacation_format', [
            'id' => $vacation->employee_id,
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
