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
        Schema::create('warehouse_outgos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('manifest_code')->unique();
            $table->date('left_date');
            $table->unsignedBigInteger('origin_warehouse_id')->index('warehouse_outgos_origin_warehouse_id_foreign');
            $table->unsignedBigInteger('warehouse_id')->index('warehouse_outgos_warehouse_id_foreign');
            $table->unsignedBigInteger('route_id')->index('warehouse_outgos_route_id_foreign');
            $table->string('driver');
            $table->string('person_scans');
            $table->integer('total_pieces')->default(0);
            $table->integer('total_guides')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_outgos');
    }
};
