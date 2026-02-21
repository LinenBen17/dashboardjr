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
        Schema::create('warehouse_outgo_guides', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('warehouse_outgo_id')->index('warehouse_outgo_guides_warehouse_outgo_id_foreign');
            $table->string('guide_number')->index();
            $table->integer('pieces')->default(1);
            $table->string('mother_guide')->nullable();
            $table->string('child_guide')->nullable();
            $table->dateTime('scanned_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_outgo_guides');
    }
};
