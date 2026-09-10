<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('depots') && !Schema::hasColumn('depots', 'owner_id')) {
            Schema::table('depots', function (Blueprint $table) {
                $table->foreignId('owner_id')
                    ->nullable()
                    ->after('cluster_id')
                    ->constrained('material_owners')
                    ->onDelete('restrict')
                    ->comment('Proprietário específico do depósito; se nulo, herda do cluster');

                $table->index(['account_id', 'owner_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('depots') && Schema::hasColumn('depots', 'owner_id')) {
            Schema::table('depots', function (Blueprint $table) {
                $table->dropForeign(['owner_id']);
                $table->dropColumn('owner_id');
            });
        }
    }
};
