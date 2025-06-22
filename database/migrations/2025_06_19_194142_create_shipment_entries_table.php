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
            $table->foreignId('mother_guide_id')->nullable()->constrained('shipment_guides')->nullOnDelete();
            $table->integer('sender_code')->nullable();
            $table->string('sender_name')->nullable();
            $table->string('sender_address')->nullable();
            $table->string('sender_phone')->nullable();
            $table->integer('receiver_code')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_address')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->string('prefix_origin');
            $table->string('prefix_destination');
            $table->foreignId('town_id')->constrained('towns')->nullOnDelete();
            $table->string('product_description');
            $table->integer('pieces');
            $table->integer('unit_price');
            $table->integer('sender_total')->nullable();
            $table->integer('receiver_total')->nullable();
            $table->integer('total')->nullable();
            $table->dateTime('date_guide');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->nullOnDelete();
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
