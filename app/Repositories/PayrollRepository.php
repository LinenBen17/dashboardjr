<?php

namespace App\Repositories;

use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;

class PayrollRepository
{
    public function getPayrollData(string $startDate, string $endDate, string $payroll, $employeeId)
    {
        logger($employeeId);
        return DB::table('employees AS e')
            ->leftJoin('agencies AS a', 'e.id_agency', '=', 'a.id') 
            ->leftJoin('payrolls AS p', 'e.id_payroll', '=', 'p.id') 
            ->leftJoin('charges AS c', 'e.id_charge', '=', 'c.id')
            ->leftJoin('detail_payrolls AS dpe', 'e.id', '=', 'dpe.employee_id')
            ->leftJoin('bonuses AS b', function($join) use ($startDate, $endDate) {
                $join->on('e.id', '=', 'b.employee_id')
                    ->whereBetween('b.date', [$startDate, $endDate]);
            })
            ->leftJoin('discounts AS d', function($join) use ($startDate, $endDate) {
                $join->on('e.id', '=', 'd.employee_id')
                    ->whereBetween('d.date', [$startDate, $endDate]);
            })
            ->leftJoin('loans AS l', 'e.id', '=', 'l.employee_id')
            ->leftJoin('installments AS i', function($join) use ($startDate, $endDate) {
                $join->on('l.id', '=', 'i.loan_id')
                    ->whereBetween('i.billing_date', [$startDate, $endDate]);
            })
            ->where('e.entry_date', '<=', now())
            ->whereRaw('p.state = ?', $payroll)
            ->when($employeeId, function ($query) use ($employeeId) {
                return $query->where('e.id', $employeeId);
            })
            ->whereNotNull('dpe.id')
            ->orderBy('e.name', 'ASC')
            ->select(
			    'a.name AS agency',
                'c.name AS charge',
                'e.id AS employee_id',
                'e.name',
                'e.last_name',
                'e.bank_account AS bank_account',
                'e.entry_date AS entry_date',
                'dpe.id AS detail_payroll_id',
                'dpe.regular_salaries',
                'dpe.bonus_of_law',
                'dpe.incentive_bonus',
                'dpe.percentage_igss',
                'b.id AS bonus_id',
                'b.date AS bonus_date',
                'b.amount AS bonus_amount',
                'd.id AS discount_id',
                'd.date AS discount_date',
                'd.type AS discount_type',
                'd.amount AS discount_amount',
                'i.id AS installment_id',
                'i.amount AS installment_amount',
                'i.billing_date AS billing_date'
            )
            ->get();
    }
    
    public function getCharges()
    {
        $charges = DB::table('charges')->pluck('name');
        return $charges->toArray();
    }

    public function getAgencies()
    {
        $agencies = DB::table('agencies')->pluck('name');
        return $agencies->toArray();
    }

    public function getBenefits()
    {
        $benefits = DB::table('benefits')->pluck('name', 'id');
        return $benefits->toArray();
    }

