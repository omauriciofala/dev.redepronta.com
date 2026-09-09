<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Neighborhood;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NeighborhoodController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Neighborhood::with('city.state');

        if ($cityId = $request->query('city_id')) {
            $query->where('city_id', $cityId);
        }

        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $neighborhoods = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $neighborhoods,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:100',
            'zone' => 'nullable|string|max:50',
        ]);

        $neighborhood = Neighborhood::create($validated);
        $neighborhood->load('city.state');

        return response()->json([
            'success' => true,
            'message' => 'Bairro cadastrado com sucesso.',
            'data' => $neighborhood,
        ], 201);
    }

    public function update(Request $request, Neighborhood $neighborhood): JsonResponse
    {
        $validated = $request->validate([
            'city_id' => 'sometimes|required|exists:cities,id',
            'name' => 'sometimes|required|string|max:100',
            'zone' => 'nullable|string|max:50',
        ]);

        $neighborhood->update($validated);
        $neighborhood->load('city.state');

        return response()->json([
            'success' => true,
            'message' => 'Bairro atualizado com sucesso.',
            'data' => $neighborhood,
        ]);
    }

    public function destroy(Neighborhood $neighborhood): JsonResponse
    {
        $neighborhood->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bairro excluído com sucesso.',
        ]);
    }
}
