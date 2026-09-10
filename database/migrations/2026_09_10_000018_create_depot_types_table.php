<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('depot_types')) {
            Schema::create('depot_types', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
                $table->string('code', 50);
                $table->string('name', 100);
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['account_id', 'code']);
                $table->index(['account_id', 'is_active']);
            });
        }

        // Flexibiliza a coluna type na tabela depots para aceitar qualquer código de tipo cadastrado
        if (Schema::hasTable('depots')) {
            $driver = DB::getDriverName();
            if ($driver === 'mysql' || $driver === 'mariadb') {
                DB::statement("ALTER TABLE depots MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'REGIONAL_BASE'");
            }
        }

        // Popula os tipos padrão para todas as contas existentes
        $accounts = DB::table('accounts')->pluck('id');
        $defaultTypes = [
            [
                'code' => 'CENTRAL',
                'name' => 'Almoxarifado Central',
                'description' => 'Depósito principal, matriz de suprimentos e recebimento de fabricantes',
            ],
            [
                'code' => 'REGIONAL_BASE',
                'name' => 'Base Regional',
                'description' => 'Base operacional regional de suporte às equipes de campo e técnicos',
            ],
            [
                'code' => 'LAB_REPAIR',
                'name' => 'Laboratório de Reparo',
                'description' => 'Laboratório de triagem, manutenção e recuperação de equipamentos e ONUs',
            ],
        ];

        foreach ($accounts as $accountId) {
            foreach ($defaultTypes as $dt) {
                $exists = DB::table('depot_types')
                    ->where('account_id', $accountId)
                    ->where('code', $dt['code'])
                    ->exists();

                if (!$exists) {
                    DB::table('depot_types')->insert([
                        'account_id' => $accountId,
                        'code' => $dt['code'],
                        'name' => $dt['name'],
                        'description' => $dt['description'],
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('depot_types');
    }
};
