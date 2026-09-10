<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Depot;
use App\Models\DepotCluster;
use App\Models\Material;
use App\Models\MaterialOwner;
use App\Models\Person;
use App\Models\StockBalance;
use App\Models\StockSerial;
use App\Models\Unit;
use App\Services\Stock\ClusterStockService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class StockWmsModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected Person $requesterPerson;
    protected MaterialOwner $owner;
    protected Unit $unitUnd;
    protected Unit $unitMt;
    protected DepotCluster $cluster;
    protected Depot $centralDepot;
    protected Depot $baseDepot;
    protected Depot $secondaryDepot;
    protected Material $materialOnu;
    protected Material $materialCable;

    protected function setUp(): void
    {
        parent::setUp();

        // Garante suporte a foreign keys no SQLite em memória
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        $this->account = Account::create([
            'name' => 'Rede Pronta Matriz',
            'subdomain' => 'matriz',
            'document' => '12.345.678/0001-90',
            'status' => 'active',
        ]);

        $this->unitUnd = Unit::firstOrCreate(['code' => 'UND'], ['name' => 'Unidade']);
        $this->unitMt = Unit::firstOrCreate(['code' => 'MT'], ['name' => 'Metro']);

        // Tipos de Depósito Padrão
        \App\Models\DepotType::create([
            'account_id' => $this->account->id,
            'code' => 'CENTRAL',
            'name' => 'Almoxarifado Central',
            'description' => 'Depósito principal',
            'is_active' => true,
        ]);
        \App\Models\DepotType::create([
            'account_id' => $this->account->id,
            'code' => 'REGIONAL_BASE',
            'name' => 'Base Regional',
            'description' => 'Base operacional regional',
            'is_active' => true,
        ]);
        \App\Models\DepotType::create([
            'account_id' => $this->account->id,
            'code' => 'LAB_REPAIR',
            'name' => 'Laboratório de Reparo',
            'description' => 'Laboratório de manutenção',
            'is_active' => true,
        ]);

        // Categorias de Material Padrão
        \App\Models\MaterialCategory::create([
            'account_id' => $this->account->id,
            'name' => 'Ativos de Rede',
            'code' => 'ATIVOS',
            'color' => '#3B82F6',
            'is_active' => true,
        ]);
        \App\Models\MaterialCategory::create([
            'account_id' => $this->account->id,
            'name' => 'Cabos & Fibras',
            'code' => 'CABOS',
            'color' => '#FC6714',
            'is_active' => true,
        ]);

        // Proprietário Padrão Regional (vínculo obrigatório da Posição Regional)
        $this->requesterPerson = Person::create([
            'account_id' => $this->account->id,
            'name' => 'Operadora Parceira Telecom',
            'document_number' => '11.222.333/0001-44',
            'status' => 'active',
            'is_requester' => true,
        ]);

        $this->owner = MaterialOwner::create([
            'account_id' => $this->account->id,
            'person_id' => $this->requesterPerson->id,
            'code' => 'PROP-PADRAO',
            'name' => 'Proprietário Padrão Regional',
            'is_active' => true,
        ]);

        // 1. Posição Regional
        $this->cluster = DepotCluster::create([
            'account_id' => $this->account->id,
            'owner_id' => $this->owner->id,
            'code' => 'REG-CAMPINAS',
            'name' => 'Posição Regional Campinas',
            'description' => 'Região Metropolitana de Campinas e RMC',
            'color' => '#FC6714',
            'is_active' => true,
        ]);

        // 2. Depósitos físicos: Central, Base Regional e Depósito Avançado
        $this->centralDepot = Depot::create([
            'account_id' => $this->account->id,
            'cluster_id' => $this->cluster->id,
            'code' => 'ALMOX-CENTRAL',
            'name' => 'Almoxarifado Central Matriz',
            'type' => 'CENTRAL',
            'is_active' => true,
        ]);

        $this->baseDepot = Depot::create([
            'account_id' => $this->account->id,
            'cluster_id' => $this->cluster->id,
            'code' => 'BASE-CPS',
            'name' => 'Base Operacional Campinas',
            'type' => 'REGIONAL_BASE',
            'is_active' => true,
        ]);

        $this->secondaryDepot = Depot::create([
            'account_id' => $this->account->id,
            'cluster_id' => $this->cluster->id,
            'code' => 'DEP-SUMARE',
            'name' => 'Depósito Avançado Sumaré',
            'type' => 'REGIONAL_BASE',
            'is_active' => true,
        ]);

        // 3. Materiais: ONU (com serial) e Cabo Drop (sem serial)
        $this->materialOnu = Material::create([
            'account_id' => $this->account->id,
            'unit_id' => $this->unitUnd->id,
            'code' => 'ONU-XPON-GIGA',
            'name' => 'ONU XPON Bridge / Router Gigabit',
            'category' => 'Ativos de Rede',
            'has_serial' => true,
            'unit_cost' => 125.00,
            'min_stock' => 10,
            'is_active' => true,
        ]);

        $this->materialCable = Material::create([
            'account_id' => $this->account->id,
            'unit_id' => $this->unitMt->id,
            'code' => 'CABO-DROP-1FO',
            'name' => 'Cabo Óptico Drop 1FO Compacto',
            'category' => 'Cabos',
            'has_serial' => false,
            'unit_cost' => 0.85,
            'min_stock' => 500,
            'is_active' => true,
        ]);
    }

    /**
     * Testa o cálculo em tempo real do Saldo da Posição Regional.
     */
    public function test_cluster_stock_service_calculates_regional_position_balance(): void
    {
        $service = app(ClusterStockService::class);

        // Insere 2000 metros de cabo na Base Regional
        StockBalance::create([
            'account_id' => $this->account->id,
            'depot_id' => $this->baseDepot->id,
            'material_id' => $this->materialCable->id,
            'quantity' => 2000,
            'reserved_quantity' => 100,
        ]);

        // Insere 300 metros de cabo no Depósito Avançado
        StockBalance::create([
            'account_id' => $this->account->id,
            'depot_id' => $this->secondaryDepot->id,
            'material_id' => $this->materialCable->id,
            'quantity' => 300,
            'reserved_quantity' => 0,
        ]);

        // Insere itens serializados na Base
        StockBalance::create([
            'account_id' => $this->account->id,
            'depot_id' => $this->baseDepot->id,
            'material_id' => $this->materialOnu->id,
            'quantity' => 5,
            'reserved_quantity' => 0,
        ]);

        StockSerial::create([
            'account_id' => $this->account->id,
            'material_id' => $this->materialOnu->id,
            'current_depot_id' => $this->baseDepot->id,
            'serial_number' => 'ALCLB001',
            'status' => 'IN_STOCK',
        ]);
        StockSerial::create([
            'account_id' => $this->account->id,
            'material_id' => $this->materialOnu->id,
            'current_depot_id' => $this->baseDepot->id,
            'serial_number' => 'ALCLB002',
            'status' => 'IN_STOCK',
        ]);

        // Insere itens serializados no Depósito Avançado
        StockBalance::create([
            'account_id' => $this->account->id,
            'depot_id' => $this->secondaryDepot->id,
            'material_id' => $this->materialOnu->id,
            'quantity' => 2,
            'reserved_quantity' => 0,
        ]);
        StockSerial::create([
            'account_id' => $this->account->id,
            'material_id' => $this->materialOnu->id,
            'current_depot_id' => $this->secondaryDepot->id,
            'serial_number' => 'ALCLB003',
            'status' => 'IN_STOCK',
        ]);

        $result = $service->calculateClusterStock($this->cluster->id, $this->account->id);

        $this->assertEquals('Posição Regional Campinas', $result['cluster']['name']);
        $this->assertEquals(2, $result['summary']['total_materials']);
        $this->assertEquals(3, $result['summary']['depots_count']);

        // Encontra o cabo
        $cableData = collect($result['materials'])->firstWhere('material_id', $this->materialCable->id);
        $this->assertNotNull($cableData);
        $this->assertEquals(2300, $cableData['total_quantity']);
        $this->assertEquals(2200, $cableData['available_quantity']); // 2300 - 100 reservado

        // Encontra o equipamento serializado
        $onuData = collect($result['materials'])->firstWhere('material_id', $this->materialOnu->id);
        $this->assertNotNull($onuData);
        $this->assertEquals(7, $onuData['total_quantity']);
        $this->assertEquals(3, $onuData['serials_in_stock']); // 2 na base + 1 no depósito avançado
    }

    /**
     * Testa transferência atômica de material convencional entre depósitos (Central -> Base).
     */
    public function test_transfer_conventional_material_between_depots(): void
    {
        $service = app(ClusterStockService::class);

        // Prepara saldo inicial na Central
        StockBalance::create([
            'account_id' => $this->account->id,
            'depot_id' => $this->centralDepot->id,
            'material_id' => $this->materialCable->id,
            'quantity' => 5000,
            'reserved_quantity' => 0,
        ]);
        StockBalance::create([
            'account_id' => $this->account->id,
            'depot_id' => $this->baseDepot->id,
            'material_id' => $this->materialCable->id,
            'quantity' => 0,
            'reserved_quantity' => 0,
        ]);

        $movement = $service->transfer([
            'source_depot_id' => $this->centralDepot->id,
            'destination_depot_id' => $this->baseDepot->id,
            'material_id' => $this->materialCable->id,
            'quantity' => 1500,
            'document_ref' => 'REQ-001',
            'notes' => 'Abastecimento da base regional',
        ], $this->account->id);

        $this->assertEquals('TRANSFER', $movement->movement_type);
        $this->assertEquals(1500, $movement->quantity);

        // Checa saldo na Central (deve ter baixado para 3500)
        $sourceBal = StockBalance::where('depot_id', $this->centralDepot->id)
            ->where('material_id', $this->materialCable->id)
            ->first();
        $this->assertEquals(3500, $sourceBal->quantity);

        // Checa saldo na Base (deve ter subido para 1500)
        $destBal = StockBalance::where('depot_id', $this->baseDepot->id)
            ->where('material_id', $this->materialCable->id)
            ->first();
        $this->assertEquals(1500, $destBal->quantity);
    }

    /**
     * Testa que transferência com saldo insuficiente gera exceção e não altera saldo.
     */
    public function test_transfer_fails_when_insufficient_stock(): void
    {
        $service = app(ClusterStockService::class);

        StockBalance::create([
            'account_id' => $this->account->id,
            'depot_id' => $this->centralDepot->id,
            'material_id' => $this->materialCable->id,
            'quantity' => 100,
            'reserved_quantity' => 0,
        ]);

        $this->expectException(ValidationException::class);

        $service->transfer([
            'source_depot_id' => $this->centralDepot->id,
            'destination_depot_id' => $this->baseDepot->id,
            'material_id' => $this->materialCable->id,
            'quantity' => 500, // Maior que 100 disponível
        ], $this->account->id);
    }

    /**
     * Testa transferência de materiais serializados (Central -> Depósito Avançado) atualizando a localização dos números de série.
     */
    public function test_transfer_serialized_materials_updates_serial_location(): void
    {
        $service = app(ClusterStockService::class);

        // Saldo inicial de 3 itens serializados na Central
        StockBalance::create([
            'account_id' => $this->account->id,
            'depot_id' => $this->centralDepot->id,
            'material_id' => $this->materialOnu->id,
            'quantity' => 3,
            'reserved_quantity' => 0,
        ]);
        StockBalance::create([
            'account_id' => $this->account->id,
            'depot_id' => $this->secondaryDepot->id,
            'material_id' => $this->materialOnu->id,
            'quantity' => 0,
            'reserved_quantity' => 0,
        ]);

        $s1 = StockSerial::create([
            'account_id' => $this->account->id,
            'material_id' => $this->materialOnu->id,
            'current_depot_id' => $this->centralDepot->id,
            'serial_number' => 'SERIAL-TEST-001',
            'status' => 'IN_STOCK',
        ]);
        $s2 = StockSerial::create([
            'account_id' => $this->account->id,
            'material_id' => $this->materialOnu->id,
            'current_depot_id' => $this->centralDepot->id,
            'serial_number' => 'SERIAL-TEST-002',
            'status' => 'IN_STOCK',
        ]);

        // Transfere 2 itens serializados para o Depósito Avançado
        $movement = $service->transfer([
            'source_depot_id' => $this->centralDepot->id,
            'destination_depot_id' => $this->secondaryDepot->id,
            'material_id' => $this->materialOnu->id,
            'quantity' => 2,
            'serial_ids' => [$s1->id, $s2->id],
            'notes' => 'Transferência para base avançada',
        ], $this->account->id);

        $this->assertEquals(2, $movement->serials->count());

        // Verifica seriais agora apontando para o depósito de destino
        $s1->refresh();
        $s2->refresh();
        $this->assertEquals($this->secondaryDepot->id, $s1->current_depot_id);
        $this->assertEquals($this->secondaryDepot->id, $s2->current_depot_id);

        // Verifica saldos
        $sourceBal = StockBalance::where('depot_id', $this->centralDepot->id)->where('material_id', $this->materialOnu->id)->first();
        $destBal = StockBalance::where('depot_id', $this->secondaryDepot->id)->where('material_id', $this->materialOnu->id)->first();
        $this->assertEquals(1, $sourceBal->quantity);
        $this->assertEquals(2, $destBal->quantity);
    }

    /**
     * Testa integridade referencial estrita ON DELETE RESTRICT (tentativa de excluir cluster com depósito deve falhar).
     */
    public function test_strict_foreign_key_on_delete_restrict_prevents_cluster_deletion(): void
    {
        $this->expectException(QueryException::class);

        // Tentativa de deletar fisicamente um cluster pai com depósitos filhos vinculados
        DB::table('depot_clusters')->where('id', $this->cluster->id)->delete();
    }

    /**
     * Testa endpoints da API de Estoque.
     */
    public function test_stock_api_endpoints(): void
    {
        // 1. Posição Regional
        $response = $this->getJson('/api/v1/stock/regional-position');
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);

        // 2. Catálogo de Materiais
        $responseMat = $this->getJson('/api/v1/materials');
        $responseMat->assertStatus(200);
        $responseMat->assertJsonStructure(['data']);

        // 3. Depósitos
        $responseDep = $this->getJson('/api/v1/depots');
        $responseDep->assertStatus(200);
        $responseDep->assertJsonStructure(['data']);

        // 4. Clusters
        $responseCl = $this->getJson('/api/v1/clusters');
        $responseCl->assertStatus(200);
        $responseCl->assertJsonStructure(['data']);

        // 5. Unidades de Medida
        $responseUnits = $this->getJson('/api/v1/materials/units');
        $responseUnits->assertStatus(200);
        $responseUnits->assertJsonStructure(['data']);
    }

    /**
     * Testa criação de depósito com sanitização de campos opcionais vazios.
     */
    public function test_can_create_depot_successfully(): void
    {
        $payload = [
            'cluster_id' => $this->cluster->id,
            'name' => 'Novo Almoxarifado Zona Norte',
            'code' => 'DEP-ZN',
            'type' => 'REGIONAL_BASE',
            'responsible_person_id' => '',
            'city_id' => '',
            'description' => 'Depósito de apoio operacional Zona Norte',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/depots', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.name', 'Novo Almoxarifado Zona Norte');
        $response->assertJsonPath('data.code', 'DEP-ZN');
        $response->assertJsonPath('data.type', 'REGIONAL_BASE');
        $response->assertJsonPath('data.responsible_person_id', null);
        $response->assertJsonPath('data.city_id', null);

        $this->assertDatabaseHas('depots', [
            'name' => 'Novo Almoxarifado Zona Norte',
            'code' => 'DEP-ZN',
            'cluster_id' => $this->cluster->id,
        ]);
    }

    /**
     * Testa atualização de depósito existente.
     */
    public function test_can_update_depot_successfully(): void
    {
        $payload = [
            'cluster_id' => $this->cluster->id,
            'name' => 'Almoxarifado Central Matriz Renovado',
            'code' => 'ALMOX-CENTRAL-MOD',
            'type' => 'CENTRAL',
            'description' => 'Atualizado para testes',
            'is_active' => true,
        ];

        $response = $this->putJson("/api/v1/depots/{$this->centralDepot->id}", $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Almoxarifado Central Matriz Renovado');
        $response->assertJsonPath('data.code', 'ALMOX-CENTRAL-MOD');

        $this->centralDepot->refresh();
        $this->assertEquals('Almoxarifado Central Matriz Renovado', $this->centralDepot->name);
        $this->assertEquals('ALMOX-CENTRAL-MOD', $this->centralDepot->code);
    }

    /**
     * Testa validação obrigatória ao cadastrar depósito.
     */
    public function test_create_depot_validation_fails_when_fields_missing(): void
    {
        $response = $this->postJson('/api/v1/depots', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cluster_id', 'name', 'code', 'type']);
    }

    /**
     * Testa bloqueio de exclusão de depósito quando houver saldo em estoque.
     */
    public function test_depot_cannot_be_deleted_if_has_stock_balance(): void
    {
        StockBalance::create([
            'account_id' => $this->account->id,
            'depot_id' => $this->secondaryDepot->id,
            'material_id' => $this->materialCable->id,
            'quantity' => 150.00,
        ]);

        $response = $this->deleteJson("/api/v1/depots/{$this->secondaryDepot->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('depots', ['id' => $this->secondaryDepot->id]);
    }

    /**
     * Testa listagem de tipos de depósito.
     */
    public function test_can_list_depot_types(): void
    {
        $response = $this->getJson('/api/v1/depot-types');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'code', 'description', 'is_active', 'depots_count'],
            ],
        ]);
    }

    /**
     * Testa criação de um novo tipo de depósito.
     */
    public function test_can_create_depot_type(): void
    {
        $payload = [
            'name' => 'Depósito de Transbordo',
            'code' => 'TRANSBORDO',
            'description' => 'Área de transbordo temporário de cargas',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/depot-types', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.code', 'TRANSBORDO');
        $response->assertJsonPath('data.name', 'Depósito de Transbordo');

        $this->assertDatabaseHas('depot_types', [
            'code' => 'TRANSBORDO',
            'name' => 'Depósito de Transbordo',
        ]);
    }

    /**
     * Testa exclusão de tipo de depósito em uso deve ser bloqueada.
     */
    public function test_cannot_delete_depot_type_in_use(): void
    {
        $type = \App\Models\DepotType::where('code', 'CENTRAL')->first();

        $response = $this->deleteJson("/api/v1/depot-types/{$type->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('depot_types', ['id' => $type->id]);
    }

    /**
     * Testa criação de depósito com tipo personalizado e pessoa responsável.
     */
    public function test_can_create_depot_with_custom_type_and_responsible_person(): void
    {
        $person = \App\Models\Person::create([
            'account_id' => $this->account->id,
            'name' => 'Carlos Gestor Almoxarifado',
            'document_number' => '111.222.333-44',
            'status' => 'active',
            'role' => 'Gestor WMS',
        ]);

        $customType = \App\Models\DepotType::create([
            'account_id' => $this->account->id,
            'name' => 'Depósito de Quarentena',
            'code' => 'QUARENTENA',
            'is_active' => true,
        ]);

        $payload = [
            'cluster_id' => $this->cluster->id,
            'name' => 'Galpão de Quarentena RMC',
            'code' => 'GALPAO-QUAR-01',
            'type' => 'QUARENTENA',
            'responsible_person_id' => $person->id,
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/depots', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.type', 'QUARENTENA');
        $response->assertJsonPath('data.responsible_person_id', $person->id);
        $response->assertJsonPath('data.responsible_person.name', 'Carlos Gestor Almoxarifado');

        $this->assertDatabaseHas('depots', [
            'code' => 'GALPAO-QUAR-01',
            'type' => 'QUARENTENA',
            'responsible_person_id' => $person->id,
        ]);
    }

    /**
     * Testa listagem de unidades de medida.
     */
    public function test_can_list_units(): void
    {
        $response = $this->getJson('/api/v1/units');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'code', 'name', 'is_active', 'materials_count'],
            ],
        ]);
    }

    /**
     * Testa criação de nova unidade de medida.
     */
    public function test_can_create_unit(): void
    {
        $payload = [
            'code' => 'KG',
            'name' => 'Quilograma',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/units', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.code', 'KG');
        $response->assertJsonPath('data.name', 'Quilograma');

        $this->assertDatabaseHas('units', [
            'code' => 'KG',
            'name' => 'Quilograma',
        ]);
    }

    /**
     * Testa bloqueio de exclusão de unidade vinculada a material.
     */
    public function test_cannot_delete_unit_in_use(): void
    {
        // $this->unitUnd está vinculada ao $this->materialOnu
        $response = $this->deleteJson("/api/v1/units/{$this->unitUnd->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('units', ['id' => $this->unitUnd->id]);
    }

    /**
     * Testa listagem de categorias de material.
     */
    public function test_can_list_material_categories(): void
    {
        $response = $this->getJson('/api/v1/material-categories');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'code', 'description', 'color', 'is_active', 'materials_count'],
            ],
        ]);
    }

    /**
     * Testa criação de nova categoria de material.
     */
    public function test_can_create_material_category(): void
    {
        $payload = [
            'name' => 'Ferramentas de Fusão Óptica',
            'code' => 'FERRAMENTAS_FUSAO',
            'description' => 'Máquinas de fusão e clivadores de alta precisão',
            'color' => '#8B5CF6',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/material-categories', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.name', 'Ferramentas de Fusão Óptica');
        $response->assertJsonPath('data.code', 'FERRAMENTAS_FUSAO');

        $this->assertDatabaseHas('material_categories', [
            'name' => 'Ferramentas de Fusão Óptica',
            'code' => 'FERRAMENTAS_FUSAO',
        ]);
    }

    /**
     * Testa bloqueio de exclusão de categoria com materiais vinculados.
     */
    public function test_cannot_delete_material_category_in_use(): void
    {
        // $this->materialOnu tem category = 'Ativos de Rede'
        $category = \App\Models\MaterialCategory::where('name', 'Ativos de Rede')->first();

        $response = $this->deleteJson("/api/v1/material-categories/{$category->id}");

        $response->assertStatus(422);
        $this->assertDatabaseHas('material_categories', ['id' => $category->id]);
    }

    /**
     * Testa listagem de proprietários de materiais.
     */
    public function test_can_list_material_owners(): void
    {
        $response = $this->getJson('/api/v1/material-owners');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [],
        ]);
    }

    /**
     * Testa criação de proprietário e garantia de que a pessoa vinculada receba o papel de solicitante.
     */
    public function test_can_create_material_owner_and_ensures_person_is_requester(): void
    {
        $person = \App\Models\Person::create([
            'account_id' => $this->account->id,
            'name' => 'Operadora Parceira Telecom',
            'document_number' => '44.555.666/0001-77',
            'status' => 'active',
            'is_requester' => false,
        ]);

        $this->assertFalse($person->is_requester);

        $payload = [
            'person_id' => $person->id,
            'code' => 'PROP-VIVO',
            'name' => 'Telefônica Brasil S.A. / Vivo',
            'description' => 'Equipamentos de comodato de fibra óptica',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/material-owners', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.code', 'PROP-VIVO');
        $response->assertJsonPath('data.name', 'Telefônica Brasil S.A. / Vivo');
        $response->assertJsonPath('data.person_id', $person->id);
        $response->assertJsonPath('data.person.is_requester', true);

        // Verifica que a pessoa agora é solicitante no banco
        $person->refresh();
        $this->assertTrue($person->is_requester);

        $this->assertDatabaseHas('material_owners', [
            'code' => 'PROP-VIVO',
            'person_id' => $person->id,
        ]);
    }

    /**
     * Testa exclusão de proprietário de materiais.
     */
    public function test_can_delete_material_owner(): void
    {
        $person = \App\Models\Person::create([
            'account_id' => $this->account->id,
            'name' => 'Claro Solicitante Provedor',
            'document_number' => '55.666.777/0001-88',
            'status' => 'active',
            'is_requester' => true,
        ]);

        $owner = \App\Models\MaterialOwner::create([
            'account_id' => $this->account->id,
            'person_id' => $person->id,
            'code' => 'PROP-CLARO',
            'name' => 'Claro Telecom Participações',
            'is_active' => true,
        ]);

        $response = $this->deleteJson("/api/v1/material-owners/{$owner->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('material_owners', ['id' => $owner->id]);
    }

    /**
     * Testa que a criação de posição regional falha quando o proprietário não é informado.
     */
    public function test_cluster_creation_fails_without_owner_id(): void
    {
        $payload = [
            'name' => 'Posição Regional Sem Dono',
            'code' => 'REG-NO-OWNER',
            'description' => 'Teste sem vínculo de proprietário',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/clusters', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['owner_id']);
    }

    /**
     * Testa criação de posição regional com proprietário obrigatório com sucesso.
     */
    public function test_can_create_cluster_with_owner_id(): void
    {
        $payload = [
            'owner_id' => $this->owner->id,
            'name' => 'Posição Regional Ribeirão Preto',
            'code' => 'REG-RIBEIRAO',
            'description' => 'Região de Ribeirão e cidades vizinhas',
            'color' => '#10B981',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/clusters', $payload);

        $response->assertStatus(201);
        $response->assertJsonPath('data.name', 'Posição Regional Ribeirão Preto');
        $response->assertJsonPath('data.owner_id', $this->owner->id);
        $response->assertJsonPath('data.owner.code', $this->owner->code);

        $this->assertDatabaseHas('depot_clusters', [
            'code' => 'REG-RIBEIRAO',
            'owner_id' => $this->owner->id,
        ]);
    }

    /**
     * Testa que não é permitido criar posição regional com proprietário inexistente.
     */
    public function test_cluster_cannot_be_created_with_invalid_owner_id(): void
    {
        $payload = [
            'owner_id' => 999999,
            'name' => 'Posição Regional Dono Invalido',
            'code' => 'REG-INVALID',
            'is_active' => true,
        ];

        $response = $this->postJson('/api/v1/clusters', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['owner_id']);
    }

    /**
     * Testa atualização do proprietário de uma posição regional.
     */
    public function test_can_update_cluster_owner(): void
    {
        $personNovo = Person::create([
            'account_id' => $this->account->id,
            'name' => 'Novo Solicitante TIM',
            'document_number' => '99.888.777/0001-66',
            'status' => 'active',
            'is_requester' => true,
        ]);

        $novoOwner = MaterialOwner::create([
            'account_id' => $this->account->id,
            'person_id' => $personNovo->id,
            'code' => 'PROP-TIM',
            'name' => 'TIM Brasil S.A.',
            'is_active' => true,
        ]);

        $payload = [
            'owner_id' => $novoOwner->id,
            'name' => $this->cluster->name,
            'code' => $this->cluster->code,
            'is_active' => true,
        ];

        $response = $this->putJson("/api/v1/clusters/{$this->cluster->id}", $payload);

        $response->assertStatus(200);
        $response->assertJsonPath('data.owner_id', $novoOwner->id);
        $response->assertJsonPath('data.owner.code', 'PROP-TIM');

        $this->assertDatabaseHas('depot_clusters', [
            'id' => $this->cluster->id,
            'owner_id' => $novoOwner->id,
        ]);
    }
}



