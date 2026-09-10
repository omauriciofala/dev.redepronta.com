<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('depots')) {
            Schema::create('depots', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->foreignId('cluster_id')->constrained('depot_clusters')->onDelete('restrict');
                $table->foreignId('responsible_person_id')->nullable()->constrained('people')->onDelete('restrict');
                $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('restrict');
                $table->string('name', 150)->comment('Nome do Depósito ex: Almoxarifado Central, Base Campinas, Depósito Avançado');
                $table->string('code', 50)->comment('Código único ou identificador interno');
                $table->enum('type', ['CENTRAL', 'REGIONAL_BASE', 'LAB_REPAIR'])->default('REGIONAL_BASE');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['account_id', 'is_active']);
                $table->index(['account_id', 'cluster_id']);
                $table->index(['account_id', 'type']);
                $table->index(['account_id', 'code']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('depots');
    }
};
