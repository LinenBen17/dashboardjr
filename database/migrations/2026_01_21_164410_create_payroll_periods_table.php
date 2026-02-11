<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();

            $table->date('period_start');
            $table->date('period_end');
            $table->unsignedTinyInteger('period_number')->nullable(); // 1 o 2
            $table->year('year');

            $table->enum('status', ['borrador', 'cerrada', 'anulada'])->default('borrador');
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->unique(['payroll_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
