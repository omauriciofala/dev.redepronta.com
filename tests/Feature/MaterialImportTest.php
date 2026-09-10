<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Material;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class MaterialImportTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected User $user;
    protected Unit $unitUn;
    protected Unit $unitMt;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::create([
            'name' => 'Empresa Teste Importação',
            'subdomain' => 'import-test',
            'is_active' => true,
        ]);

        $this->user = User::factory()->create([
            'name' => 'Operador WMS',
            'email' => 'wms@import-test.com',
        ]);

        session(['active_account_id' => $this->account->id]);

        $this->unitUn = Unit::whereIn('code', ['UND', 'UN'])->first() ?? Unit::create(['code' => 'UND', 'name' => 'Unidade', 'is_active' => true]);
        $this->unitMt = Unit::where('code', 'MT')->first() ?? Unit::create(['code' => 'MT', 'name' => 'Metro', 'is_active' => true]);
    }

    /**
     * Testa download da planilha modelo em CSV.
     */
    public function test_can_download_material_import_template(): void
    {
        $response = $this->get('/api/v1/materials/import-template');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename="modelo_importacao_materiais.csv"', $response->headers->get('Content-Disposition') ?? '');

        $content = $response->getContent();
        $this->assertStringContainsString('Código', $content);
        $this->assertStringContainsString('Nome do Material', $content);
    }

    /**
     * Testa download da planilha modelo em formato Excel (XLSX).
     */
    public function test_can_download_material_import_template_xlsx(): void
    {
        $response = $this->get('/api/v1/materials/import-template?format=xlsx');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('modelo_importacao_materiais.xlsx', $response->headers->get('Content-Disposition') ?? '');
    }

    /**
     * Testa importação de planilha contendo apenas Código e Nome do Material.
     */
    public function test_can_import_materials_with_code_and_name_only(): void
    {
        $csvData = "Código;Nome do Material\n";
        $csvData .= "MAT-IMP-001;Roteador Wi-Fi Mesh AX3000\n";
        $csvData .= "MAT-IMP-002;Adaptador Fibra Óptica SC/UPC\n";

        $file = UploadedFile::fake()->createWithContent('materiais_simples.csv', $csvData);

        $response = $this->postJson('/api/v1/materials/import', [
            'file' => $file,
            'update_existing' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.imported_count', 2);
        $response->assertJsonPath('data.updated_count', 0);
        $response->assertJsonPath('data.failed_count', 0);

        $this->assertDatabaseHas('materials', [
            'account_id' => $this->account->id,
            'code' => 'MAT-IMP-001',
            'name' => 'Roteador Wi-Fi Mesh AX3000',
            'unit_id' => $this->unitUn->id,
        ]);

        $this->assertDatabaseHas('materials', [
            'account_id' => $this->account->id,
            'code' => 'MAT-IMP-002',
            'name' => 'Adaptador Fibra Óptica SC/UPC',
            'unit_id' => $this->unitUn->id,
        ]);
    }

    /**
     * Testa importação completa com todas as colunas (delimitador vírgula ou ponto-e-vírgula).
     */
    public function test_can_import_materials_with_full_columns(): void
    {
        $csvData = "Código;Nome do Material;Categoria;Unidade;Serializado;Custo Unitário;Estoque Mínimo\n";
        $csvData .= "ONU-FIBRA-GIGA;ONU GPON Gigabit;Equipamentos;UN;S;125,50;20\n";
        $csvData .= "CABO-DROP;Cabo Drop Óptico Flat;Cabos & Fibras;MT;N;0,45;1500\n";

        $file = UploadedFile::fake()->createWithContent('materiais_completos.csv', $csvData);

        $response = $this->postJson('/api/v1/materials/import', [
            'file' => $file,
            'update_existing' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.imported_count', 2);

        $this->assertDatabaseHas('materials', [
            'account_id' => $this->account->id,
            'code' => 'ONU-FIBRA-GIGA',
            'name' => 'ONU GPON Gigabit',
            'category' => 'Equipamentos',
            'unit_id' => $this->unitUn->id,
            'has_serial' => true,
            'unit_cost' => 125.50,
            'min_stock' => 20.00,
        ]);

        $this->assertDatabaseHas('materials', [
            'account_id' => $this->account->id,
            'code' => 'CABO-DROP',
            'name' => 'Cabo Drop Óptico Flat',
            'category' => 'Cabos & Fibras',
            'unit_id' => $this->unitMt->id,
            'has_serial' => false,
            'unit_cost' => 0.45,
            'min_stock' => 1500.00,
        ]);
    }

    /**
     * Testa atualização de materiais existentes pelo código.
     */
    public function test_can_update_existing_materials_on_import(): void
    {
        Material::create([
            'account_id' => $this->account->id,
            'unit_id' => $this->unitUn->id,
            'code' => 'SW-8PORTAS',
            'name' => 'Switch 8 Portas 10/100 Antigo',
            'category' => 'Equipamentos',
            'unit_cost' => 50.00,
            'is_active' => true,
        ]);

        $csvData = "Código;Nome do Material;Custo Unitário\n";
        $csvData .= "SW-8PORTAS;Switch 8 Portas Gigabit Gerenciável;85,00\n";

        $file = UploadedFile::fake()->createWithContent('atualizacao.csv', $csvData);

        $response = $this->postJson('/api/v1/materials/import', [
            'file' => $file,
            'update_existing' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.imported_count', 0);
        $response->assertJsonPath('data.updated_count', 1);

        $this->assertDatabaseHas('materials', [
            'account_id' => $this->account->id,
            'code' => 'SW-8PORTAS',
            'name' => 'Switch 8 Portas Gigabit Gerenciável',
            'unit_cost' => 85.00,
        ]);
    }

    /**
     * Testa reporte de erros em linhas sem código ou sem nome.
     */
    public function test_reports_errors_for_invalid_rows(): void
    {
        $csvData = "Código;Nome do Material\n";
        $csvData .= ";Material Sem Código\n";
        $csvData .= "COD-SEM-NOME;\n";
        $csvData .= "COD-VALIDO;Material Válido\n";

        $file = UploadedFile::fake()->createWithContent('materiais_com_erros.csv', $csvData);

        $response = $this->postJson('/api/v1/materials/import', [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.imported_count', 1);
        $response->assertJsonPath('data.failed_count', 2);
        $this->assertCount(2, $response->json('data.errors'));
    }

    /**
     * Testa rejeição de arquivo inválido.
     */
    public function test_rejects_invalid_file_extension(): void
    {
        $file = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

        $response = $this->postJson('/api/v1/materials/import', [
            'file' => $file,
        ]);

        $response->assertStatus(422);
        $response->assertJsonPath('message', 'O arquivo deve estar no formato Excel (.xlsx) ou CSV (.csv, .txt).');
    }

    /**
     * Testa importação a partir do arquivo XLSX modelo oficial (docs/modelo_importacao_materiais.xlsx).
     */
    public function test_can_import_from_real_xlsx_template(): void
    {
        $xlsxPath = base_path('docs/modelo_importacao_materiais.xlsx');
        if (!file_exists($xlsxPath)) {
            $this->markTestSkipped('Arquivo modelo_importacao_materiais.xlsx não encontrado.');
        }

        $file = new UploadedFile($xlsxPath, 'modelo_importacao_materiais.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->postJson('/api/v1/materials/import', [
            'file' => $file,
            'update_existing' => true,
        ]);

        $response->assertStatus(200);
        $this->assertGreaterThan(100, $response->json('data.imported_count'));
        $this->assertEquals(0, $response->json('data.failed_count'));

        $this->assertDatabaseHas('materials', [
            'account_id' => $this->account->id,
            'code' => '1AB8',
            'name' => '10GBASE-BXD SFP+ BiDi Tx1330/Rx1270nm 10km LC DDM',
        ]);
    }
}
