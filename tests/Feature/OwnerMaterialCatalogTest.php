<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\DepotCluster;
use App\Models\Depot;
use App\Models\Material;
use App\Models\MaterialOwner;
use App\Models\OwnerMaterial;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class OwnerMaterialCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected User $user;
    protected Unit $unit;
    protected MaterialOwner $owner;
    protected Material $material1;
    protected Material $material2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::create([
            'name' => 'Operação Provedor e Telecom',
            'subdomain' => 'telecom-ops',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'name' => 'Gestor WMS',
            'email' => 'gestor@telecom-ops.com',
        ]);

        session(['active_account_id' => $this->account->id]);

        $this->unit = Unit::firstOrCreate(
            ['code' => 'UND'],
            ['name' => 'Unidade', 'is_active' => true]
        );

        $person = \App\Models\Person::create([
            'account_id' => $this->account->id,
            'name' => 'Telefônica Brasil S.A.',
            'document_number' => '02.558.157/0001-62',
            'status' => 'active',
        ]);

        $this->owner = MaterialOwner::create([
            'account_id' => $this->account->id,
            'person_id' => $person->id,
            'code' => 'VIVO-SP',
            'name' => 'Telefônica Brasil / Vivo SP',
            'is_active' => true,
        ]);

        $this->material1 = Material::create([
            'account_id' => $this->account->id,
            'unit_id' => $this->unit->id,
            'code' => 'SYS-ONU-01',
            'name' => 'ONU GPON Bridge Router',
            'is_active' => true,
        ]);

        $this->material2 = Material::create([
            'account_id' => $this->account->id,
            'unit_id' => $this->unit->id,
            'code' => 'SYS-CABO-FO',
            'name' => 'Cabo Óptico Drop 1 FO',
            'is_active' => true,
        ]);
    }

    /**
     * Testa o download do modelo CSV do catálogo do proprietário.
     */
    public function test_can_download_owner_material_catalog_template(): void
    {
        $response = $this->get("/api/v1/material-owners/{$this->owner->id}/materials/template");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('modelo_catalogo_proprietario_VIVO-SP.csv', $response->headers->get('Content-Disposition') ?? '');

        $content = $response->getContent();
        $this->assertStringContainsString('codigo_proprietario', $content);
        $this->assertStringContainsString('codigo_sistema_sku', $content);
    }

    /**
     * Testa criação, listagem e atualização de De/Para de materiais.
     */
    public function test_can_crud_owner_materials_mapping(): void
    {
        // 1. Criar vínculo
        $storeResponse = $this->postJson("/api/v1/material-owners/{$this->owner->id}/materials", [
            'material_id' => $this->material1->id,
            'owner_code' => 'VIVO-MOD-100',
            'owner_name' => 'Módem Óptico Vivo Fibra',
            'notes' => 'Padrão contratual Vivo',
        ]);

        $storeResponse->assertStatus(201);
        $storeResponse->assertJsonPath('data.owner_code', 'VIVO-MOD-100');
        $storeResponse->assertJsonPath('data.owner_name', 'Módem Óptico Vivo Fibra');

        $this->assertDatabaseHas('owner_materials', [
            'account_id' => $this->account->id,
            'material_owner_id' => $this->owner->id,
            'material_id' => $this->material1->id,
            'owner_code' => 'VIVO-MOD-100',
        ]);

        // 2. Listar
        $listResponse = $this->getJson("/api/v1/material-owners/{$this->owner->id}/materials");
        $listResponse->assertStatus(200);
        $listResponse->assertJsonCount(1, 'data');

        $ownerMaterialId = $listResponse->json('data.0.id');

        // 3. Atualizar
        $updateResponse = $this->putJson("/api/v1/material-owners/{$this->owner->id}/materials/{$ownerMaterialId}", [
            'owner_code' => 'VIVO-MOD-200',
            'owner_name' => 'Módem Óptico Wi-Fi 6 Vivo Fibra',
            'is_active' => true,
        ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJsonPath('data.owner_code', 'VIVO-MOD-200');

        // 4. Remover / Excluir
        $deleteResponse = $this->deleteJson("/api/v1/material-owners/{$this->owner->id}/materials/{$ownerMaterialId}");
        $deleteResponse->assertStatus(200);

        $this->assertSoftDeleted('owner_materials', [
            'id' => $ownerMaterialId,
        ]);
    }

    /**
     * Testa importação de planilha CSV com códigos do proprietário.
     */
    public function test_can_import_owner_materials_csv(): void
    {
        $csv = "Código Sistema (SKU);Nome Sistema;Código do Proprietário;Nome no Proprietário;Observações\n";
        $csv .= "SYS-ONU-01;ONU GPON;VIV-ONT-70;HGU GPON VIVO 70;Contrato SP\n";
        $csv .= "SYS-CABO-FO;Cabo Óptico;VIV-CAB-DROP;CABO DROP COG VIVO;Bobinas de 1km\n";

        $file = UploadedFile::fake()->createWithContent('catalogo_vivo.csv', $csv);

        $response = $this->postJson("/api/v1/material-owners/{$this->owner->id}/materials/import", [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.imported_count', 2);
        $response->assertJsonPath('data.failed_count', 0);

        $this->assertDatabaseHas('owner_materials', [
            'account_id' => $this->account->id,
            'material_owner_id' => $this->owner->id,
            'material_id' => $this->material1->id,
            'owner_code' => 'VIV-ONT-70',
            'owner_name' => 'HGU GPON VIVO 70',
        ]);
    }

    /**
     * Testa que /api/v1/materials resolve código e nome do proprietário quando owner_id é informado.
     */
    public function test_materials_api_resolves_owner_alias_when_owner_id_provided(): void
    {
        OwnerMaterial::create([
            'account_id' => $this->account->id,
            'material_owner_id' => $this->owner->id,
            'material_id' => $this->material1->id,
            'owner_code' => 'VIV-EQP-999',
            'owner_name' => 'Equipamento Terminal Vivo',
            'is_active' => true,
        ]);

        // Consulta padrão (sem proprietário): dados canônicos
        $defaultRes = $this->getJson('/api/v1/materials');
        $defaultRes->assertStatus(200);
        $m1Default = collect($defaultRes->json('data'))->firstWhere('id', $this->material1->id);
        $this->assertEquals('SYS-ONU-01', $m1Default['code']);
        $this->assertEquals('ONU GPON Bridge Router', $m1Default['name']);
        $this->assertFalse($m1Default['has_owner_alias']);

        // Consulta filtrando pelo proprietário Vivo: dados do catálogo do proprietário
        $ownerRes = $this->getJson("/api/v1/materials?owner_id={$this->owner->id}");
        $ownerRes->assertStatus(200);
        $m1Owner = collect($ownerRes->json('data'))->firstWhere('id', $this->material1->id);
        $this->assertEquals('VIV-EQP-999', $m1Owner['code']);
        $this->assertEquals('Equipamento Terminal Vivo', $m1Owner['name']);
        $this->assertEquals('SYS-ONU-01', $m1Owner['system_code']);
        $this->assertTrue($m1Owner['has_owner_alias']);

        // Material sem alias continua retornando dados canônicos
        $m2Owner = collect($ownerRes->json('data'))->firstWhere('id', $this->material2->id);
        $this->assertEquals('SYS-CABO-FO', $m2Owner['code']);
        $this->assertFalse($m2Owner['has_owner_alias']);
    }

    /**
     * Testa que /api/v1/materials resolve código do proprietário vinculado ao depósito ou herdado do cluster.
     */
    public function test_materials_api_resolves_owner_alias_by_depot_or_cluster(): void
    {
        OwnerMaterial::create([
            'account_id' => $this->account->id,
            'material_owner_id' => $this->owner->id,
            'material_id' => $this->material1->id,
            'owner_code' => 'VIV-DEPOT-RESOLVED',
            'owner_name' => 'Material do Depósito Vivo',
            'is_active' => true,
        ]);

        $clusterCapital = DepotCluster::create([
            'account_id' => $this->account->id,
            'code' => 'CLUST-CAPITAL',
            'name' => 'Cluster Capital',
            'is_active' => true,
        ]);

        // 1. Depósito com owner_id direto
        $depotDirect = Depot::create([
            'account_id' => $this->account->id,
            'cluster_id' => $clusterCapital->id,
            'name' => 'Depósito Vivo Capital',
            'code' => 'DEP-VIV-CAP',
            'owner_id' => $this->owner->id,
            'type' => 'CENTRAL',
            'is_active' => true,
        ]);

        $depotRes = $this->getJson("/api/v1/materials?depot_id={$depotDirect->id}");
        $depotRes->assertStatus(200);
        $m1Direct = collect($depotRes->json('data'))->firstWhere('id', $this->material1->id);
        $this->assertEquals('VIV-DEPOT-RESOLVED', $m1Direct['code']);
        $this->assertTrue($m1Direct['has_owner_alias']);

        // 2. Depósito sem owner direto, mas cluster com owner_id
        $cluster = DepotCluster::create([
            'account_id' => $this->account->id,
            'code' => 'CLUST-INTERIOR',
            'name' => 'Regional Vivo Interior',
            'owner_id' => $this->owner->id,
            'is_active' => true,
        ]);

        $depotInherited = Depot::create([
            'account_id' => $this->account->id,
            'cluster_id' => $cluster->id,
            'name' => 'Depósito Campinas',
            'code' => 'DEP-CPS',
            'type' => 'REGIONAL_BASE',
            'is_active' => true,
        ]);

        $clusterRes = $this->getJson("/api/v1/materials?depot_id={$depotInherited->id}");
        $clusterRes->assertStatus(200);
        $m1Inherited = collect($clusterRes->json('data'))->firstWhere('id', $this->material1->id);
        $this->assertEquals('VIV-DEPOT-RESOLVED', $m1Inherited['code']);
        $this->assertTrue($m1Inherited['has_owner_alias']);
    }
}
