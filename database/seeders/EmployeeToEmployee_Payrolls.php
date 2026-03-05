<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeToEmployee_Payrolls extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee = Employee::all();

        // Asignar cada empleado a la planilla con ID 1 (Administrativa) y con ID 2 (Institucional)
        foreach ($employee as $emp) {
            DB::table('employee_payrolls')->insert([
                'employee_id' => $emp->id,
                'payroll_id' => 1, // Asignar a la planilla con ID 1
                'active' => true,
            ]);

            DB::table('employee_payrolls')->insert([
                'employee_id' => $emp->id,
                'payroll_id' => 2, // Asignar a la planilla con ID 2
                'active' => true,
            ]);
        }
    }
}
