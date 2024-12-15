<?php

declare(strict_types=1);

namespace App\MoonShine\Controllers;

use App\Models\DetailPayroll;
use App\Repositories\PayrollRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use MoonShine\MoonShineRequest;
use MoonShine\Http\Controllers\MoonShineController;
use MoonShine\Pages\ViewPage; // Import the correct ViewPage
use Symfony\Component\HttpFoundation\Response;

final class Report extends MoonShineController
{
    protected PayrollRepository $payrollRepository;

    public function __construct(PayrollRepository $payrollRepository)
    {
        $this->payrollRepository = $payrollRepository;
    }

    public function getPayroll(MoonShineRequest $request): Response // Cambiar el tipo de retorno a Response
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|date',
            'to' => 'required|date',
            'payroll' => 'required|string',
        ]);

        $validator->after(function ($validator) use ($request) {
            try {
                $from = Carbon::parse($request->from);
                $to = Carbon::parse($request->to);
                $ultimoDiaMes = $from->copy()->endOfMonth()->day;

                $rango1 = $from->day === 1 && $to->day === 15 && $from->isSameMonth($to);
                $rango2 = $from->day === 16 && $to->day === $ultimoDiaMes && $from->isSameMonth($to);

                if (!($rango1 || $rango2)) {
                    $validator->errors()->add('from', 'Las fechas deben estar en los rangos 1-15 o 16-último día del mes.');
                    $validator->errors()->add('to', 'Las fechas deben estar en los rangos 1-15 o 16-último día del mes.');
                }

            } catch (\Exception $e) {
                $validator->errors()->add('from', 'Formato de fecha inválido.');
                $validator->errors()->add('to', 'Formato de fecha inválido.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput(); // Esto ahora es válido
        }

        $from = Carbon::parse($request->from);
        $to = Carbon::parse($request->to);
        $payroll = $request->payroll;

        /*  Funcion getPayrollData*/
        $payrollData = $this->payrollRepository->getPayrollDataWithCalculations($from->format('Y-m-d'), $to->format('Y-m-d'), $payroll);

        // Devuelve un ViewPage
        return response()->view('reports.payroll', [
            'payrollData' => $payrollData,
            'from' => $from->format('d-m-Y'),
            'to' => $to->format('d-m-Y'),
        ]);
    }
    public function getPayslips(MoonShineRequest $request): Response
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|date',
            'to' => 'required|date',
            'payroll' => 'required|string',
        ]);

        $validator->after(function ($validator) use ($request) {
            try {
                $from = Carbon::parse($request->from);
                $to = Carbon::parse($request->to);
                $ultimoDiaMes = $from->copy()->endOfMonth()->day;

                $rango1 = $from->day === 1 && $to->day === 15 && $from->isSameMonth($to);
                $rango2 = $from->day === 16 && $to->day === $ultimoDiaMes && $from->isSameMonth($to);

                if (!($rango1 || $rango2)) {
                    $validator->errors()->add('from', 'Las fechas deben estar en los rangos 1-15 o 16-último día del mes.');
                    $validator->errors()->add('to', 'Las fechas deben estar en los rangos 1-15 o 16-último día del mes.');
                }

            } catch (\Exception $e) {
                $validator->errors()->add('from', 'Formato de fecha inválido.');
                $validator->errors()->add('to', 'Formato de fecha inválido.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput(); // Esto ahora es válido
        }

        $from = Carbon::parse($request->from);
        $to = Carbon::parse($request->to);
        $payroll = $request->payroll;
    }
}