<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Depot;
use App\Models\DepotCluster;
use App\Models\Material;
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

        // 1. Posição Regional
        $this->cluster = DepotCluster::create([
            'account_id' => $this->account->id,
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
}
