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
            $table->id();
            $table->foreignId('employee_id');
            $table->unique('employee_id');
            $table->decimal('regular_salaries', 8, 2);
            $table->decimal('bonus_of_law', 8, 2)->default(250);
            $table->decimal('incentive_bonus', 8, 2);
            $table->float('percentage_igss')->default(0);
            $table->float('percentage_isr')->default(0);
            $table->decimal('phone_discount', 8, 2)->default(0);
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
