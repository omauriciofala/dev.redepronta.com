<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\StoreClusterRequest;
use App\Models\DepotCluster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClusterController extends Controller
{
    protected function getAccountId(): int
    {
        return session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
    }

    public function index(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $query = DepotCluster::where('account_id', $accountId)->withCount('depots');

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

        $clusters = $query->orderBy('name')->get();

        return response()->json([
            'data' => $clusters,
        ]);
    }

    public function store(StoreClusterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['account_id'] = $this->getAccountId();

        $cluster = DepotCluster::create($data);

        return response()->json([
            'message' => 'Cluster regional criado com sucesso.',
            'data' => $cluster,
        ], 201);
    }

    public function show(DepotCluster $cluster): JsonResponse
    {
        $cluster->load(['depots.responsiblePerson']);
        return response()->json(['data' => $cluster]);
    }

    public function update(StoreClusterRequest $request, DepotCluster $cluster): JsonResponse
    {
        $cluster->update($request->validated());
        return response()->json([
            'message' => 'Cluster regional atualizado com sucesso.',
            'data' => $cluster,
        ]);
    }

    public function destroy(DepotCluster $cluster): JsonResponse
    {
        // Se houver depósitos vinculados, a foreign key RESTRICT no banco ou a validação impede a exclusão
        if ($cluster->depots()->exists()) {
            return response()->json([
                'message' => 'Não é possível excluir este cluster pois existem depósitos vinculados a ele. Inative o cluster ou mova os depósitos.',
            ], 422);
        }

        $cluster->delete();
        return response()->json([
            'message' => 'Cluster regional excluído com sucesso.',
        ]);
    }
}
