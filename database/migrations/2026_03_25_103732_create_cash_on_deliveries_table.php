<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cash_on_deliveries', function (Blueprint $table) {
            $table->id();

            // Relación con envío
            $table->foreignId('shipment_entry_id')
                ->constrained()
                ->cascadeOnDelete();

            // Datos base
            $table->string('no_pce')->unique();
            $table->decimal('amount', 10, 2);
            $table->integer('pieces')->default(1);
            $table->decimal('shipment_price', 10, 2)->default(0);

            // Configuración
            $table->enum('shipment_paid_by', ['sender', 'receiver']);
            $table->boolean('include_commission')->default(false);
            $table->decimal('commission_rate', 5, 4)->default(0.05);

            // Resultados calculados
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('total_receiver', 10, 2);
            $table->decimal('total_sender', 10, 2);
            $table->decimal('per_piece_receiver', 10, 2);
            $table->decimal('per_piece_sender', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_on_deliveries');
    }
};
