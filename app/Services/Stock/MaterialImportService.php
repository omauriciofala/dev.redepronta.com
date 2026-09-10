<?php

namespace App\Services\Stock;

use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Unit;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ZipArchive;

class MaterialImportService
{
    /**
     * Caminho do arquivo modelo XLSX pré-definido.
     */
    protected string $xlsxTemplatePath;

    public function __construct()
    {
        $this->xlsxTemplatePath = base_path('docs/modelo_importacao_materiais.xlsx');
    }

    /**
     * Retorna se o arquivo XLSX modelo existe no sistema.
     */
    public function hasXlsxTemplate(): bool
    {
        return file_exists($this->xlsxTemplatePath);
    }

    /**
     * Retorna o caminho do arquivo XLSX modelo.
     */
    public function getXlsxTemplatePath(): string
    {
        return $this->xlsxTemplatePath;
    }

    /**
     * Gera o conteúdo da planilha modelo em formato CSV (UTF-8 com BOM).
     */
    public function generateTemplateCsv(): string
    {
        $headers = [
            'Código',
            'Nome do Material',
            'Categoria',
            'Unidade',
            'Serializado (S/N)',
            'Custo Unitário',
            'Estoque Mínimo',
        ];

        $sampleRows = [
            [
                'ONU-XPON-001',
                'ONU XPON Bridge Gigabit Wi-Fi 2.4/5GHz',
                'Equipamentos',
                'UN',
                'S',
                '135.50',
                '15',
            ],
            [
                'CABO-DROP-1FO',
                'Cabo Drop Óptico Flat Compacto 1FO (Metro)',
                'Cabos & Fibras',
                'MT',
                'N',
                '0.48',
                '1000',
            ],
            [
                'CON-FAST-SCAPC',
                'Conector Rápido SC/APC Monomodo Pré-Polido',
                'Conectores & Passivos',
                'UN',
                'N',
                '2.30',
                '200',
            ],
            [
                'FITA-ISOLANTE',
                'Fita Isolante 19mm x 20m Alta Fusão',
                'Geral / Diversos',
                'UN',
                'N',
                '6.80',
                '50',
            ],
        ];

        $output = fopen('php://memory', 'r+');

        // Adiciona UTF-8 BOM para compatibilidade com Microsoft Excel em pt-BR
        fwrite($output, "\xEF\xBB\xBF");

        // Escreve cabeçalho e exemplos com delimitador ';'
        fputcsv($output, $headers, ';', '"', '\\');
        foreach ($sampleRows as $row) {
            fputcsv($output, $row, ';', '"', '\\');
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * Processa a importação a partir de um arquivo enviado (.xlsx, .csv ou .txt).
     */
    public function importFromUploadedFile(UploadedFile $file, int $accountId, bool $updateExisting = true): array
    {
        $realPath = $file->getRealPath();
        if (!$realPath || !file_exists($realPath)) {
            throw new InvalidArgumentException('Arquivo inválido ou inacessível para leitura.');
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'xlsx') {
            $rows = $this->parseXlsxRows($realPath);
        } elseif (in_array($extension, ['csv', 'txt'])) {
            $rawContent = file_get_contents($realPath);
            if ($rawContent === false || trim($rawContent) === '') {
                throw new InvalidArgumentException('O arquivo enviado está vazio.');
            }
            $rows = $this->parseCsvRows($rawContent);
        } else {
            throw new InvalidArgumentException('Formato de arquivo não suportado. Envie um arquivo .xlsx ou .csv.');
        }

        return $this->processRows($rows, $accountId, $updateExisting);
    }

    /**
     * Importa materiais a partir de uma string com conteúdo CSV.
     */
    public function importFromCsvString(string $csvContent, int $accountId, bool $updateExisting = true): array
    {
        $rows = $this->parseCsvRows($csvContent);
        return $this->processRows($rows, $accountId, $updateExisting);
    }

    /**
     * Faz o parsing de linhas de uma planilha .xlsx sem dependências externas.
     */
    public function parseXlsxRows(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new InvalidArgumentException('Não foi possível abrir o arquivo XLSX.');
        }

        // 1. Extrai strings compartilhadas
        $sharedStrings = [];
        if (($xmlIndex = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $xmlContent = $zip->getFromIndex($xmlIndex);
            if ($xmlContent) {
                $xml = simplexml_load_string($xmlContent);
                if ($xml && isset($xml->si)) {
                    foreach ($xml->si as $si) {
                        if (isset($si->t)) {
                            $sharedStrings[] = (string)$si->t;
                        } elseif (isset($si->r)) {
                            // Células com formatação rica (run elements)
                            $combined = '';
                            foreach ($si->r as $r) {
                                $combined .= (string)($r->t ?? '');
                            }
                            $sharedStrings[] = $combined;
                        } else {
                            $sharedStrings[] = '';
                        }
                    }
                }
            }
        }

        // 2. Localiza a primeira planilha (sheet1.xml)
        $sheetIndex = $zip->locateName('xl/worksheets/sheet1.xml');
        if ($sheetIndex === false) {
            // Tenta qualquer outra planilha se não achar sheet1
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);
                if (str_starts_with($name, 'xl/worksheets/sheet') && str_ends_with($name, '.xml')) {
                    $sheetIndex = $i;
                    break;
                }
            }
        }

        if ($sheetIndex === false) {
            $zip->close();
            throw new InvalidArgumentException('Planilha vazia ou estrutura XLSX não reconhecida.');
        }

        $sheetXmlContent = $zip->getFromIndex($sheetIndex);
        $zip->close();

        if (!$sheetXmlContent) {
            throw new InvalidArgumentException('Não foi possível ler os dados da planilha XLSX.');
        }

        $sheetXml = simplexml_load_string($sheetXmlContent);
        if (!$sheetXml || !isset($sheetXml->sheetData)) {
            return [];
        }

        $rows = [];
        foreach ($sheetXml->sheetData->row as $row) {
            $rowValues = [];
            foreach ($row->c as $cell) {
                $type = (string)($cell['t'] ?? '');
                $val = (string)($cell->v ?? '');

                if ($type === 's' && isset($sharedStrings[(int)$val])) {
                    $val = $sharedStrings[(int)$val];
                } elseif (isset($cell->is->t)) {
                    $val = (string)$cell->is->t;
                }

                $rowValues[] = trim($val);
            }
            $rows[] = $rowValues;
        }

        return $rows;
    }

