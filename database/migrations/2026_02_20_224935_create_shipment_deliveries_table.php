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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('shipment_entry_id')->index('shipment_deliveries_shipment_entry_id_foreign');
            $table->string('received_by_name')->nullable();
            $table->string('received_by_document')->nullable();
            $table->boolean('signed')->nullable();
            $table->enum('status', ['pending', 'failed', 'delivered'])->default('pending');
            $table->text('observations')->nullable();
            $table->string('place_photo_path')->nullable();
            $table->string('signed_receipt_path')->nullable();
            $table->string('signature_path')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index('shipment_deliveries_created_by_foreign');
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
