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
        Schema::table('detail_payrolls', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['district_id']);

            $table->dropColumn('employee_id');
            $table->dropColumn('district_id');
        });
    }

    public function down(): void
    {
        Schema::table('detail_payrolls', function (Blueprint $table) {
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('district_id')->constrained();
        });
    }
};
