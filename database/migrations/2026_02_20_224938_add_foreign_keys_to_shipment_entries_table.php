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
        Schema::table('shipment_entries', function (Blueprint $table) {
            $table->foreign(['created_by'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['payment_method_id'])->references(['id'])->on('payment_methods')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['receiver_code'])->references(['id'])->on('customers')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['sender_code'])->references(['id'])->on('customers')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['shipment_manifest_id'])->references(['id'])->on('shipment_manifests')->onUpdate('no action')->onDelete('set null');
            $table->foreign(['town_id'])->references(['id'])->on('towns')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_entries', function (Blueprint $table) {
            $table->dropForeign('shipment_entries_created_by_foreign');
            $table->dropForeign('shipment_entries_payment_method_id_foreign');
            $table->dropForeign('shipment_entries_receiver_code_foreign');
            $table->dropForeign('shipment_entries_sender_code_foreign');
            $table->dropForeign('shipment_entries_shipment_manifest_id_foreign');
            $table->dropForeign('shipment_entries_town_id_foreign');
        });
    }
};
