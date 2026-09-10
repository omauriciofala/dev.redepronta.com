<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\TransferStockRequest;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockSerial;
use App\Services\Stock\ClusterStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct(
        protected ClusterStockService $stockService
    ) {}

    protected function getAccountId(): int
    {
        return session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
    }

    /**
     * Retorna o saldo consolidado de materiais por Posição Regional.
     */
    public function regionalPosition(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $clusterId = $request->integer('cluster_id') ?: null;

        if ($clusterId) {
            $data = $this->stockService->calculateClusterStock($clusterId, $accountId);
            return response()->json(['data' => $data]);
        }

        $data = $this->stockService->getRegionalPosition($accountId);
        return response()->json(['data' => $data]);
    }

    /**
     * Consulta de Saldos Físicos por Depósito ou Material.
     */
    public function balances(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $query = StockBalance::where('account_id', $accountId)
            ->with(['depot.cluster', 'depot.responsiblePerson', 'material.unit']);

        if ($request->filled('depot_id')) {
            $query->where('depot_id', $request->query('depot_id'));
        }

        if ($request->filled('material_id')) {
            $query->where('material_id', $request->query('material_id'));
        }

        if ($request->boolean('positive_only', false)) {
            $query->where('quantity', '>', 0);
        }

        $balances = $query->get();

        return response()->json([
            'data' => $balances,
        ]);
    }

    /**
     * Consulta e Rastreabilidade Individual de Materiais Serializados.
     */
    public function serials(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $query = StockSerial::where('account_id', $accountId)
            ->with(['material.unit', 'currentDepot.cluster', 'currentDepot.responsiblePerson']);

        if ($request->filled('search')) {
            $s = $request->query('search');
            $query->where(function ($q) use ($s) {
                $q->where('serial_number', 'like', "%{$s}%")
                  ->orWhere('mac_address', 'like', "%{$s}%");
            });
        }

        if ($request->filled('material_id')) {
            $query->where('material_id', $request->query('material_id'));
        }

        if ($request->filled('depot_id')) {
            $query->where('current_depot_id', $request->query('depot_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $perPage = $request->integer('per_page', 50);
        $serials = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($serials);
    }

    /**
     * Executa a Transferência de Materiais entre Depósitos com Atomicidade Estrita.
     */
    public function transfer(TransferStockRequest $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $userId = auth()->id();

        $movement = $this->stockService->transfer($request->validated(), $accountId, $userId);

        return response()->json([
            'message' => 'Transferência de estoque realizada com sucesso.',
            'data' => $movement,
        ], 201);
    }

    /**
     * Entrada de Estoque / Compras.
     */
    public function entry(Request $request): JsonResponse
    {
        $request->validate([
            'destination_depot_id' => ['required', 'integer', 'exists:depots,id'],
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'document_ref' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'serials' => ['nullable', 'array'],
        ]);

        $accountId = $this->getAccountId();
        $userId = auth()->id();

        $movement = $this->stockService->entry($request->all(), $accountId, $userId);

        return response()->json([
            'message' => 'Entrada de estoque registrada com sucesso.',
            'data' => $movement,
        ], 201);
    }

    /**
     * Histórico de Movimentações para Auditoria.
     */
    public function movements(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $query = StockMovement::where('account_id', $accountId)
            ->with(['material.unit', 'sourceDepot', 'destinationDepot', 'user', 'serials']);

        if ($request->filled('material_id')) {
            $query->where('material_id', $request->query('material_id'));
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->query('movement_type'));
        }

        $movements = $query->orderByDesc('created_at')->paginate($request->integer('per_page', 20));

        return response()->json($movements);
    }
}
