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


    protected function generateValidCpf(): string
    {
        $n = [rand(1, 9), rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9)];
        $d1 = 0;
        for ($i = 0; $i < 9; $i++) {
            $d1 += $n[$i] * (10 - $i);
        }
        $r1 = ($d1 * 10) % 11;
        $n[9] = ($r1 >= 10) ? 0 : $r1;

        $d2 = 0;
        for ($i = 0; $i < 10; $i++) {
            $d2 += $n[$i] * (11 - $i);
        }
        $r2 = ($d2 * 10) % 11;
        $n[10] = ($r2 >= 10) ? 0 : $r2;

        return implode('', $n);
    }

    protected function generateValidCnpj(): string
    {
        $n = [rand(1, 9), rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9), rand(0, 9), 0, 0, 0, 1];
        $w1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $s1 = 0;
        for ($i = 0; $i < 12; $i++) {
            $s1 += $n[$i] * $w1[$i];
        }
        $r1 = $s1 % 11;
        $n[12] = ($r1 < 2) ? 0 : 11 - $r1;

        $w2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $s2 = 0;
        for ($i = 0; $i < 13; $i++) {
            $s2 += $n[$i] * $w2[$i];
        }
        $r2 = $s2 % 11;
        $n[13] = ($r2 < 2) ? 0 : 11 - $r2;

        return implode('', $n);
    }

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
        $cnpj = '00000000000191';

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
        $cpf = $this->generateValidCpf();

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
            'document_number' => $this->generateValidCpf(),
        ]);

        $this->expectException(QueryException::class);
        $this->city->delete();
    }

    public function test_can_create_person_with_brazilian_date_format(): void
    {
        $payload = [
            'person_type' => 'individual',
            'name' => 'Carlos da Silva Brasil',
            'document_number' => $cpf = $this->generateValidCpf(),
            'birth_date' => '25/12/1985',
            'email' => 'carlos.brasil@redepronta.com.br',
            'city_id' => $this->city->id,
        ];

        $response = $this->postJson('/api/v1/people', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.birth_date', '1985-12-25')
            ->assertJsonPath('data.birth_date_formatted', '25/12/1985');

        $this->assertDatabaseHas('people', [
            'document_number' => $cpf,
            'birth_date' => '1985-12-25',
        ]);
    }

    public function test_can_save_distinct_commercial_address(): void
    {
        $payload = [
            'person_type' => 'individual',
            'name' => 'Empresário Fernando',
            'document_number' => $this->generateValidCpf(),
            'email' => 'fernando@empresa.com.br',
            'postal_code' => '01001-000',
            'street' => 'Praça da Sé',
            'number' => '100',
            'city_id' => $this->city->id,
            'commercial_same_as_residential' => false,
            'commercial_postal_code' => '04578-000',
            'commercial_street' => 'Avenida das Nações Unidas',
            'commercial_number' => '12901',
            'commercial_neighborhood' => 'Brooklin',
            'commercial_city_id' => $this->city->id,
        ];

        $response = $this->postJson('/api/v1/people', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.residential_address.street', 'Praça da Sé')
            ->assertJsonPath('data.commercial_address.same_as_residential', false)
            ->assertJsonPath('data.commercial_address.street', 'Avenida das Nações Unidas')
            ->assertJsonPath('data.commercial_address.number', '12901');
    }

    public function test_city_search_requires_at_least_three_characters(): void
    {
        // 2 characters should return empty
        $responseShort = $this->getJson('/api/v1/cities?q=Sã');
        $responseShort->assertStatus(200)->assertJson(['data' => []]);

        // 3 characters should search and return matching
        $responseMatch = $this->getJson('/api/v1/cities?q=São');
        $responseMatch->assertStatus(200);
        $this->assertNotEmpty($responseMatch->json('data'));
    }

        public function test_can_create_person_with_all_simplified_roles_and_registration_date(): void
    {
        $cpf = $this->generateValidCpf();
        $payload = [
            'person_type' => 'individual',
            'name' => 'Carlos Alberto Motorista e Técnico',
            'trade_name' => 'Betão Operações',
            'document_number' => $cpf,
            'registration_date' => '08/09/2026',
            'group_name' => 'Técnicos Próprios & Parceiros',
            'is_client' => false,
            'is_supplier' => false,
            'is_employee' => true,
            'is_outsourced' => true,
            'is_seller' => false,
            'is_driver' => true,
            'is_carrier' => false,
        ];

        $response = $this->postJson('/api/v1/people', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.personas.is_employee', true)
            ->assertJsonPath('data.personas.is_outsourced', true)
            ->assertJsonPath('data.personas.is_driver', true)
            ->assertJsonPath('data.registration_date_formatted', '08/09/2026');

        $this->assertDatabaseHas('people', [
            'document_number' => $cpf,
            'group_name' => 'Técnicos Próprios & Parceiros',
            'is_employee' => 1,
            'is_outsourced' => 1,
            'is_driver' => 1,
        ]);
    }

    public function test_can_search_postal_code_via_viacep_endpoint(): void
    {
        // CEP da Praça da Sé em São Paulo
        $response = $this->getJson('/api/v1/cep/01001000');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.street', 'Praça da Sé')
            ->assertJsonPath('data.neighborhood', 'Sé')
            ->assertJsonPath('data.state_code', 'SP')
            ->assertJsonPath('data.ibge_code', '3550308');
    }

        public function test_can_save_person_with_geo_coordinates_and_reference(): void
    {
        $cpf = $this->generateValidCpf();
        $payload = [
            'person_type' => 'individual',
            'name' => 'Cliente com Coordenadas GPS',
            'document_number' => $cpf,
            'postal_code' => '01001-000',
            'street' => 'Praça da Sé',
            'number' => '100',
            'neighborhood' => 'Sé',
            'city_id' => $this->city->id,
            'reference' => 'Em frente à Catedral da Sé, portão lateral preto',
            'latitude' => -23.550520,
            'longitude' => -46.633308,
            'commercial_same_as_residential' => false,
            'commercial_postal_code' => '01310-100',
            'commercial_street' => 'Avenida Paulista',
            'commercial_number' => '1000',
            'commercial_neighborhood' => 'Bela Vista',
            'commercial_city_id' => $this->city->id,
            'commercial_reference' => 'Próximo à Estação Trianon Masp',
            'commercial_latitude' => -23.565734,
            'commercial_longitude' => -46.651582,
        ];

        $response = $this->postJson('/api/v1/people', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.address.reference', 'Em frente à Catedral da Sé, portão lateral preto')
            ->assertJsonPath('data.address.latitude', -23.550520)
            ->assertJsonPath('data.address.longitude', -46.633308)
            ->assertJsonPath('data.commercial_address.reference', 'Próximo à Estação Trianon Masp')
            ->assertJsonPath('data.commercial_address.latitude', -23.565734)
            ->assertJsonPath('data.commercial_address.longitude', -46.651582);

        $this->assertDatabaseHas('people', [
            'document_number' => $cpf,
            'reference' => 'Em frente à Catedral da Sé, portão lateral preto',
            'latitude' => -23.550520,
            'longitude' => -46.633308,
            'commercial_reference' => 'Próximo à Estação Trianon Masp',
            'commercial_latitude' => -23.565734,
            'commercial_longitude' => -46.651582,
        ]);
    }

    public function test_index_returns_persona_counts_in_metadata(): void
    {
        Person::create([
            'account_id' => $this->account->id,
            'name' => 'Cliente Um',
            'person_type' => 'individual',
            'document_number' => $this->generateValidCpf(),
            'is_client' => true,
            'is_employee' => false,
            'status' => 'active',
        ]);

        Person::create([
            'account_id' => $this->account->id,
            'name' => 'Colaborador Dois',
            'person_type' => 'individual',
            'document_number' => $this->generateValidCpf(),
            'is_client' => false,
            'is_employee' => true,
            'status' => 'active',
        ]);

        $response = $this->getJson('/api/v1/people');

        $response->assertOk()
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'counts' => [
                        'total',
                        'client',
                        'supplier',
                        'employee',
                        'outsourced',
                        'seller',
                        'driver',
                        'carrier',
                        'requester',
                    ],
                ],
            ]);

        $this->assertEquals(2, $response->json('meta.counts.total'));
        $this->assertEquals(1, $response->json('meta.counts.client'));
        $this->assertEquals(1, $response->json('meta.counts.employee'));
    }

    public function test_index_supports_state_and_status_filtering(): void
    {
        $rjState = State::create(['code' => 'RJ', 'name' => 'Rio de Janeiro']);
        $rjCity = City::create([
            'ibge_code' => '3304557',
            'name' => 'Rio de Janeiro',
            'state_id' => $rjState->id,
        ]);

        Person::create([
            'account_id' => $this->account->id,
            'name' => 'Paulista Ativo',
            'person_type' => 'individual',
            'document_number' => $this->generateValidCpf(),
            'city_id' => $this->city->id,
            'status' => 'active',
        ]);

        Person::create([
            'account_id' => $this->account->id,
            'name' => 'Carioca Inativo',
            'person_type' => 'individual',
            'document_number' => $this->generateValidCpf(),
            'city_id' => $rjCity->id,
            'status' => 'inactive',
        ]);

        // Filtro por Estado SP
        $resSP = $this->getJson('/api/v1/people?state_code=SP');
        $resSP->assertOk();
        $this->assertCount(1, $resSP->json('data'));
        $this->assertEquals('Paulista Ativo', $resSP->json('data.0.name'));

        // Filtro por Status inactive
        $resInactive = $this->getJson('/api/v1/people?status=inactive');
        $resInactive->assertOk();
        $this->assertCount(1, $resInactive->json('data'));
        $this->assertEquals('Carioca Inativo', $resInactive->json('data.0.name'));
    }

    public function test_index_supports_sorting_by_name_city_and_date(): void
    {
        $cityA = City::create([
            'ibge_code' => '3500105',
            'name' => 'Adamantina',
            'state_id' => $this->city->state_id,
        ]);

        $cityZ = City::create([
            'ibge_code' => '3557204',
            'name' => 'Zacarias',
            'state_id' => $this->city->state_id,
        ]);

        Person::create([
            'account_id' => $this->account->id,
            'name' => 'Bruno Zacarias',
            'person_type' => 'individual',
            'document_number' => $this->generateValidCpf(),
            'city_id' => $cityZ->id,
            'registration_date' => '2026-01-01',
            'status' => 'active',
        ]);

        Person::create([
            'account_id' => $this->account->id,
            'name' => 'Ana Adamantina',
            'person_type' => 'individual',
            'document_number' => $this->generateValidCpf(),
            'city_id' => $cityA->id,
            'registration_date' => '2026-05-01',
            'status' => 'active',
        ]);

        // Ordenação por nome DESC
        $resNameDesc = $this->getJson('/api/v1/people?sort_by=name&sort_direction=desc');
        $resNameDesc->assertOk();
        $this->assertEquals('Bruno Zacarias', $resNameDesc->json('data.0.name'));

        // Ordenação por cidade ASC
        $resCityAsc = $this->getJson('/api/v1/people?sort_by=city&sort_direction=asc');
        $resCityAsc->assertOk();
        $this->assertEquals('Ana Adamantina', $resCityAsc->json('data.0.name'));

        // Ordenação por data DESC
        $resDateDesc = $this->getJson('/api/v1/people?sort_by=date&sort_direction=desc');
        $resDateDesc->assertOk();
        $this->assertEquals('Ana Adamantina', $resDateDesc->json('data.0.name'));
    }

    public function test_requester_and_employee_are_distinct_roles_and_can_be_filtered_separately(): void
    {
        $cpfRequester = $this->generateValidCpf();
        $cpfEmployee = $this->generateValidCpf();

        // 1. Cadastrar Solicitante (sem ser funcionário)
        $resReq = $this->postJson('/api/v1/people', [
            'person_type' => 'individual',
            'name' => 'Maria Solicitante de Chamados',
            'document_number' => $cpfRequester,
            'is_requester' => true,
            'is_employee' => false,
            'is_client' => true,
        ]);
        $resReq->assertCreated()
            ->assertJsonPath('data.personas.is_requester', true)
            ->assertJsonPath('data.personas.is_employee', false);

        // 2. Cadastrar Funcionário (sem ser solicitante)
        $resEmp = $this->postJson('/api/v1/people', [
            'person_type' => 'individual',
            'name' => 'João Técnico Funcionário CLT',
            'document_number' => $cpfEmployee,
            'is_requester' => false,
            'is_employee' => true,
        ]);
        $resEmp->assertCreated()
            ->assertJsonPath('data.personas.is_requester', false)
            ->assertJsonPath('data.personas.is_employee', true);

        // 3. Filtrar por persona=requester
        $filterReq = $this->getJson('/api/v1/people?persona=requester');
        $filterReq->assertOk();
        $namesReq = collect($filterReq->json('data'))->pluck('name');
        $this->assertTrue($namesReq->contains('Maria Solicitante de Chamados'));
        $this->assertFalse($namesReq->contains('João Técnico Funcionário CLT'));

        // 4. Filtrar por persona=employee
        $filterEmp = $this->getJson('/api/v1/people?persona=employee');
        $filterEmp->assertOk();
        $namesEmp = collect($filterEmp->json('data'))->pluck('name');
        $this->assertTrue($namesEmp->contains('João Técnico Funcionário CLT'));
        $this->assertFalse($namesEmp->contains('Maria Solicitante de Chamados'));

        // 5. Validar contadores separados na resposta
        $this->assertGreaterThanOrEqual(1, $filterReq->json('meta.counts.requester'));
        $this->assertGreaterThanOrEqual(1, $filterEmp->json('meta.counts.employee'));
    }
}
