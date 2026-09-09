<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Account;
use App\Models\City;
use App\Models\State;
use App\Models\Neighborhood;
use App\Models\PersonGroup;
use App\Models\Cnae;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BasicRecordsApiTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected State $state;
    protected City $city;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::create([
            'name' => 'Conta Teste',
            'subdomain' => 'teste',
            'status' => 'active',
        ]);

        $this->state = State::create([
            'code' => 'MG',
            'name' => 'Minas Gerais',
        ]);

        $this->city = City::create([
            'state_id' => $this->state->id,
            'name' => 'Belo Horizonte',
            'ibge_code' => '3106200',
        ]);
    }

    public function test_can_crud_states(): void
    {
        // 1. Listar
        $res = $this->getJson('/api/v1/states');
        $res->assertOk()
            ->assertJsonPath('data.0.code', 'MG');

        // 2. Criar
        $createRes = $this->postJson('/api/v1/states', [
            'code' => 'RJ',
            'name' => 'Rio de Janeiro',
        ]);
        $createRes->assertCreated()
            ->assertJsonPath('data.code', 'RJ');
        $stateId = $createRes->json('data.id');

        // 3. Atualizar
        $updateRes = $this->putJson("/api/v1/states/{$stateId}", [
            'name' => 'Rio de Janeiro Atualizado',
        ]);
        $updateRes->assertOk()
            ->assertJsonPath('data.name', 'Rio de Janeiro Atualizado');

        // 4. Excluir estado sem cidades
        $deleteRes = $this->deleteJson("/api/v1/states/{$stateId}");
        $deleteRes->assertOk();
        $this->assertDatabaseMissing('states', ['id' => $stateId]);

        // 5. Impedir exclusão de estado com cidades vinculadas
        $deleteBlocked = $this->deleteJson("/api/v1/states/{$this->state->id}");
        $deleteBlocked->assertStatus(422);
    }

    public function test_can_crud_cities(): void
    {
        // 1. Listar
        $res = $this->getJson('/api/v1/cities?all=1');
        $res->assertOk()
            ->assertJsonFragment(['name' => 'Belo Horizonte']);

        // 2. Criar
        $createRes = $this->postJson('/api/v1/cities', [
            'state_id' => $this->state->id,
            'name' => 'Contagem',
            'ibge_code' => '3118601',
        ]);
        $createRes->assertCreated()
            ->assertJsonPath('data.name', 'Contagem');
        $cityId = $createRes->json('data.id');

        // 3. Atualizar
        $updateRes = $this->putJson("/api/v1/cities/{$cityId}", [
            'name' => 'Contagem Grande',
        ]);
        $updateRes->assertOk()
            ->assertJsonPath('data.name', 'Contagem Grande');

        // 4. Excluir cidade sem pessoas
        $deleteRes = $this->deleteJson("/api/v1/cities/{$cityId}");
        $deleteRes->assertOk();
        $this->assertDatabaseMissing('cities', ['id' => $cityId]);
    }

    public function test_can_crud_neighborhoods(): void
    {
        // 1. Criar
        $createRes = $this->postJson('/api/v1/neighborhoods', [
            'city_id' => $this->city->id,
            'name' => 'Savassi',
            'zone' => 'Centro-Sul',
        ]);
        $createRes->assertCreated()
            ->assertJsonPath('data.name', 'Savassi');
        $id = $createRes->json('data.id');

        // 2. Listar
        $listRes = $this->getJson("/api/v1/neighborhoods?city_id={$this->city->id}");
        $listRes->assertOk()
            ->assertJsonFragment(['name' => 'Savassi']);

        // 3. Atualizar
        $updateRes = $this->putJson("/api/v1/neighborhoods/{$id}", [
            'name' => 'Savassi Nobre',
        ]);
        $updateRes->assertOk()
            ->assertJsonPath('data.name', 'Savassi Nobre');

        // 4. Excluir
        $deleteRes = $this->deleteJson("/api/v1/neighborhoods/{$id}");
        $deleteRes->assertOk();
        $this->assertDatabaseMissing('neighborhoods', ['id' => $id]);
    }

    public function test_can_crud_person_groups(): void
    {
        // 1. Criar
        $createRes = $this->postJson('/api/v1/person-groups', [
            'name' => 'Clientes VIP',
            'description' => 'Clientes com atendimento preferencial',
            'color' => '#10B981',
            'is_active' => true,
        ]);
        $createRes->assertCreated()
            ->assertJsonPath('data.name', 'Clientes VIP');
        $id = $createRes->json('data.id');

        // 2. Listar
        $listRes = $this->getJson('/api/v1/person-groups');
        $listRes->assertOk()
            ->assertJsonFragment(['name' => 'Clientes VIP']);

        // 3. Atualizar
        $updateRes = $this->putJson("/api/v1/person-groups/{$id}", [
            'name' => 'Clientes Black VIP',
        ]);
        $updateRes->assertOk()
            ->assertJsonPath('data.name', 'Clientes Black VIP');

        // 4. Excluir
        $deleteRes = $this->deleteJson("/api/v1/person-groups/{$id}");
        $deleteRes->assertOk();
        $this->assertDatabaseMissing('person_groups', ['id' => $id]);
    }

    public function test_can_crud_cnaes(): void
    {
        // 1. Criar
        $createRes = $this->postJson('/api/v1/cnaes', [
            'code' => '6110-8/03',
            'description' => 'Serviços de telecomunicações por fio',
            'is_active' => true,
        ]);
        $createRes->assertCreated()
            ->assertJsonPath('data.code', '6110-8/03');
        $id = $createRes->json('data.id');

        // 2. Listar
        $listRes = $this->getJson('/api/v1/cnaes?search=telecom');
        $listRes->assertOk()
            ->assertJsonFragment(['code' => '6110-8/03']);

        // 3. Atualizar
        $updateRes = $this->putJson("/api/v1/cnaes/{$id}", [
            'description' => 'Serviços de telecomunicações por fio atualizados',
        ]);
        $updateRes->assertOk()
            ->assertJsonPath('data.description', 'Serviços de telecomunicações por fio atualizados');

        // 4. Excluir
        $deleteRes = $this->deleteJson("/api/v1/cnaes/{$id}");
        $deleteRes->assertOk();
        $this->assertDatabaseMissing('cnaes', ['id' => $id]);
    }
}
