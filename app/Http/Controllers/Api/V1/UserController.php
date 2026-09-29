<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Access\StoreUserRequest;
use App\Http\Requests\Access\UpdateUserRequest;
use App\Models\User;
use App\Services\Access\AccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected AccessService $accessService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status', 'role_id', 'is_super_admin']);
        $users = $this->accessService->listUsers($filters, $request->integer('per_page', 15));
        $stats = $this->accessService->getStats();

        return response()->json([
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'stats' => $stats,
            ],
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->accessService->createUser($request->validated());

        return response()->json([
            'message' => 'Usuário cadastrado com sucesso!',
            'data' => $user,
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        $user->load(['role.permissions', 'person', 'permissions']);

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'person_id' => $user->person_id,
                'person' => $user->person ? [
                    'id' => $user->person->id,
                    'name' => $user->person->name,
                    'trade_name' => $user->person->trade_name,
                    'document_number' => $user->person->document_number,
                ] : null,
                'role_id' => $user->role_id,
                'role' => $user->role ? [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'slug' => $user->role->slug,
                ] : null,
                'is_super_admin' => (bool) $user->is_super_admin,
                'status' => $user->status,
                'permissions' => $user->permissions->pluck('slug')->all(),
                'effective_permissions' => $user->getAllPermissionsSlugs(),
                'created_at' => $user->created_at?->format('d/m/Y H:i'),
            ],
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $updated = $this->accessService->updateUser($user, $request->validated());

        return response()->json([
            'message' => 'Usuário atualizado com sucesso!',
            'data' => $updated,
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->accessService->deleteUser($user, $request->user());

        return response()->json([
            'message' => 'Usuário removido com sucesso.',
        ]);
    }

    public function toggleStatus(Request $request, User $user): JsonResponse
    {
        $updated = $this->accessService->toggleUserStatus($user, $request->user());

        return response()->json([
            'message' => $updated->status === 'active' ? 'Usuário ativado com sucesso!' : 'Usuário inativado com sucesso!',
            'data' => $updated,
        ]);
    }
}
