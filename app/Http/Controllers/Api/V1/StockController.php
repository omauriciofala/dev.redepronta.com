<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Stock\StoreStockMovementRequest;
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
     * Executa movimentação centralizada de materiais (Entrada, Saída, Devolução, Transferência).
     */
     public function movement(StoreStockMovementRequest $request): JsonResponse
     {
         $accountId = $this->getAccountId();
         $userId = auth()->id();
 
         $movement = $this->stockService->processMovement($request->validated(), $accountId, $userId);
 
         $labels = [
             'ENTRY' => 'Entrada',
             'EXIT' => 'Saída',
             'RETURN' => 'Devolução',
             'TRANSFER' => 'Transferência',
         ];
         $type = strtoupper($request->input('movement_type', 'TRANSFER'));
         $label = $labels[$type] ?? 'Movimentação';
 
         return response()->json([
             'message' => "{$label} de estoque realizada com sucesso.",
             'data' => $movement,
         ], 201);
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
     * Upload de Anexo para Movimentação de Estoque (Documentos e Fotos).
     */
    public function uploadAttachment(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:20480'], // max 20MB
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $accountId = $this->getAccountId();
        $attachment = $this->stockService->uploadAttachment(
            $request->file('file'),
            $accountId,
            $request->input('description')
        );

        return response()->json([
            'message' => 'Anexo enviado com sucesso.',
            'data' => $attachment,
        ], 201);
    }

    /**
     * Histórico de Movimentações para Auditoria e Visão de Operações.
     */
    public function movements(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $query = StockMovement::where('account_id', $accountId)
            ->with([
                'material.unit',
                'sourceDepot.cluster',
                'destinationDepot.cluster',
                'user',
                'serials',
                'receiver',
                'driver',
                'attachments',
            ]);

        // Busca global por texto (protocolo, documento, SKU, nome do material, notas ou serial)
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('protocol', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('document_ref', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('material', function ($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('serials', function ($sq) use ($search) {
                      $sq->where('serial_number', 'like', "%{$search}%")
                         ->orWhere('mac_address', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('material_id')) {
            $query->where('material_id', $request->query('material_id'));
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->query('movement_type'));
        }

        // Filtro unificado de depósito (origem ou destino)
        if ($request->filled('depot_id')) {
            $depotId = $request->query('depot_id');
            $query->where(function ($dq) use ($depotId) {
                $dq->where('source_depot_id', $depotId)
                   ->orWhere('destination_depot_id', $depotId);
            });
        }

        if ($request->filled('source_depot_id')) {
            $query->where('source_depot_id', $request->query('source_depot_id'));
        }

        if ($request->filled('destination_depot_id')) {
            $query->where('destination_depot_id', $request->query('destination_depot_id'));
        }

        // Filtro por período de datas
        if ($request->filled('start_date')) {
            $startDate = $request->query('start_date');
            $query->where(function ($dq) use ($startDate) {
                $dq->whereDate('movement_date', '>=', $startDate)
                   ->orWhere(function ($sub) use ($startDate) {
                       $sub->whereNull('movement_date')->whereDate('created_at', '>=', $startDate);
                   });
            });
        }

        if ($request->filled('end_date')) {
            $endDate = $request->query('end_date');
            $query->where(function ($dq) use ($endDate) {
                $dq->whereDate('movement_date', '<=', $endDate)
                   ->orWhere(function ($sub) use ($endDate) {
                       $sub->whereNull('movement_date')->whereDate('created_at', '<=', $endDate);
                   });
            });
        }

        $perPage = min($request->integer('per_page', 20), 100);
        $movements = $query->orderByDesc('id')->paginate($perPage);

        return response()->json($movements);
    }

    /**
     * Listagem agregada de Movimentações agrupadas por Documento / Protocolo.
     */
    public function documents(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $query = StockMovement::where('account_id', $accountId);

        // Busca global por texto (protocolo, documento, número, material, serial ou notas)
        if ($request->filled('search')) {
            $search = trim($request->query('search'));
            $query->where(function ($q) use ($search) {
                $q->where('protocol', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('document_ref', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('material', function ($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('serials', function ($sq) use ($search) {
                      $sq->where('serial_number', 'like', "%{$search}%")
                         ->orWhere('mac_address', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('material_id')) {
            $query->where('material_id', $request->query('material_id'));
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->query('movement_type'));
        }

        if ($request->filled('depot_id')) {
            $depotId = $request->query('depot_id');
            $query->where(function ($dq) use ($depotId) {
                $dq->where('source_depot_id', $depotId)
                   ->orWhere('destination_depot_id', $depotId);
            });
        }

        if ($request->filled('source_depot_id')) {
            $query->where('source_depot_id', $request->query('source_depot_id'));
        }

        if ($request->filled('destination_depot_id')) {
            $query->where('destination_depot_id', $request->query('destination_depot_id'));
        }

        if ($request->filled('start_date')) {
            $startDate = $request->query('start_date');
            $query->where(function ($dq) use ($startDate) {
                $dq->whereDate('movement_date', '>=', $startDate)
                   ->orWhere(function ($sub) use ($startDate) {
                       $sub->whereNull('movement_date')->whereDate('created_at', '>=', $startDate);
                   });
            });
        }

        if ($request->filled('end_date')) {
            $endDate = $request->query('end_date');
            $query->where(function ($dq) use ($endDate) {
                $dq->whereDate('movement_date', '<=', $endDate)
                   ->orWhere(function ($sub) use ($endDate) {
                       $sub->whereNull('movement_date')->whereDate('created_at', '<=', $endDate);
                   });
            });
        }

        // Agrupamento por documento/protocolo
        $protocolQuery = (clone $query)
            ->selectRaw("COALESCE(NULLIF(protocol, ''), CONCAT('MOV-', id)) as doc_key, MAX(id) as max_id")
            ->groupBy('doc_key')
            ->orderByDesc('max_id');

        $perPage = min($request->integer('per_page', 20), 100);
        $paginatedKeys = $protocolQuery->paginate($perPage);

        $docKeys = collect($paginatedKeys->items())->pluck('doc_key')->toArray();

        if (empty($docKeys)) {
            return response()->json([
                'data' => [],
                'current_page' => $paginatedKeys->currentPage(),
                'last_page' => $paginatedKeys->lastPage(),
                'total' => $paginatedKeys->total(),
                'per_page' => $paginatedKeys->perPage(),
                'from' => $paginatedKeys->firstItem(),
                'to' => $paginatedKeys->lastItem(),
            ]);
        }

        // Carrega as movimentações de todos os protocolos da página atual
        $movements = StockMovement::where('account_id', $accountId)
            ->where(function ($q) use ($docKeys) {
                $q->whereIn('protocol', $docKeys)
                  ->orWhereIn('id', array_filter(array_map(function ($k) {
                      return str_starts_with($k, 'MOV-') ? (int) substr($k, 4) : null;
                  }, $docKeys)));
            })
            ->with([
                'material.unit',
                'sourceDepot.cluster',
                'destinationDepot.cluster',
                'user',
                'serials',
                'receiver',
                'driver',
                'attachments',
            ])
            ->orderByDesc('id')
            ->get();

        $grouped = $movements->groupBy(function ($m) {
            return !empty($m->protocol) ? $m->protocol : 'MOV-' . $m->id;
        });

        $documents = [];
        foreach ($docKeys as $key) {
            if (!isset($grouped[$key])) {
                continue;
            }
            $items = $grouped[$key];
            $first = $items->first();

            $documents[] = [
                'key' => $key,
                'protocol' => $first->protocol,
                'document_number' => $first->document_number,
                'document_ref' => $first->document_ref,
                'movement_type' => $first->movement_type,
                'movement_date' => $first->movement_date ?? $first->created_at,
                'document_date' => $first->document_date,
                'created_at' => $first->created_at,
                'source_depot' => $first->sourceDepot,
                'destination_depot' => $first->destinationDepot,
                'receiver' => $first->receiver,
                'driver' => $first->driver,
                'user' => $first->user,
                'notes' => $first->notes,
                'items_count' => $items->count(),
                'total_quantity' => (float) $items->sum('quantity'),
                'serials_count' => $items->flatMap->serials->unique('id')->count(),
                'attachments_count' => $items->flatMap->attachments->unique('id')->count(),
                'items' => $items->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'material' => $m->material,
                        'quantity' => (float) $m->quantity,
                        'serials' => $m->serials,
                        'attachments' => $m->attachments,
                    ];
                })->values(),
            ];
        }

        return response()->json([
            'data' => $documents,
            'current_page' => $paginatedKeys->currentPage(),
            'last_page' => $paginatedKeys->lastPage(),
            'total' => $paginatedKeys->total(),
            'per_page' => $paginatedKeys->perPage(),
            'from' => $paginatedKeys->firstItem(),
            'to' => $paginatedKeys->lastItem(),
        ]);
    }

    /**
     * Detalhes de um Documento de Movimentação por Protocolo.
     */
    public function documentDetail(string $protocol): JsonResponse
    {
        $accountId = $this->getAccountId();
        $items = StockMovement::where('account_id', $accountId)
            ->where(function ($q) use ($protocol) {
                $q->where('protocol', $protocol)
                  ->orWhere('document_number', $protocol);
            })
            ->with([
                'material.unit',
                'sourceDepot.cluster',
                'destinationDepot.cluster',
                'user',
                'serials',
                'receiver',
                'driver',
                'attachments',
            ])
            ->orderBy('id')
            ->get();

        if ($items->isEmpty()) {
            return response()->json(['message' => 'Documento de movimentação não encontrado.'], 404);
        }

        $first = $items->first();

        return response()->json([
            'protocol' => $first->protocol,
            'document_number' => $first->document_number,
            'document_ref' => $first->document_ref,
            'movement_type' => $first->movement_type,
            'movement_date' => $first->movement_date ?? $first->created_at,
            'document_date' => $first->document_date,
            'created_at' => $first->created_at,
            'source_depot' => $first->sourceDepot,
            'destination_depot' => $first->destinationDepot,
            'receiver' => $first->receiver,
            'driver' => $first->driver,
            'user' => $first->user,
            'notes' => $first->notes,
            'items_count' => $items->count(),
            'total_quantity' => (float) $items->sum('quantity'),
            'serials_count' => $items->flatMap->serials->unique('id')->count(),
            'attachments_count' => $items->flatMap->attachments->unique('id')->count(),
            'items' => $items->values(),
        ]);
    }
}

