<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Person;
use App\Models\State;
use App\Models\User;
use App\Models\Account;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class IntegrationApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected City $city;

    protected Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::create([
            'name' => 'Matriz Test',
            'subdomain' => 'matriz',
            'status' => 'active',
        ]);

        $this->user = User::factory()->create([
            'account_id' => $this->account->id,
        ]);

        $state = State::create([
            'code' => 'DF',
            'name' => 'Distrito Federal',
        ]);

        $this->city = City::create([
            'state_id' => $state->id,
            'ibge_code' => '5300108',
            'name' => 'Brasília',
        ]);
    }

    public function test_can_list_registered_system_integrations(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/integrations/list');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'total',
                'data' => [
                    '*' => ['id', 'name', 'category', 'url', 'status'],
                ],
            ]);

        $this->assertGreaterThanOrEqual(4, $response->json('total'));
    }

    public function test_can_query_cnpj_from_cnpja_service(): void
    {
        Http::fake([
            'open.cnpja.com/office/*' => Http::response([
                'taxId' => '00000000000191',
                'company' => [
                    'name' => 'BANCO DO BRASIL SA',
                    'nature' => ['text' => 'Sociedade de Economia Mista'],
                    'size' => ['text' => 'Demais'],
                ],
                'alias' => 'Direcao Geral',
                'status' => ['id' => 2, 'text' => 'Ativa'],
                'statusDate' => '2005-11-03',
                'founded' => '1966-08-01',
                'address' => [
                    'municipality' => 5300108,
                    'street' => 'Quadra Saun Quadra 5 Bloco B',
                    'number' => 'SN',
                    'details' => 'Edifício Sede',
                    'district' => 'Asa Norte',
                    'city' => 'Brasília',
                    'state' => 'DF',
                    'zip' => '70040912',
                ],
                'phones' => [['area' => '61', 'number' => '34939002']],
                'emails' => [['address' => 'secex@bb.com.br']],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/integrations/cnpj/00000000000191');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'BANCO DO BRASIL SA')
            ->assertJsonPath('data.trade_name', 'Direcao Geral')
            ->assertJsonPath('data.address.city_id', $this->city->id)
            ->assertJsonPath('data.address.city_name', 'Brasília')
            ->assertJsonPath('data.address.state_code', 'DF');
    }

    public function test_rejects_cnpj_with_invalid_length(): void
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/integrations/cnpj/12345');

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_rejects_person_with_invalid_cpf_mathematical_digits(): void
    {
        $payload = [
            'person_type' => 'individual',
            'name' => 'Pessoa Com CPF Falso',
            'document_number' => '123.456.789-00', // Inválido
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/people', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['document_number']);
    }

    public function test_rejects_person_with_invalid_cnpj_mathematical_digits(): void
    {
        $payload = [
            'person_type' => 'legal',
            'name' => 'Empresa Com CNPJ Falso',
            'document_number' => '11.222.333/0001-99', // Inválido
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/people', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['document_number']);
    }

    public function test_accepts_person_with_valid_cnpj(): void
    {
        $payload = [
            'person_type' => 'legal',
            'name' => 'Empresa Banco do Brasil SA',
            'document_number' => '00.000.000/0001-91', // CNPJ Válido
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/people', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Empresa Banco do Brasil SA')
            ->assertJsonPath('data.document_number', '00000000000191');
    }
}
