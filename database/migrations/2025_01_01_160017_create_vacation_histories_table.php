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
            $table->id(); // History_ID
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->year('year'); // Year
            $table->date('period_start'); // Period Start Date
            $table->date('period_end'); // Period End Date
            $table->integer('days_allocated'); // Days_Allocated
            $table->integer('days_used'); // Days_Used
            //$table->integer('days_carried_over'); // Days_Carried_Over (optional)
            $table->integer('days_remaining'); // Days_Remaining
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
