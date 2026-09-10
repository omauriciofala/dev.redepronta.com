<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->string('tracking_type', 20)->default('BULK')->after('category')->comment('Tipo de rastreabilidade: SERIAL, BATCH (Lote/Metragem), BULK (A Granel)');
            $table->boolean('track_batch')->default(false)->after('has_serial')->comment('Indica se o material possui controle de lote e metragem');

            $table->index(['account_id', 'tracking_type']);
            $table->index(['account_id', 'track_batch']);
        });

        // Atualiza registros existentes
        DB::table('materials')->where('has_serial', true)->update([
            'tracking_type' => 'SERIAL',
            'track_batch' => false,
        ]);

        DB::table('materials')->where('has_serial', false)->update([
            'tracking_type' => 'BULK',
            'track_batch' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropIndex(['account_id', 'tracking_type']);
            $table->dropIndex(['account_id', 'track_batch']);
            $table->dropColumn(['tracking_type', 'track_batch']);
        });
    }
};
