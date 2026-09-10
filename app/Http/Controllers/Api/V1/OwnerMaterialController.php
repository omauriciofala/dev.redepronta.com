<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialOwner;
use App\Models\OwnerMaterial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function template(MaterialOwner $materialOwner): Response
    {
        $accountId = $this->getAccountId();
        abort_if($materialOwner->account_id !== $accountId, 403, 'Acesso negado.');

        $headers = ['codigo_proprietario', 'nome_proprietario', 'codigo_sistema_sku', 'observacoes'];
        $examples = [
            ['MAT-VIV-001', 'CABO DROP OPTICO 1 FO BOBINA 1KM', 'CAB-DROP-1FO', 'Código interno de rede FTTH'],
            ['MAT-VIV-002', 'ONU XPON WIFI 6 DUAL BAND', 'ONU-XPON-GIGA', 'Homologado Anatel'],
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

        $safeCode = preg_replace('/[^a-zA-Z0-9_-]/', '_', $materialOwner->code);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"modelo_catalogo_proprietario_{$safeCode}.csv\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function import(Request $request, MaterialOwner $materialOwner): JsonResponse
    {
        $accountId = $this->getAccountId();
        abort_if($materialOwner->account_id !== $accountId, 403, 'Acesso negado.');

        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
        ], [
            'file.required' => 'O arquivo de importação é obrigatório.',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['csv', 'txt'])) {
            return response()->json([
                'message' => 'No momento a importação do catálogo do proprietário aceita arquivos .csv ou .txt formatados com ponto e vírgula.',
            ], 422);
        }

        $lines = file($file->getRealPath(), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (empty($lines)) {
            return response()->json(['message' => 'O arquivo enviado está vazio.'], 422);
        }

        // Detecta separador
        $firstLine = $lines[0];
        $separator = str_contains($firstLine, ';') ? ';' : ',';

        $headerRow = str_getcsv(ltrim($firstLine, "\xEF\xBB\xBF"), $separator);
        $headerMap = [];
        foreach ($headerRow as $idx => $headerName) {
            $raw = mb_strtolower(trim($headerName), 'UTF-8');
            $clean = str_replace(
                ['á','à','ã','â','é','ê','í','ó','ô','õ','ú','ü','ç'],
                ['a','a','a','a','e','e','i','o','o','o','u','u','c'],
                $raw
            );

            if (str_contains($clean, 'proprietario') && (str_contains($clean, 'codigo') || str_contains($clean, 'sku'))) {
                $headerMap['owner_code'] = $idx;
            } elseif (str_contains($clean, 'proprietario') && (str_contains($clean, 'nome') || str_contains($clean, 'descricao'))) {
                $headerMap['owner_name'] = $idx;
            } elseif ((str_contains($clean, 'sistema') || str_contains($clean, 'interno') || str_contains($clean, 'canonico')) && (str_contains($clean, 'codigo') || str_contains($clean, 'sku'))) {
                $headerMap['system_code'] = $idx;
            } elseif (str_contains($clean, 'obs') || str_contains($clean, 'nota')) {
                $headerMap['notes'] = $idx;
            }
        }

        // Fallbacks se nomes simples
        if (!isset($headerMap['owner_code']) && isset($headerRow[0])) $headerMap['owner_code'] = 0;
        if (!isset($headerMap['owner_name']) && isset($headerRow[1])) $headerMap['owner_name'] = 1;
        if (!isset($headerMap['system_code']) && isset($headerRow[2])) $headerMap['system_code'] = 2;

        $imported = 0;
        $updated = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            for ($i = 1; $i < count($lines); $i++) {
                $cols = str_getcsv($lines[$i], $separator);
                $ownerCode = isset($headerMap['owner_code'], $cols[$headerMap['owner_code']]) ? trim($cols[$headerMap['owner_code']]) : '';
                $ownerName = isset($headerMap['owner_name'], $cols[$headerMap['owner_name']]) ? trim($cols[$headerMap['owner_name']]) : '';
                $systemCode = isset($headerMap['system_code'], $cols[$headerMap['system_code']]) ? trim($cols[$headerMap['system_code']]) : '';
                $notes = isset($headerMap['notes'], $cols[$headerMap['notes']]) ? trim($cols[$headerMap['notes']]) : null;

                if ($ownerCode === '' || $systemCode === '') {
                    continue;
                }

                // Busca o material canônico
                $material = Material::where('account_id', $accountId)
                    ->where(function ($q) use ($systemCode) {
                        $q->where('code', $systemCode)
                          ->orWhere('name', $systemCode);
                    })
                    ->first();

                if (!$material) {
                    $errors[] = "Linha " . ($i + 1) . ": SKU do sistema '{$systemCode}' não encontrado.";
                    continue;
                }

                if ($ownerName === '') {
                    $ownerName = $material->name;
                }

                // Cria ou atualiza o De/Para
                $existing = OwnerMaterial::where('account_id', $accountId)
                    ->where('material_owner_id', $materialOwner->id)
                    ->where('material_id', $material->id)
                    ->first();

                if ($existing) {
                    $existing->update([
                        'owner_code' => $ownerCode,
                        'owner_name' => $ownerName,
                        'notes' => $notes ?: $existing->notes,
                    ]);
                    $updated++;
                } else {
                    OwnerMaterial::create([
                        'account_id' => $accountId,
                        'material_owner_id' => $materialOwner->id,
                        'material_id' => $material->id,
                        'owner_code' => $ownerCode,
                        'owner_name' => $ownerName,
                        'notes' => $notes,
                        'is_active' => true,
                    ]);
                    $imported++;
                }
            }

            DB::commit();

            return response()->json([
                'message' => sprintf('Importação concluída: %d inseridos, %d atualizados%s.', $imported, $updated, count($errors) > 0 ? ', com avisos em ' . count($errors) . ' linha(s)' : ''),
                'data' => [
                    'imported_count' => $imported,
                    'updated_count' => $updated,
                    'failed_count' => count($errors),
                    'errors' => $errors,
                ],
                'imported_count' => $imported,
                'updated_count' => $updated,
                'errors' => $errors,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Falha ao processar arquivo: ' . $e->getMessage(),
            ], 422);
        }
    }
}
