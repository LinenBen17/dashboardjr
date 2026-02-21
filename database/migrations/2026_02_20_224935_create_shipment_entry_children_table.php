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
        Schema::create('shipment_entry_children', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('shipment_entry_id')->index('shipment_entry_children_shipment_entry_id_foreign');
            $table->string('child_guide')->nullable()->unique();
            $table->unsignedBigInteger('product_id')->index('shipment_entry_children_product_id_foreign');
            $table->decimal('price', 10)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_entry_children');
    }
};
