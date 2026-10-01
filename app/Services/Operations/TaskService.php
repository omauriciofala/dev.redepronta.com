<?php

namespace App\Services\Operations;

use App\Models\Task;
use App\Models\Ticket;
use App\Models\TicketReason;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function list(array $filters, int $accountId)
    {
        $query = Task::with(['assignedUser', 'customerPerson', 'ticket'])
            ->where('account_id', $accountId);

        if (!empty($filters['status']) && $filters['status'] !== 'ALL') {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority']) && $filters['priority'] !== 'ALL') {
            $query->where('priority', $filters['priority']);
        }

        if (!empty($filters['source']) && $filters['source'] !== 'ALL') {
            $query->where('source', $filters['source']);
        }

        if (!empty($filters['search'])) {
            $term = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                  ->orWhere('task_number', 'like', $term)
                  ->orWhere('description', 'like', $term);
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    public function create(array $data, int $accountId): Task
    {
        return DB::transaction(function () use ($data, $accountId) {
            $taskCount = Task::where('account_id', $accountId)->count() + 1;
            $taskNumber = sprintf('TSK-%s-%04d', date('Y'), $taskCount);

            return Task::create([
                'account_id' => $accountId,
                'task_number' => $taskNumber,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'source' => $data['source'] ?? 'MANUAL',
                'priority' => $data['priority'] ?? 'MEDIUM',
                'status' => 'INBOX',
                'assigned_user_id' => $data['assigned_user_id'] ?? null,
                'customer_person_id' => $data['customer_person_id'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ]);
        });
    }

    public function update(int $id, array $data, int $accountId): Task
    {
        $task = Task::where('account_id', $accountId)->findOrFail($id);

        $task->fill(array_filter([
            'title' => $data['title'] ?? null,
            'description' => $data['description'] ?? null,
            'priority' => $data['priority'] ?? null,
            'status' => $data['status'] ?? null,
            'assigned_user_id' => array_key_exists('assigned_user_id', $data) ? $data['assigned_user_id'] : $task->assigned_user_id,
            'customer_person_id' => array_key_exists('customer_person_id', $data) ? $data['customer_person_id'] : $task->customer_person_id,
            'metadata' => $data['metadata'] ?? null,
        ], fn ($val) => !is_null($val)));

        if (isset($data['status']) && $data['status'] === 'RESOLVED_INTERNAL' && !$task->resolved_at) {
            $task->resolved_at = now();
        }

        $task->save();

        return $task->fresh(['assignedUser', 'customerPerson', 'ticket']);
    }

    public function delete(int $id, int $accountId): bool
    {
        $task = Task::where('account_id', $accountId)->findOrFail($id);

        if ($task->ticket()->exists() || $task->status === 'PROMOTED_TICKET') {
            throw ValidationException::withMessages([
                'task' => 'Não é permitido excluir uma tarefa já promovida para Chamado formal (Ticket).',
            ]);
        }

        return $task->delete();
    }

    public function promoteToTicket(int $taskId, array $ticketData, int $accountId): Ticket
    {
        return DB::transaction(function () use ($taskId, $ticketData, $accountId) {
            $task = Task::where('account_id', $accountId)->lockForUpdate()->findOrFail($taskId);

            if ($task->ticket()->exists() || $task->status === 'PROMOTED_TICKET') {
                throw ValidationException::withMessages([
                    'task' => 'Esta tarefa já foi promovida anteriormente para um Chamado.',
                ]);
            }

            // Calcular prazo SLA baseado no motivo
            $reason = TicketReason::where('account_id', $accountId)->findOrFail($ticketData['reason_id']);
            $slaHours = $reason->default_sla_hours ?? 24;
            $slaDueAt = now()->addHours($slaHours);

            // Gerar protocolo único TK-YYYY-XXXX
            $ticketCount = Ticket::where('account_id', $accountId)->count() + 1;
            $protocol = sprintf('TK-%s-%04d', date('Y'), $ticketCount);

            $ticket = Ticket::create([
                'account_id' => $accountId,
                'origin_task_id' => $task->id,
                'protocol' => $protocol,
                'title' => $ticketData['title'] ?? $task->title,
                'description' => $ticketData['description'] ?? $task->description,
                'customer_person_id' => $ticketData['customer_person_id'] ?? $task->customer_person_id,
                'requester_person_id' => $ticketData['requester_person_id'] ?? null,
                'city_id' => $ticketData['city_id'],
                'department_id' => $ticketData['department_id'],
                'category_id' => $ticketData['category_id'],
                'reason_id' => $ticketData['reason_id'],
                'priority' => $ticketData['priority'] ?? $reason->default_priority ?? 'MEDIUM',
                'status' => 'OPEN',
                'address_street' => $ticketData['address_street'] ?? null,
                'address_number' => $ticketData['address_number'] ?? null,
                'address_neighborhood' => $ticketData['address_neighborhood'] ?? null,
                'address_postal_code' => $ticketData['address_postal_code'] ?? null,
                'latitude' => $ticketData['latitude'] ?? null,
                'longitude' => $ticketData['longitude'] ?? null,
                'sla_due_at' => $slaDueAt,
            ]);

            // Atualiza status da tarefa para PROMOTED_TICKET
            $task->update([
                'status' => 'PROMOTED_TICKET',
                'resolved_at' => now(),
            ]);

            return $ticket->fresh(['customerPerson', 'city', 'department', 'category', 'reason', 'originTask']);
        });
    }

    public function getCounts(int $accountId): array
    {
        return [
            'total' => Task::where('account_id', $accountId)->count(),
            'inbox' => Task::where('account_id', $accountId)->where('status', 'INBOX')->count(),
            'triaged' => Task::where('account_id', $accountId)->where('status', 'TRIAGED')->count(),
            'promoted' => Task::where('account_id', $accountId)->where('status', 'PROMOTED_TICKET')->count(),
            'resolved' => Task::where('account_id', $accountId)->where('status', 'RESOLVED_INTERNAL')->count(),
        ];
    }
}
