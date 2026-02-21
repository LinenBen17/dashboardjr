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
        Schema::table('employee_payrolls', function (Blueprint $table) {
            $table->foreign(['employee_id'])->references(['id'])->on('employees')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['payroll_id'])->references(['id'])->on('payrolls')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_payrolls', function (Blueprint $table) {
            $table->dropForeign('employee_payrolls_employee_id_foreign');
            $table->dropForeign('employee_payrolls_payroll_id_foreign');
        });
    }
};
