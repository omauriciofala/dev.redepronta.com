<?php

namespace App\Services\Operations;

use App\Models\Dispatch;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DispatchService
{
    public function list(array $filters, int $accountId)
    {
        $query = Dispatch::with([
            'ticket.customerPerson',
            'ticket.city.state',
            'ticket.category',
            'workerPerson',
            'depot.cluster',
        ])->where('account_id', $accountId);

        if (!empty($filters['status']) && $filters['status'] !== 'ALL') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['worker_person_id']) && $filters['worker_person_id'] !== 'ALL') {
            $query->where('worker_person_id', $filters['worker_person_id']);
        }

        if (!empty($filters['depot_id']) && $filters['depot_id'] !== 'ALL') {
            $query->where('depot_id', $filters['depot_id']);
        }

        if (!empty($filters['search'])) {
            $term = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('dispatch_number', 'like', $term)
                  ->orWhere('notes', 'like', $term)
                  ->orWhereHas('ticket', function ($qt) use ($term) {
                      $qt->where('protocol', 'like', $term)
                         ->orWhere('title', 'like', $term);
                  })
                  ->orWhereHas('workerPerson', function ($qw) use ($term) {
                      $qw->where('name', 'like', $term);
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data, int $accountId): Dispatch
    {
        return DB::transaction(function () use ($data, $accountId) {
            $ticket = Ticket::where('account_id', $accountId)->lockForUpdate()->findOrFail($data['ticket_id']);

            $dispatchCount = Dispatch::where('account_id', $accountId)->count() + 1;
            $dispatchNumber = sprintf('DSP-%s-%04d', date('Y'), $dispatchCount);

            $dispatch = Dispatch::create([
                'account_id' => $accountId,
                'ticket_id' => $ticket->id,
                'worker_person_id' => $data['worker_person_id'],
                'depot_id' => $data['depot_id'],
                'dispatch_number' => $dispatchNumber,
                'status' => 'DISPATCHED',
                'scheduled_at' => $data['scheduled_at'] ?? now(),
                'dispatched_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);

            $ticket->update(['status' => 'IN_FIELD']);

            return $dispatch->fresh(['ticket.customerPerson', 'workerPerson', 'depot']);
        });
    }

    public function advanceStatus(int $id, string $newStatus, ?string $notes, int $accountId): Dispatch
    {
        return DB::transaction(function () use ($id, $newStatus, $notes, $accountId) {
            $dispatch = Dispatch::where('account_id', $accountId)->lockForUpdate()->findOrFail($id);

            $dispatch->status = $newStatus;
            if ($notes) {
                $dispatch->notes = ($dispatch->notes ? $dispatch->notes . "\n" : '') . sprintf('[%s] %s', now()->format('d/m/Y H:i'), $notes);
            }

            if ($newStatus === 'ARRIVED_SITE' && !$dispatch->arrived_at) {
                $dispatch->arrived_at = now();
            }

            if (in_array($newStatus, ['COMPLETED', 'APPROVED']) && !$dispatch->completed_at) {
                $dispatch->completed_at = now();
                // Se concluído, resolve o ticket
                $dispatch->ticket()->update([
                    'status' => 'CLOSED',
                    'resolved_at' => now(),
                    'closed_at' => now(),
                ]);
            }

            if ($newStatus === 'FAILED') {
                $dispatch->ticket()->update(['status' => 'WAITING_DISPATCH']);
            }

            $dispatch->save();

            return $dispatch->fresh(['ticket.customerPerson', 'workerPerson', 'depot']);
        });
    }

    public function delete(int $id, int $accountId): bool
    {
        $dispatch = Dispatch::where('account_id', $accountId)->findOrFail($id);

        if (in_array($dispatch->status, ['IN_SERVICE', 'RFO_SUBMITTED', 'APPROVED', 'COMPLETED'])) {
            throw ValidationException::withMessages([
                'dispatch' => 'Não é permitido excluir um acionamento que já foi executado ou está em atendimento no local.',
            ]);
        }

        return $dispatch->delete();
    }

    public function getCounts(int $accountId): array
    {
        return [
            'total' => Dispatch::where('account_id', $accountId)->count(),
            'dispatched' => Dispatch::where('account_id', $accountId)->where('status', 'DISPATCHED')->count(),
            'on_route' => Dispatch::where('account_id', $accountId)->where('status', 'ON_ROUTE')->count(),
            'arrived_site' => Dispatch::where('account_id', $accountId)->where('status', 'ARRIVED_SITE')->count(),
            'in_service' => Dispatch::where('account_id', $accountId)->where('status', 'IN_SERVICE')->count(),
            'completed' => Dispatch::where('account_id', $accountId)->whereIn('status', ['APPROVED', 'COMPLETED'])->count(),
        ];
    }
}
