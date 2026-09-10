<?php

namespace App\Services\Stock;

use App\Models\Depot;
use App\Models\DepotCluster;
use App\Models\Material;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\StockMovementAttachment;
use App\Models\StockSerial;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            $movement = $this->createMovementRecord(
                $accountId,
                $materialId,
                $sourceDepotId,
                $destDepotId,
                $userId,
                'TRANSFER',
                $quantity,
                $data
            );

            if (!empty($validSerials)) {
                $movement->serials()->attach($validSerials->pluck('id'));
            }

            return $movement->load(['material.unit', 'sourceDepot', 'destinationDepot', 'receiver', 'driver', 'attachments', 'serials']);
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

            // Cria ou atualiza os seriais para status IN_STOCK
            $createdSerials = [];
            if ($material->has_serial) {
                foreach ($serialsData as $item) {
                    $sn = is_array($item) ? ($item['serial_number'] ?? '') : (string)$item;
                    $mac = is_array($item) ? ($item['mac_address'] ?? null) : null;
                    $sn = trim($sn);
                    if (empty($sn)) continue;

                    $serial = StockSerial::updateOrCreate(
                        [
                            'account_id' => $accountId,
                            'serial_number' => $sn,
                        ],
                        [
                            'material_id' => $materialId,
                            'current_depot_id' => $depotId,
                            'mac_address' => $mac ? trim($mac) : null,
                            'status' => 'IN_STOCK',
                        ]
                    );
                    $createdSerials[] = $serial->id;
                }
            }

            $movement = $this->createMovementRecord(
                $accountId,
                $materialId,
                null,
                $depotId,
                $userId,
                'ENTRY',
                $quantity,
                $data
            );

            if (!empty($createdSerials)) {
                $movement->serials()->attach($createdSerials);
            }

            return $movement->load(['material.unit', 'destinationDepot', 'receiver', 'driver', 'attachments', 'serials']);
        });
    }

    /**
     * Realiza saída direta de estoque (baixa operacional, técnicos ou aplicação externa).
     */
    public function exit(array $data, int $accountId, ?int $userId = null): StockMovement
    {
        $sourceDepotId = (int)$data['source_depot_id'];
        $materialId = (int)$data['material_id'];
        $quantity = (float)$data['quantity'];
        $serialIds = $data['serial_ids'] ?? [];

        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => ['A quantidade a retirar deve ser maior que zero.'],
            ]);
        }

        return DB::transaction(function () use ($accountId, $sourceDepotId, $materialId, $quantity, $serialIds, $userId, $data) {
            $sourceDepot = Depot::where('account_id', $accountId)->findOrFail($sourceDepotId);
            $material = Material::where('account_id', $accountId)->findOrFail($materialId);

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
                            'Um ou mais números de série selecionados não estão disponíveis no depósito de origem com status EM ESTOQUE.'
                        ],
                    ]);
                }
            }

            $sourceBalance->decrement('quantity', $quantity);

            if (!empty($validSerials)) {
                foreach ($validSerials as $serial) {
                    $serial->update([
                        'status' => 'INSTALLED_CUSTOMER',
                    ]);
                }
            }

            $movement = $this->createMovementRecord(
                $accountId,
                $materialId,
                $sourceDepotId,
                null,
                $userId,
                'EXIT',
                $quantity,
                $data
            );

            if (!empty($validSerials)) {
                $movement->serials()->attach($validSerials->pluck('id'));
            }

            return $movement->load(['material.unit', 'sourceDepot', 'receiver', 'driver', 'attachments', 'serials']);
        });
    }

    /**
     * Realiza devolução de estoque ao proprietário (saída do depósito interno com baixa documental).
     */
    public function returnStock(array $data, int $accountId, ?int $userId = null): StockMovement
    {
        $sourceDepotId = (int)$data['source_depot_id'];
        $destDepotId = !empty($data['destination_depot_id']) ? (int)$data['destination_depot_id'] : null;
        $materialId = (int)$data['material_id'];
        $quantity = (float)$data['quantity'];
        $serialIds = $data['serial_ids'] ?? [];
        $serialsData = $data['serials'] ?? [];

        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => ['A quantidade a devolver deve ser maior que zero.'],
            ]);
        }

        return DB::transaction(function () use ($accountId, $destDepotId, $sourceDepotId, $materialId, $quantity, $serialIds, $serialsData, $userId, $data) {
            $sourceDepot = Depot::where('account_id', $accountId)->findOrFail($sourceDepotId);
            $material = Material::where('account_id', $accountId)->findOrFail($materialId);

            $sourceBalance = StockBalance::where('account_id', $accountId)
                ->where('depot_id', $sourceDepotId)
                ->where('material_id', $materialId)
                ->lockForUpdate()
                ->first();

            $availableQty = $sourceBalance ? (float)$sourceBalance->quantity - (float)$sourceBalance->reserved_quantity : 0;

            if ($availableQty < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => [
                        "Saldo insuficiente no depósito '{$sourceDepot->name}'. Disponível: {$availableQty}, Solicitado para devolução: {$quantity}."
                    ],
                ]);
            }

            $affectedSerialIds = [];

            if ($material->has_serial) {
                if (!empty($serialIds)) {
                    if (count($serialIds) !== (int)$quantity) {
                        throw ValidationException::withMessages([
                            'serial_ids' => ["Para devolução de itens serializados, selecione exatamente {$quantity} seriais."],
                        ]);
                    }

                    $existingSerials = StockSerial::where('account_id', $accountId)
                        ->where('material_id', $materialId)
                        ->where('current_depot_id', $sourceDepotId)
                        ->where('status', 'IN_STOCK')
                        ->whereIn('id', $serialIds)
                        ->lockForUpdate()
                        ->get();

                    if ($existingSerials->count() !== count($serialIds)) {
                        throw ValidationException::withMessages([
                            'serial_ids' => [
                                'Um ou mais números de série selecionados não estão disponíveis no depósito interno de origem com status EM ESTOQUE.'
                            ],
                        ]);
                    }

                    foreach ($existingSerials as $serial) {
                        $serial->update([
                            'status' => 'RETURNED',
                        ]);
                        $affectedSerialIds[] = $serial->id;
                    }
                } elseif (!empty($serialsData)) {
                    if (count($serialsData) !== (int)$quantity) {
                        throw ValidationException::withMessages([
                            'serials' => ["Para devolução de itens serializados, informe {$quantity} seriais."],
                        ]);
                    }

                    $snList = [];
                    foreach ($serialsData as $item) {
                        $sn = is_array($item) ? ($item['serial_number'] ?? '') : (string)$item;
                        $sn = trim($sn);
                        if (!empty($sn)) {
                            $snList[] = $sn;
                        }
                    }

                    $existingSerials = StockSerial::where('account_id', $accountId)
                        ->where('material_id', $materialId)
                        ->where('current_depot_id', $sourceDepotId)
                        ->where('status', 'IN_STOCK')
                        ->whereIn('serial_number', $snList)
                        ->lockForUpdate()
                        ->get();

                    if ($existingSerials->count() !== count($snList)) {
                        throw ValidationException::withMessages([
                            'serials' => [
                                'Um ou mais números de série informados não foram encontrados no depósito interno de origem com status EM ESTOQUE.'
                            ],
                        ]);
                    }

                    foreach ($existingSerials as $serial) {
                        $serial->update([
                            'status' => 'RETURNED',
                        ]);
                        $affectedSerialIds[] = $serial->id;
                    }
                } else {
                    throw ValidationException::withMessages([
                        'serial_ids' => ['Informe ou selecione os números de série para a devolução.'],
                    ]);
                }
            }

            // Débito no depósito interno de origem
            $sourceBalance->decrement('quantity', $quantity);

            $movement = $this->createMovementRecord(
                $accountId,
                $materialId,
                $sourceDepotId,
                null,
                $userId,
                'RETURN',
                $quantity,
                $data
            );

            if (!empty($affectedSerialIds)) {
                $movement->serials()->attach($affectedSerialIds);
            }

            return $movement->load(['material.unit', 'sourceDepot', 'receiver', 'driver', 'attachments', 'serials']);
        });
    }

    /**
     * Processador central de movimentações (Entrada, Saída, Devolução, Transferência).
     * Suporta movimentação atômica de um único item ou de múltiplos itens em lote (grid).
     *
     * @return StockMovement|StockMovement[]
     */
    public function processMovement(array $data, int $accountId, ?int $userId = null): StockMovement|array
    {
        $type = strtoupper($data['movement_type'] ?? 'TRANSFER');

        if (!empty($data['items']) && is_array($data['items'])) {
            return DB::transaction(function () use ($data, $accountId, $userId, $type) {
                $createdMovements = [];
                foreach ($data['items'] as $item) {
                    $itemPayload = array_merge($data, $item);
                    unset($itemPayload['items']);
                    $itemPayload['movement_type'] = $type;

                    $movement = match ($type) {
                        'TRANSFER' => $this->transfer($itemPayload, $accountId, $userId),
                        'ENTRY' => $this->entry($itemPayload, $accountId, $userId),
                        'EXIT' => $this->exit($itemPayload, $accountId, $userId),
                        'RETURN' => $this->returnStock($itemPayload, $accountId, $userId),
                        default => throw ValidationException::withMessages([
                            'movement_type' => ['Tipo de movimentação inválido. Permite apenas Entrada, Saída, Devolução ou Transferência.'],
                        ]),
                    };
                    $createdMovements[] = $movement;
                }
                return $createdMovements;
            });
        }

        return match ($type) {
            'TRANSFER' => $this->transfer($data, $accountId, $userId),
            'ENTRY' => $this->entry($data, $accountId, $userId),
            'EXIT' => $this->exit($data, $accountId, $userId),
            'RETURN' => $this->returnStock($data, $accountId, $userId),
            default => throw ValidationException::withMessages([
                'movement_type' => ['Tipo de movimentação inválido. Permite apenas Entrada, Saída, Devolução ou Transferência.'],
            ]),
        };
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

    /**
     * Cria o registro de movimentação de estoque com os metadados estendidos.
     */
    protected function createMovementRecord(
        int $accountId,
        int $materialId,
        ?int $sourceDepotId,
        ?int $destinationDepotId,
        ?int $userId,
        string $type,
        float $quantity,
        array $data
    ): StockMovement {
        $movementDate = !empty($data['movement_date'])
            ? Carbon::parse($data['movement_date'])->toDateString()
            : now()->toDateString();

        $documentDate = !empty($data['document_date'])
            ? Carbon::parse($data['document_date'])->toDateString()
            : null;

        $movement = StockMovement::create([
            'account_id' => $accountId,
            'material_id' => $materialId,
            'source_depot_id' => $sourceDepotId,
            'destination_depot_id' => $destinationDepotId,
            'user_id' => $userId,
            'movement_type' => $type,
            'quantity' => $quantity,
            'document_ref' => $data['document_ref'] ?? $data['document_number'] ?? null,
            'document_number' => $data['document_number'] ?? $data['document_ref'] ?? null,
            'document_date' => $documentDate,
            'movement_date' => $movementDate,
            'receiver_person_id' => $data['receiver_person_id'] ?? null,
            'driver_person_id' => $data['driver_person_id'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        if (!empty($data['attachments']) && is_array($data['attachments'])) {
            $this->saveAttachments($movement, $data['attachments'], $accountId);
        }

        return $movement;
    }

    /**
     * Vincula ou grava anexos na movimentação de estoque.
     */
    public function saveAttachments(StockMovement $movement, array $attachments, int $accountId): void
    {
        foreach ($attachments as $att) {
            if ($att instanceof \Illuminate\Http\UploadedFile) {
                $path = $att->store("stock_attachments/{$accountId}", 'public');
                StockMovementAttachment::create([
                    'account_id' => $accountId,
                    'stock_movement_id' => $movement->id,
                    'file_name' => $att->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $att->getClientMimeType() ?? $att->getClientOriginalExtension(),
                    'file_size' => $att->getSize(),
                    'description' => null,
                ]);
            } elseif (is_array($att)) {
                if (!empty($att['file_path'])) {
                    StockMovementAttachment::create([
                        'account_id' => $accountId,
                        'stock_movement_id' => $movement->id,
                        'file_name' => $att['file_name'] ?? basename($att['file_path']),
                        'file_path' => $att['file_path'],
                        'file_type' => $att['file_type'] ?? null,
                        'file_size' => $att['file_size'] ?? null,
                        'description' => $att['description'] ?? null,
                    ]);
                }
            }
        }
    }

    /**
     * Realiza o upload isolado de um anexo para retorno de metadados na interface.
     */
    public function uploadAttachment(\Illuminate\Http\UploadedFile $file, int $accountId, ?string $description = null): array
    {
        $path = $file->store("stock_attachments/{$accountId}", 'public');
        return [
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType() ?? $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'url' => Storage::disk('public')->url($path),
            'description' => $description,
        ];
    }
}
