<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\StoreMaterialRequest;
use App\Models\Material;
use App\Models\Unit;
use App\Services\Stock\MaterialImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaterialController extends Controller
{
    protected function getAccountId(): int
    {
        return session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
    }

    public function index(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $query = Material::where('account_id', $accountId)->with('unit');

        $ownerId = $request->query('owner_id');
        if (!$ownerId && $request->filled('depot_id')) {
            $depot = \App\Models\Depot::with('cluster')->where('account_id', $accountId)->find($request->query('depot_id'));
            $ownerId = $depot?->owner_id ?? $depot?->cluster?->owner_id;
        }

        if ($ownerId) {
            $query->with(['ownerMaterials' => function ($q) use ($ownerId) {
                $q->where('material_owner_id', $ownerId);
            }]);
        }

        if ($request->filled('search')) {
            $s = trim($request->query('search'));
            $query->where(function ($q) use ($s, $ownerId) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%");

                if ($ownerId) {
                    $q->orWhereHas('ownerMaterials', function ($omq) use ($s, $ownerId) {
                        $omq->where('material_owner_id', $ownerId)
                            ->where(function ($sub) use ($s) {
                                $sub->where('owner_code', 'like', "%{$s}%")
                                    ->orWhere('owner_name', 'like', "%{$s}%");
                            });
                    });
                }
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->has('has_serial')) {
            $query->where('has_serial', $request->boolean('has_serial'));
        }

        if ($request->has('track_batch')) {
            $query->where('track_batch', $request->boolean('track_batch'));
        }

        if ($request->filled('tracking_type')) {
            $tt = strtoupper(trim((string) $request->query('tracking_type')));
            if ($tt === 'SERIALIZADO') $tt = 'SERIAL';
            if (in_array($tt, ['LOTE', 'METRAGEM'])) $tt = 'BATCH';
            if (in_array($tt, ['GRANEL', 'CONVENCIONAL'])) $tt = 'BULK';
            $query->where('tracking_type', $tt);
        }

        if ($request->boolean('active_only', false)) {
            $query->where('is_active', true);
        }

        $materials = $query->orderBy('name')->get();

        $owner = $ownerId ? \App\Models\MaterialOwner::where('account_id', $accountId)->find($ownerId) : null;

        $materials->transform(function ($material) use ($ownerId, $owner) {
            $systemCode = $material->code;
            $systemName = $material->name;
            $ownerMat = $ownerId ? $material->ownerMaterials?->first() : null;

            $data = $material->toArray();
            $data['system_code'] = $systemCode;
            $data['system_name'] = $systemName;
            $data['owner_code'] = $ownerMat?->owner_code;
            $data['owner_name'] = $ownerMat?->owner_name;
            $data['has_owner_alias'] = (bool) $ownerMat;
            $data['owner_id'] = $owner?->id;
            $data['owner_info'] = $owner ? ['id' => $owner->id, 'code' => $owner->code, 'name' => $owner->name] : null;

            if ($ownerMat) {
                $data['code'] = $ownerMat->owner_code;
                $data['name'] = $ownerMat->owner_name;
            }

            return $data;
        });

        return response()->json([
            'data' => $materials,
            'owner_active' => $ownerId ? true : false,
        ]);
    }

    public function units(): JsonResponse
    {
        $units = Unit::where('is_active', true)->orderBy('code')->get();
        return response()->json(['data' => $units]);
    }

    public function downloadTemplate(Request $request, MaterialImportService $importService): Response
    {
        $format = strtolower($request->query('format', 'csv'));

        if ($format === 'xlsx' && $importService->hasXlsxTemplate()) {
            return response()->download(
                $importService->getXlsxTemplatePath(),
                'modelo_importacao_materiais.xlsx',
                ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            );
        }

        $csv = $importService->generateTemplateCsv();

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="modelo_importacao_materiais.csv"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function import(Request $request, MaterialImportService $importService): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'update_existing' => ['nullable', 'boolean'],
        ], [
            'file.required' => 'O arquivo da planilha modelo é obrigatório.',
            'file.file' => 'O arquivo enviado é inválido.',
            'file.max' => 'O arquivo não pode ultrapassar 10MB.',
        ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['xlsx', 'csv', 'txt'])) {
            return response()->json([
                'message' => 'O arquivo deve estar no formato Excel (.xlsx) ou CSV (.csv, .txt).',
            ], 422);
        }

        $updateExisting = $request->boolean('update_existing', true);
        $accountId = $this->getAccountId();

        try {
            $result = $importService->importFromUploadedFile(
                $file,
                $accountId,
                $updateExisting
            );

            $msg = sprintf(
                'Importação concluída: %d material(is) cadastrado(s), %d atualizado(s)%s.',
                $result['imported_count'],
                $result['updated_count'],
                $result['failed_count'] > 0 ? ", {$result['failed_count']} com erro(s)" : ''
            );

            return response()->json([
                'message' => $msg,
                'data' => $result,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Falha na importação do arquivo: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function store(StoreMaterialRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['account_id'] = $this->getAccountId();

        $material = Material::create($data);
        $material->load('unit');

        return response()->json([
            'message' => 'Material cadastrado com sucesso.',
            'data' => $material,
        ], 201);
    }

    public function show(Material $material): JsonResponse
    {
        $material->load('unit');
        return response()->json(['data' => $material]);
    }

    public function update(StoreMaterialRequest $request, Material $material): JsonResponse
    {
        $material->update($request->validated());
        $material->load('unit');

        return response()->json([
            'message' => 'Material atualizado com sucesso.',
            'data' => $material,
        ]);
    }

    public function destroy(Material $material): JsonResponse
    {
        $hasBalance = $material->balances()->where('quantity', '>', 0)->exists();
        $hasSerials = $material->serials()->exists();

        if ($hasBalance || $hasSerials) {
            return response()->json([
                'message' => 'Não é possível excluir este material pois ele possui saldo ou histórico de seriais vinculados. Inative o cadastro.',
            ], 422);
        }

        $material->delete();
        return response()->json([
            'message' => 'Material excluído com sucesso.',
        ]);
    }
}
