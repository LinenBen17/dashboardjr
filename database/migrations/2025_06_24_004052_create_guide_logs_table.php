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
        Schema::create('guide_logs', function (Blueprint $table) {
            $table->id();
            // Relación con la guía
            $table->foreignId('shipment_entry_id')->constrained()->onDelete('cascade');
            // Usuario que hizo la acción
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // Acción realizada: created, updated, deleted, etc.
            $table->string('action');
            // Descripción de la acción
            $table->text('description')->nullable();
            // Datos antes y después (opcional, si quieres ver los cambios exactos)
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guide_logs');
    }
};
