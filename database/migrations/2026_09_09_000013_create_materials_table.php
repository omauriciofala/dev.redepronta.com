<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('materials')) {
            Schema::create('materials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->foreignId('unit_id')->constrained('units')->onDelete('restrict');
                $table->string('code', 50)->comment('Código SKU interno ex: ONU-XPON-GIGA, DROP-1FO');
                $table->string('name', 255)->comment('Descrição completa do material');
                $table->text('description')->nullable();
                $table->string('category', 100)->nullable()->comment('Categoria ex: Ativos de Rede, Cabos, Conectores, Passivos');
                $table->boolean('has_serial')->default(false)->comment('Exige rastreamento de número de série (ONUs, Roteadores)');
                $table->decimal('unit_cost', 12, 2)->default(0)->comment('Custo unitário médio/recente');
                $table->decimal('min_stock', 12, 2)->default(0)->comment('Estoque mínimo recomendado');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['account_id', 'is_active']);
                $table->index(['account_id', 'code']);
                $table->index(['account_id', 'has_serial']);
                $table->index(['account_id', 'category']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
