<?php

namespace App\Filament\Resources\DetailPayrollResource\Pages;

use App\Filament\Resources\DetailPayrollResource;
use App\Models\DetailPayroll;
use App\Models\Payroll;
use App\Models\PayrollPeriodDetails;
use App\Models\PayrollPeriods;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Assets\Asset;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ManageDetailPayrolls extends ManageRecords
{
    protected static string $resource = DetailPayrollResource::class;

    public ?int $payrollPeriodId = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('Generar Reportes de Planilla')
                ->color('info')
                ->form([
                    Grid::make(4)
                        ->schema([
                            Select::make('payroll_id')
                                ->label('Seleccione el Tipo Planilla')
                                ->options(function () {
                                    return Payroll::all()->pluck('name', 'id')->toArray();
                                })
                                ->required(),
                            Select::make('payroll_type')
                                ->label('Seleccione Tipo de Reporte')
                                ->options([
                                    'impresion_de_planilla' => 'Impresión de Planilla',
                                    'impresion_de_boletas_de_pago' => 'Impresión de Boletas de Pago',
                                ])
                                ->required(),
                            Select::make('month')
                                ->label('Seleccione el Mes')
                                ->options([
                                    '01' => 'Enero',
                                    '02' => 'Febrero',
                                    '03' => 'Marzo',
                                    '04' => 'Abril',
                                    '05' => 'Mayo',
                                    '06' => 'Junio',
                                    '07' => 'Julio',
                                    '08' => 'Agosto',
                                    '09' => 'Septiembre',
                                    '10' => 'Octubre',
                                    '11' => 'Noviembre',
                                    '12' => 'Diciembre',
                                ])
                                ->required(),
                            TextInput::make('year')
                                ->label('Ingrese el Año')
                                ->default(date('Y'))
                                ->required(),
                            Select::make('period_number')
                                ->label('Seleccione el Periodo')
                                ->options([
                                    '1' => 'Primera Quincena',
                                    '2' => 'Fin De Mes',
                                ])
                                ->required(),
                        ]),
                ])
                ->action(function (array $data) {
                    if ($data['payroll_type'] == 'impresion_de_boletas_de_pago') {
                        $this->payrollPeriodId = null;

                        $payroll_period_details = PayrollPeriodDetails::whereHas('payrollPeriod', function ($query) use ($data) {
                            $query->where('payroll_id', $data['payroll_id'])
                                ->where('year', $data['year'])
                                ->where('period_number', $data['period_number'])
                                ->whereMonth('period_start', $data['month']);
                        })->get();
                    } elseif ($data['payroll_type'] == 'impresion_de_planilla') {
                        $exists = PayrollPeriods::where([
                            'payroll_id' => $data['payroll_id'],
                            'period_start' => $data['year'] . '-' . $data['month'] . '-' . ($data['period_number'] == 1 ? '01' : '16'),
                            'period_end' => $data['year'] . '-' . $data['month'] . '-' . ($data['period_number'] == 1 ? '15' : Carbon::create($data['year'], $data['month'], 1)->endOfMonth()->day),
                            'year' => $data['year'],
                            'period_number' => $data['period_number'],
                        ])->exists();

                        if ($exists) {
                            Notification::make()
                                ->title('La planilla para este período ya existe.')
                                ->warning()
                                ->send();

                            $this->payrollPeriodId = PayrollPeriods::where([
                                'payroll_id' => $data['payroll_id'],
                                'period_start' => $data['year'] . '-' . $data['month'] . '-' . ($data['period_number'] == 1 ? '01' : '16'),
                                'period_end' => $data['year'] . '-' . $data['month'] . '-' . ($data['period_number'] == 1 ? '15' : Carbon::create($data['year'], $data['month'], 1)->endOfMonth()->day),
                                'year' => $data['year'],
                                'period_number' => $data['period_number'],
                            ])->first()->id; // guardar ID para impresión

                            return;
                        }

                        try {
                            if ($data['period_number'] == 1) {
                                $period_start = Carbon::create($data['year'], $data['month'], 1);
                                $period_end   = Carbon::create($data['year'], $data['month'], 15);
                            } else {
                                $period_start = Carbon::create($data['year'], $data['month'], 16);
                                $period_end   = Carbon::create($data['year'], $data['month'], 1)->endOfMonth();
                            }

                            $payroll_period = PayrollPeriods::create([
                                'payroll_id' => $data['payroll_id'],
                                'period_start' => $period_start,
                                'period_end' => $period_end,
                                'period_number' => $data['period_number'],
                                'year' => $data['year'],
                                'status' => 'borrador',
                            ]);

                            // Call the command to generate payroll details
                            $employee_payrolls = DB::table('employee_payrolls')
                                ->where('payroll_id', $data['payroll_id'])
                                ->where('active', 1)
                                ->get();

                            foreach ($employee_payrolls as $employee_payroll) {
                                $detail_payroll = DetailPayroll::where('employee_payroll_id', $employee_payroll->id)->first();

                                $salary_base = floatval($detail_payroll->regular_salaries) / 2;
                                $bonus_of_law = floatval($detail_payroll->bonus_of_law) / 2;
                                $incentive_bonus = floatval($detail_payroll->incentive_bonus) / 2;
                                $igss = (floatval($detail_payroll->regular_salaries) * (floatval($detail_payroll->percentage_igss) / 100)) / 2;
                                $isr = (floatval($detail_payroll->regular_salaries) * (floatval($detail_payroll->percentage_isr) / 100)) / 2;
                                $phone_discount = $data['period_number'] == '2' ? floatval($detail_payroll->phone_discount) : 0;

                                $bonuses = DB::table('bonuses')
                                    ->where('employee_payroll_id', $employee_payroll->id)
                                    ->whereBetween('date', [$period_start, $period_end])
                                    ->sum('amount');

                                $total_bonuses = $bonuses;

                                $discounts = DB::table('discounts')
                                    ->where('employee_payroll_id', $employee_payroll->id)
                                    ->whereBetween('date', [$period_start, $period_end])
                                    ->sum('amount');

                                $total_installments = DB::table('installments')
                                    ->join('loans', 'installments.loan_id', '=', 'loans.id')
                                    ->where('loans.employee_payroll_id', $employee_payroll->id)
                                    ->whereBetween('installments.billing_date', [$period_start, $period_end])
                                    ->sum('installments.amount');

                                DB::table('installments')
                                    ->join('loans', 'installments.loan_id', '=', 'loans.id')
                                    ->where('loans.employee_payroll_id', $employee_payroll->id)
                                    ->whereBetween('installments.billing_date', [$period_start, $period_end])
                                    ->update(['status' => 1]);

                                $total_discounts = $discounts + $total_installments;

                                $total_pay = $salary_base + $bonus_of_law + $incentive_bonus + $total_bonuses - $igss - $isr - $phone_discount - $total_discounts;

                                // Here you would calculate the salary details as needed
                                PayrollPeriodDetails::create([
                                    'payroll_period_id' => $payroll_period->id,
                                    'employee_id' => $employee_payroll->employee_id,
                                    'salary_base' => $salary_base,
                                    'bonus_of_law' => $bonus_of_law,
                                    'incentive_bonus' => $incentive_bonus,
                                    'igss' => $igss,
                                    'isr' => $isr,
                                    'phone_discount' => $phone_discount,
                                    'total_bonuses' => $total_bonuses,
                                    'total_discounts' => $total_discounts,
                                    'total_installments' => $total_installments,
                                    'total_pay' => $total_pay,
                                ]);
                            }
                            $this->payrollPeriodId = $payroll_period->id; // guardar ID
                        } catch (\Throwable $th) {
                            Notification::make()
                                ->title('Error al generar la planilla: ' . $th->getMessage())
                                ->danger()
                                ->send();
                            return;
                        }
                        Notification::make()
                            ->title('Planilla generada exitosamente')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Tipo de reporte no válido.')
                            ->danger()
                            ->send();
                        return;
                    }
                })
                ->extraModalFooterActions(fn() => [
                    Actions\Action::make('ImprimirPlanilla')
                        ->label('Imprimir Planilla')
                        ->url(
                            fn() => filled($this->payrollPeriodId)
                                ? route('payroll.printPayroll', ['id' => $this->payrollPeriodId])
                                : null
                        )
                        ->openUrlInNewTab()
                        ->visible(fn() => filled($this->payrollPeriodId)),
                    Actions\Action::make('ImprimirBoletas')
                        ->label('Imprimir Boletas de Pago')
                        ->url(
                            fn() => filled($this->payrollPeriodId)
                                ? route('payroll.generatePayslipsReport', [
                                    'payroll_id' => PayrollPeriods::find($this->payrollPeriodId)->payroll_id,
                                    'month' => Carbon::parse(PayrollPeriods::find($this->payrollPeriodId)->period_start)->format('m'),
                                    'year' => Carbon::parse(PayrollPeriods::find($this->payrollPeriodId)->period_start)->format('Y'),
                                    'period_number' => PayrollPeriods::find($this->payrollPeriodId)->period_number,
                                ])
                                : null
                        )
                        ->openUrlInNewTab()
                        ->visible(fn() => filled($this->payrollPeriodId)),
                ])
        ];
    }
}
