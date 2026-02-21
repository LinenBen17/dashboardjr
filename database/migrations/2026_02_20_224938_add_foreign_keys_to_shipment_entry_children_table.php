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
        Schema::table('shipment_entry_children', function (Blueprint $table) {
            $table->foreign(['product_id'])->references(['id'])->on('products')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['shipment_entry_id'])->references(['id'])->on('shipment_entries')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_entry_children', function (Blueprint $table) {
            $table->dropForeign('shipment_entry_children_product_id_foreign');
            $table->dropForeign('shipment_entry_children_shipment_entry_id_foreign');
        });
    }
};
