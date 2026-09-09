<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_paginate_cities(): void
    {
        $state = State::create(['code' => 'SP', 'name' => 'São Paulo']);

        for ($i = 1; $i <= 25; $i++) {
            City::create([
                'state_id' => $state->id,
                'name' => "Cidade {$i}",
                'ibge_code' => sprintf('35000%02d', $i),
            ]);
        }

        $response = $this->getJson('/api/v1/cities?page=1&per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                    'from',
                    'to',
                ]
            ])
            ->assertJsonPath('meta.total', 25)
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.last_page', 3);

        $this->assertCount(10, $response->json('data'));
    }

    public function test_can_search_cities_with_pagination(): void
    {
        $state = State::create(['code' => 'SP', 'name' => 'São Paulo']);

        City::create(['state_id' => $state->id, 'name' => 'Campinas', 'ibge_code' => '3509502']);
        City::create(['state_id' => $state->id, 'name' => 'São Paulo', 'ibge_code' => '3550308']);
        City::create(['state_id' => $state->id, 'name' => 'Santos', 'ibge_code' => '3548500']);

        $response = $this->getJson('/api/v1/cities?search=Camp&paginate=1');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Campinas');
    }
}
