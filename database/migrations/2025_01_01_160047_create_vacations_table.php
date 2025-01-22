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
            $table->id(); // Vacation_ID
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('request_date'); // Request_Date
            $table->date('start_date'); // Start_Date
            $table->date('end_date'); // End_Date
            $table->integer('days_requested'); // Days_Requested
            $table->foreignId('vacation_type_id')->constrained()->onDelete('cascade'); // Type_ID FK
            $table->foreignId('vacation_history_id')->constrained()->onDelete('cascade'); // History_ID FK (optional)
            $table->string('comments'); // Comments
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
