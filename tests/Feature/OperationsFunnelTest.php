<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\City;
use App\Models\Department;
use App\Models\Depot;
use App\Models\DepotCluster;
use App\Models\Person;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\TicketCategory;
use App\Models\TicketReason;
use App\Models\User;
use Database\Seeders\OperationsCatalogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsFunnelTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected User $user;
    protected Person $customer;
    protected Person $worker;
    protected City $city;
    protected Department $department;
    protected TicketCategory $category;
    protected TicketReason $reason;
    protected Depot $depot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::firstOrCreate(
            ['subdomain' => 'matriz'],
            [
                'name' => 'Rede Pronta Matriz',
                'subdomain' => 'matriz',
                'document' => '12345678000190',
                'email' => 'matriz@redepronta.com',
                'status' => 'active',
            ]
        );

        $this->user = User::firstOrCreate(
            ['email' => 'operador@redepronta.com'],
            [
                'account_id' => $this->account->id,
                'name' => 'Operador NOC',
                'email' => 'operador@redepronta.com',
                'password' => bcrypt('secret123'),
                'is_super_admin' => true,
            ]
        );

        $this->actingAs($this->user);

        // Semear catálogos caso não existam
        $seeder = new OperationsCatalogSeeder();
        $seeder->seedForAccount($this->account);

        $state = \App\Models\State::firstOrCreate(['id' => 1], [
            'name' => 'São Paulo',
            'code' => 'SP',
        ]);

        $this->city = City::firstOrCreate(['id' => 1], [
            'state_id' => $state->id,
            'ibge_code' => '3550308',
            'name' => 'São Paulo',
            'location' => 'POINT(-46.6333 -23.5505)',
        ]);

        $this->department = Department::where('account_id', $this->account->id)->first();
        $this->category = TicketCategory::where('department_id', $this->department->id)->first();
        $this->reason = TicketReason::where('category_id', $this->category->id)->first();

        // Cliente
        $this->customer = Person::firstOrCreate(
            ['document_number' => '11122233344', 'account_id' => $this->account->id],
            [
                'name' => 'Cliente Teste FTTH',
                'email' => 'cliente@empresa.com.br',
                'phone' => '(11) 98888-7777',
                'is_client' => true,
                'status' => 'active',
                'city_id' => $this->city->id,
            ]
        );

        // Técnico de Campo
        $this->worker = Person::firstOrCreate(
            ['document_number' => '55566677788', 'account_id' => $this->account->id],
            [
                'name' => 'Técnico Especialista em Fibra',
                'email' => 'tecnico@redepronta.com',
                'phone' => '(11) 97777-6666',
                'is_employee' => true,
                'status' => 'active',
                'city_id' => $this->city->id,
            ]
        );

        // Depósito / Base de Apoio
        $cluster = DepotCluster::firstOrCreate(
            ['account_id' => $this->account->id, 'code' => 'SP-CAPITAL'],
            ['name' => 'Regional São Paulo Capital']
        );

        $this->depot = Depot::firstOrCreate(
            ['account_id' => $this->account->id, 'code' => 'BASE-OESTE'],
            [
                'cluster_id' => $cluster->id,
                'name' => 'Base Operacional Oeste',
                'type' => 'REGIONAL_BASE',
                'is_active' => true,
            ]
        );
    }

    public function test_can_create_and_list_tasks_in_inbox(): void
    {
        $response = $this->postJson('/api/v1/operations/tasks', [
            'title' => 'Cliente informou luz vermelha no modem pelo WhatsApp',
            'description' => 'LOS piscando desde as 14h após temporal na região.',
            'source' => 'WHATSAPP',
            'priority' => 'HIGH',
            'customer_person_id' => $this->customer->id,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('data.title', 'Cliente informou luz vermelha no modem pelo WhatsApp');
        $response->assertJsonPath('data.status', 'INBOX');
        $this->assertStringStartsWith('TSK-', $response->json('data.task_number'));

        // Listar tarefas
        $listResponse = $this->getJson('/api/v1/operations/tasks?status=INBOX');
        $listResponse->assertStatus(200);
        $listResponse->assertJsonStructure([
            'data',
            'meta' => ['total', 'counts'],
        ]);
        $this->assertGreaterThanOrEqual(1, $listResponse->json('meta.total'));
    }

    public function test_can_promote_task_to_ticket_with_calculated_sla(): void
    {
        // 1. Criar Tarefa
        $task = Task::create([
            'account_id' => $this->account->id,
            'task_number' => 'TSK-TEST-001',
            'title' => 'Rompimento de Fibra na Rua das Flores',
            'description' => 'Caminhão baú arrebentou cabeamento drop.',
            'source' => 'AI',
            'priority' => 'CRITICAL',
            'status' => 'INBOX',
            'customer_person_id' => $this->customer->id,
        ]);

        // 2. Promover para Chamado (Ticket)
        $response = $this->postJson("/api/v1/operations/tasks/{$task->id}/promote", [
            'customer_person_id' => $this->customer->id,
            'city_id' => $this->city->id,
            'department_id' => $this->department->id,
            'category_id' => $this->category->id,
            'reason_id' => $this->reason->id,
            'priority' => 'CRITICAL',
            'address_street' => 'Rua das Flores',
            'address_number' => '100',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => ['id', 'protocol', 'sla_due_at', 'status'],
        ]);

        $ticket = Ticket::find($response->json('data.id'));
        $this->assertNotNull($ticket);
        $this->assertStringStartsWith('TK-', $ticket->protocol);
        $this->assertEquals('OPEN', $ticket->status);
        $this->assertNotNull($ticket->sla_due_at);

        // A tarefa de origem deve estar como PROMOTED_TICKET
        $task->refresh();
        $this->assertEquals('PROMOTED_TICKET', $task->status);
        $this->assertNotNull($task->resolved_at);
    }

    public function test_cannot_delete_task_already_promoted_to_ticket(): void
    {
        $task = Task::create([
            'account_id' => $this->account->id,
            'task_number' => 'TSK-TEST-PROMOTED',
            'title' => 'Demanda já promovida',
            'source' => 'MANUAL',
            'priority' => 'MEDIUM',
            'status' => 'PROMOTED_TICKET',
        ]);

        Ticket::create([
            'account_id' => $this->account->id,
            'origin_task_id' => $task->id,
            'protocol' => 'TK-TEST-PROMOTED-01',
            'title' => 'Chamado derivado',
            'customer_person_id' => $this->customer->id,
            'city_id' => $this->city->id,
            'department_id' => $this->department->id,
            'category_id' => $this->category->id,
            'reason_id' => $this->reason->id,
            'status' => 'OPEN',
        ]);

        $response = $this->deleteJson("/api/v1/operations/tasks/{$task->id}");
        $response->assertStatus(422);
    }

    public function test_can_dispatch_ticket_to_field_worker_and_track_status_lifecycle(): void
    {
        // 1. Criar Chamado
        $ticket = Ticket::create([
            'account_id' => $this->account->id,
            'protocol' => 'TK-2026-FSM-01',
            'title' => 'Substituição de Conector Óptico Atenuado',
            'customer_person_id' => $this->customer->id,
            'city_id' => $this->city->id,
            'department_id' => $this->department->id,
            'category_id' => $this->category->id,
            'reason_id' => $this->reason->id,
            'priority' => 'HIGH',
            'status' => 'OPEN',
        ]);

        // 2. Despachar para técnico em campo
        $dispatchResponse = $this->postJson("/api/v1/operations/tickets/{$ticket->id}/dispatch", [
            'worker_person_id' => $this->worker->id,
            'depot_id' => $this->depot->id,
            'notes' => 'Levar máquina de fusão e conectores SC-APC adicionais.',
        ]);

        $dispatchResponse->assertStatus(201);
        $dispatchId = $dispatchResponse->json('data.id');
        $this->assertStringStartsWith('DSP-', $dispatchResponse->json('data.dispatch_number'));
        $this->assertEquals('DISPATCHED', $dispatchResponse->json('data.status'));

        // O ticket deve passar para IN_FIELD
        $ticket->refresh();
        $this->assertEquals('IN_FIELD', $ticket->status);

        // 3. Avançar status: ON_ROUTE (A caminho)
        $routeResponse = $this->patchJson("/api/v1/operations/dispatches/{$dispatchId}/advance-status", [
            'status' => 'ON_ROUTE',
            'notes' => 'Técnico saiu da base oeste em direção ao endereço.',
        ]);
        $routeResponse->assertStatus(200);
        $routeResponse->assertJsonPath('data.status', 'ON_ROUTE');

        // 4. Avançar status: ARRIVED_SITE (Chegou no local)
        $arrivedResponse = $this->patchJson("/api/v1/operations/dispatches/{$dispatchId}/advance-status", [
            'status' => 'ARRIVED_SITE',
        ]);
        $arrivedResponse->assertStatus(200);
        $arrivedResponse->assertJsonPath('data.status', 'ARRIVED_SITE');
        $this->assertNotNull($arrivedResponse->json('data.arrived_at'));

        // 5. Concluir atendimento: COMPLETED
        $completedResponse = $this->patchJson("/api/v1/operations/dispatches/{$dispatchId}/advance-status", [
            'status' => 'COMPLETED',
            'notes' => 'Conector substituído, sinal restabelecido em -19.2 dBm.',
        ]);
        $completedResponse->assertStatus(200);
        $completedResponse->assertJsonPath('data.status', 'COMPLETED');
        $this->assertNotNull($completedResponse->json('data.completed_at'));

        // Ao concluir o acionamento, o ticket deve ser encerrado automaticamente
        $ticket->refresh();
        $this->assertEquals('CLOSED', $ticket->status);
        $this->assertNotNull($ticket->closed_at);
    }

    public function test_strict_integrity_on_delete_restrict_for_ticket_foreign_keys(): void
    {
        // Cria um ticket ativo vinculado à pessoa cliente
        $ticket = Ticket::create([
            'account_id' => $this->account->id,
            'protocol' => 'TK-INTEGRITY-001',
            'title' => 'Teste de Integridade Relacional',
            'customer_person_id' => $this->customer->id,
            'city_id' => $this->city->id,
            'department_id' => $this->department->id,
            'category_id' => $this->category->id,
            'reason_id' => $this->reason->id,
            'status' => 'OPEN',
        ]);

        // Tentativa de exclusão física da pessoa no banco deve ser bloqueada pelo MariaDB (ON DELETE RESTRICT)
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->customer->forceDelete();
    }

    public function test_can_fetch_operations_catalogs(): void
    {
        $response = $this->getJson('/api/v1/operations/catalogs');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'departments',
                'workers',
                'depots',
            ],
        ]);
        $this->assertNotEmpty($response->json('data.departments'));
        $this->assertNotEmpty($response->json('data.workers'));
        $this->assertNotEmpty($response->json('data.depots'));
    }
}
