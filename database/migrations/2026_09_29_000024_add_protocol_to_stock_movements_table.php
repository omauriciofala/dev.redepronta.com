<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('stock_movements', 'protocol')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->string('protocol', 50)->nullable()->after('movement_type')->comment('Protocolo único da movimentação (agrupador de lote/NF)');
                $table->index(['account_id', 'protocol']);
            });
        }

        // Gera protocolo retroativo para movimentações que ainda não possuem
        $movementsWithoutProtocol = DB::table('stock_movements')->whereNull('protocol')->get();
        foreach ($movementsWithoutProtocol as $mv) {
            $datePrefix = $mv->created_at ? date('ymdHis', strtotime($mv->created_at)) : date('ymdHis');
            $generatedProtocol = $datePrefix . '-' . str_pad((string)($mv->id % 1000), 3, '0', STR_PAD_LEFT);
            DB::table('stock_movements')->where('id', $mv->id)->update(['protocol' => $generatedProtocol]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stock_movements', 'protocol')) {
            Schema::table('stock_movements', function (Blueprint $table) {
                $table->dropIndex(['account_id', 'protocol']);
                $table->dropColumn('protocol');
            });
        }
    }
};
