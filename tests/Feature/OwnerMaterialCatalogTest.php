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
     * Testa o download do modelo CSV de 3 colunas: Cód., Nome do Material, Cód. Prop.
     */
    public function test_can_download_owner_material_catalog_three_column_template(): void
    {
        $response = $this->get("/api/v1/material-owners/{$this->owner->id}/materials/template");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('modelo_importacao_proprietario_VIVO-SP.csv', $response->headers->get('Content-Disposition') ?? '');

        $content = $response->getContent();
        $this->assertStringContainsString('Cód.', $content);
        $this->assertStringContainsString('Nome do Material', $content);
        $this->assertStringContainsString('Cód. Prop.', $content);
    }

    /**
     * Testa o preview da importação com:
     * 1. Código existente e nome customizado
     * 2. Código existente e nome em branco (herda nome do sistema)
     * 3. Código em branco (gera código de 4 dígitos alfanuméricos em maiúsculas)
     * 4. Linha com erro (Cód. Prop. vazio)
     */
    public function test_can_preview_owner_materials_import(): void
    {
        $csv = "Cód.;Nome do Material;Cód. Prop.\n";
        $csv .= "SYS-ONU-01;Módem Óptico Wi-Fi 6 Vivo;VIV-ONT-70\n"; // Existente com nome customizado
        $csv .= "SYS-CABO-FO;;VIV-CAB-DROP\n"; // Existente com nome em branco -> deve herdar 'Cabo Óptico Drop 1 FO'
        $csv .= ";Conector Rápido SC/APC Click;VIV-CON-APC\n"; // Código em branco -> deve gerar código de 4 dígitos
        $csv .= "SYS-ONU-01;Item Sem Cod Prop;\n"; // Erro: Cód. Prop. vazio

        $file = UploadedFile::fake()->createWithContent('planilha_preview.csv', $csv);

        $response = $this->postJson("/api/v1/material-owners/{$this->owner->id}/materials/preview", [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('can_import', true);
        $response->assertJsonPath('summary.total_rows', 4);
        $response->assertJsonPath('summary.to_link_existing', 2);
        $response->assertJsonPath('summary.to_create_material', 1);
        $response->assertJsonPath('summary.errors_count', 1);

        $rows = $response->json('preview_rows');
        $this->assertCount(4, $rows);

        // Linha 1: Existente com nome customizado
        $this->assertEquals('SYS-ONU-01', $rows[0]['system_code']);
        $this->assertEquals('Módem Óptico Wi-Fi 6 Vivo', $rows[0]['owner_name']);
        $this->assertEquals('custom', $rows[0]['name_source']);
        $this->assertFalse($rows[0]['is_generated_code']);
        $this->assertEquals('link_existing', $rows[0]['action']);
        $this->assertEquals('valid', $rows[0]['status']);

        // Linha 2: Existente com nome em branco -> herdou do sistema
        $this->assertEquals('SYS-CABO-FO', $rows[1]['system_code']);
        $this->assertEquals('Cabo Óptico Drop 1 FO', $rows[1]['owner_name']);
        $this->assertEquals('inherited', $rows[1]['name_source']);
        $this->assertFalse($rows[1]['is_generated_code']);
        $this->assertEquals('valid', $rows[1]['status']);

        // Linha 3: Código em branco -> gerou código de 4 caracteres alfanuméricos em maiúsculas
        $this->assertNotEmpty($rows[2]['system_code']);
        $this->assertEquals(4, strlen($rows[2]['system_code']));
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{4}$/', $rows[2]['system_code']);
        $this->assertTrue($rows[2]['is_generated_code']);
        $this->assertEquals('Conector Rápido SC/APC Click', $rows[2]['owner_name']);
        $this->assertEquals('create_material_and_link', $rows[2]['action']);
        $this->assertEquals('valid', $rows[2]['status']);

        // Linha 4: Erro
        $this->assertEquals('error', $rows[3]['status']);
        $this->assertStringContainsString('Cód. Prop.', $rows[3]['message']);
    }

    /**
     * Testa confirmação e efetivação da importação a partir dos dados validados do preview.
     */
    public function test_can_confirm_and_import_owner_materials_after_preview(): void
    {
        $csv = "Cód.;Nome do Material;Cód. Prop.\n";
        $csv .= "SYS-ONU-01;Módem Vivo Fibra;VIV-ONT-70\n";
        $csv .= "SYS-CABO-FO;;VIV-CAB-DROP\n"; // herda nome do sistema
        $csv .= ";Patch Cord Óptico SM 2M;VIV-PAT-2M\n"; // cria novo material no sistema com código de 4 dígitos

        $file = UploadedFile::fake()->createWithContent('planilha_teste.csv', $csv);

        // 1. Gera preview
        $previewRes = $this->postJson("/api/v1/material-owners/{$this->owner->id}/materials/preview", [
            'file' => $file,
        ]);
        $previewRes->assertStatus(200);
        $previewRows = $previewRes->json('preview_rows');

        // 2. Confirma a importação enviando o preview aprovado
        $importRes = $this->postJson("/api/v1/material-owners/{$this->owner->id}/materials/import", [
            'rows' => $previewRows,
        ]);

        $importRes->assertStatus(200);
        $importRes->assertJsonPath('data.created_materials', 1);
        $importRes->assertJsonPath('data.created_links', 3);

        // 3. Validações no Banco de Dados
        // 3.1 Material existente 1 (nome customizado)
        $this->assertDatabaseHas('owner_materials', [
            'account_id' => $this->account->id,
            'material_owner_id' => $this->owner->id,
            'material_id' => $this->material1->id,
            'owner_code' => 'VIV-ONT-70',
            'owner_name' => 'Módem Vivo Fibra',
        ]);

        // 3.2 Material existente 2 (herdou nome do sistema 'Cabo Óptico Drop 1 FO')
        $this->assertDatabaseHas('owner_materials', [
            'account_id' => $this->account->id,
            'material_owner_id' => $this->owner->id,
            'material_id' => $this->material2->id,
            'owner_code' => 'VIV-CAB-DROP',
            'owner_name' => 'Cabo Óptico Drop 1 FO',
        ]);

        // 3.3 Novo material criado no sistema com código de 4 dígitos
        $createdMaterial = Material::where('account_id', $this->account->id)
            ->where('name', 'Patch Cord Óptico SM 2M')
            ->first();

        $this->assertNotNull($createdMaterial);
        $this->assertEquals(4, strlen($createdMaterial->code));
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{4}$/', $createdMaterial->code);
        $this->assertEquals($this->unit->id, $createdMaterial->unit_id);

        // 3.4 Vínculo do novo material criado
        $this->assertDatabaseHas('owner_materials', [
            'account_id' => $this->account->id,
            'material_owner_id' => $this->owner->id,
            'material_id' => $createdMaterial->id,
            'owner_code' => 'VIV-PAT-2M',
            'owner_name' => 'Patch Cord Óptico SM 2M',
        ]);
    }

    /**
     * Testa criação, listagem e atualização manual de De/Para de materiais.
     */
    public function test_can_crud_owner_materials_mapping(): void
    {
        $storeResponse = $this->postJson("/api/v1/material-owners/{$this->owner->id}/materials", [
            'material_id' => $this->material1->id,
            'owner_code' => 'VIVO-MOD-100',
            'owner_name' => 'Módem Óptico Vivo Fibra',
            'notes' => 'Padrão contratual Vivo',
        ]);

        $storeResponse->assertStatus(201);
        $storeResponse->assertJsonPath('data.owner_code', 'VIVO-MOD-100');

        $listResponse = $this->getJson("/api/v1/material-owners/{$this->owner->id}/materials");
        $listResponse->assertStatus(200);
        $listResponse->assertJsonCount(1, 'data');

        $ownerMaterialId = $listResponse->json('data.0.id');

        $updateResponse = $this->putJson("/api/v1/material-owners/{$this->owner->id}/materials/{$ownerMaterialId}", [
            'owner_code' => 'VIVO-MOD-200',
            'owner_name' => 'Módem Óptico Wi-Fi 6 Vivo Fibra',
            'is_active' => true,
        ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJsonPath('data.owner_code', 'VIVO-MOD-200');

        $deleteResponse = $this->deleteJson("/api/v1/material-owners/{$this->owner->id}/materials/{$ownerMaterialId}");
        $deleteResponse->assertStatus(200);

        $this->assertSoftDeleted('owner_materials', [
            'id' => $ownerMaterialId,
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

        // Sem proprietário ativo: dados canônicos
        $defaultRes = $this->getJson('/api/v1/materials');
        $defaultRes->assertStatus(200);
        $m1Default = collect($defaultRes->json('data'))->firstWhere('id', $this->material1->id);
        $this->assertEquals('SYS-ONU-01', $m1Default['code']);
        $this->assertFalse($m1Default['has_owner_alias']);

        // Com proprietário ativo: dados do proprietário
        $ownerRes = $this->getJson("/api/v1/materials?owner_id={$this->owner->id}");
        $ownerRes->assertStatus(200);
        $m1Owner = collect($ownerRes->json('data'))->firstWhere('id', $this->material1->id);
        $this->assertEquals('VIV-EQP-999', $m1Owner['code']);
        $this->assertEquals('Equipamento Terminal Vivo', $m1Owner['name']);
        $this->assertEquals('SYS-ONU-01', $m1Owner['system_code']);
        $this->assertTrue($m1Owner['has_owner_alias']);
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

    /**
     * Testa o download do modelo XLSX de 3 colunas para o proprietário.
     */
    public function test_can_download_owner_material_catalog_xlsx_template(): void
    {
        $response = $this->get("/api/v1/material-owners/{$this->owner->id}/materials/template?format=xlsx");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('modelo_importacao_proprietario_VIVO-SP.xlsx', $response->headers->get('Content-Disposition') ?? '');
    }

    /**
     * Testa prévia e importação a partir do arquivo XLSX modelo de 3 colunas no catálogo do proprietário.
     */
    public function test_can_preview_and_import_owner_materials_from_xlsx_file(): void
    {
        $xlsxPath = base_path('docs/modelo_importacao_materiais.xlsx');
        if (!file_exists($xlsxPath)) {
            $this->markTestSkipped('Arquivo modelo_importacao_materiais.xlsx não encontrado.');
        }

        $file = new UploadedFile($xlsxPath, 'modelo_importacao_materiais.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        // 1. Cadastra os materiais no catálogo geral primeiro para existirem no sistema
        $importService = app(\App\Services\Stock\MaterialImportService::class);
        $importService->importFromUploadedFile($file, $this->account->id, true);

        // 2. Preview no catálogo do proprietário com o mesmo arquivo XLSX de 3 colunas
        $previewRes = $this->postJson("/api/v1/material-owners/{$this->owner->id}/materials/preview", [
            'file' => $file,
        ]);

        $previewRes->assertStatus(200);
        $previewRes->assertJsonPath('can_import', true);
        $this->assertGreaterThan(100, $previewRes->json('summary.total_rows'));
        $this->assertEquals(0, $previewRes->json('summary.errors_count'));
        $this->assertGreaterThan(100, $previewRes->json('summary.to_link_existing'));

        // 3. Importação dos dados da prévia
        $previewRows = $previewRes->json('preview_rows');
        // Pega as primeiras 5 linhas para importar com velocidade
        $subsetRows = array_slice($previewRows, 0, 5);

        $importRes = $this->postJson("/api/v1/material-owners/{$this->owner->id}/materials/import", [
            'rows' => $subsetRows,
        ]);

        $importRes->assertStatus(200);
        $this->assertEquals(5, $importRes->json('data.imported_count'));

        // Valida que o primeiro item foi criado/vinculado com o código de proprietário
        $firstItem = $subsetRows[0];
        $this->assertDatabaseHas('owner_materials', [
            'account_id' => $this->account->id,
            'material_owner_id' => $this->owner->id,
            'owner_code' => $firstItem['owner_code'],
        ]);
    }
}

