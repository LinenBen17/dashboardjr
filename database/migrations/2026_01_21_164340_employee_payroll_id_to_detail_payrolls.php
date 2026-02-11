<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('detail_payrolls', function (Blueprint $table) {
            $table->foreignId('employee_payroll_id')
                ->nullable()
                ->after('id')
                ->constrained('employee_payrolls')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('detail_payrolls', function (Blueprint $table) {
            $table->dropForeign(['employee_payroll_id']);
            $table->dropColumn('employee_payroll_id');
        });
    }
};
