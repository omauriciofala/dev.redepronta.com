<?php

namespace App\Services\Access;

use App\Models\Account;
use App\Models\Permission;
use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AccessService
{
    /**
     * Lista usuários paginados com filtros.
     */
    public function listUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $accountId = session('active_account_id') ?? Account::first()?->id ?? 1;

        $query = User::with(['role', 'person', 'permissions'])
            ->where('account_id', $accountId);

        if (!empty($filters['search'])) {
            $term = trim($filters['search']);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhereHas('person', function ($qp) use ($term) {
                      $qp->where('name', 'like', "%{$term}%")
                         ->orWhere('trade_name', 'like', "%{$term}%")
                         ->orWhere('document_number', 'like', "%{$term}%");
                  });
            });
        }

        if (!empty($filters['status']) && in_array($filters['status'], ['active', 'inactive'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['role_id'])) {
            $query->where('role_id', $filters['role_id']);
        }

        if (isset($filters['is_super_admin']) && $filters['is_super_admin'] !== '') {
            $query->where('is_super_admin', filter_var($filters['is_super_admin'], FILTER_VALIDATE_BOOLEAN));
        }

        $query->orderBy('name', 'asc');

        return $query->paginate($perPage);
    }

    /**
     * Cria um novo usuário.
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $accountId = session('active_account_id') ?? Account::first()?->id ?? 1;

            $user = new User();
            $user->account_id = $accountId;
            $user->name = $data['name'];
            $user->email = trim($data['email']);
            $user->password = Hash::make($data['password']);
            $user->role_id = $data['role_id'] ?? null;
            $user->person_id = $data['person_id'] ?? null;
            $user->is_super_admin = filter_var($data['is_super_admin'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $user->status = $data['status'] ?? 'active';
            $user->save();

            // Se vinculou a uma Pessoa, garante que is_user seja true na Pessoa
            if (!empty($user->person_id)) {
                Person::where('id', $user->person_id)->update(['is_user' => true]);
            }

            // Sincronizar permissões diretas se enviadas
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $permIds = Permission::whereIn('slug', $data['permissions'])->pluck('id')->all();
                $user->permissions()->sync($permIds);
            }

            return $user->load(['role', 'person', 'permissions']);
        });
    }

    /**
     * Atualiza um usuário existente.
     */
    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            if (isset($data['name'])) {
                $user->name = $data['name'];
            }
            if (isset($data['email'])) {
                $user->email = trim($data['email']);
            }
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            if (array_key_exists('role_id', $data)) {
                $user->role_id = $data['role_id'] ? (int) $data['role_id'] : null;
            }
            if (array_key_exists('person_id', $data)) {
                $oldPersonId = $user->person_id;
                $newPersonId = $data['person_id'] ? (int) $data['person_id'] : null;
                $user->person_id = $newPersonId;

                if ($newPersonId) {
                    Person::where('id', $newPersonId)->update(['is_user' => true]);
                }
                if ($oldPersonId && $oldPersonId !== $newPersonId) {
                    // Se a pessoa anterior não tiver outro usuário, pode desligar
                    $otherUser = User::where('person_id', $oldPersonId)->where('id', '!=', $user->id)->exists();
                    if (!$otherUser) {
                        Person::where('id', $oldPersonId)->update(['is_user' => false]);
                    }
                }
            }
            if (array_key_exists('is_super_admin', $data)) {
                $user->is_super_admin = filter_var($data['is_super_admin'], FILTER_VALIDATE_BOOLEAN);
            }
            if (isset($data['status'])) {
                $user->status = $data['status'];
            }

            $user->save();

            // Sincronizar permissões diretas se enviadas
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $permIds = Permission::whereIn('slug', $data['permissions'])->pluck('id')->all();
                $user->permissions()->sync($permIds);
            }

            return $user->fresh(['role', 'person', 'permissions']);
        });
    }

    /**
     * Inativa ou remove o usuário.
     */
    public function deleteUser(User $user, ?User $currentUser = null): void
    {
        if ($currentUser && $currentUser->id === $user->id) {
            throw ValidationException::withMessages([
                'user' => ['Você não pode excluir o seu próprio usuário logado.'],
            ]);
        }

        // Verifica se é o único Super Admin ativo
        if ($user->is_super_admin) {
            $otherSuperAdminCount = User::where('account_id', $user->account_id)
                ->where('is_super_admin', true)
                ->where('id', '!=', $user->id)
                ->where('status', 'active')
                ->count();

            if ($otherSuperAdminCount === 0) {
                throw ValidationException::withMessages([
                    'user' => ['Não é possível excluir o único Super Administrador da conta.'],
                ]);
            }
        }

        DB::transaction(function () use ($user) {
            if ($user->person_id) {
                Person::where('id', $user->person_id)->update(['is_user' => false]);
            }
            $user->permissions()->detach();
            $user->delete();
        });
    }

    /**
     * Alterna status do usuário.
     */
    public function toggleUserStatus(User $user, ?User $currentUser = null): User
    {
        if ($currentUser && $currentUser->id === $user->id) {
            throw ValidationException::withMessages([
                'user' => ['Você não pode desativar o seu próprio usuário logado.'],
            ]);
        }

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);
        return $user->fresh(['role', 'person']);
    }

    /**
     * Lista todos os papéis da conta.
     */
    public function listRoles(?int $accountId = null): Collection
    {
        $accountId = $accountId ?? session('active_account_id') ?? Account::first()?->id ?? 1;

        return Role::with(['permissions'])
            ->withCount('users')
            ->where('account_id', $accountId)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Cria um papel.
     */
    public function createRole(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $accountId = session('active_account_id') ?? Account::first()?->id ?? 1;

            $role = Role::create([
                'account_id' => $accountId,
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'is_system' => false,
            ]);

            if (!empty($data['permissions']) && is_array($data['permissions'])) {
                $permIds = Permission::whereIn('slug', $data['permissions'])->pluck('id')->all();
                $role->permissions()->sync($permIds);
            }

            return $role->load('permissions');
        });
    }

    /**
     * Atualiza um papel.
     */
    public function updateRole(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {
            $role->update([
                'name' => $data['name'] ?? $role->name,
                'slug' => $data['slug'] ?? $role->slug,
                'description' => $data['description'] ?? $role->description,
            ]);

            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $permIds = Permission::whereIn('slug', $data['permissions'])->pluck('id')->all();
                $role->permissions()->sync($permIds);
            }

            return $role->fresh('permissions');
        });
    }

    /**
     * Exclui um papel.
     */
    public function deleteRole(Role $role): void
    {
        if ($role->is_system) {
            throw ValidationException::withMessages([
                'role' => ['Papéis nativos do sistema não podem ser excluídos.'],
            ]);
        }

        if ($role->users()->count() > 0) {
            throw ValidationException::withMessages([
                'role' => ['Existem usuários vinculados a este papel. Reatribua-os antes de excluir.'],
            ]);
        }

        DB::transaction(function () use ($role) {
            $role->permissions()->detach();
            $role->delete();
        });
    }

    /**
     * Retorna todas as permissões do sistema catalogadas por módulo.
     */
    public function listPermissions(): array
    {
        $perms = Permission::orderBy('module')->orderBy('name')->get();

        $grouped = [];
        foreach ($perms as $perm) {
            $grouped[$perm->module][] = [
                'id' => $perm->id,
                'module' => $perm->module,
                'name' => $perm->name,
                'slug' => $perm->slug,
                'description' => $perm->description,
            ];
        }

        return [
            'modules' => $grouped,
            'all' => $perms,
        ];
    }

    /**
     * Estatísticas gerais do módulo de acessos.
     */
    public function getStats(?int $accountId = null): array
    {
        $accountId = $accountId ?? session('active_account_id') ?? Account::first()?->id ?? 1;

        $userStats = DB::table('users')
            ->where('account_id', $accountId)
            ->selectRaw("
                COUNT(*) as total,
                COALESCE(SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END), 0) as active,
                COALESCE(SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END), 0) as inactive,
                COALESCE(SUM(CASE WHEN is_super_admin = 1 THEN 1 ELSE 0 END), 0) as super_admin,
                COALESCE(SUM(CASE WHEN person_id IS NOT NULL THEN 1 ELSE 0 END), 0) as linked_people
            ")->first();

        $totalRoles = DB::table('roles')->where('account_id', $accountId)->count();
        $totalPermissions = DB::table('permissions')->count();

        return [
            'users_total' => (int) ($userStats->total ?? 0),
            'users_active' => (int) ($userStats->active ?? 0),
            'users_inactive' => (int) ($userStats->inactive ?? 0),
            'users_super_admin' => (int) ($userStats->super_admin ?? 0),
            'users_linked_people' => (int) ($userStats->linked_people ?? 0),
            'roles_total' => $totalRoles,
            'permissions_total' => $totalPermissions,
        ];
    }
}
