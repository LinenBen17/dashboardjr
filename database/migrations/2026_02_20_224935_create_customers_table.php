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
            $table->bigIncrements('id');
            $table->string('code')->unique();
            $table->string('name', 255);
            $table->string('address', 255);
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->unsignedBigInteger('payment_method_id')->index('customers_payment_method_id_foreign');
            $table->unsignedBigInteger('departament_id')->index('customers_departament_id_foreign');
            $table->unsignedBigInteger('town_id')->index('customers_town_id_foreign');
            $table->string('prefix_origin', 10);
            $table->unsignedBigInteger('employee_id')->index('customers_employee_id_foreign');
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