    /**
     * Faz o parsing de string CSV em matriz de linhas.
     */
    public function parseCsvRows(string $csvContent): array
    {
        // Converte codificação para UTF-8 se necessário
        $encoding = mb_detect_encoding($csvContent, ['UTF-8', 'ISO-8859-1', 'Windows-1252', 'ASCII'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $csvContent = mb_convert_encoding($csvContent, 'UTF-8', $encoding);
        }

        // Remove BOM se presente
        if (str_starts_with($csvContent, "\xEF\xBB\xBF")) {
            $csvContent = substr($csvContent, 3);
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($csvContent));
        if (empty($lines)) {
            return [];
        }

        // Detecta delimitador
        $firstLine = $lines[0];
        $delimiter = ';';
        $countSemicolon = substr_count($firstLine, ';');
        $countComma = substr_count($firstLine, ',');
        $countTab = substr_count($firstLine, "\t");

        if ($countTab > $countSemicolon && $countTab > $countComma) {
            $delimiter = "\t";
        } elseif ($countComma > $countSemicolon) {
            $delimiter = ',';
        }

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $csvContent);
        rewind($stream);

        $rows = [];
        while (($row = fgetcsv($stream, 0, $delimiter, '"', '\\')) !== false) {
            $rows[] = $row;
        }
        fclose($stream);

        return $rows;
    }

