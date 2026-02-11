<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_period_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->decimal('salary_base', 10, 2);
            $table->decimal('bonus_of_law', 10, 2)->default(0);
            $table->decimal('incentive_bonus', 10, 2)->default(0);
            $table->decimal('igss', 10, 2)->default(0);
            $table->decimal('isr', 10, 2)->default(0);
            $table->decimal('phone_discount', 10, 2)->default(0);

            $table->decimal('total_pay', 10, 2);

            $table->timestamps();

            $table->unique(['payroll_period_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_period_details');
    }
};
