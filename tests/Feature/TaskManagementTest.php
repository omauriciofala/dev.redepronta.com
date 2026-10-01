<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\City;
use App\Models\Person;
use App\Models\State;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected User $user;
    protected User $operator;
    protected Person $customer;

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
            ['email' => 'admin@redepronta.com'],
            [
                'account_id' => $this->account->id,
                'name' => 'Administrador Matriz',
                'email' => 'admin@redepronta.com',
                'password' => bcrypt('secret123'),
                'is_super_admin' => true,
            ]
        );

        $this->operator = User::create([
            'account_id' => $this->account->id,
            'name' => 'Operador Triagem',
            'email' => 'triagem@redepronta.com',
            'password' => bcrypt('secret123'),
            'is_active' => true,
        ]);

        $state = State::firstOrCreate(['id' => 1], [
            'name' => 'São Paulo',
            'code' => 'SP',
        ]);

        $city = City::firstOrCreate(['id' => 1], [
            'state_id' => $state->id,
            'ibge_code' => '3550308',
            'name' => 'São Paulo',
            'location' => 'POINT(-46.6333 -23.5505)',
        ]);

        $this->customer = Person::create([
            'account_id' => $this->account->id,
            'name' => 'Cliente Corporativo Tarefa',
            'is_customer' => true,
            'status' => 'ACTIVE',
            'city_id' => $city->id,
        ]);

        $this->actingAs($this->user);
    }

    public function test_can_create_task_and_list_with_kanban_unpaginated(): void
    {
        // Criação de 3 tarefas
        for ($i = 1; $i <= 3; $i++) {
            $this->postJson('/api/v1/operations/tasks', [
                'title' => "Tarefa de Teste {$i}",
                'description' => "Descrição detalhada {$i}",
                'source' => 'WHATSAPP',
                'priority' => 'HIGH',
                'customer_person_id' => $this->customer->id,
            ])->assertStatus(201);
        }

        // Listagem normal paginada
        $responseList = $this->getJson('/api/v1/operations/tasks?per_page=2');
        $responseList->assertStatus(200);
        $this->assertCount(2, $responseList->json('data'));
        $this->assertEquals(3, $responseList->json('meta.total'));

        // Listagem para Quadro Kanban (per_page = -1 traz todas)
        $responseKanban = $this->getJson('/api/v1/operations/tasks?per_page=-1');
        $responseKanban->assertStatus(200);
        $this->assertCount(3, $responseKanban->json('data'));
        $this->assertEquals(3, $responseKanban->json('meta.counts.inbox'));
    }

    public function test_can_add_internal_comments_to_task(): void
    {
        $task = Task::create([
            'account_id' => $this->account->id,
            'task_number' => 'TSK-2026-TEST-001',
            'title' => 'Verificar oscilação de sinal',
            'description' => 'Cliente relatou lentidão',
            'source' => 'WHATSAPP',
            'status' => 'INBOX',
            'priority' => 'MEDIUM',
        ]);

        // Adiciona comentário interno
        $commentRes = $this->postJson("/api/v1/operations/tasks/{$task->id}/comments", [
            'comment' => 'Primeiro contato realizado com o cliente via WhatsApp.',
        ]);

        $commentRes->assertStatus(201);
        $commentRes->assertJsonPath('data.comment', 'Primeiro contato realizado com o cliente via WhatsApp.');
        $commentRes->assertJsonPath('data.user.name', 'Administrador Matriz');

        // Listar comentários
        $listRes = $this->getJson("/api/v1/operations/tasks/{$task->id}/comments");
        $listRes->assertStatus(200);
        $this->assertCount(1, $listRes->json('data'));

        // Verificar contagem no task list
        $taskRes = $this->getJson('/api/v1/operations/tasks');
        $taskRes->assertStatus(200);
        $this->assertEquals(1, $taskRes->json('data.0.comments_count'));
    }

    public function test_can_assign_operator_to_task(): void
    {
        $task = Task::create([
            'account_id' => $this->account->id,
            'task_number' => 'TSK-2026-TEST-002',
            'title' => 'Atribuir técnico responsável',
            'description' => 'Triagem pendente',
            'source' => 'MANUAL',
            'status' => 'INBOX',
            'priority' => 'LOW',
        ]);

        // Atribuir operador
        $assignRes = $this->patchJson("/api/v1/operations/tasks/{$task->id}/assign", [
            'assigned_user_id' => $this->operator->id,
        ]);

        $assignRes->assertStatus(200);
        $assignRes->assertJsonPath('data.assigned_user.name', 'Operador Triagem');

        $task->refresh();
        $this->assertEquals($this->operator->id, $task->assigned_user_id);

        // Desatribuir operador
        $unassignRes = $this->patchJson("/api/v1/operations/tasks/{$task->id}/assign", [
            'assigned_user_id' => null,
        ]);

        $unassignRes->assertStatus(200);
        $this->assertNull($unassignRes->json('data.assigned_user'));
        $task->refresh();
        $this->assertNull($task->assigned_user_id);
    }

    public function test_can_update_task_status_in_kanban(): void
    {
        $task = Task::create([
            'account_id' => $this->account->id,
            'task_number' => 'TSK-2026-TEST-003',
            'title' => 'Mover card no Kanban',
            'description' => 'Fluxo de triagem',
            'source' => 'EMAIL',
            'status' => 'INBOX',
            'priority' => 'MEDIUM',
        ]);

        // Move para TRIAGED
        $res1 = $this->patchJson("/api/v1/operations/tasks/{$task->id}/status", [
            'status' => 'TRIAGED',
        ]);
        $res1->assertStatus(200);
        $res1->assertJsonPath('data.status', 'TRIAGED');

        // Move para RESOLVED_INTERNAL
        $res2 = $this->patchJson("/api/v1/operations/tasks/{$task->id}/status", [
            'status' => 'RESOLVED_INTERNAL',
        ]);
        $res2->assertStatus(200);
        $res2->assertJsonPath('data.status', 'RESOLVED_INTERNAL');
    }

    public function test_task_comment_foreign_keys_enforce_on_delete_restrict(): void
    {
        $task = Task::create([
            'account_id' => $this->account->id,
            'task_number' => 'TSK-2026-TEST-004',
            'title' => 'Integridade FK Task Comment',
            'source' => 'EMAIL',
            'status' => 'INBOX',
            'priority' => 'HIGH',
        ]);

        TaskComment::create([
            'account_id' => $this->account->id,
            'task_id' => $task->id,
            'user_id' => $this->user->id,
            'comment' => 'Comentário para validação de integridade referencial.',
        ]);

        // A tentativa de excluir a tarefa diretamente no banco deve ser bloqueada se houver comentários vinculados
        $this->expectException(\Illuminate\Database\QueryException::class);
        $task->forceDelete();
    }
}
