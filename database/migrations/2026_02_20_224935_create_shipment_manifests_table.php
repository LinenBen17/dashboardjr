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
        Schema::create('shipment_manifests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('manifest_code')->unique();
            $table->date('date');
            $table->unsignedBigInteger('route_id')->index('shipment_manifests_route_id_foreign');
            $table->unsignedBigInteger('agency_origin_id')->index('shipment_manifests_agency_origin_id_foreign');
            $table->unsignedBigInteger('agency_destination_id')->index('shipment_manifests_agency_destination_id_foreign');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_manifests');
    }
};
