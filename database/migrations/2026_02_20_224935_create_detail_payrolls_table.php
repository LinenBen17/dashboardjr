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
        Schema::create('detail_payrolls', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_payroll_id')->nullable()->index('detail_payrolls_employee_payroll_id_foreign');
            $table->decimal('regular_salaries');
            $table->decimal('bonus_of_law')->default(250);
            $table->decimal('incentive_bonus');
            $table->double('percentage_igss')->default(0);
            $table->double('percentage_isr')->default(0);
            $table->decimal('phone_discount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_payrolls');
    }
};
