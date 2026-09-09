<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('people', function (Blueprint $table) {
            // Documentos adicionais
            $table->string('rg_issuer', 30)->nullable()->after('rg_ie')->comment('Órgão Emissor do RG (ex: SSP, DETRAN)');
            $table->date('rg_issue_date')->nullable()->after('rg_issuer')->comment('Data de emissão do RG');
            $table->string('state_registration', 30)->nullable()->after('rg_issue_date')->comment('Inscrição Estadual (IE)');
            $table->string('municipal_registration', 30)->nullable()->after('state_registration')->comment('Inscrição Municipal (IM)');
            $table->string('cnae', 50)->nullable()->after('municipal_registration')->comment('Código e descrição do CNAE principal');
            $table->string('suframa_registration', 30)->nullable()->after('cnae')->comment('Número de Inscrição SUFRAMA');

            // Filiação e Dados Pessoais / PJ
            $table->string('mother_name', 150)->nullable()->after('suframa_registration')->comment('Nome completo da Mãe');
            $table->string('father_name', 150)->nullable()->after('mother_name')->comment('Nome completo do Pai');
            $table->date('birth_or_foundation_date')->nullable()->after('father_name')->comment('Data de Fundação (PJ) ou Nascimento (PF)');
            $table->decimal('share_capital', 15, 2)->nullable()->after('birth_or_foundation_date')->comment('Capital Social da Pessoa Jurídica');
            $table->string('birth_place', 100)->nullable()->after('share_capital')->comment('Naturalidade (Cidade/UF)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('people', function (Blueprint $table) {
            $table->dropColumn([
                'rg_issuer',
                'rg_issue_date',
                'state_registration',
                'municipal_registration',
                'cnae',
                'suframa_registration',
                'mother_name',
                'father_name',
                'birth_or_foundation_date',
                'share_capital',
                'birth_place',
            ]);
        });
    }
};
