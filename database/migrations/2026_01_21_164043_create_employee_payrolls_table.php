<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_payrolls');
    }
};
