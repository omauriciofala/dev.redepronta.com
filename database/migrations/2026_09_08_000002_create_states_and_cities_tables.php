<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2)->unique()->comment('Sigla UF ex: MG, SP, RJ');
            $table->string('name', 50);
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained('states')->onDelete('restrict');
            $table->string('ibge_code', 7)->unique()->comment('Código IBGE oficial de 7 dígitos');
            $table->string('name', 100);
            $table->timestamps();

            $table->index(['name', 'state_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
    }
};
