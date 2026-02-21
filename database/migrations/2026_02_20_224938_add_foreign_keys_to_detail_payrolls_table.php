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
            $table->foreign(['employee_payroll_id'])->references(['id'])->on('employee_payrolls')->onUpdate('no action')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_payrolls', function (Blueprint $table) {
            $table->dropForeign('detail_payrolls_employee_payroll_id_foreign');
        });
    }
};
