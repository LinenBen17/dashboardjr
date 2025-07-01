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
            $table->id();
            $table->string('mother')->unique();
            $table->foreignId('sender_code')->nullable()->references('id')->on('customers');
            $table->string('sender_name');
            $table->string('sender_address');
            $table->string('sender_phone');
            $table->foreignId('receiver_code')->nullable()->references('id')->on('customers');
            $table->string('receiver_name');
            $table->string('receiver_address');
            $table->string('receiver_phone');
            $table->string('prefix_origin');
            $table->string('prefix_destination');
            $table->foreignId('town_id')->constrained();
            $table->foreignId('product_id')->nullable();
            $table->string('product_description');
            $table->integer('pieces');
            $table->decimal('unit_price');
            $table->decimal('sender_total');
            $table->decimal('receiver_total');
            $table->decimal('total');
            $table->dateTime('date_guide');
            $table->foreignId('payment_method_id')->constrained();
            $table->integer('no_manifest')->nullable();
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
