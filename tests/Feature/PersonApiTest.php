<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Account;
use App\Models\City;
use App\Models\Person;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;

class PersonApiTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected City $city;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::create([
            'name' => 'Matriz Test',
            'subdomain' => 'matriz',
            'status' => 'active',
        ]);

        $state = State::create([
            'code' => 'SP',
            'name' => 'São Paulo',
        ]);

        $this->city = City::create([
            'ibge_code' => '3550308',
            'state_id' => $state->id,
            'name' => 'São Paulo',
        ]);
    }

    public function test_can_list_people(): void
    {
        $response = $this->getJson('/api/v1/people');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links',
                'meta' => ['current_page', 'per_page', 'total'],
            ]);
    }

    public function test_can_create_individual_person(): void
    {
        $cpf = '98765432100';

        $payload = [
            'person_type' => 'individual',
            'name' => 'Técnico de Campo João',
            'document_number' => $cpf,
            'email' => 'joao.campo@provedor.com.br',
            'phone' => '(11) 91111-2222',
            'is_employee' => true,
            'is_requester' => false,
            'city_id' => $this->city->id,
        ];

        $response = $this->postJson('/api/v1/people', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Técnico de Campo João')
            ->assertJsonPath('data.personas.is_employee', true)
            ->assertJsonPath('data.address.city_id', $this->city->id);

        $this->assertDatabaseHas('people', [
            'document_number' => $cpf,
            'name' => 'Técnico de Campo João',
        ]);
    }

    public function test_can_create_legal_entity_person(): void
    {
        $cnpj = '12345678000199';

        $payload = [
            'person_type' => 'legal',
            'name' => 'Distribuidora de Fibra Óptica LTDA',
            'trade_name' => 'Fibra Distribuidora',
            'document_number' => $cnpj,
            'rg_ie' => '123.456.789.000',
            'email' => 'compras@fibradistribuidora.com.br',
            'is_supplier' => true,
            'city_id' => $this->city->id,
        ];

        $response = $this->postJson('/api/v1/people', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.trade_name', 'Fibra Distribuidora')
            ->assertJsonPath('data.personas.is_supplier', true);
    }

    public function test_cannot_create_duplicate_document_number(): void
    {
        $cpf = '11122233344';

        Person::create([
            'account_id' => $this->account->id,
            'person_type' => 'individual',
            'name' => 'Primeiro Registro',
            'document_number' => $cpf,
        ]);

        $payload = [
            'person_type' => 'individual',
            'name' => 'Segundo Registro Duplicado',
            'document_number' => $cpf,
        ];

        $response = $this->postJson('/api/v1/people', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['document_number']);
    }

    public function test_strict_relational_integrity_blocks_city_deletion_if_person_linked(): void
    {
        Person::create([
            'account_id' => $this->account->id,
            'city_id' => $this->city->id,
            'person_type' => 'individual',
            'name' => 'Morador de São Paulo',
            'document_number' => '55566677788',
        ]);

        $this->expectException(QueryException::class);
        $this->city->delete();
    }
}
