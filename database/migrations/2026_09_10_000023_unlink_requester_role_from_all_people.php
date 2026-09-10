<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('people')) {
            DB::table('people')->update(['is_requester' => false]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Operação de desvinculação em massa irreversível para dados fictícios
    }
};
