<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            // 1. Quitar la foreign key existente
            $table->dropForeign(['employee_id']);
            // 2. Cambiar la columna a nullable
            $table->unsignedBigInteger('employee_id')->nullable()->change();
            // 3. Volver a crear la foreign key (ahora permite null)
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->nullOnDelete(); // se pone en NULL si borras el empleado
        });
    }

    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->unsignedBigInteger('employee_id')->nullable(false)->change();
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->cascadeOnDelete();
        });
    }
};
