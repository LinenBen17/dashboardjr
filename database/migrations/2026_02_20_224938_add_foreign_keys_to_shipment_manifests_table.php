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
        Schema::table('shipment_manifests', function (Blueprint $table) {
            $table->foreign(['agency_destination_id'])->references(['id'])->on('agencies')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['agency_origin_id'])->references(['id'])->on('agencies')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['route_id'])->references(['id'])->on('routes')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_manifests', function (Blueprint $table) {
            $table->dropForeign('shipment_manifests_agency_destination_id_foreign');
            $table->dropForeign('shipment_manifests_agency_origin_id_foreign');
            $table->dropForeign('shipment_manifests_route_id_foreign');
        });
    }
};
