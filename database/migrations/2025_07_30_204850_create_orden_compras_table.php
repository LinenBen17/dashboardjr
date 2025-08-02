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
        Schema::create('orden_compras', function (Blueprint $table) {
            $table->id();
            $table->integer('numero_orden')->unique();
            $table->string('ruta');
            $table->date('fecha');
            $table->string('proveedor');
            $table->string('placa');
            $table->integer('cantidad');
            $table->string('descripcion');
            $table->decimal('precioUnitario', 10, 2);
            $table->decimal('sub-total', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_compras');
    }
};
