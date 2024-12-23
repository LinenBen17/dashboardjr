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

    private function validateDateRange(MoonShineRequest $request, $validator): void
    {
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
    }

    public function getPayroll(MoonShineRequest $request): Response
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|date',
            'to' => 'required|date',
            'payroll' => 'required|string',
        ]);

        $from = Carbon::parse($request->from);
        $to = Carbon::parse($request->to);
        $payroll = $request->payroll;

        $this->validateDateRange($request, $validator);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $payrollData = $this->payrollRepository->getPayrollDataWithCalculations($from->format('Y-m-d'), $to->format('Y-m-d'), $payroll);

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
        
        $from = Carbon::parse($request->from);
        $to = Carbon::parse($request->to);
        $payroll = $request->payroll;
        $employeeId = $request->employee_id ? $request->employee_id : null;

        $this->validateDateRange($request, $validator);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $payrollData = $this->payrollRepository->getPayrollDataWithCalculations($from->format('Y-m-d'), $to->format('Y-m-d'), $payroll, $employeeId);
        
        return response()->view('reports.payslips', [
            'payrollData' => $payrollData,
            'from' => $from->format('d-m-Y'),
            'to' => $to->format('d-m-Y'),
            'employeeId' => $employeeId,
        ]);
    }

    public function getBenefitPayroll(MoonShineRequest $request): Response
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|date',
            'to' => 'required|date',
            'payroll' => 'required|string',
            'benefit' => 'required|string',
        ]);

        $from = Carbon::parse($request->from);
        $to = Carbon::parse($request->to);
        $payroll = $request->payroll;
        $benefit = $request->benefit;

        $validator->after(function ($validator) use ($request) {
            try {
                $from = Carbon::parse($request->from);
                $to = Carbon::parse($request->to);

                if ($from->diffInDays($to) < 365 || $from->diffInDays($to) > 365) {
                    $validator->errors()->add('from', 'El rango de fecha debe ser de un año.');
                    $validator->errors()->add('to', 'El rango de fechas debe ser de un año.');
                    logger($from->diffInDays($to)); 
                }

            } catch (\Exception $e) {
                $validator->errors()->add('from', 'Formato de fecha inválido.');
                $validator->errors()->add('to', 'Formato de fecha inválido.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $payrollData = $this->payrollRepository->getPayrollDataWithCalculations($from->format('Y-m-d'), $to->format('Y-m-d'), $payroll, null, $benefit);

        return response()->view('reports.benefit_payroll', [
            'payrollData' => $payrollData,
            'from' => $from->format('d-m-Y'),
            'to' => $to->format('d-m-Y'),
            'benefit' => $benefit,
        ]);
    }
}