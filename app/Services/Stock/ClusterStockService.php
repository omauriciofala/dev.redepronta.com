<?php

namespace App\Services\Stock;

use App\Models\Depot;
use App\Models\DepotCluster;
use App\Models\Material;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockSerial;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClusterStockService
{
    /**
     * Calcula o Saldo Consolidado em tempo real para uma Posição Regional.
     * Consolida todos os depósitos da respectiva posição regional.
     */
    public function calculateClusterStock(int $clusterId, int $accountId): array
    {
        $cluster = DepotCluster::where('account_id', $accountId)
            ->with(['depots.responsiblePerson'])
            ->findOrFail($clusterId);

        $depots = $cluster->depots->where('is_active', true);
        $depotIds = $depots->pluck('id')->toArray();

        if (empty($depotIds)) {
            return [
                'cluster' => $cluster,
                'summary' => [
                    'total_materials' => 0,
                    'total_items_count' => 0,
                    'total_serials_in_stock' => 0,
                    'depots_count' => 0,
                ],
                'materials' => [],
            ];
        }

        // Saldos físicos em todos os depósitos deste cluster / posição regional
        $balances = StockBalance::where('account_id', $accountId)
            ->whereIn('depot_id', $depotIds)
            ->with(['material.unit', 'depot.responsiblePerson'])
            ->get();

        // Contagem de seriais disponíveis na posição regional por material
        $serialsCount = StockSerial::where('account_id', $accountId)
            ->whereIn('current_depot_id', $depotIds)
            ->where('status', 'IN_STOCK')
            ->select('material_id', DB::raw('count(*) as aggregate'))
            ->groupBy('material_id')
            ->pluck('aggregate', 'material_id')
            ->toArray();

        // Agrupa saldos por material
        $grouped = $balances->groupBy('material_id');
        $materialsList = [];
        $totalItemsCount = 0;

        foreach ($grouped as $matId => $matBalances) {
            $material = $matBalances->first()->material;
            if (!$material) continue;

            $regionalQty = 0;
            $reservedQty = 0;
            $breakdown = [];

            foreach ($matBalances as $bal) {
                $depot = $bal->depot;
                $qty = (float)$bal->quantity;
                $res = (float)$bal->reserved_quantity;
                $reservedQty += $res;
                $regionalQty += $qty;

                $breakdown[] = [
                    'depot_id' => $depot?->id,
                    'depot_name' => $depot?->name,
                    'depot_code' => $depot?->code,
                    'depot_type' => $depot?->type,
                    'responsible_name' => $depot?->responsiblePerson?->name,
                    'quantity' => $qty,
                    'reserved_quantity' => $res,
                    'available_quantity' => max(0, $qty - $res),
                ];
            }

            $totalItemsCount += $regionalQty;

            $materialsList[] = [
                'material_id' => $material->id,
                'code' => $material->code,
                'name' => $material->name,
                'category' => $material->category,
                'unit' => $material->unit?->code ?? 'UND',
                'has_serial' => (bool)$material->has_serial,
                'unit_cost' => (float)$material->unit_cost,
                'min_stock' => (float)$material->min_stock,
                'total_quantity' => $regionalQty,
                'total_virtual_quantity' => $regionalQty,
                'total_reserved_quantity' => $reservedQty,
                'available_quantity' => max(0, $regionalQty - $reservedQty),
                'available_virtual_quantity' => max(0, $regionalQty - $reservedQty),
                'serials_in_stock' => $serialsCount[$material->id] ?? 0,
                'is_low_stock' => ($material->min_stock > 0 && $regionalQty <= $material->min_stock),
                'breakdown' => $breakdown,
            ];
        }

        // Ordena por nome do material
        usort($materialsList, fn($a, $b) => strcmp($a['name'], $b['name']));

        $totalSerialsInStock = array_sum($serialsCount);

        return [
            'cluster' => [
                'id' => $cluster->id,
                'name' => $cluster->name,
                'code' => $cluster->code,
                'color' => $cluster->color,
                'description' => $cluster->description,
                'depots_count' => $depots->count(),
            ],
            'summary' => [
                'total_materials' => count($materialsList),
                'total_items_count' => $totalItemsCount,
                'total_serials_in_stock' => $totalSerialsInStock,
                'depots_count' => $depots->count(),
            ],
            'materials' => $materialsList,
        ];
    }

    /**
     * Realiza a transferência de materiais entre depósitos com atomicidade garantida (DB::transaction).
     * Suporta materiais convencionais e serializados.
     */
    public function transfer(array $data, int $accountId, ?int $userId = null): StockMovement
    {
        $sourceDepotId = (int)$data['source_depot_id'];
        $destDepotId = (int)$data['destination_depot_id'];
        $materialId = (int)$data['material_id'];
        $quantity = (float)$data['quantity'];
        $serialIds = $data['serial_ids'] ?? [];

        if ($sourceDepotId === $destDepotId) {
            throw ValidationException::withMessages([
                'destination_depot_id' => ['O depósito de destino deve ser diferente do depósito de origem.'],
            ]);
        }

        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => ['A quantidade a transferir deve ser maior que zero.'],
            ]);
        }

        return DB::transaction(function () use (
            $accountId,
            $sourceDepotId,
            $destDepotId,
            $materialId,
            $quantity,
            $serialIds,
            $userId,
            $data
        ) {
            // 1. Valida depósitos pertencentes ao tenant
            $sourceDepot = Depot::where('account_id', $accountId)->findOrFail($sourceDepotId);
            $destDepot = Depot::where('account_id', $accountId)->findOrFail($destDepotId);
            $material = Material::where('account_id', $accountId)->findOrFail($materialId);

            // 2. Trava e valida saldo na origem
            $sourceBalance = StockBalance::where('account_id', $accountId)
                ->where('depot_id', $sourceDepotId)
                ->where('material_id', $materialId)
                ->lockForUpdate()
                ->first();

            $availableQty = $sourceBalance ? (float)$sourceBalance->quantity - (float)$sourceBalance->reserved_quantity : 0;

            if ($availableQty < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => [
                        "Saldo insuficiente no depósito '{$sourceDepot->name}'. Disponível: {$availableQty}, Solicitado: {$quantity}."
                    ],
                ]);
            }

            // 3. Validação estrita para materiais serializados
            $validSerials = [];
            if ($material->has_serial) {
                if (count($serialIds) !== (int)$quantity) {
                    throw ValidationException::withMessages([
                        'serial_ids' => [
                            "Para itens serializados, selecione exatamente {$quantity} número(s) de série (selecionados: " . count($serialIds) . ")."
                        ],
                    ]);
                }

                $validSerials = StockSerial::where('account_id', $accountId)
                    ->where('material_id', $materialId)
                    ->where('current_depot_id', $sourceDepotId)
                    ->where('status', 'IN_STOCK')
                    ->whereIn('id', $serialIds)
                    ->lockForUpdate()
                    ->get();

                if ($validSerials->count() !== count($serialIds)) {
                    throw ValidationException::withMessages([
                        'serial_ids' => [
                            'Um ou mais números de série selecionados não estão disponíveis no depósito de origem ou não possuem status EM ESTOQUE.'
                        ],
                    ]);
                }
            }

            // 4. Débito no depósito de origem
            $sourceBalance->decrement('quantity', $quantity);

            // 5. Crédito no depósito de destino
            $destBalance = StockBalance::firstOrCreate(
                [
                    'account_id' => $accountId,
                    'depot_id' => $destDepotId,
                    'material_id' => $materialId,
                ],
                [
                    'quantity' => 0,
                    'reserved_quantity' => 0,
                ]
            );
            $destBalance->increment('quantity', $quantity);

            // 6. Atualização de localização física dos seriais
            if (!empty($validSerials)) {
                foreach ($validSerials as $serial) {
                    $serial->update([
                        'current_depot_id' => $destDepotId,
                        'status' => 'IN_STOCK',
                    ]);
                }
            }

            // 7. Registro da movimentação para rastreabilidade e auditoria
            $movement = StockMovement::create([
                'account_id' => $accountId,
                'material_id' => $materialId,
                'source_depot_id' => $sourceDepotId,
                'destination_depot_id' => $destDepotId,
                'user_id' => $userId,
                'movement_type' => 'TRANSFER',
                'quantity' => $quantity,
                'document_ref' => $data['document_ref'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_at' => now(),
            ]);

            if (!empty($validSerials)) {
                $movement->serials()->attach($validSerials->pluck('id'));
            }

            return $movement->load(['material.unit', 'sourceDepot', 'destinationDepot', 'serials']);
        });
    }

    /**
     * Realiza entrada direta de estoque (compra/inventário).
     */
    public function entry(array $data, int $accountId, ?int $userId = null): StockMovement
    {
        $depotId = (int)$data['destination_depot_id'];
        $materialId = (int)$data['material_id'];
        $quantity = (float)$data['quantity'];
        $serialsData = $data['serials'] ?? []; // array de strings ou objetos { serial_number, mac_address }

        return DB::transaction(function () use ($accountId, $depotId, $materialId, $quantity, $serialsData, $userId, $data) {
            $depot = Depot::where('account_id', $accountId)->findOrFail($depotId);
            $material = Material::where('account_id', $accountId)->findOrFail($materialId);

            if ($material->has_serial) {
                if (count($serialsData) !== (int)$quantity) {
                    throw ValidationException::withMessages([
                        'serials' => ["Para materiais serializados é necessário informar {$quantity} seriais."],
                    ]);
                }
            }

            // Atualiza saldo
            $balance = StockBalance::firstOrCreate(
                ['account_id' => $accountId, 'depot_id' => $depotId, 'material_id' => $materialId],
                ['quantity' => 0, 'reserved_quantity' => 0]
            );
            $balance->increment('quantity', $quantity);

            // Cria os seriais
            $createdSerials = [];
            if ($material->has_serial) {
                foreach ($serialsData as $item) {
                    $sn = is_array($item) ? ($item['serial_number'] ?? '') : (string)$item;
                    $mac = is_array($item) ? ($item['mac_address'] ?? null) : null;

                    $serial = StockSerial::create([
                        'account_id' => $accountId,
                        'material_id' => $materialId,
                        'current_depot_id' => $depotId,
                        'serial_number' => trim($sn),
                        'mac_address' => $mac ? trim($mac) : null,
                        'status' => 'IN_STOCK',
                    ]);
                    $createdSerials[] = $serial->id;
                }
            }

            $movement = StockMovement::create([
                'account_id' => $accountId,
                'material_id' => $materialId,
                'source_depot_id' => null,
                'destination_depot_id' => $depotId,
                'user_id' => $userId,
                'movement_type' => 'ENTRY',
                'quantity' => $quantity,
                'document_ref' => $data['document_ref'] ?? null,
                'notes' => $data['notes'] ?? 'Entrada direta de estoque',
                'created_at' => now(),
            ]);

            if (!empty($createdSerials)) {
                $movement->serials()->attach($createdSerials);
            }

            return $movement->load(['material.unit', 'destinationDepot', 'serials']);
        });
    }

    /**
     * Retorna a visão consolidada de todas as posições regionais da conta.
     */
    public function getRegionalPosition(int $accountId, ?int $clusterId = null): array
    {
        $clustersQuery = DepotCluster::where('account_id', $accountId)->where('is_active', true);

        if ($clusterId) {
            $clustersQuery->where('id', $clusterId);
        }

        $clusters = $clustersQuery->get();
        $result = [];

        foreach ($clusters as $cl) {
            $result[] = $this->calculateClusterStock($cl->id, $accountId);
        }

        return $result;
    }
}
