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
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_employee_id_foreign');
            $table->dropColumn('employee_id');

            $table->unsignedBigInteger('seller_id')->index('customers_seller_id_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('customers_seller_id_foreign');
            $table->dropColumn('seller_id');

            $table->unsignedBigInteger('employee_id')->index('customers_employee_id_foreign');
        });
    }
};
