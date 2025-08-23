<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Agregar índice a departaments.prefix
        Schema::table('departaments', function (Blueprint $table) {
            $table->index('prefix', 'idx_prefix');
        });
    }

    public function down(): void
    {
        // Eliminar índices en caso de rollback
        Schema::table('departaments', function (Blueprint $table) {
            $table->dropIndex('idx_prefix');
        });
    }
};
