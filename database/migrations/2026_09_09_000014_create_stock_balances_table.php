<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_balances')) {
            Schema::create('stock_balances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->foreignId('depot_id')->constrained('depots')->onDelete('restrict');
                $table->foreignId('material_id')->constrained('materials')->onDelete('restrict');
                $table->decimal('quantity', 12, 2)->default(0)->comment('Quantidade física em estoque no depósito');
                $table->decimal('reserved_quantity', 12, 2)->default(0)->comment('Quantidade reservada para chamados em andamento');
                $table->timestamps();

                $table->unique(['depot_id', 'material_id'], 'uk_depot_material');
                $table->index(['account_id', 'depot_id']);
                $table->index(['account_id', 'material_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_balances');
    }
};
