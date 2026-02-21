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
        Schema::create('payroll_period_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('payroll_period_id');
            $table->unsignedBigInteger('employee_id')->index('payroll_period_details_employee_id_foreign');
            $table->decimal('salary_base', 10);
            $table->decimal('bonus_of_law', 10)->default(0);
            $table->decimal('incentive_bonus', 10)->default(0);
            $table->decimal('total_bonuses', 15)->default(0);
            $table->decimal('igss', 10)->default(0);
            $table->decimal('isr', 10)->default(0);
            $table->decimal('phone_discount', 10)->default(0);
            $table->decimal('total_discounts', 15)->default(0);
            $table->decimal('total_installments', 10)->default(0);
            $table->decimal('total_pay', 10);
            $table->timestamps();

            $table->unique(['payroll_period_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_period_details');
    }
};
