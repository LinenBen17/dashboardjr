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
        Schema::create('date_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('benefit_id')->constrained();
            $table->date('date_from')->unique();
            $table->date('date_to')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('date_benefits');
    }
};
