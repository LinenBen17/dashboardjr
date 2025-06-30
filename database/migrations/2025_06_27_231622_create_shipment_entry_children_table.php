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
            $table->id();
            $table->foreignId('shipment_entry_id')->constrained('shipment_entries')->onDelete('cascade');
            $table->string('child_guide')->nullable()->unique();
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('price', 10, 2)->default(0.00);
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
