<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign('loans_employee_id_foreign');
            $table->dropColumn('employee_id');
            $table->foreignId('employee_payroll_id')
                ->after('id')
                ->constrained('employee_payrolls')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign('loans_employee_payroll_id_foreign');
            $table->dropColumn('employee_payroll_id');
            $table->foreignId('employee_id')
                ->after('id')
                ->constrained('employees')
                ->cascadeOnDelete();
        });
    }
};
