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
            // total descuentos de diferentes tipos
            $table->decimal('total_bonuses', 15, 2)->default(0)->after('incentive_bonus');
            $table->decimal('total_discounts', 15, 2)->default(0)->after('phone_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_period_details', function (Blueprint $table) {
            //
            $table->dropColumn('total_bonuses');
            $table->dropColumn('total_discounts');
        });
    }
};
