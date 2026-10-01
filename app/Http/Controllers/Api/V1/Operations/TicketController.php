<?php

namespace App\Http\Controllers\Api\V1\Operations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Operations\StoreTicketRequest;
use App\Http\Requests\Operations\UpdateTicketRequest;
use App\Http\Requests\Operations\DispatchTicketRequest;
use App\Models\Ticket;
use App\Services\Operations\TicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(
        protected TicketService $ticketService
    ) {}

    protected function getAccountId(): int
    {
        return session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
    }

    public function index(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $filters = $request->only(['status', 'priority', 'department_id', 'city_id', 'search', 'per_page']);

        $tickets = $this->ticketService->list($filters, $accountId);
        $counts = $this->ticketService->getCounts($accountId);

        return response()->json([
            'data' => $tickets->items(),
            'meta' => [
                'current_page' => $tickets->currentPage(),
                'last_page' => $tickets->lastPage(),
                'per_page' => $tickets->perPage(),
                'total' => $tickets->total(),
                'counts' => $counts,
            ],
        ]);
    }

    public function store(StoreTicketRequest $request): JsonResponse
    {
        $ticket = $this->ticketService->create($request->validated(), $this->getAccountId());
        $ticket->load(['customerPerson', 'city.state', 'department', 'category', 'reason']);

        return response()->json([
            'message' => sprintf('Chamado criado com sucesso! Protocolo: %s', $ticket->protocol),
            'data' => $ticket,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $ticket = Ticket::where('account_id', $this->getAccountId())
            ->with([
                'customerPerson',
                'requesterPerson',
                'city.state',
                'department',
                'category',
                'reason',
                'originTask',
                'dispatches.workerPerson',
                'dispatches.depot',
            ])
            ->findOrFail($id);

        return response()->json(['data' => $ticket]);
    }

    public function update(UpdateTicketRequest $request, int $id): JsonResponse
    {
        $ticket = $this->ticketService->update($id, $request->validated(), $this->getAccountId());

        return response()->json([
            'message' => 'Chamado atualizado com sucesso!',
            'data' => $ticket,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->ticketService->delete($id, $this->getAccountId());

        return response()->json([
            'message' => 'Chamado removido com sucesso!',
        ]);
    }

    public function dispatch(DispatchTicketRequest $request, int $id): JsonResponse
    {
        $dispatch = $this->ticketService->dispatchToField($id, $request->validated(), $this->getAccountId());

        return response()->json([
            'message' => sprintf('Acionamento de campo criado com sucesso! Número: %s', $dispatch->dispatch_number),
            'data' => $dispatch,
        ], 201);
    }
}
