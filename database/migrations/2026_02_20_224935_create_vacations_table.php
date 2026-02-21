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
        Schema::create('vacations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('employee_id')->index('vacations_employee_id_foreign');
            $table->date('request_date');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_requested');
            $table->unsignedBigInteger('vacation_type_id')->index('vacations_vacation_type_id_foreign');
            $table->unsignedBigInteger('vacation_history_id')->index('vacations_vacation_history_id_foreign');
            $table->string('comments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacations');
    }
};
