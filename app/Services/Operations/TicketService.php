<?php

namespace App\Services\Operations;

use App\Models\Ticket;
use App\Models\TicketReason;
use App\Models\Dispatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketService
{
    public function list(array $filters, int $accountId)
    {
        $query = Ticket::with([
            'customerPerson',
            'requesterPerson',
            'city.state',
            'department',
            'category',
            'reason',
            'dispatches.workerPerson',
            'dispatches.depot',
        ])->where('account_id', $accountId);

        if (!empty($filters['status']) && $filters['status'] !== 'ALL') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority']) && $filters['priority'] !== 'ALL') {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['department_id']) && $filters['department_id'] !== 'ALL') {
            $query->where('department_id', $filters['department_id']);
        }

        if (!empty($filters['city_id']) && $filters['city_id'] !== 'ALL') {
            $query->where('city_id', $filters['city_id']);
        }

        if (!empty($filters['search'])) {
            $term = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('protocol', 'like', $term)
                  ->orWhere('title', 'like', $term)
                  ->orWhere('description', 'like', $term)
                  ->orWhereHas('customerPerson', function ($qp) use ($term) {
                      $qp->where('name', 'like', $term)->orWhere('document_number', 'like', $term);
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data, int $accountId): Ticket
    {
        return DB::transaction(function () use ($data, $accountId) {
            $ticketCount = Ticket::where('account_id', $accountId)->count() + 1;
            $protocol = sprintf('TK-%s-%04d', date('Y'), $ticketCount);

            $reason = TicketReason::where('account_id', $accountId)->findOrFail($data['reason_id']);
            $slaHours = $reason->default_sla_hours ?? 24;
            $slaDueAt = now()->addHours($slaHours);

            return Ticket::create([
                'account_id' => $accountId,
                'origin_task_id' => $data['origin_task_id'] ?? null,
                'protocol' => $protocol,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'customer_person_id' => $data['customer_person_id'],
                'requester_person_id' => $data['requester_person_id'] ?? null,
                'city_id' => $data['city_id'],
                'department_id' => $data['department_id'],
                'category_id' => $data['category_id'],
                'reason_id' => $data['reason_id'],
                'priority' => $data['priority'] ?? $reason->default_priority ?? 'MEDIUM',
                'status' => 'OPEN',
                'address_street' => $data['address_street'] ?? null,
                'address_number' => $data['address_number'] ?? null,
                'address_neighborhood' => $data['address_neighborhood'] ?? null,
                'address_postal_code' => $data['address_postal_code'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'sla_due_at' => $slaDueAt,
            ]);
        });
    }

    public function update(int $id, array $data, int $accountId): Ticket
    {
        $ticket = Ticket::where('account_id', $accountId)->findOrFail($id);

        $ticket->fill(array_filter([
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'requester_person_id' => array_key_exists('requester_person_id', $data) ? $data['requester_person_id'] : $ticket->requester_person_id,
            'department_id' => $data['department_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'reason_id' => $data['reason_id'] ?? null,
            'priority' => $data['priority'] ?? null,
            'status' => $data['status'] ?? null,
            'address_street' => $data['address_street'] ?? null,
            'address_number' => $data['address_number'] ?? null,
            'address_neighborhood' => $data['address_neighborhood'] ?? null,
            'address_postal_code' => $data['address_postal_code'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ], fn ($val) => !is_null($val)));

        if (isset($data['status'])) {
            if (in_array($data['status'], ['RESOLVED_REMOTE', 'CLOSED']) && !$ticket->resolved_at) {
                $ticket->resolved_at = now();
            }
            if ($data['status'] === 'CLOSED' && !$ticket->closed_at) {
                $ticket->closed_at = now();
            }
        }

        $ticket->save();

        return $ticket->fresh([
            'customerPerson',
            'requesterPerson',
            'city.state',
            'department',
            'category',
            'reason',
            'dispatches.workerPerson',
            'dispatches.depot',
        ]);
    }

    public function delete(int $id, int $accountId): bool
    {
        $ticket = Ticket::where('account_id', $accountId)->findOrFail($id);

        if ($ticket->dispatches()->exists()) {
            throw ValidationException::withMessages([
                'ticket' => 'Não é permitido excluir um chamado que possui acionamentos de campo vinculados. Cancele o chamado ou os acionamentos.',
            ]);
        }

        return $ticket->delete();
    }

    public function dispatchToField(int $ticketId, array $dispatchData, int $accountId): Dispatch
    {
        return DB::transaction(function () use ($ticketId, $dispatchData, $accountId) {
            $ticket = Ticket::where('account_id', $accountId)->lockForUpdate()->findOrFail($ticketId);

            $dispatchCount = Dispatch::where('account_id', $accountId)->count() + 1;
            $dispatchNumber = sprintf('DSP-%s-%04d', date('Y'), $dispatchCount);

            $dispatch = Dispatch::create([
                'account_id' => $accountId,
                'ticket_id' => $ticket->id,
                'worker_person_id' => $dispatchData['worker_person_id'],
                'depot_id' => $dispatchData['depot_id'],
                'dispatch_number' => $dispatchNumber,
                'status' => 'DISPATCHED',
                'scheduled_at' => $dispatchData['scheduled_at'] ?? now(),
                'dispatched_at' => now(),
                'notes' => $dispatchData['notes'] ?? null,
            ]);

            // Atualiza status do chamado para IN_FIELD
            $ticket->update([
                'status' => 'IN_FIELD',
            ]);

            return $dispatch->fresh(['ticket', 'workerPerson', 'depot']);
        });
    }

    public function getCounts(int $accountId): array
    {
        $now = now();
        return [
            'total' => Ticket::where('account_id', $accountId)->count(),
            'open' => Ticket::where('account_id', $accountId)->whereIn('status', ['OPEN', 'IN_TRIAGE'])->count(),
            'waiting_dispatch' => Ticket::where('account_id', $accountId)->where('status', 'WAITING_DISPATCH')->count(),
            'in_field' => Ticket::where('account_id', $accountId)->where('status', 'IN_FIELD')->count(),
            'resolved' => Ticket::where('account_id', $accountId)->whereIn('status', ['RESOLVED_REMOTE', 'CLOSED'])->count(),
            'sla_breached' => Ticket::where('account_id', $accountId)
                ->whereNotIn('status', ['RESOLVED_REMOTE', 'CLOSED', 'CANCELED'])
                ->where('sla_due_at', '<', $now)
                ->count(),
        ];
    }
}
