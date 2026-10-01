<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Depot;
use App\Models\DepotCluster;
use App\Models\DepotType;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockSerial;
use App\Models\Unit;
use App\Models\User;
use App\Services\Stock\ClusterStockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WmsIntegrityAndConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected User $user;
    protected DepotCluster $cluster;
    protected DepotType $depotType;
    protected Depot $depotA;
    protected Depot $depotB;
    protected MaterialCategory $category;
    protected Unit $unit;
    protected Material $conventionalMaterial;
    protected Material $serializedMaterial;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::firstOrCreate(['id' => 1], [
            'name' => 'Rede Pronta Matriz',
            'subdomain' => 'matriz',
            'status' => 'active',
        ]);

        $this->user = User::create([
            'account_id' => $this->account->id,
            'name' => 'Operador WMS Teste',
            'email' => 'wms.test@redepronta.com',
            'password' => bcrypt('password'),
            'is_super_admin' => true,
            'status' => 'active',
        ]);

        $this->cluster = DepotCluster::create([
            'account_id' => $this->account->id,
            'code' => 'SP-CAPITAL',
            'name' => 'São Paulo Capital',
            'status' => 'active',
        ]);

        $this->depotType = DepotType::create([
            'account_id' => $this->account->id,
            'name' => 'Almoxarifado Central',
            'code' => 'CENTRAL',
            'status' => 'active',
        ]);

        $this->depotA = Depot::create([
            'account_id' => $this->account->id,
            'cluster_id' => $this->cluster->id,
            'depot_type_id' => $this->depotType->id,
            'name' => 'Depósito Central Lapa',
            'code' => 'DEP-LAPA',
            'status' => 'active',
        ]);

        $this->depotB = Depot::create([
            'account_id' => $this->account->id,
            'cluster_id' => $this->cluster->id,
            'depot_type_id' => $this->depotType->id,
            'name' => 'Depósito Base Pinheiros',
            'code' => 'DEP-PINHEIROS',
            'status' => 'active',
        ]);

        $this->category = MaterialCategory::create([
            'account_id' => $this->account->id,
            'name' => 'Cabos & Fibras',
            'code' => 'CABOS',
            'status' => 'active',
        ]);

        $this->unit = Unit::firstOrCreate([
            'code' => 'MT',
        ], [
            'name' => 'Metro',
            'symbol' => 'm',
            'status' => 'active',
        ]);

        $this->conventionalMaterial = Material::create([
            'account_id' => $this->account->id,
            'unit_id' => $this->unit->id,
            'code' => 'MAT-CABO-DROP',
            'name' => 'Cabo Drop Óptico 1FO',
            'has_serial' => false,
            'tracking_type' => 'BULK',
            'is_active' => true,
        ]);

        $this->serializedMaterial = Material::create([
            'account_id' => $this->account->id,
            'unit_id' => $this->unit->id,
            'code' => 'MAT-ONU-XPON',
            'name' => 'ONU XPON Bridge / Router',
            'has_serial' => true,
            'tracking_type' => 'SERIAL',
            'is_active' => true,
        ]);
    }

    public function test_serial_lifecycle_state_transitions_are_strictly_tracked(): void
    {
        $serialNumber = 'ONU-XPON-2026-001';

        // 1. Entrada de ONU serializada no Depósito A via API
        $responseEntry = $this->actingAs($this->user)->postJson('/api/v1/stock/movement', [
            'movement_type' => 'ENTRY',
            'destination_depot_id' => $this->depotA->id,
            'document_number' => 'NF-1001',
            'items' => [
                [
                    'material_id' => $this->serializedMaterial->id,
                    'quantity' => 1,
                    'serials' => [$serialNumber],
                ]
            ],
            'notes' => 'Entrada de lote novo',
        ]);

        $responseEntry->assertStatus(201);
        $dataEntry = $responseEntry->json('data');
        $protocolEntry = is_array($dataEntry) && isset($dataEntry[0]) ? $dataEntry[0]['protocol'] : ($dataEntry['protocol'] ?? null);
        $this->assertNotEmpty($protocolEntry);

        // Verifica estado do serial no banco
        $serial = StockSerial::where('serial_number', $serialNumber)->first();
        $this->assertNotNull($serial);
        $this->assertEquals('IN_STOCK', $serial->status);
        $this->assertEquals($this->depotA->id, $serial->current_depot_id);

        // 2. Transferência do Depósito A para o Depósito B
        $responseTransfer = $this->actingAs($this->user)->postJson('/api/v1/stock/movement', [
            'movement_type' => 'TRANSFER',
            'source_depot_id' => $this->depotA->id,
            'destination_depot_id' => $this->depotB->id,
            'document_number' => 'TRANSF-001',
            'items' => [
                [
                    'material_id' => $this->serializedMaterial->id,
                    'quantity' => 1,
                    'serial_ids' => [$serial->id],
                ]
            ],
            'notes' => 'Transferência entre depósitos',
        ]);

        $responseTransfer->assertStatus(201);

        $serial->refresh();
        $this->assertEquals('IN_STOCK', $serial->status);
        $this->assertEquals($this->depotB->id, $serial->current_depot_id);

        // 3. Checagem dos saldos
        $balA = StockBalance::where('depot_id', $this->depotA->id)->where('material_id', $this->serializedMaterial->id)->value('quantity');
        $balB = StockBalance::where('depot_id', $this->depotB->id)->where('material_id', $this->serializedMaterial->id)->value('quantity');

        $this->assertEquals(0, (float) $balA);
        $this->assertEquals(1, (float) $balB);
    }

    public function test_cannot_transfer_serial_not_present_in_source_depot(): void
    {
        // Cria serial vinculado ao Depósito B
        StockSerial::create([
            'account_id' => $this->account->id,
            'material_id' => $this->serializedMaterial->id,
            'current_depot_id' => $this->depotB->id,
            'serial_number' => 'SERIAL-BASE-B',
            'status' => 'IN_STOCK',
        ]);

        StockBalance::create([
            'account_id' => $this->account->id,
            'depot_id' => $this->depotB->id,
            'material_id' => $this->serializedMaterial->id,
            'quantity' => 1,
        ]);

        // Tentativa inválida de transferir a partir do Depósito A
        $response = $this->actingAs($this->user)->postJson('/api/v1/stock/movement', [
            'movement_type' => 'TRANSFER',
            'source_depot_id' => $this->depotA->id,
            'destination_depot_id' => $this->depotB->id,
            'items' => [
                [
                    'material_id' => $this->serializedMaterial->id,
                    'quantity' => 1,
                    'serials' => ['SERIAL-BASE-B'],
                ]
            ],
        ]);

        $response->assertStatus(422);
    }

    public function test_stock_movement_generates_unique_traceable_protocol(): void
    {
        $r1 = $this->actingAs($this->user)->postJson('/api/v1/stock/movement', [
            'movement_type' => 'ENTRY',
            'destination_depot_id' => $this->depotA->id,
            'items' => [
                ['material_id' => $this->conventionalMaterial->id, 'quantity' => 500]
            ],
        ]);
        $r1->assertStatus(201);
        $d1 = $r1->json('data');
        $p1 = is_array($d1) && isset($d1[0]) ? $d1[0]['protocol'] : ($d1['protocol'] ?? null);

        $r2 = $this->actingAs($this->user)->postJson('/api/v1/stock/movement', [
            'movement_type' => 'ENTRY',
            'destination_depot_id' => $this->depotA->id,
            'items' => [
                ['material_id' => $this->conventionalMaterial->id, 'quantity' => 200]
            ],
        ]);
        $r2->assertStatus(201);
        $d2 = $r2->json('data');
        $p2 = is_array($d2) && isset($d2[0]) ? $d2[0]['protocol'] : ($d2['protocol'] ?? null);

        $this->assertNotEmpty($p1);
        $this->assertNotEmpty($p2);
        $this->assertNotEquals($p1, $p2);
    }

    public function test_atomic_rollback_on_failed_batch_transfer(): void
    {
        // Entrada inicial de 100m de cabo no Depósito A
        $this->actingAs($this->user)->postJson('/api/v1/stock/movement', [
            'movement_type' => 'ENTRY',
            'destination_depot_id' => $this->depotA->id,
            'items' => [
                ['material_id' => $this->conventionalMaterial->id, 'quantity' => 100]
            ],
        ])->assertStatus(201);

        // Tenta transferir lote: cabo (que tem) e serial (que NÃO tem)
        $response = $this->actingAs($this->user)->postJson('/api/v1/stock/movement', [
            'movement_type' => 'TRANSFER',
            'source_depot_id' => $this->depotA->id,
            'destination_depot_id' => $this->depotB->id,
            'items' => [
                ['material_id' => $this->conventionalMaterial->id, 'quantity' => 50],
                ['material_id' => $this->serializedMaterial->id, 'quantity' => 1, 'serials' => ['SERIAL-INEXISTENTE']],
            ],
        ]);

        $response->assertStatus(422);

        // Saldo do cabo no Depósito A DEVE permanecer 100 (rollback atômico garantido)
        $balance = StockBalance::where('depot_id', $this->depotA->id)
            ->where('material_id', $this->conventionalMaterial->id)
            ->value('quantity');

        $this->assertEquals(100, (float) $balance);
    }

    public function test_documents_api_returns_structured_payload_with_protocol(): void
    {
        $resp = $this->actingAs($this->user)->postJson('/api/v1/stock/movement', [
            'movement_type' => 'ENTRY',
            'destination_depot_id' => $this->depotA->id,
            'document_number' => 'NF-991122',
            'items' => [
                ['material_id' => $this->conventionalMaterial->id, 'quantity' => 250]
            ],
            'notes' => 'Carga documentada',
        ]);
        $resp->assertStatus(201);
        $dataResp = $resp->json('data');
        $protocol = is_array($dataResp) && isset($dataResp[0]) ? $dataResp[0]['protocol'] : ($dataResp['protocol'] ?? null);

        $response = $this->actingAs($this->user)->getJson('/api/v1/stock/documents');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['protocol', 'movement_type', 'created_at']
                ]
            ]);

        $protocols = collect($response->json('data'))->pluck('protocol')->all();
        $this->assertContains($protocol, $protocols);

        // Consulta documento individual
        $responseDoc = $this->actingAs($this->user)->getJson("/api/v1/stock/documents/{$protocol}");
        $responseDoc->assertStatus(200)
            ->assertJson([
                'protocol' => $protocol,
                'document_number' => 'NF-991122',
            ]);
    }
}
