<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriodDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    private function buildPayrollData($payrollPeriodId)
    {
        $period = DB::table('payroll_periods')
            ->where('id', $payrollPeriodId)
            ->first();

        $payroll = DB::table('payrolls')
            ->where('id', $period->payroll_id)
            ->first();

        $employees = DB::table('payroll_period_details as ppd')
            ->join('employees as e', 'ppd.employee_id', '=', 'e.id')
            ->join('employee_payrolls as ep', function ($join) use ($period) {
                $join->on('ep.employee_id', '=', 'e.id')
                    ->where('ep.payroll_id', '=', $period->payroll_id);
            })
            ->join('agencies as ag', 'e.id_agency', '=', 'ag.id')
            ->join('charges as ch', 'e.id_charge', '=', 'ch.id')
            ->where('ppd.payroll_period_id', $payrollPeriodId)
            ->select(
                'ppd.id as ppd_id',
                'ep.id as employee_payroll_id',
                'e.name',
                'e.last_name',
                'e.bank_account',
                'e.entry_date as fechaIngreso',
                'ag.name as agency_name',
                'ch.name as charge_name',
                'ppd.salary_base',
                'ppd.bonus_of_law',
                'ppd.incentive_bonus',
                'ppd.igss',
                'ppd.isr',
                'ppd.phone_discount',
                'ppd.total_pay'
            )
            ->get();


        $bonuses = DB::table('bonuses')
            ->whereBetween('date', [$period->period_start, $period->period_end])
            ->select(
                'employee_payroll_id',
                DB::raw('SUM(amount) as total_bonus')
            )
            ->groupBy('employee_payroll_id')
            ->pluck('total_bonus', 'employee_payroll_id');

        $discounts = DB::table('discounts')
            ->whereBetween('date', [$period->period_start, $period->period_end])
            ->select(
                'employee_payroll_id',
                DB::raw("SUM(CASE WHEN type = 'anticipo' THEN amount ELSE 0 END) as anticipos"),
                DB::raw("SUM(CASE WHEN type = 'ausencia' THEN amount ELSE 0 END) as ausencias"),
                DB::raw("SUM(CASE WHEN type = 'otro' THEN amount ELSE 0 END) as otros")
            )
            ->groupBy('employee_payroll_id')
            ->get()
            ->keyBy('employee_payroll_id');

        $installments = DB::table('installments')
            ->join('loans', 'installments.loan_id', '=', 'loans.id')
            ->whereBetween('installments.billing_date', [$period->period_start, $period->period_end])
            ->select(
                'loans.employee_payroll_id',
                DB::raw('SUM(installments.amount) as total_installments')
            )
            ->groupBy('loans.employee_payroll_id')
            ->pluck('total_installments', 'employee_payroll_id');

        // 3. Asignar valor a variables from, to, agencies, charges, data
        $from = $period->period_start ?? null;
        $to   = $period->period_end ?? null;

        // AGENCIAS ÚNICAS
        $agencies = $employees
            ->pluck('agency_name')
            ->unique()
            ->values()
            ->toArray();

        // CARGOS ÚNICOS
        $charges = $employees
            ->pluck('charge_name')
            ->unique()
            ->values()
            ->toArray();

        // DATA DE EMPLEADOS
        $data = [];
        $counter = 1;

        foreach ($employees as $row) {
            $bonus = $bonuses[$row->employee_payroll_id] ?? 0;
            $discount = $discounts[$row->employee_payroll_id] ?? null;

            $anticipos = $discount->anticipos ?? 0;
            $ausencias = $discount->ausencias ?? 0;
            $otros     = $discount->otros ?? 0;
            $installment = $installments[$row->employee_payroll_id] ?? 0;

            $totalDevengado =
                $row->salary_base +
                $row->bonus_of_law +
                $row->incentive_bonus +
                $bonus;

            $totalDescuento =
                $row->igss +
                $row->isr +
                $row->phone_discount +
                $installment +
                $anticipos +
                $ausencias +
                $otros;

            $data[] = [
                'id' => $counter++,
                'ctaBancaria' => $row->bank_account,
                'empleado' => $row->name . ' ' . $row->last_name,
                'fechaIngreso' => $row->fechaIngreso,
                'cargo' => $row->charge_name,
                'agencia' => $row->agency_name,

                'sueldo' => $row->salary_base,
                'bonoLey' => $row->bonus_of_law,
                'bonoIncentivo' => $row->incentive_bonus,
                'bonoMonto' => $bonus,
                'totalDevengado' => $totalDevengado,

                'igss' => $row->igss,
                'isr' => $row->isr,
                'phone_discount' => $row->phone_discount,
                'anticipos' => $anticipos,
                'ausencias' => $ausencias,
                'otros' => $otros,
                'installments' => $installment,

                'totalDescuento' => $totalDescuento,
                'liquido' => $row->total_pay,

                'payroll_status' => $payroll->name,
            ];
        }

        // 4. Calcular totales
        $totals = [
            collect($data)->sum('sueldo'),
            collect($data)->sum('bonoLey'),
            collect($data)->sum('bonoIncentivo'),
            collect($data)->sum('bonoMonto'),
            collect($data)->sum('totalDevengado'),
            collect($data)->sum('anticipos'),
            collect($data)->sum('ausencias'),
            collect($data)->sum('phone_discount'),
            collect($data)->sum('installments'),
            collect($data)->sum('otros'),
            collect($data)->sum('totalDescuento'),
            collect($data)->sum('igss'),
            collect($data)->sum('isr'),
            collect($data)->sum('liquido'),
        ];

        // 5. Agrupar
        $payrollData = [
            'agencies' => $agencies,
            'charges'  => $charges,
            'data'     => $data,
            'totals'   => $totals,
        ];

        return [
            'from' => $period->period_start ?? null,
            'to'   => $period->period_end ?? null,
            'data' => $data,
            'totals' => $totals,
            'agencies' => $agencies,
            'charges' => $charges,
        ];
    }
    public function generatePayrollReport($payrollPeriodId)
    {
        $payroll_period_details = PayrollPeriodDetails::where('payroll_period_id', $payrollPeriodId)->get();

        $payrollData = $this->buildPayrollData($payrollPeriodId);

        return view('filament.resources.reports.payroll', [
            'payroll_period_details' => $payroll_period_details,
            'from' => $payrollData['from'],
            'to' => $payrollData['to'],
            'payrollData' => $payrollData,
        ]);
    }
    public function generatePayslipsReport(Request $request)
    {
        $payroll_id = $request->payroll_id;
        $year = $request->year;
        $month = $request->month;
        $period_number = $request->period_number;

        $period = DB::table('payroll_periods')
            ->where('payroll_id', $payroll_id)
            ->where('year', $year)
            ->where('period_number', $period_number)
            ->whereMonth('period_start', $month)
            ->first();

        if (!$period) {
            abort(404);
        }

        $payrollData = $this->buildPayrollData($period->id);

        return view('filament.resources.reports.payslips', [
            'from' => $payrollData['from'],
            'to' => $payrollData['to'],
            'payrollData' => $payrollData,
        ]);
    }
}
