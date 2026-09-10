<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->foreignId('material_id')->constrained('materials')->onDelete('restrict');
                $table->foreignId('source_depot_id')->nullable()->constrained('depots')->onDelete('restrict');
                $table->foreignId('destination_depot_id')->nullable()->constrained('depots')->onDelete('restrict');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->enum('movement_type', ['ENTRY', 'EXIT', 'TRANSFER', 'ADJUSTMENT', 'RETURN'])->default('TRANSFER');
                $table->decimal('quantity', 12, 2)->comment('Quantidade movimentada');
                $table->string('document_ref', 100)->nullable()->comment('Número da NF, Requisição ou OFS');
                $table->text('notes')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['account_id', 'movement_type']);
                $table->index(['account_id', 'material_id']);
                $table->index(['account_id', 'source_depot_id']);
                $table->index(['account_id', 'destination_depot_id']);
                $table->index(['account_id', 'created_at']);
            });
        }

        if (!Schema::hasTable('stock_movement_serials')) {
            Schema::create('stock_movement_serials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stock_movement_id')->constrained('stock_movements')->onDelete('cascade');
                $table->foreignId('stock_serial_id')->constrained('stock_serials')->onDelete('restrict');

                $table->unique(['stock_movement_id', 'stock_serial_id'], 'uk_movement_serial');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movement_serials');
        Schema::dropIfExists('stock_movements');
    }
};
