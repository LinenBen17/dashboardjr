<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bonuses', function (Blueprint $table) {

            $table->dropForeign('bonuses_employee_id_foreign');

            $table->dropColumn('employee_id');

            $table->foreignId('employee_payroll_id')
                ->after('id')
                ->constrained('employee_payrolls')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bonuses', function (Blueprint $table) {

            $table->dropForeign(['employee_payroll_id']);
            $table->dropColumn('employee_payroll_id');

            $table->foreignId('employee_id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }
};
