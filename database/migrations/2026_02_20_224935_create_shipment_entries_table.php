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
        Schema::create('shipment_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mother')->unique();
            $table->unsignedBigInteger('sender_code')->nullable()->index('shipment_entries_sender_code_foreign');
            $table->string('sender_name');
            $table->string('sender_address');
            $table->string('sender_phone');
            $table->unsignedBigInteger('receiver_code')->nullable()->index('shipment_entries_receiver_code_foreign');
            $table->string('receiver_name');
            $table->string('receiver_address');
            $table->string('receiver_phone');
            $table->string('prefix_origin');
            $table->string('prefix_destination');
            $table->unsignedBigInteger('town_id')->index('shipment_entries_town_id_foreign');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_description');
            $table->integer('pieces');
            $table->decimal('unit_price');
            $table->decimal('sender_total');
            $table->decimal('receiver_total');
            $table->decimal('total');
            $table->dateTime('date_guide');
            $table->unsignedBigInteger('payment_method_id')->index('shipment_entries_payment_method_id_foreign');
            $table->unsignedBigInteger('shipment_manifest_id')->nullable()->index('shipment_entries_shipment_manifest_id_foreign');
            $table->unsignedBigInteger('created_by')->nullable()->index('shipment_entries_created_by_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_entries');
    }
};
