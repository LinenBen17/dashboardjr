<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('users')->update([
            'custom_fields' => DB::raw(
                "JSON_SET(COALESCE(custom_fields, '{}'), '$.agency_id', '')"
            )
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->update([
            'custom_fields' => DB::raw(
                "JSON_REMOVE(custom_fields, '$.agency_id')"
            )
        ]);
    }
};
