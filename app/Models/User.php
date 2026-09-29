<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'account_id',
        'person_id',
        'role_id',
        'name',
        'email',
        'password',
        'is_super_admin',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions');
    }

    /**
     * Verifica se o usuário é Super Admin.
     * Super Admin possui acesso irrestrito e livre a todas as rotas e permissões.
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    /**
     * Verifica se o usuário possui uma permissão específica.
     */
    public function hasPermission(string $slug): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Verifica permissões diretas do usuário
        if ($this->permissions->contains('slug', $slug)) {
            return true;
        }

        // Verifica permissões herdadas do papel
        if ($this->role && $this->role->permissions->contains('slug', $slug)) {
            return true;
        }

        return false;
    }

    /**
     * Retorna a lista de slugs de todas as permissões efetivas do usuário.
     */
    public function getAllPermissionsSlugs(): array
    {
        if ($this->isSuperAdmin()) {
            return Permission::pluck('slug')->all();
        }

        $directSlugs = $this->permissions->pluck('slug')->all();
        $roleSlugs = $this->role ? $this->role->permissions->pluck('slug')->all() : [];

        return array_values(array_unique(array_merge($directSlugs, $roleSlugs)));
    }
}
