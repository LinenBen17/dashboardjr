<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function __invoke($id)
    {
        Carbon::setLocale('es'); // Configura el idioma a español
        // Unir datos de tabla vacations y vacations_histories, y de employees para obtener datos en base el id
        try {
            $loan = DB::table('loans')
                ->join('employees', 'loans.employee_id', '=', 'employees.id')
                ->select('loans.*', 'employees.name', 'employees.last_name', 'employees.dpi')
                ->where('loans.id', $id)
                ->first();
        } catch (\Throwable $th) {
            Logger($th);
        }

        return response()->view('filament.resources.loans.loan_format', [
            'id' => $loan->id,
            'employee_name' => $loan->name . ' ' . $loan->last_name,
            'employee_dpi' => $loan->dpi,
            'created_at' => Carbon::parse($loan->created_at)->translatedFormat('d \d\e F \d\e\l Y'),
            'amount_loan' => $loan->amount_loan,
            'no_share' => $loan->no_share,
            'amount_share' => $loan->amount_share,
            'comments' => $loan->comments,
        ]);
    }
}
