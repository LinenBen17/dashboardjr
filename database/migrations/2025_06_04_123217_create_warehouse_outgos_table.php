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
            $table->id();
            $table->string('manifest_code')->unique();
            $table->date('left_date');
            $table->foreignId('origin_warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('warehouse_id')->constrained('warehouses')->onDelete('cascade');
            $table->foreignId('route_id')->constrained('routes')->onDelete('cascade');
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
