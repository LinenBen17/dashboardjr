<?php

namespace Database\Seeders;

use App\Models\DetailPayroll as ModelsDetailPayroll;
use App\Models\Employee;
use App\Models\EmployeePayrolls;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetailPayroll extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeePayrolls = EmployeePayrolls::all();

        foreach ($employeePayrolls as $employeePayroll) {
            if (Employee::find($employeePayroll->employee_id)->id_agency == 1) {
                ModelsDetailPayroll::create([
                    'employee_payroll_id' => $employeePayroll->id,
                    'regular_salaries' => 4002.28,
                    'bonus_of_law' => 250.00,
                    'incentive_bonus' => 0.00,
                    'percentage_igss' => 4.83,
                    'percentage_isr' => 0.0737,
                    'phone_discount' => 0.00,
                ]);
            } else {
                ModelsDetailPayroll::create([
                    'employee_payroll_id' => $employeePayroll->id,
                    'regular_salaries' => 3816.90,
                    'bonus_of_law' => 250.00,
                    'incentive_bonus' => 0.00,
                    'percentage_igss' => 4.83,
                    'percentage_isr' => 0.00,
                    'phone_discount' => 0.00,
                ]);
            }
        }
    }
}
