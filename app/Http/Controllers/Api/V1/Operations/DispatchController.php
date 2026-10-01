<?php

namespace App\Http\Controllers\Api\V1\Operations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Operations\DispatchTicketRequest;
use App\Http\Requests\Operations\AdvanceDispatchStatusRequest;
use App\Models\Dispatch;
use App\Services\Operations\DispatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    public function __construct(
        protected DispatchService $dispatchService
    ) {}

    protected function getAccountId(): int
    {
        return session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
    }

    public function index(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $filters = $request->only(['status', 'worker_person_id', 'depot_id', 'search', 'per_page']);

        $dispatches = $this->dispatchService->list($filters, $accountId);
        $counts = $this->dispatchService->getCounts($accountId);

        return response()->json([
            'data' => $dispatches->items(),
            'meta' => [
                'current_page' => $dispatches->currentPage(),
                'last_page' => $dispatches->lastPage(),
                'per_page' => $dispatches->perPage(),
                'total' => $dispatches->total(),
                'counts' => $counts,
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $dispatch = Dispatch::where('account_id', $this->getAccountId())
            ->with([
                'ticket.customerPerson',
                'ticket.city.state',
                'ticket.department',
                'ticket.category',
                'ticket.reason',
                'workerPerson',
                'depot.cluster',
            ])
            ->findOrFail($id);

        return response()->json(['data' => $dispatch]);
    }

    public function advanceStatus(AdvanceDispatchStatusRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();
        $dispatch = $this->dispatchService->advanceStatus(
            $id,
            $validated['status'],
            $validated['notes'] ?? null,
            $this->getAccountId()
        );

        return response()->json([
            'message' => sprintf('Status do acionamento atualizado para %s!', $dispatch->status),
            'data' => $dispatch,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->dispatchService->delete($id, $this->getAccountId());

        return response()->json([
            'message' => 'Acionamento cancelado/removido com sucesso!',
        ]);
    }
}