    public function getPayrollDataWithCalculations(string $startDate, string $endDate, string $payroll, string $employeeId = null)
    {
        // Obtener los datos crudos
        $rawData = $this->getPayrollData($startDate, $endDate, $payroll, $employeeId);

        // Inicializar las estructuras de datos
        $groupedData = [];
        $totals = [
            "sueldo" => 0,
            "bonoLey" => 0,
            "bonoIncentivo" => 0,
            "otrosIngresos" => 0,
            "totalDevengado" => 0,
            "igss" => 0,
            "anticipo" => 0,
            "ausencias" => 0,
            "otrosDescuentos" => 0,
            "installments" => 0,
            "totalDescuento" => 0,
            "liquido" => 0,
        ];

        // Agrupar y procesar los datos
        foreach ($rawData as $row) {
            $id = $row->employee_id;

            // Inicializar el registro del empleado si no existe
            if (!isset($groupedData[$id])) {
                $groupedData[$id] = [
                    "id" => $id,
                    "ctaBancaria" => $row->bank_account,
                    "fechaIngreso" => $row->entry_date,
                    "empleado" => $row->name . " " . $row->last_name,
                    "cargo" => $row->charge,
                    "agencia" => $row->agency,
                    "sueldo" => round($row->regular_salaries / 2, 2),
                    "bonoLey" => round($row->bonus_of_law / 2, 2),
                    "bonoIncentivo" => round($row->incentive_bonus / 2, 2),
                    "igss" => $row->percentage_igss,
                    "bonoMonto" => 0,
                    "ausencias" => 0,
                    "anticipos" => 0,
                    "otros" => 0,
                    "installments" => 0,
                ];
            }

            // Inicializar estructuras auxiliares para evitar duplicados
            if (!isset($groupedData[$id]['processed_bonuses'])) {
                $groupedData[$id]['processed_bonuses'] = [];
            }
            if (!isset($groupedData[$id]['processed_discounts'])) {
                $groupedData[$id]['processed_discounts'] = [];
            }
            if (!isset($groupedData[$id]['processed_installments'])) {
                $groupedData[$id]['processed_installments'] = [];
            }

            // Sumar los bonos, excluyendo duplicados
            if (!is_null($row->bonus_id)) {
                if (!in_array($row->bonus_id, $groupedData[$id]['processed_bonuses'])) {
                    $groupedData[$id]['bonoMonto'] += floatval($row->bonus_amount);
                    $groupedData[$id]['processed_bonuses'][] = $row->bonus_id;
                }
            }

            // Sumar los descuentos, excluyendo duplicados
            if (!is_null($row->discount_id)) {
                if (!in_array($row->discount_id, $groupedData[$id]['processed_discounts'])) {
                    $discountAmount = floatval($row->discount_amount);
                    if ($row->discount_type == 'ausencia') {
                        $groupedData[$id]['ausencias'] += $discountAmount;
                    } elseif ($row->discount_type == 'anticipo') {
                        $groupedData[$id]['anticipos'] += $discountAmount;
                    } else {
                        $groupedData[$id]['otros'] += $discountAmount;
                    }
                    $groupedData[$id]['processed_discounts'][] = $row->discount_id;
                }
            }
            if (!is_null($row->installment_id)) {
                if (!in_array($row->installment_id, $groupedData[$id]['processed_installments'])) {
                    $groupedData[$id]['installments'] += floatval($row->installment_amount);
                    $groupedData[$id]['processed_installments'][] = $row->installment_id;
                }
            }

        }

        // Calcular totales
        foreach ($groupedData as &$employee) {
            $totalDevengado = $employee['sueldo'] + $employee['bonoLey'] + $employee['bonoIncentivo'] + $employee['bonoMonto'];
            $igss = number_format($employee['igss'] / 100 * $employee['sueldo'], 2);
            $totalDescuentos = $employee['anticipos'] + $employee['ausencias'] + $employee['otros'] + $employee['installments'];
            $liquido = $totalDevengado - $totalDescuentos - $igss;

            $employee['totalDevengado'] = $totalDevengado;
            $employee['igssCalculated'] = $igss;
            $employee['totalDescuento'] = $totalDescuentos;
            $employee['liquido'] = $liquido;

            // Sumar a los totales generales
            $totals['sueldo'] += $employee['sueldo'];
            $totals['bonoLey'] += $employee['bonoLey'];
            $totals['bonoIncentivo'] += $employee['bonoIncentivo'];
            $totals['otrosIngresos'] += $employee['bonoMonto'];
            $totals['totalDevengado'] += $totalDevengado;
            $totals['igss'] += $igss;
            $totals['anticipo'] += $employee['anticipos'];
            $totals['ausencias'] += $employee['ausencias'];
            $totals['otrosDescuentos'] += $employee['otros'];
            $totals['installments'] += $employee['installments'];
            $totals['totalDescuento'] += $totalDescuentos;
            $totals['liquido'] += $liquido;
        }

        // Retornar los datos agrupados y los totales
        return [
            'charges' => $this->getCharges(),
            'agencies' => $this->getAgencies(),
            'benefits' => $this->getBenefits(),
            'data' => array_values($groupedData),
            'totals' => $totals,
        ];
    }
}
