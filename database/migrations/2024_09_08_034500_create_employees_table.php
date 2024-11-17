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
            $table->id();
            
            $table->string('name', 25);
            $table->string('last_name', 25);
            $table->text('comments');
            $table->date('entry_date');
            $table->foreignId('id_agency')
                ->nullable()
                ->constrained('agencies')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('id_charge')
                ->nullable()
                ->constrained('charges')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->foreignId('id_payroll')
                ->nullable()
                ->constrained('payrolls')
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->bigInteger('bank_account');

            $table->string('address', 500);
            $table->foreignId('town_id', 20)
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('departament_id', 20)
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->integer('zone');
            $table->string('birthplace');
            $table->string('nationality');

            $table->string('phone', 15);
            $table->string('cellphone', 15);

            $table->date('birth_date');
            $table->integer('age');
            
            $table->foreignId('civil_status_id', 10)
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->foreignId('gender_id', 10)
                ->nullable()
                ->constrained()
                ->cascadeOnUpdate()
                ->nullOnDelete();

            $table->integer('children');

            $table->bigInteger('dpi');
            $table->bigInteger('nit');
            $table->string('email', 100);

            $table->string('photo');

            $table->timestamps();
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
    