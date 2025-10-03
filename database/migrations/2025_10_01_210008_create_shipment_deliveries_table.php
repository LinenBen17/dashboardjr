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
        Schema::create('shipment_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_entry_id')->constrained()->cascadeOnDelete();

            // Datos de quien recibe
            $table->string('received_by_name')->nullable();
            $table->string('received_by_document')->nullable();
            $table->boolean('signed')->nullable();

            // Estado del intento
            $table->enum('status', ['pending', 'failed', 'delivered'])
                ->default('pending');
            $table->text('observations')->nullable();

            // Evidencias
            $table->string('place_photo_path')->nullable(); // Foto si estaba cerrado
            $table->string('signed_receipt_path')->nullable(); // Guía firmada
            $table->string('signature_path')->nullable(); // Firma digital si la usas en app móvil

            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_deliveries');
    }
};
