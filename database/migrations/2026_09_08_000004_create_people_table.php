<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
            $table->foreignId('city_id')->nullable()->constrained('cities')->onDelete('restrict');
            $table->foreignId('gender_id')->nullable()->constrained('genders')->onDelete('restrict');

            // Tipo de Pessoa
            $table->enum('person_type', ['individual', 'legal'])->default('individual')->comment('individual = PF, legal = PJ');

            // Identificação Principal
            $table->string('name', 150)->comment('Nome completo para PF ou Razão Social para PJ');
            $table->string('trade_name', 150)->nullable()->comment('Nome Fantasia para PJ');
            $table->string('document_number', 20)->nullable()->comment('CPF ou CNPJ limpo');
            $table->string('rg_ie', 30)->nullable()->comment('RG para PF ou Inscrição Estadual para PJ');
            $table->date('birth_date')->nullable()->comment('Data de Nascimento (PF) ou Fundação (PJ)');

            // Flags Polimórficas das Personas (Axioma 1)
            $table->boolean('is_employee')->default(false)->comment('Flag de Colaborador');
            $table->boolean('is_supplier')->default(false)->comment('Flag de Fornecedor');
            $table->boolean('is_client')->default(false)->comment('Flag de Cliente');
            $table->boolean('is_requester')->default(false)->comment('Flag de Solicitante');

            // Contatos
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();

            // Endereço
            $table->string('postal_code', 10)->nullable()->comment('CEP');
            $table->string('street', 150)->nullable();
            $table->string('number', 30)->nullable();
            $table->string('complement', 100)->nullable();
            $table->string('neighborhood', 100)->nullable();

            // Status e Observações
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Índices de busca e integridade
            $table->index(['account_id', 'status']);
            $table->index(['account_id', 'document_number']);
            $table->index(['account_id', 'name']);
            $table->index(['account_id', 'is_employee']);
            $table->index(['account_id', 'is_supplier']);
            $table->index(['account_id', 'is_client']);
            $table->index(['account_id', 'is_requester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
