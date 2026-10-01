<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabela TASKS: Porta de entrada universal (APIs, IA, E-mail, Chat, Manual)
        if (!Schema::hasTable('tasks')) {
            Schema::create('tasks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->string('task_number', 40)->comment('Identificador legível ex: TSK-2026-0001');
                $table->string('title', 255);
                $table->text('description')->nullable();
                $table->enum('source', ['MANUAL', 'API', 'AI', 'EMAIL', 'WHATSAPP'])->default('MANUAL');
                $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->default('MEDIUM');
                $table->enum('status', ['INBOX', 'TRIAGED', 'PROMOTED_TICKET', 'RESOLVED_INTERNAL', 'CANCELED'])->default('INBOX');
                $table->foreignId('assigned_user_id')->nullable()->constrained('users')->onDelete('restrict');
                $table->foreignId('customer_person_id')->nullable()->constrained('people')->onDelete('restrict');
                $table->json('metadata')->nullable()->comment('Metadados livres: payload do webhook, transcrição IA, headers');
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['account_id', 'status']);
                $table->index(['account_id', 'task_number']);
                $table->index(['account_id', 'priority']);
                $table->index(['account_id', 'source']);
            });
        }

        // 2. Tabela TICKETS: Chamados formais com SLA e diagnóstico
        if (!Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->foreignId('origin_task_id')->nullable()->constrained('tasks')->onDelete('restrict');
                $table->string('protocol', 40)->comment('Protocolo auditável do chamado ex: TK-2026-0001');
                $table->string('title', 255);
                $table->text('description')->nullable();

                // Pessoas envolvidas
                $table->foreignId('customer_person_id')->constrained('people')->onDelete('restrict')->comment('Cliente atendido');
                $table->foreignId('requester_person_id')->nullable()->constrained('people')->onDelete('restrict')->comment('Solicitante/Contato no local');

                // Classificação operacional
                $table->foreignId('city_id')->constrained('cities')->onDelete('restrict');
                $table->foreignId('department_id')->constrained('departments')->onDelete('restrict');
                $table->foreignId('category_id')->constrained('ticket_categories')->onDelete('restrict');
                $table->foreignId('reason_id')->constrained('ticket_reasons')->onDelete('restrict');

                // Gravidade e Status
                $table->enum('priority', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->default('MEDIUM');
                $table->enum('status', ['OPEN', 'IN_TRIAGE', 'WAITING_DISPATCH', 'IN_FIELD', 'RESOLVED_REMOTE', 'CLOSED', 'CANCELED'])->default('OPEN');

                // Localização do Atendimento
                $table->string('address_street', 150)->nullable();
                $table->string('address_number', 30)->nullable();
                $table->string('address_neighborhood', 100)->nullable();
                $table->string('address_postal_code', 10)->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();

                // Controle de SLA e Fechamento
                $table->timestamp('sla_due_at')->nullable()->comment('Prazo fatal calculado por reason_id');
                $table->timestamp('resolved_at')->nullable();
                $table->timestamp('closed_at')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['account_id', 'status']);
                $table->index(['account_id', 'protocol']);
                $table->index(['account_id', 'sla_due_at']);
                $table->index(['account_id', 'customer_person_id']);
                $table->index(['account_id', 'city_id']);
                $table->index(['account_id', 'department_id']);
            });
        }

        // 3. Tabela DISPATCHES: Acionamentos de campo (FSM - Field Service Management)
        if (!Schema::hasTable('dispatches')) {
            Schema::create('dispatches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('account_id')->constrained('accounts')->onDelete('restrict');
                $table->foreignId('ticket_id')->constrained('tickets')->onDelete('restrict');
                $table->foreignId('worker_person_id')->constrained('people')->onDelete('restrict')->comment('Técnico ou responsável em campo');
                $table->foreignId('depot_id')->constrained('depots')->onDelete('restrict')->comment('Base ou veículo de apoio');
                $table->string('dispatch_number', 40)->comment('Número do acionamento ex: DSP-2026-0001');

                $table->enum('status', [
                    'DISPATCHED',      // Despachado
                    'ON_ROUTE',        // Em deslocamento
                    'ARRIVED_SITE',    // No local
                    'IN_SERVICE',      // Executando serviço
                    'RFO_SUBMITTED',   // RFO enviado pelo técnico
                    'APPROVED',        // Aprovado pela supervisão/NOC
                    'COMPLETED',       // Concluído
                    'CANCELED',        // Cancelado
                    'FAILED'           // Insucesso / Reagendado
                ])->default('DISPATCHED');

                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('dispatched_at')->nullable();
                $table->timestamp('arrived_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->text('notes')->nullable();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['account_id', 'status']);
                $table->index(['account_id', 'dispatch_number']);
                $table->index(['account_id', 'ticket_id']);
                $table->index(['account_id', 'worker_person_id']);
                $table->index(['account_id', 'depot_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatches');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('tasks');
    }
};
