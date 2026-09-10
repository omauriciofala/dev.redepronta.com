<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('depot_clusters')) {
            Schema::create('depot_clusters', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->string('name', 150)->comment('Nome da Posição Regional ex: Região Metropolitana, Região do Vale');
                $table->string('code', 50)->comment('Sigla ou Código da Posição Regional ex: REG-METRO, REG-SUL');
                $table->text('description')->nullable();
                $table->string('color', 20)->default('#FC6714');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index(['account_id', 'is_active']);
                $table->index(['account_id', 'code']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('depot_clusters');
    }
};
