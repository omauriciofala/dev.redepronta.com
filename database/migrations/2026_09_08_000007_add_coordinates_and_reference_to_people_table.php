<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            // Campos adicionais do Endereço Residencial / Principal
            $table->text('reference')->nullable()->after('neighborhood')->comment('Ponto de referência ou instruções de localização');
            $table->decimal('latitude', 10, 8)->nullable()->after('reference')->comment('Coordenada de Latitude');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude')->comment('Coordenada de Longitude');

            // Campos adicionais do Endereço Comercial
            $table->text('commercial_reference')->nullable()->after('commercial_neighborhood')->comment('Referência ou instruções de localização comercial');
            $table->decimal('commercial_latitude', 10, 8)->nullable()->after('commercial_reference')->comment('Latitude comercial');
            $table->decimal('commercial_longitude', 11, 8)->nullable()->after('commercial_latitude')->comment('Longitude comercial');
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn([
                'reference',
                'latitude',
                'longitude',
                'commercial_reference',
                'commercial_latitude',
                'commercial_longitude',
            ]);
        });
    }
};
