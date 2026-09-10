<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialOwner;
use App\Models\OwnerMaterial;
use App\Services\Stock\MaterialImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OwnerMaterialController extends Controller
{
    protected function getAccountId(): int
    {
        return session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
    }

    public function index(Request $request, MaterialOwner $materialOwner): JsonResponse
    {
        $accountId = $this->getAccountId();
        abort_if($materialOwner->account_id !== $accountId, 403, 'Acesso negado.');

        $query = OwnerMaterial::where('account_id', $accountId)
            ->where('material_owner_id', $materialOwner->id)
            ->with(['material.unit']);

        if ($request->filled('search')) {
            $s = trim($request->query('search'));
            $query->where(function ($q) use ($s) {
                $q->where('owner_code', 'like', "%{$s}%")
                  ->orWhere('owner_name', 'like', "%{$s}%")
                  ->orWhereHas('material', function ($mq) use ($s) {
                      $mq->where('code', 'like', "%{$s}%")
                         ->orWhere('name', 'like', "%{$s}%");
                  });
            });
        }

        if ($request->boolean('active_only', false)) {
            $query->where('is_active', true);
        }

        $items = $query->orderBy('owner_name')->get();

        return response()->json([
            'data' => $items,
            'owner' => [
                'id' => $materialOwner->id,
                'code' => $materialOwner->code,
                'name' => $materialOwner->name,
            ],
            'total' => $items->count(),
        ]);
    }

    public function store(Request $request, MaterialOwner $materialOwner): JsonResponse
    {
        $accountId = $this->getAccountId();
        abort_if($materialOwner->account_id !== $accountId, 403, 'Acesso negado.');

        $validated = $request->validate([
            'material_id' => [
                'required',
                'integer',
                'exists:materials,id',
            ],
            'owner_code' => ['required', 'string', 'max:100'],
            'owner_name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'material_id.required' => 'Selecione o material canônico do sistema.',
            'owner_code.required' => 'O código / SKU do proprietário é obrigatório.',
            'owner_name.required' => 'O nome do material no proprietário é obrigatório.',
        ]);

        // Valida se o material pertence à mesma conta
        $material = Material::where('account_id', $accountId)->findOrFail($validated['material_id']);

        // Verifica unicidade de (material_owner_id, material_id) e (material_owner_id, owner_code)
        $existing = OwnerMaterial::where('account_id', $accountId)
            ->where('material_owner_id', $materialOwner->id)
            ->where(function ($q) use ($validated) {
                $q->where('material_id', $validated['material_id'])
                  ->orWhere('owner_code', $validated['owner_code']);
            })
            ->first();

        if ($existing) {
            if ($existing->material_id === (int)$validated['material_id']) {
                return response()->json([
                    'message' => "Este material já possui um código cadastrado para este proprietário ({$existing->owner_code}).",
                ], 422);
            }
            if (strcasecmp($existing->owner_code, $validated['owner_code']) === 0) {
                return response()->json([
                    'message' => "O código do proprietário '{$validated['owner_code']}' já está em uso para outro material.",
                ], 422);
            }
        }

        $item = OwnerMaterial::create([
            'account_id' => $accountId,
            'material_owner_id' => $materialOwner->id,
            'material_id' => $material->id,
            'owner_code' => trim($validated['owner_code']),
            'owner_name' => trim($validated['owner_name']),
            'notes' => $validated['notes'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $item->load(['material.unit']);

        return response()->json([
            'message' => 'Material vinculado ao catálogo do proprietário com sucesso.',
            'data' => $item,
        ], 201);
    }

    public function show(MaterialOwner $materialOwner, OwnerMaterial $ownerMaterial): JsonResponse
    {
        $accountId = $this->getAccountId();
        abort_if($materialOwner->account_id !== $accountId || $ownerMaterial->account_id !== $accountId, 403, 'Acesso negado.');

        $ownerMaterial->load(['material.unit', 'owner']);

        return response()->json([
            'data' => $ownerMaterial,
        ]);
    }

    public function update(Request $request, MaterialOwner $materialOwner, OwnerMaterial $ownerMaterial): JsonResponse
    {
        $accountId = $this->getAccountId();
        abort_if($materialOwner->account_id !== $accountId || $ownerMaterial->account_id !== $accountId, 403, 'Acesso negado.');

        $validated = $request->validate([
            'owner_code' => ['required', 'string', 'max:100'],
            'owner_name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'owner_code.required' => 'O código / SKU do proprietário é obrigatório.',
            'owner_name.required' => 'O nome do material no proprietário é obrigatório.',
        ]);

        // Verifica unicidade de owner_code para outro item deste mesmo proprietário
        $conflict = OwnerMaterial::where('account_id', $accountId)
            ->where('material_owner_id', $materialOwner->id)
            ->where('id', '!=', $ownerMaterial->id)
            ->where('owner_code', $validated['owner_code'])
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => "O código '{$validated['owner_code']}' já está em uso neste proprietário.",
            ], 422);
        }

        $ownerMaterial->update([
            'owner_code' => trim($validated['owner_code']),
            'owner_name' => trim($validated['owner_name']),
            'notes' => $validated['notes'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $ownerMaterial->load(['material.unit']);

        return response()->json([
            'message' => 'Catálogo do proprietário atualizado com sucesso.',
            'data' => $ownerMaterial,
        ]);
    }

    public function destroy(MaterialOwner $materialOwner, OwnerMaterial $ownerMaterial): JsonResponse
    {
        $accountId = $this->getAccountId();
        abort_if($materialOwner->account_id !== $accountId || $ownerMaterial->account_id !== $accountId, 403, 'Acesso negado.');

        $ownerMaterial->delete();

        return response()->json([
            'message' => 'Item removido do catálogo do proprietário.',
        ]);
    }

    public function template(Request $request, MaterialOwner $materialOwner, MaterialImportService $importService): Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $accountId = $this->getAccountId();
        abort_if($materialOwner->account_id !== $accountId, 403, 'Acesso negado.');

        $format = strtolower($request->query('format', 'csv'));
        $safeCode = preg_replace('/[^a-zA-Z0-9_-]/', '_', $materialOwner->code);

        if ($format === 'xlsx' && $importService->hasXlsxTemplate()) {
            return response()->download(
                $importService->getXlsxTemplatePath(),
                "modelo_importacao_proprietario_{$safeCode}.xlsx",
                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            );
        }

        // O Modelo consiste exatamente em 3 colunas: Cód., Nome do Material, Cód. Prop.
        $headers = ['Cód.', 'Nome do Material', 'Cód. Prop.'];
        $examples = [
            ['MAT-001', 'CABO DROP OPTICO 1 FO BOBINA 1KM', 'TEL-CAB-01'],
            ['MAT-002', '', 'TEL-ONT-02'],
            ['', 'CONECTOR OPTICO SC-APC FAST CLIQUE', 'TEL-CON-APC'],
        ];

        $handle = fopen('php://temp', 'r+');
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
        fputcsv($handle, $headers, ';');
        foreach ($examples as $row) {
            fputcsv($handle, $row, ';');
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"modelo_importacao_proprietario_{$safeCode}.csv\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Gera um código de 4 caracteres alfanuméricos em maiúsculas (letras e números) único para o sistema.
     */
    protected function generateFourDigitCode(int $accountId, array $reserved = []): string
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($attempt = 0; $attempt < 100; $attempt++) {
            $code = '';
            for ($i = 0; $i < 4; $i++) {
                $code .= $chars[random_int(0, strlen($chars) - 1)];
            }

            if (in_array($code, $reserved, true)) {
                continue;
            }

            $exists = Material::where('account_id', $accountId)->where('code', $code)->exists();
            if (!$exists) {
                return $code;
            }
        }

        return strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 4));
    }

    /**
     * Mapeia os índices das 3 colunas da planilha do proprietário.
     */
    protected function mapThreeColumnHeader(array $headerRow): array
    {
        $headerMap = [];
        foreach ($headerRow as $idx => $headerName) {
            $raw = mb_strtolower(trim($headerName), 'UTF-8');
            $clean = str_replace(
                ['á','à','ã','â','é','ê','í','ó','ô','õ','ú','ü','ç','.'],
                ['a','a','a','a','e','e','i','o','o','o','u','u','c',''],
                $raw
            );

            if (str_contains($clean, 'prop') && (str_contains($clean, 'cod') || str_contains($clean, 'sku') || str_contains($clean, 'codigo'))) {
                $headerMap['owner_code'] = $idx;
            } elseif (str_contains($clean, 'nome') || str_contains($clean, 'desc') || str_contains($clean, 'material')) {
                $headerMap['material_name'] = $idx;
            } elseif (str_contains($clean, 'cod') || str_contains($clean, 'sku') || str_contains($clean, 'sistema')) {
                $headerMap['system_code'] = $idx;
            }
        }

        // Posições padrão para 3 colunas se não mapeado pelos títulos
        if (!isset($headerMap['system_code']) && isset($headerRow[0])) $headerMap['system_code'] = 0;
        if (!isset($headerMap['material_name']) && isset($headerRow[1])) $headerMap['material_name'] = 1;
        if (!isset($headerMap['owner_code']) && isset($headerRow[2])) $headerMap['owner_code'] = 2;

        return $headerMap;
    }

    /**
     * Pré-visualização da planilha antes de efetivar a importação.
     */
    public function preview(Request $request, MaterialOwner $materialOwner, MaterialImportService $importService): JsonResponse
    {
        $accountId = $this->getAccountId();
        abort_if($materialOwner->account_id !== $accountId, 403, 'Acesso negado.');

        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ], [
            'file.required' => 'O arquivo de importação é obrigatório para visualização prévia.',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['xlsx', 'csv', 'txt'])) {
            return response()->json([
                'message' => 'O arquivo deve estar no formato Excel (.xlsx) ou CSV (.csv, .txt).',
            ], 422);
        }

        if ($ext === 'xlsx') {
            $parsedRows = $importService->parseXlsxRows($file->getRealPath());
            if (empty($parsedRows)) {
                return response()->json(['message' => 'O arquivo enviado está vazio.'], 422);
            }
            $headerRow = $parsedRows[0];
            $dataRows = array_slice($parsedRows, 1);
        } else {
            $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (empty($lines)) {
                return response()->json(['message' => 'O arquivo enviado está vazio.'], 422);
            }

            $firstLine = $lines[0];
            $separator = str_contains($firstLine, ';') ? ';' : ',';
            $headerRow = str_getcsv(ltrim($firstLine, "\xEF\xBB\xBF"), $separator);
            $dataRows = [];
            for ($i = 1; $i < count($lines); $i++) {
                $dataRows[] = str_getcsv($lines[$i], $separator);
            }
        }

        $headerMap = $this->mapThreeColumnHeader($headerRow);

        $previewRows = [];
        $reservedCodes = [];
        $toCreateMaterial = 0;
        $toLinkExisting = 0;
        $toUpdateLink = 0;
        $errorsCount = 0;

        foreach ($dataRows as $idx => $cols) {
            $rawSystemCode = isset($headerMap['system_code'], $cols[$headerMap['system_code']]) ? trim((string)$cols[$headerMap['system_code']]) : '';
            $rawMaterialName = isset($headerMap['material_name'], $cols[$headerMap['material_name']]) ? trim((string)$cols[$headerMap['material_name']]) : '';
            $rawOwnerCode = isset($headerMap['owner_code'], $cols[$headerMap['owner_code']]) ? trim((string)$cols[$headerMap['owner_code']]) : '';

            // Pula linhas em branco
            if ($rawSystemCode === '' && $rawMaterialName === '' && $rawOwnerCode === '') {
                continue;
            }

            $lineNum = $idx + 2;
            $status = 'valid';
            $message = null;
            $action = 'link_existing';
            $actionLabel = 'Vincular Existente';
            $nameSource = 'custom';
            $nameNote = '';
            $isGenerated = false;
            $systemCode = $rawSystemCode;
            $systemName = $rawMaterialName;
            $ownerName = $rawMaterialName;

            // Validação 1: Cód. Prop. é obrigatório
            if ($rawOwnerCode === '') {
                $status = 'error';
                $action = 'error';
                $actionLabel = 'Erro';
                $message = 'Código do Proprietário (Cód. Prop.) não informado.';
                $errorsCount++;
            }
            // Caso A: Cód. do sistema preenchido -> busca existente no sistema
            elseif ($rawSystemCode !== '') {
                $material = Material::where('account_id', $accountId)
                    ->where(function ($q) use ($rawSystemCode) {
                        $q->where('code', $rawSystemCode)
                          ->orWhere('name', $rawSystemCode);
                    })
                    ->first();

                if (!$material) {
                    $status = 'error';
                    $action = 'error';
                    $actionLabel = 'Erro';
                    $message = "Código '{$rawSystemCode}' não encontrado no sistema. Para gerar um novo material, deixe a coluna Cód. em branco.";
                    $errorsCount++;
                } else {
                    $systemCode = $material->code;
                    $systemName = $material->name;

                    // Herança de nome: se em branco ou igual, herda do sistema
                    if ($rawMaterialName === '' || mb_strtolower($rawMaterialName) === mb_strtolower($material->name)) {
                        $ownerName = $material->name;
                        $nameSource = 'inherited';
                        $nameNote = "Herdará o nome do sistema: {$material->name}";
                    } else {
                        $ownerName = $rawMaterialName;
                        $nameSource = 'custom';
                        $nameNote = 'Nome personalizado para o proprietário';
                    }

                    // Checa se vínculo já existe
                    $existingLink = OwnerMaterial::where('account_id', $accountId)
                        ->where('material_owner_id', $materialOwner->id)
                        ->where(function ($q) use ($material, $rawOwnerCode) {
                            $q->where('material_id', $material->id)
                              ->orWhere('owner_code', $rawOwnerCode);
                        })
                        ->first();

                    if ($existingLink) {
                        $action = 'update_link';
                        $actionLabel = 'Atualizar Vínculo';
                        $toUpdateLink++;
                    } else {
                        $action = 'link_existing';
                        $actionLabel = 'Vincular Existente';
                        $toLinkExisting++;
                    }
                }
            }
            // Caso B: Cód. do sistema em branco -> gera código de 4 dígitos e cadastra material
            else {
                if ($rawMaterialName === '') {
                    $status = 'error';
                    $action = 'error';
                    $actionLabel = 'Erro';
                    $message = 'Nome do Material é obrigatório para cadastrar um novo material quando o Cód. estiver em branco.';
                    $errorsCount++;
                } else {
                    $newCode = $this->generateFourDigitCode($accountId, $reservedCodes);
                    $reservedCodes[] = $newCode;

                    $systemCode = $newCode;
                    $systemName = $rawMaterialName;
                    $ownerName = $rawMaterialName;
                    $isGenerated = true;
                    $action = 'create_material_and_link';
                    $actionLabel = 'Criar Material e Vincular';
                    $nameSource = 'new_material';
                    $nameNote = "Novo material no sistema com SKU de 4 dígitos [{$newCode}]";
                    $toCreateMaterial++;
                }
            }

            $previewRows[] = [
                'line' => $lineNum,
                'system_code' => $systemCode,
                'is_generated_code' => $isGenerated,
                'system_name' => $systemName,
                'owner_name' => $ownerName,
                'name_source' => $nameSource,
                'name_note' => $nameNote,
                'owner_code' => $rawOwnerCode,
                'action' => $action,
                'action_label' => $actionLabel,
                'status' => $status,
                'message' => $message,
            ];
        }

        $canImport = ($toCreateMaterial + $toLinkExisting + $toUpdateLink) > 0;

        return response()->json([
            'can_import' => $canImport,
            'summary' => [
                'total_rows' => count($previewRows),
                'to_create_material' => $toCreateMaterial,
                'to_link_existing' => $toLinkExisting,
                'to_update_link' => $toUpdateLink,
                'errors_count' => $errorsCount,
            ],
            'preview_rows' => $previewRows,
        ]);
    }

    /**
     * Importação definitiva das linhas do catálogo do proprietário (após preview).
     */
    public function import(Request $request, MaterialOwner $materialOwner, MaterialImportService $importService): JsonResponse
    {
        $accountId = $this->getAccountId();
        abort_if($materialOwner->account_id !== $accountId, 403, 'Acesso negado.');

        $rowsToProcess = [];

        // Modo 1: Recebe array 'rows' aprovado na prévia
        if ($request->has('rows') && is_array($request->input('rows'))) {
            $rowsToProcess = $request->input('rows');
        }
        // Modo 2: Recebe arquivo direto (retrocompatibilidade)
        elseif ($request->hasFile('file')) {
            $previewResponse = $this->preview($request, $materialOwner, $importService);
            $previewData = json_decode($previewResponse->getContent(), true);
            $rowsToProcess = $previewData['preview_rows'] ?? [];
        } else {
            return response()->json([
                'message' => 'Nenhum dado ou arquivo enviado para importação.',
            ], 422);
        }

        if (empty($rowsToProcess)) {
            return response()->json(['message' => 'Nenhuma linha válida encontrada para importar.'], 422);
        }

        DB::beginTransaction();
        try {
            // Unidade de medida padrão para materiais recém-criados
            $defaultUnit = \App\Models\Unit::whereIn('code', ['UND', 'UN'])->first()
                ?? \App\Models\Unit::where('is_active', true)->first()
                ?? \App\Models\Unit::create(['code' => 'UND', 'name' => 'Unidade', 'is_active' => true]);

            $createdMaterials = 0;
            $createdLinks = 0;
            $updatedLinks = 0;
            $failedRows = 0;
            $errors = [];
            foreach ($rowsToProcess as $row) {
                if (($row['status'] ?? 'valid') === 'error') {
                    $failedRows++;
                    if (!empty($row['message'])) {
                        $errors[] = "Linha " . ($row['line'] ?? '?') . ": " . $row['message'];
                    }
                    continue;
                }

                $ownerCode = trim($row['owner_code'] ?? '');
                $ownerName = trim($row['owner_name'] ?? '');
                $systemCode = trim($row['system_code'] ?? '');
                $systemName = trim($row['system_name'] ?? '') ?: $ownerName;
                $isGenerated = (bool) ($row['is_generated_code'] ?? false);
                $action = $row['action'] ?? '';

                if ($ownerCode === '') {
                    $failedRows++;
                    continue;
                }

                $material = null;

                // Caso: Criar novo material no sistema
                if ($isGenerated || $action === 'create_material_and_link') {
                    // Garante que o código de 4 dígitos não colidiu no intervalo
                    $finalCode = $systemCode;
                    if (Material::where('account_id', $accountId)->where('code', $finalCode)->exists()) {
                        $finalCode = $this->generateFourDigitCode($accountId);
                    }

                    $material = Material::create([
                        'account_id' => $accountId,
                        'unit_id' => $defaultUnit->id,
                        'code' => $finalCode,
                        'name' => $systemName,
                        'is_active' => true,
                    ]);
                    $createdMaterials++;
                } else {
                    // Busca material existente
                    $material = Material::where('account_id', $accountId)
                        ->where(function ($q) use ($systemCode) {
                            $q->where('code', $systemCode)
                              ->orWhere('name', $systemCode);
                        })
                        ->first();

                    if (!$material) {
                        $failedRows++;
                        $errors[] = "Material com SKU '{$systemCode}' não foi localizado.";
                        continue;
                    }
                }

                if ($ownerName === '') {
                    $ownerName = $material->name;
                }

                // Cria ou atualiza o De/Para
                $existing = OwnerMaterial::where('account_id', $accountId)
                    ->where('material_owner_id', $materialOwner->id)
                    ->where(function ($q) use ($material, $ownerCode) {
                        $q->where('material_id', $material->id)
                          ->orWhere('owner_code', $ownerCode);
                    })
                    ->first();

                if ($existing) {
                    $existing->update([
                        'material_id' => $material->id,
                        'owner_code' => $ownerCode,
                        'owner_name' => $ownerName,
                        'is_active' => true,
                    ]);
                    $updatedLinks++;
                } else {
                    OwnerMaterial::create([
                        'account_id' => $accountId,
                        'material_owner_id' => $materialOwner->id,
                        'material_id' => $material->id,
                        'owner_code' => $ownerCode,
                        'owner_name' => $ownerName,
                        'is_active' => true,
                    ]);
                    $createdLinks++;
                }
            }

            DB::commit();

            $totalImported = $createdLinks + $createdMaterials;

            return response()->json([
                'message' => sprintf(
                    'Importação concluída: %d novo(s) material(is) no sistema, %d novo(s) vínculo(s) e %d atualizado(s)%s.',
                    $createdMaterials,
                    $createdLinks,
                    $updatedLinks,
                    $failedRows > 0 ? " ({$failedRows} linha(s) ignorada(s))" : ''
                ),
                'data' => [
                    'created_materials' => $createdMaterials,
                    'created_links' => $createdLinks,
                    'updated_links' => $updatedLinks,
                    'imported_count' => $totalImported,
                    'updated_count' => $updatedLinks,
                    'failed_count' => $failedRows,
                    'errors' => $errors,
                ],
                'imported_count' => $totalImported,
                'updated_count' => $updatedLinks,
                'failed_count' => $failedRows,
                'errors' => $errors,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao importar materiais do proprietário: ' . $e->getMessage(), [
                'exception' => $e,
                'owner_id' => $materialOwner->id,
            ]);
            return response()->json([
                'message' => 'Falha ao processar importação: ' . $e->getMessage(),
            ], 422);
        }
    }
}