    /**
     * Processa a matriz de linhas e realiza o cadastro/atualização no banco.
     */
    public function processRows(array $rows, int $accountId, bool $updateExisting = true): array
    {
        if (empty($rows)) {
            throw new InvalidArgumentException('O arquivo não contém registros legíveis.');
        }

        $headerRow = $rows[0];
        $columnMap = $this->mapHeaders($headerRow);

        if (!isset($columnMap['code']) || !isset($columnMap['name'])) {
            throw new InvalidArgumentException(
                'O arquivo deve conter obrigatoriamente as colunas "Código" (ou CÓD.) e "Nome do Material" (ou DESCRIÇÃO) no cabeçalho.'
            );
        }

        // Carrega unidades disponíveis para otimização
        $units = Unit::all();
        $defaultUnit = $units->firstWhere('code', 'UND') ?? $units->firstWhere('code', 'UN') ?? $units->first();
        if (!$defaultUnit) {
            $defaultUnit = Unit::create([
                'code' => 'UND',
                'name' => 'Unidade',
                'is_active' => true,
            ]);
        }

        $unitsByCode = $units->keyBy(fn ($u) => strtoupper(trim($u->code)));
        $unitsByName = $units->keyBy(fn ($u) => mb_strtolower(trim($u->name)));

        // Mapeia sinônimos comuns
        if (isset($unitsByCode['UND']) && !isset($unitsByCode['UN'])) {
            $unitsByCode['UN'] = $unitsByCode['UND'];
        }
        if (isset($unitsByCode['MT']) && !isset($unitsByCode['M'])) {
            $unitsByCode['M'] = $unitsByCode['MT'];
        }

        $importedCount = 0;
        $updatedCount = 0;
        $failedCount = 0;
        $errors = [];
        $totalRows = 0;

        DB::beginTransaction();
        try {
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                $lineNumber = $i + 1;

                // Ignora linhas completamente vazias
                if (count(array_filter($row, fn ($val) => trim((string)$val) !== '')) === 0) {
                    continue;
                }

                $totalRows++;

                $code = isset($columnMap['code']) && isset($row[$columnMap['code']]) ? trim((string)$row[$columnMap['code']]) : '';
                $name = isset($columnMap['name']) && isset($row[$columnMap['name']]) ? trim((string)$row[$columnMap['name']]) : '';

                if (empty($code)) {
                    $failedCount++;
                    $errors[] = [
                        'line' => $lineNumber,
                        'message' => 'Código do material não informado ou em branco.',
                    ];
                    continue;
                }

                if (empty($name)) {
                    $failedCount++;
                    $errors[] = [
                        'line' => $lineNumber,
                        'code' => $code,
                        'message' => 'Nome/descrição do material não informado ou em branco.',
                    ];
                    continue;
                }

                // Categoria
                $category = null;
                if (isset($columnMap['category']) && isset($row[$columnMap['category']])) {
                    $rawCat = trim((string)$row[$columnMap['category']]);
                    if (!empty($rawCat)) {
                        $category = $rawCat;
                    }
                }
                if (!$category) {
                    $category = 'Geral / Diversos';
                }

                // Unidade de medida
                $unitId = $defaultUnit->id;
                if (isset($columnMap['unit']) && isset($row[$columnMap['unit']])) {
                    $rawUnit = trim((string)$row[$columnMap['unit']]);
                    if (!empty($rawUnit)) {
                        $upperUnit = strtoupper($rawUnit);
                        $lowerUnit = mb_strtolower($rawUnit);
                        if (isset($unitsByCode[$upperUnit])) {
                            $unitId = $unitsByCode[$upperUnit]->id;
                        } elseif (isset($unitsByName[$lowerUnit])) {
                            $unitId = $unitsByName[$lowerUnit]->id;
                        } else {
                            $newUnit = Unit::firstOrCreate(
                                ['code' => Str::limit($upperUnit, 10, '')],
                                ['name' => $rawUnit, 'is_active' => true]
                            );
                            $unitId = $newUnit->id;
                            $unitsByCode[$upperUnit] = $newUnit;
                        }
                    }
                }

                // Serializado (S/N)
                $hasSerial = false;
                if (isset($columnMap['has_serial']) && isset($row[$columnMap['has_serial']])) {
                    $rawSerial = mb_strtoupper(trim((string)$row[$columnMap['has_serial']]));
                    $hasSerial = in_array($rawSerial, ['S', 'SIM', 'TRUE', '1', 'Y', 'YES']);
                }

                // Custo Unitário
                $unitCost = 0.0;
                if (isset($columnMap['unit_cost']) && isset($row[$columnMap['unit_cost']])) {
                    $rawCost = trim((string)$row[$columnMap['unit_cost']]);
                    $rawCost = str_replace(['R$', ' ', '.'], '', $rawCost);
                    $rawCost = str_replace(',', '.', $rawCost);
                    if (is_numeric($rawCost)) {
                        $unitCost = max(0.0, (float)$rawCost);
                    }
                }

                // Estoque Mínimo
                $minStock = 0.0;
                if (isset($columnMap['min_stock']) && isset($row[$columnMap['min_stock']])) {
                    $rawMin = trim((string)$row[$columnMap['min_stock']]);
                    $rawMin = str_replace([' ', '.'], '', $rawMin);
                    $rawMin = str_replace(',', '.', $rawMin);
                    if (is_numeric($rawMin)) {
                        $minStock = max(0.0, (float)$rawMin);
                    }
                }

                // Busca se material já existe
                $existingMaterial = Material::where('account_id', $accountId)
                    ->where('code', $code)
                    ->first();

                if ($existingMaterial) {
                    if ($updateExisting) {
                        $existingMaterial->update([
                            'name' => $name,
                            'category' => $category,
                            'unit_id' => $unitId,
                            'has_serial' => $hasSerial,
                            'unit_cost' => $unitCost > 0 ? $unitCost : $existingMaterial->unit_cost,
                            'min_stock' => $minStock > 0 ? $minStock : $existingMaterial->min_stock,
                            'is_active' => true,
                        ]);
                        $updatedCount++;
                    }
                } else {
                    Material::create([
                        'account_id' => $accountId,
                        'code' => $code,
                        'name' => $name,
                        'category' => $category,
                        'unit_id' => $unitId,
                        'has_serial' => $hasSerial,
                        'unit_cost' => $unitCost,
                        'min_stock' => $minStock,
                        'is_active' => true,
                    ]);
                    $importedCount++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return [
            'total_rows' => $totalRows,
            'imported_count' => $importedCount,
            'updated_count' => $updatedCount,
            'failed_count' => $failedCount,
            'errors' => $errors,
        ];
    }

    /**
     * Mapeia os índices das colunas a partir do cabeçalho informado.
     */
    protected function mapHeaders(array $headers): array
    {
        $map = [];

        foreach ($headers as $index => $header) {
            $normalized = $this->normalizeString((string)$header);

            if (in_array($normalized, ['codigo', 'cod', 'code', 'sku', 'codigodomaterial'])) {
                $map['code'] = $index;
            } elseif (in_array($normalized, ['nome', 'nomedomaterial', 'material', 'name', 'descricao', 'descricaodomaterial', 'desc'])) {
                $map['name'] = $index;
            } elseif (in_array($normalized, ['categoria', 'category', 'cat', 'grupodematerial'])) {
                $map['category'] = $index;
            } elseif (in_array($normalized, ['unidade', 'unid', 'unit', 'unidadedemedida', 'medida', 'uom'])) {
                $map['unit'] = $index;
            } elseif (in_array($normalized, ['serializado', 'hasserial', 'serial', 'rastreamentoserial', 'serializadosn', 'exigeserial'])) {
                $map['has_serial'] = $index;
            } elseif (in_array($normalized, ['custo', 'custounitario', 'customedio', 'unitcost', 'preco', 'precocusto'])) {
                $map['unit_cost'] = $index;
            } elseif (in_array($normalized, ['estoqueminimo', 'estoquemin', 'minstock', 'estminimo'])) {
                $map['min_stock'] = $index;
            }
        }

        return $map;
    }

    /**
     * Normaliza uma string para comparação de cabeçalho (sem acentos, minúsculo, sem caracteres especiais).
     */
    protected function normalizeString(string $value): string
    {
        $value = trim($value);
        $value = mb_strtolower($value, 'UTF-8');
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        return preg_replace('/[^a-z0-9]/', '', $value) ?: '';
    }
}
