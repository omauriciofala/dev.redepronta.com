<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genders', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('Masculino, Feminino, Outro, Não Informado');
            $table->string('code', 10)->unique()->comment('M, F, O, N');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('genders');
    }
};
