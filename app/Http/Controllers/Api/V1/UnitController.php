<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\StoreUnitRequest;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Unit::query()->withCount('materials');

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            });
        }

        if ($request->boolean('active_only', false)) {
            $query->where('is_active', true);
        }

        $units = $query->orderBy('code')->get();

        return response()->json([
            'data' => $units,
        ]);
    }

    public function store(StoreUnitRequest $request): JsonResponse
    {
        $unit = Unit::create($request->validated());

        return response()->json([
            'message' => 'Unidade de medida criada com sucesso.',
            'data' => $unit,
        ], 201);
    }

    public function show(Unit $unit): JsonResponse
    {
        $unit->loadCount('materials');
        return response()->json(['data' => $unit]);
    }

    public function update(StoreUnitRequest $request, Unit $unit): JsonResponse
    {
        $unit->update($request->validated());
        $unit->loadCount('materials');

        return response()->json([
            'message' => 'Unidade de medida atualizada com sucesso.',
            'data' => $unit,
        ]);
    }

    public function destroy(Unit $unit): JsonResponse
    {
        if ($unit->materials()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir esta unidade de medida pois ela está associada a materiais no catálogo de estoque. Inative a unidade se não for mais utilizá-la.',
            ], 422);
        }

        $unit->delete();

        return response()->json([
            'message' => 'Unidade de medida excluída com sucesso.',
        ]);
    }
}
