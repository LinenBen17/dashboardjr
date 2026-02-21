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
        Schema::create('employees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 25);
            $table->string('last_name', 25);
            $table->text('comments');
            $table->date('entry_date');
            $table->unsignedBigInteger('id_agency')->nullable()->index('employees_id_agency_foreign');
            $table->unsignedBigInteger('id_charge')->nullable()->index('employees_id_charge_foreign');
            $table->bigInteger('bank_account');
            $table->string('address', 500);
            $table->unsignedBigInteger('town_id')->nullable()->index('employees_town_id_foreign');
            $table->unsignedBigInteger('departament_id')->nullable()->index('employees_departament_id_foreign');
            $table->integer('zone');
            $table->string('birthplace');
            $table->string('phone', 15);
            $table->string('cellphone', 15);
            $table->date('birth_date');
            $table->integer('age');
            $table->unsignedBigInteger('civil_status_id')->nullable()->index('employees_civil_status_id_foreign');
            $table->unsignedBigInteger('gender_id')->nullable()->index('employees_gender_id_foreign');
            $table->integer('children');
            $table->bigInteger('dpi');
            $table->bigInteger('nit');
            $table->string('email', 100);
            $table->string('photo');
            $table->timestamps();
            $table->unsignedBigInteger('nationality_id')->index('employees_nationality_id_foreign');
            $table->unsignedBigInteger('status_id')->nullable()->index('employees_status_id_foreign');
            $table->unsignedBigInteger('id_payroll')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
