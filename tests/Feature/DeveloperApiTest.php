<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeveloperApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_developer_stats(): void
    {
        $response = $this->getJson('/api/v1/dev/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_people',
                    'individual_people',
                    'legal_people',
                    'active_people',
                    'inactive_people',
                    'total_groups',
                    'total_cities',
                    'total_states',
                    'total_cnaes',
                    'server_time',
                ]
            ]);
    }

    public function test_can_reset_system(): void
    {
        $account = Account::create([
            'name' => 'Matriz Teste',
            'subdomain' => 'matriz',
            'document' => '12345678000190',
            'email' => 'admin@teste.com',
            'status' => 'active',
        ]);

        Person::create([
            'account_id' => $account->id,
            'person_type' => 'individual',
            'name' => 'Pessoa de Teste',
            'document_number' => '12345678901',
            'status' => 'active',
        ]);

        $this->assertEquals(1, Person::count());

        $response = $this->postJson('/api/v1/dev/reset');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertEquals(0, Person::count());
    }

    public function test_can_populate_people_via_api(): void
    {
        Account::create([
            'name' => 'Matriz Teste',
            'subdomain' => 'matriz',
            'document' => '12345678000190',
            'email' => 'admin@teste.com',
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/dev/populate-people', [
            'count' => 10,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'total_people' => 10,
            ]);

        $this->assertEquals(10, Person::count());
    }
}
