<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReconfController extends Controller
{
    /**
     * Exibe o relatório público de movimentação de materiais (Reconf).
     */
    public function show(string $protocol, Request $request): View
    {
        $movements = StockMovement::with([
            'account',
            'material.unit',
            'sourceDepot.cluster.owner',
            'sourceDepot.owner',
            'sourceDepot.responsiblePerson',
            'destinationDepot.cluster.owner',
            'destinationDepot.owner',
            'destinationDepot.responsiblePerson',
            'user',
            'receiver',
            'driver',
            'serials',
            'attachments',
        ])
        ->where('protocol', $protocol)
        ->get();

        // Fallback por ID se não encontrar por protocolo e for numérico
        if ($movements->isEmpty() && is_numeric($protocol)) {
            $movements = StockMovement::with([
                'account',
                'material.unit',
                'sourceDepot.cluster',
                'sourceDepot.responsiblePerson',
                'sourceDepot.effectiveOwner',
                'destinationDepot.cluster',
                'destinationDepot.responsiblePerson',
                'destinationDepot.effectiveOwner',
                'user',
                'receiver',
                'driver',
                'serials',
                'attachments',
            ])
            ->where('id', $protocol)
            ->get();
        }

        if ($movements->isEmpty()) {
            abort(404, 'Protocolo de movimentação não encontrado.');
        }

        $first = $movements->first();
        $account = $first->account;

        // Tipo formatado em português
        $typeLabel = match (strtoupper($first->movement_type)) {
            'ENTRY' => 'ENTRADA',
            'EXIT' => 'SAÍDA',
            'TRANSFER' => 'TRANSFERÊNCIA',
            'RETURN' => 'DEVOLUÇÃO',
            'ADJUSTMENT' => 'AJUSTE',
            default => strtoupper($first->movement_type),
        };

        $reportTitle = match (strtoupper($first->movement_type)) {
            'ENTRY' => 'Relatório de Recebimento de Materiais',
            'TRANSFER' => 'Relatório de Transferência de Materiais',
            'EXIT' => 'Relatório de Saída / Expedição de Materiais',
            'RETURN' => 'Relatório de Devolução de Materiais',
            default => 'Relatório de Movimentação de Materiais',
        };

        // Posição Regional: prioriza cluster do destino (ou da origem)
        $regionalPosition = $first->destinationDepot?->cluster?->name
            ?? $first->sourceDepot?->cluster?->name
            ?? ($first->destinationDepot ? $first->destinationDepot->name : ($first->sourceDepot ? $first->sourceDepot->name : 'NÃO ESPECIFICADA'));

        // Solicitante / Proprietário do Contrato
        $owner = $first->destinationDepot?->effectiveOwner?->name
            ?? $first->sourceDepot?->effectiveOwner?->name
            ?? $account?->trade_name
            ?? $account?->name
            ?? 'REDE PRONTA';

        // Responsável pelo depósito ou usuário do sistema
        $responsible = $first->destinationDepot?->responsiblePerson?->name
            ?? $first->sourceDepot?->responsiblePerson?->name
            ?? $first->user?->name
            ?? '-';

        // Motorista e Recebedor
        $driverName = $first->driver?->name ?? '-';
        $receiverName = $first->receiver?->name ?? '-';

        // Documento
        $documentRef = $first->document_number ?? $first->document_ref ?? '-';
        $documentType = 'NF / DOC';
        if (str_contains(strtoupper($documentRef), 'OS-') || str_contains(strtoupper($documentRef), 'REQ-')) {
            $documentType = 'REQUISIÇÃO';
        }

        // Itens
        $items = $movements->map(function ($mv) {
            return [
                'id' => $mv->id,
                'code' => $mv->material?->code ?? '-',
                'name' => $mv->material?->name ?? 'Material não identificado',
                'quantity' => (float)$mv->quantity,
                'unit' => $mv->material?->unit?->code ?? 'UND',
                'has_serial' => (bool)$mv->material?->has_serial,
                'serials' => $mv->serials->map(fn($s) => [
                    'serial_number' => $s->serial_number,
                    'mac_address' => $s->mac_address,
                ])->values()->all(),
            ];
        });

        // Todos os seriais agrupados por material para o modal
        $serialsByMaterial = $items->filter(fn($item) => !empty($item['serials']));

        return view('public.reconf', [
            'protocol' => $first->protocol ?? $protocol,
            'movements' => $movements,
            'first' => $first,
            'account' => $account,
            'reportTitle' => $reportTitle,
            'typeLabel' => $typeLabel,
            'documentType' => $documentType,
            'documentNumber' => $documentRef,
            'documentDate' => $first->document_date ? $first->document_date->format('d/m/Y') : '-',
            'movementDate' => $first->movement_date ? $first->movement_date->format('d/m/Y') : ($first->created_at ? $first->created_at->format('d/m/Y') : '-'),
            'regionalPosition' => $regionalPosition,
            'driverName' => $driverName,
            'receiverName' => $receiverName,
            'responsibleName' => $responsible,
            'ownerName' => $owner,
            'items' => $items,
            'serialsByMaterial' => $serialsByMaterial,
            'totalItemsCount' => $items->count(),
            'autoPrint' => $request->has('print'),
        ]);
    }
}
