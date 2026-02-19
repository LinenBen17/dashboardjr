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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name', 255);
            $table->string('address', 255);
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            $table->foreignId('departament_id')->constrained()->onDelete('cascade');
            $table->foreignId('town_id')->constrained()->onDelete('cascade');
            $table->string('prefix_origin', 10);
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
