<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            // Data do Cadastro e Grupo
            $table->date('registration_date')->nullable()->after('birth_date')->comment('Data canônica de cadastro (Dia, Mês e Ano)');
            $table->string('group_name', 100)->nullable()->after('registration_date')->comment('Grupo ou Categoria da Pessoa');

            // Novos Papéis Canônicos do Cadastro
            $table->boolean('is_outsourced')->default(false)->after('is_requester')->comment('Terceirizado (S/N)');
            $table->boolean('is_seller')->default(false)->after('is_outsourced')->comment('Vendedor (S/N)');
            $table->boolean('is_driver')->default(false)->after('is_seller')->comment('Motorista (S/N)');
            $table->boolean('is_carrier')->default(false)->after('is_driver')->comment('Transportadora (S/N)');

            // Índices para filtros operacionais
            $table->index(['account_id', 'is_outsourced']);
            $table->index(['account_id', 'is_seller']);
            $table->index(['account_id', 'is_driver']);
            $table->index(['account_id', 'is_carrier']);
        });
    }

    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropIndex(['account_id', 'is_outsourced']);
            $table->dropIndex(['account_id', 'is_seller']);
            $table->dropIndex(['account_id', 'is_driver']);
            $table->dropIndex(['account_id', 'is_carrier']);

            $table->dropColumn([
                'registration_date',
                'group_name',
                'is_outsourced',
                'is_seller',
                'is_driver',
                'is_carrier',
            ]);
        });
    }
};
