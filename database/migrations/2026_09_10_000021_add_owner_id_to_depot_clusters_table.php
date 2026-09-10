<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('depot_clusters') && !Schema::hasColumn('depot_clusters', 'owner_id')) {
            Schema::table('depot_clusters', function (Blueprint $table) {
                $table->foreignId('owner_id')
                    ->nullable()
                    ->after('account_id')
                    ->constrained('material_owners')
                    ->onDelete('restrict');

                $table->index(['account_id', 'owner_id']);
            });

            // Se houver clusters existentes sem owner_id, garante a associação
            $accounts = DB::table('accounts')->pluck('id');
            foreach ($accounts as $accountId) {
                $owner = DB::table('material_owners')
                    ->where('account_id', $accountId)
                    ->first();

                if (!$owner) {
                    // Busca ou cria uma pessoa solicitante para o proprietário padrão
                    $person = DB::table('people')
                        ->where('account_id', $accountId)
                        ->first();

                    $personId = $person?->id;
                    if (!$personId) {
                        $personId = DB::table('people')->insertGetId([
                            'account_id' => $accountId,
                            'name' => 'Responsável Operacional / Solicitante',
                            'document_number' => '000.000.000-00',
                            'status' => 'active',
                            'is_requester' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        DB::table('people')->where('id', $personId)->update(['is_requester' => true]);
                    }

                    $ownerId = DB::table('material_owners')->insertGetId([
                        'account_id' => $accountId,
                        'person_id' => $personId,
                        'code' => 'PROPRIO',
                        'name' => 'Estoque Próprio / Operação Matriz',
                        'description' => 'Proprietário padrão do estoque próprio da empresa',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $ownerId = $owner->id;
                }

                DB::table('depot_clusters')
                    ->where('account_id', $accountId)
                    ->whereNull('owner_id')
                    ->update(['owner_id' => $ownerId]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('depot_clusters') && Schema::hasColumn('depot_clusters', 'owner_id')) {
            Schema::table('depot_clusters', function (Blueprint $table) {
                $table->dropForeign(['owner_id']);
                $table->dropColumn('owner_id');
            });
        }
    }
};
