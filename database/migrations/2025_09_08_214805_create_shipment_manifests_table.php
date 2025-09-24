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
            $table->id();
            $table->string('manifest_code')->unique();
            $table->date('date');

            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
            $table->foreignId('agency_origin_id')->constrained('agencies')->onDelete('cascade');

            //en local la tabla routes y agencies no son InnoDB si no MyISAM, por lo que no se pueden crear claves foráneas
            // usar: ALTER TABLE routes ENGINE=InnoDB; y ALTER TABLE agencies ENGINE=InnoDB;
            $table->foreignId('agency_destination_id')->constrained('agencies')->onDelete('cascade');
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
