<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('material_owners')) {
            Schema::create('material_owners', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
                $table->foreignId('person_id')->constrained('people')->onDelete('restrict')->comment('Pessoa com papel de solicitante vinculada ao proprietário');
                $table->string('code', 50)->comment('Código único ou identificador do proprietário ex: VIVO, CLARO, PROPRIO');
                $table->string('name', 150)->comment('Razão social ou nome de exibição do proprietário');
                $table->text('description')->nullable()->comment('Observações contratuais, custódia ou comodato');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['account_id', 'code']);
                $table->index(['account_id', 'person_id']);
                $table->index(['account_id', 'is_active']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('material_owners');
    }
};
