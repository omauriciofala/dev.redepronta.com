<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cnae;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CnaeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Cnae::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('active_only')) {
            $query->where('is_active', true);
        }

        $cnaes = $query->orderBy('code')->get();

        return response()->json([
            'success' => true,
            'data' => $cnaes,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:cnaes,code',
            'description' => 'required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;

        $cnae = Cnae::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'CNAE cadastrado com sucesso.',
            'data' => $cnae,
        ], 201);
    }

    public function update(Request $request, Cnae $cnae): JsonResponse
    {
        $validated = $request->validate([
            'code' => 'sometimes|required|string|max:20|unique:cnaes,code,' . $cnae->id,
            'description' => 'sometimes|required|string|max:255',
            'is_active' => 'boolean',
        ]);

        $cnae->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'CNAE atualizado com sucesso.',
            'data' => $cnae,
        ]);
    }

    public function destroy(Cnae $cnae): JsonResponse
    {
        $cnae->delete();

        return response()->json([
            'success' => true,
            'message' => 'CNAE excluído com sucesso.',
        ]);
    }
}
