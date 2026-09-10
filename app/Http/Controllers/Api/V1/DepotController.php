<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\StoreDepotRequest;
use App\Models\Depot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepotController extends Controller
{
    protected function getAccountId(): int
    {
        return session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
    }

    public function index(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $query = Depot::where('account_id', $accountId)->with(['cluster', 'responsiblePerson', 'city.state']);

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            });
        }

        if ($request->filled('cluster_id')) {
            $query->where('cluster_id', $request->query('cluster_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->boolean('active_only', false)) {
            $query->where('is_active', true);
        }

        $depots = $query->orderBy('name')->get();

        return response()->json([
            'data' => $depots,
        ]);
    }

    public function store(StoreDepotRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['account_id'] = $this->getAccountId();

        $depot = Depot::create($data);
        $depot->load(['cluster', 'responsiblePerson', 'city.state']);

        return response()->json([
            'message' => 'Depósito criado com sucesso.',
            'data' => $depot,
        ], 201);
    }

    public function show(Depot $depot): JsonResponse
    {
        $depot->load(['cluster', 'responsiblePerson', 'city.state', 'balances.material.unit']);
        return response()->json(['data' => $depot]);
    }

    public function update(StoreDepotRequest $request, Depot $depot): JsonResponse
    {
        $depot->update($request->validated());
        $depot->load(['cluster', 'responsiblePerson', 'city.state']);

        return response()->json([
            'message' => 'Depósito atualizado com sucesso.',
            'data' => $depot,
        ]);
    }

    public function destroy(Depot $depot): JsonResponse
    {
        // Se houver saldos positivos ou seriais vinculados, a integridade RESTRICT ou a regra bloqueia
        $hasStock = $depot->balances()->where('quantity', '>', 0)->exists();
        $hasSerials = $depot->serials()->where('status', 'IN_STOCK')->exists();

        if ($hasStock || $hasSerials) {
            return response()->json([
                'message' => 'Não é possível excluir este depósito pois ainda existem itens com saldo em estoque ou números de série alocados. Transfira os materiais antes de excluir ou inative o depósito.',
            ], 422);
        }

        $depot->delete();
        return response()->json([
            'message' => 'Depósito excluído com sucesso.',
        ]);
    }
}
