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
            $table->unsignedBigInteger('shipment_manifest_id')->nullable()->after('payment_method_id');
            $table->foreign('shipment_manifest_id')->references('id')->on('shipment_manifests')->nullOnDelete();

            $table->dropColumn('no_manifest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_entries', function (Blueprint $table) {
            $table->dropForeign(['shipment_manifest_id']);
            $table->dropColumn('shipment_manifest_id');

            $table->string('no_manifest')->nullable()->after('payment_method_id');
        });
    }
};
