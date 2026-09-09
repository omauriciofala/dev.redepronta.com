<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PersonGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PersonGroupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $accountId = $request->user()?->account_id ?? 1;

        $groups = PersonGroup::where('account_id', $accountId)
            ->withCount('people')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $groups,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $accountId = $request->user()?->account_id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $validated['account_id'] = $accountId;
        $validated['color'] = $validated['color'] ?? '#FC6714';
        $validated['is_active'] = $validated['is_active'] ?? true;

        $group = PersonGroup::create($validated);
        $group->loadCount('people');

        return response()->json([
            'success' => true,
            'message' => 'Grupo de pessoas cadastrado com sucesso.',
            'data' => $group,
        ], 201);
    }

    public function show(PersonGroup $personGroup): JsonResponse
    {
        $personGroup->loadCount('people');

        return response()->json([
            'success' => true,
            'data' => $personGroup,
        ]);
    }

    public function update(Request $request, PersonGroup $personGroup): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        $personGroup->update($validated);
        $personGroup->loadCount('people');

        return response()->json([
            'success' => true,
            'message' => 'Grupo atualizado com sucesso.',
            'data' => $personGroup,
        ]);
    }

    public function destroy(PersonGroup $personGroup): JsonResponse
    {
        if ($personGroup->people()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir um grupo que possui pessoas vinculadas. Remova o vínculo antes.',
            ], 422);
        }

        $personGroup->delete();

        return response()->json([
            'success' => true,
            'message' => 'Grupo excluído com sucesso.',
        ]);
    }
}
