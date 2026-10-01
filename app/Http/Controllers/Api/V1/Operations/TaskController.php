<?php

namespace App\Http\Controllers\Api\V1\Operations;

use App\Http\Controllers\Controller;
use App\Http\Requests\Operations\StoreTaskRequest;
use App\Http\Requests\Operations\UpdateTaskRequest;
use App\Http\Requests\Operations\PromoteTaskRequest;
use App\Http\Requests\Operations\StoreTaskCommentRequest;
use App\Models\Task;
use App\Services\Operations\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    protected function getAccountId(): int
    {
        return session('active_account_id') ?? \App\Models\Account::first()?->id ?? 1;
    }

    protected function getUserId(): int
    {
        return auth()->id() ?? \App\Models\User::first()?->id ?? 1;
    }

    public function index(Request $request): JsonResponse
    {
        $accountId = $this->getAccountId();
        $filters = $request->only(['status', 'priority', 'source', 'assigned_user_id', 'search', 'per_page']);

        $tasks = $this->taskService->list($filters, $accountId);
        $counts = $this->taskService->getCounts($accountId);

        if ($tasks instanceof \Illuminate\Database\Eloquent\Collection) {
            return response()->json([
                'data' => $tasks,
                'meta' => [
                    'total' => $tasks->count(),
                    'counts' => $counts,
                ],
            ]);
        }

        return response()->json([
            'data' => $tasks->items(),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
                'counts' => $counts,
            ],
        ]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->create($request->validated(), $this->getAccountId());

        return response()->json([
            'message' => 'Tarefa criada com sucesso na caixa de entrada!',
            'data' => $task,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $task = Task::where('account_id', $this->getAccountId())
            ->with([
                'assignedUser',
                'customerPerson',
                'ticket.department',
                'ticket.category',
                'ticket.reason',
                'comments.user',
            ])
            ->findOrFail($id);

        return response()->json(['data' => $task]);
    }

    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $task = $this->taskService->update($id, $request->validated(), $this->getAccountId());

        return response()->json([
            'message' => 'Tarefa atualizada com sucesso!',
            'data' => $task,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->taskService->delete($id, $this->getAccountId());

        return response()->json([
            'message' => 'Tarefa removida com sucesso!',
        ]);
    }

    public function promote(PromoteTaskRequest $request, int $id): JsonResponse
    {
        $ticket = $this->taskService->promoteToTicket($id, $request->validated(), $this->getAccountId());

        return response()->json([
            'message' => sprintf('Tarefa promovida para Chamado com sucesso! Protocolo: %s', $ticket->protocol),
            'data' => $ticket,
        ], 201);
    }

    public function comments(int $id): JsonResponse
    {
        $comments = $this->taskService->getComments($id, $this->getAccountId());

        return response()->json(['data' => $comments]);
    }

    public function addComment(StoreTaskCommentRequest $request, int $id): JsonResponse
    {
        $comment = $this->taskService->addComment(
            $id,
            $request->validated('comment'),
            $this->getUserId(),
            $this->getAccountId()
        );

        return response()->json([
            'message' => 'Comentário adicionado com sucesso!',
            'data' => $comment,
        ], 201);
    }

    public function assign(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'assigned_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $task = $this->taskService->assignUser(
            $id,
            $request->input('assigned_user_id'),
            $this->getAccountId()
        );

        return response()->json([
            'message' => 'Responsável pela tarefa atualizado com sucesso!',
            'data' => $task,
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:INBOX,TRIAGED,PROMOTED_TICKET,RESOLVED_INTERNAL,CANCELED'],
        ]);

        $task = $this->taskService->updateStatus(
            $id,
            $request->input('status'),
            $this->getAccountId()
        );

        return response()->json([
            'message' => sprintf('Status da tarefa atualizado para %s!', $task->status),
            'data' => $task,
        ]);
    }
}
