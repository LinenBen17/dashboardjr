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
        Schema::table('guide_logs', function (Blueprint $table) {
            $table->foreign(['shipment_entry_id'])->references(['id'])->on('shipment_entries')->onUpdate('no action')->onDelete('cascade');
            $table->foreign(['user_id'])->references(['id'])->on('users')->onUpdate('no action')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guide_logs', function (Blueprint $table) {
            $table->dropForeign('guide_logs_shipment_entry_id_foreign');
            $table->dropForeign('guide_logs_user_id_foreign');
        });
    }
};
