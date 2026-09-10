<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('units')) {
            Schema::create('units', function (Blueprint $table) {
                $table->id();
                $table->string('code', 10)->unique()->comment('Código abreviado ex: UND, MT, PC, CX, RL, KM, PAR, BOB');
                $table->string('name', 100)->comment('Nome por extenso da unidade');
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('code');
            });

            // Semeia unidades fundamentais de telecom e ERP
            DB::table('units')->insert([
                ['code' => 'UND', 'name' => 'Unidade', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'MT',  'name' => 'Metro', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'PC',  'name' => 'Peça', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'CX',  'name' => 'Caixa', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'BOB', 'name' => 'Bobina', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'RL',  'name' => 'Rolo', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'KM',  'name' => 'Quilômetro', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
                ['code' => 'PAR', 'name' => 'Par', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
