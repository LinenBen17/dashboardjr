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
        Schema::table('payroll_period_details', function (Blueprint $table) {
            $table->decimal('total_installments', 10, 2)->default(0)->after('total_discounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_period_details', function (Blueprint $table) {
            $table->dropColumn('total_installments');
        });
    }
};
