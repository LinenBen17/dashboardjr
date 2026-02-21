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
        Schema::table('payroll_period_details', function (Blueprint $table) {
            $table->foreign(['employee_id'])->references(['id'])->on('employees')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['payroll_period_id'])->references(['id'])->on('payroll_periods')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_period_details', function (Blueprint $table) {
            $table->dropForeign('payroll_period_details_employee_id_foreign');
            $table->dropForeign('payroll_period_details_payroll_period_id_foreign');
        });
    }
};
