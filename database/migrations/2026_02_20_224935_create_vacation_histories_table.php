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
        Schema::create('vacation_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id')->index('vacation_histories_employee_id_foreign');
            $table->year('year');
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('days_allocated');
            $table->integer('days_used');
            $table->integer('days_remaining');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacation_histories');
    }
};
