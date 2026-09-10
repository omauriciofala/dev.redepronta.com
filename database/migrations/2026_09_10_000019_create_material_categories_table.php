<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('material_categories')) {
            Schema::create('material_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
                $table->string('name', 100);
                $table->string('code', 50)->nullable();
                $table->text('description')->nullable();
                $table->string('color', 20)->default('#FC6714');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->unique(['account_id', 'name']);
                $table->index(['account_id', 'is_active']);
                $table->index(['account_id', 'code']);
            });
        }

        // Popula as categorias padrão para as contas existentes
        $accounts = DB::table('accounts')->pluck('id');
        $defaultCategories = [
            [
                'name' => 'Equipamentos',
                'code' => 'EQUIPAMENTOS',
                'description' => 'Roteadores Wi-Fi, ONUs GPON/EPON, switches e ativos de rede',
                'color' => '#3B82F6',
            ],
            [
                'name' => 'Cabos & Fibras',
                'code' => 'CABOS_FIBRAS',
                'description' => 'Cabos drop ópticos, cabos UTP, cordões ópticos e bobinas',
                'color' => '#FC6714',
            ],
            [
                'name' => 'Conectores & Passivos',
                'code' => 'CONECTORES_PASSIVOS',
                'description' => 'Conectores rápidos, adaptadores SC/APC, caixas de emenda e splitters',
                'color' => '#10B981',
            ],
            [
                'name' => 'Ferramentas & EPIs',
                'code' => 'FERRAMENTAS_EPIS',
                'description' => 'Máquinas de fusão, clivadores, alicates decapadores, capacetes e EPIs',
                'color' => '#8B5CF6',
            ],
            [
                'name' => 'Geral / Diversos',
                'code' => 'GERAL',
                'description' => 'Insumos gerais de infraestrutura, fixadores, fitas e consumíveis',
                'color' => '#6B7280',
            ],
        ];

        foreach ($accounts as $accountId) {
            foreach ($defaultCategories as $cat) {
                $exists = DB::table('material_categories')
                    ->where('account_id', $accountId)
                    ->where('name', $cat['name'])
                    ->exists();

                if (!$exists) {
                    DB::table('material_categories')->insert([
                        'account_id' => $accountId,
                        'name' => $cat['name'],
                        'code' => $cat['code'],
                        'description' => $cat['description'],
                        'color' => $cat['color'],
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
        Schema::dropIfExists('material_categories');
    }
};
