<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('owner_materials')) {
            Schema::create('owner_materials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
                $table->foreignId('material_owner_id')->constrained('material_owners')->onDelete('cascade')->comment('Proprietário / Solicitante');
                $table->foreignId('material_id')->constrained('materials')->onDelete('cascade')->comment('Material canônico do sistema');
                $table->string('owner_code', 100)->comment('Código / SKU utilizado pelo proprietário');
                $table->string('owner_name', 255)->comment('Nome / Descrição do material no proprietário');
                $table->text('notes')->nullable()->comment('Observações adicionais ou especificações do proprietário');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->unique(['account_id', 'material_owner_id', 'material_id'], 'owner_mat_acc_owner_mat_unique');
                $table->index(['account_id', 'material_owner_id', 'owner_code'], 'owner_mat_acc_owner_code_idx');
                $table->index(['account_id', 'owner_code'], 'owner_mat_acc_code_idx');
                $table->index(['account_id', 'is_active'], 'owner_mat_acc_active_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('owner_materials');
    }
};
