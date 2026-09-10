<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\StoreMaterialCategoryRequest;
use App\Models\Material;
use App\Models\MaterialCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialCategoryController extends Controller
{
    protected function getAccountId(): int
    {
        return session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
    }

    public function index(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $query = MaterialCategory::where('account_id', $accountId)->withCount('materials');

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->boolean('active_only', false)) {
            $query->where('is_active', true);
        }

        $categories = $query->orderBy('name')->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function store(StoreMaterialCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['account_id'] = $this->getAccountId();

        $category = MaterialCategory::create($data);

        return response()->json([
            'message' => 'Categoria de material criada com sucesso.',
            'data' => $category,
        ], 201);
    }

    public function show(MaterialCategory $materialCategory): JsonResponse
    {
        $materialCategory->loadCount('materials');
        return response()->json(['data' => $materialCategory]);
    }

    public function update(StoreMaterialCategoryRequest $request, MaterialCategory $materialCategory): JsonResponse
    {
        $oldName = $materialCategory->name;
        $data = $request->validated();

        $materialCategory->update($data);

        // Se o nome foi alterado, sincroniza os materiais que utilizavam o nome anterior
        if (!empty($data['name']) && $data['name'] !== $oldName) {
            Material::where('account_id', $materialCategory->account_id)
                ->where('category', $oldName)
                ->update(['category' => $data['name']]);
        }

        $materialCategory->loadCount('materials');

        return response()->json([
            'message' => 'Categoria de material atualizada com sucesso.',
            'data' => $materialCategory,
        ]);
    }

    public function destroy(MaterialCategory $materialCategory): JsonResponse
    {
        $hasMaterials = Material::where('account_id', $materialCategory->account_id)
            ->where(function ($q) use ($materialCategory) {
                $q->where('category', $materialCategory->name)
                  ->orWhere('category', $materialCategory->code);
            })
            ->exists();

        if ($hasMaterials) {
            return response()->json([
                'message' => 'Não é possível excluir esta categoria pois existem materiais vinculados a ela no catálogo de estoque. Reclassifique os materiais antes de excluir ou inative a categoria.',
            ], 422);
        }

        $materialCategory->delete();

        return response()->json([
            'message' => 'Categoria de material excluída com sucesso.',
        ]);
    }
}
