<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\StoreMaterialRequest;
use App\Models\Material;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        if ($request->has('has_serial')) {
            $query->where('has_serial', $request->boolean('has_serial'));
        }

        if ($request->boolean('active_only', false)) {
            $query->where('is_active', true);
        }

        $materials = $query->orderBy('name')->get();

        return response()->json([
            'data' => $materials,
        ]);
    }

    public function units(): JsonResponse
    {
        $units = Unit::where('is_active', true)->orderBy('code')->get();
        return response()->json(['data' => $units]);
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
