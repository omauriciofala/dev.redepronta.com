<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Access\StoreRoleRequest;
use App\Http\Requests\Access\UpdateRoleRequest;
use App\Models\Role;
use App\Services\Access\AccessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        protected AccessService $accessService
    ) {}

    public function index(): JsonResponse
    {
        $roles = $this->accessService->listRoles();

        return response()->json([
            'data' => $roles->map(fn (Role $r) => [
                'id' => $r->id,
                'name' => $r->name,
                'slug' => $r->slug,
                'description' => $r->description,
                'is_system' => (bool) $r->is_system,
                'users_count' => $r->users_count ?? 0,
                'permissions_count' => $r->permissions->count(),
                'permissions' => $r->permissions->pluck('slug')->all(),
            ]),
        ]);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->accessService->createRole($request->validated());

        return response()->json([
            'message' => 'Papel criado com sucesso!',
            'data' => $role,
        ], 201);
    }

    public function show(Role $role): JsonResponse
    {
        $role->load('permissions')->loadCount('users');

        return response()->json([
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'slug' => $role->slug,
                'description' => $role->description,
                'is_system' => (bool) $role->is_system,
                'users_count' => $role->users_count,
                'permissions' => $role->permissions->pluck('slug')->all(),
            ],
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $updated = $this->accessService->updateRole($role, $request->validated());

        return response()->json([
            'message' => 'Papel atualizado com sucesso!',
            'data' => $updated,
        ]);
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->accessService->deleteRole($role);

        return response()->json([
            'message' => 'Papel excluído com sucesso.',
        ]);
    }
}
