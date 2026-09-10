<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\StoreDepotTypeRequest;
use App\Models\DepotType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepotTypeController extends Controller
{
    protected function getAccountId(): int
    {
        return session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
    }

    public function index(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $query = DepotType::where('account_id', $accountId)->withCount('depots');

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

        $types = $query->orderBy('name')->get();

        return response()->json([
            'data' => $types,
        ]);
    }

    public function store(StoreDepotTypeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['account_id'] = $this->getAccountId();

        $depotType = DepotType::create($data);

        return response()->json([
            'message' => 'Tipo de depósito criado com sucesso.',
            'data' => $depotType,
        ], 201);
    }

    public function show(DepotType $depotType): JsonResponse
    {
        $depotType->loadCount('depots');
        return response()->json(['data' => $depotType]);
    }

    public function update(StoreDepotTypeRequest $request, DepotType $depotType): JsonResponse
    {
        $depotType->update($request->validated());
        return response()->json([
            'message' => 'Tipo de depósito atualizado com sucesso.',
            'data' => $depotType,
        ]);
    }

    public function destroy(DepotType $depotType): JsonResponse
    {
        if ($depotType->depots()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir este tipo de depósito pois existem depósitos cadastrados com este tipo. Inative o tipo de depósito ou altere os depósitos vinculados.',
            ], 422);
        }

        $depotType->delete();
        return response()->json([
            'message' => 'Tipo de depósito excluído com sucesso.',
        ]);
    }
}
