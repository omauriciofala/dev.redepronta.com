<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_serials')) {
            Schema::create('stock_serials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->foreignId('material_id')->constrained('materials')->onDelete('restrict');
                $table->foreignId('current_depot_id')->constrained('depots')->onDelete('restrict');
                $table->string('serial_number', 100)->comment('Número de série (GPON SN, Serial Fabricante)');
                $table->string('mac_address', 50)->nullable()->comment('Endereço MAC físico');
                $table->enum('status', ['IN_STOCK', 'IN_TRANSIT', 'INSTALLED_CUSTOMER', 'DEFECTIVE', 'DISCARDED', 'RETURNED'])->default('IN_STOCK');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['account_id', 'serial_number'], 'uk_serial_account');
                $table->index(['account_id', 'material_id', 'status']);
                $table->index(['account_id', 'current_depot_id']);
                $table->index(['account_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_serials');
    }
};
