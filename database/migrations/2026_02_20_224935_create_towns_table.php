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
        Schema::create('towns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 30);
            $table->timestamps();
            $table->boolean('status')->default(false);
            $table->unsignedBigInteger('route_id')->nullable()->index('towns_route_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('towns');
    }
};
