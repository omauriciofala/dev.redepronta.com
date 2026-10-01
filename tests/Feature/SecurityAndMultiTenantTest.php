<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Permission;
use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndMultiTenantTest extends TestCase
{
    use RefreshDatabase;

    protected Account $accountA;
    protected Account $accountB;
    protected User $adminA;
    protected User $adminB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\AccessControlSeeder::class);

        // Conta A (Tenant Matriz)
        $this->accountA = Account::firstOrCreate(['id' => 1], [
            'name' => 'Empresa Matriz A',
            'subdomain' => 'matriza',
            'status' => 'active',
        ]);

        $this->adminA = User::create([
            'account_id' => $this->accountA->id,
            'name' => 'Admin Conta A',
            'email' => 'admin.a@redepronta.com',
            'password' => bcrypt('password123'),
            'is_super_admin' => true,
            'status' => 'active',
        ]);

        // Conta B (Tenant Filial)
        $this->accountB = Account::create([
            'id' => 2,
            'name' => 'Empresa Filial B',
            'subdomain' => 'filialb',
            'status' => 'active',
        ]);

        $this->adminB = User::create([
            'account_id' => $this->accountB->id,
            'name' => 'Admin Conta B',
            'email' => 'admin.b@redepronta.com',
            'password' => bcrypt('password123'),
            'is_super_admin' => true,
            'status' => 'active',
        ]);
    }

    public function test_tenant_isolation_prevents_viewing_users_from_another_tenant(): void
    {
        // Cria usuário adicional na Conta B
        User::create([
            'account_id' => $this->accountB->id,
            'name' => 'Operador Exclusivo Conta B',
            'email' => 'operador.b@redepronta.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);

        // Simula requisição da Conta A
        session(['active_account_id' => $this->accountA->id]);
        $response = $this->actingAs($this->adminA)->getJson('/api/v1/users');

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('name')->all();

        $this->assertContains('Admin Conta A', $names);
        $this->assertNotContains('Admin Conta B', $names);
        $this->assertNotContains('Operador Exclusivo Conta B', $names);
    }

    public function test_tenant_isolation_prevents_viewing_people_from_another_tenant(): void
    {
        // Cria pessoa na Conta A
        $personA = Person::create([
            'account_id' => $this->accountA->id,
            'name' => 'Cliente Conta A',
            'type' => 'individual',
            'status' => 'active',
        ]);

        // Cria pessoa na Conta B
        $personB = Person::create([
            'account_id' => $this->accountB->id,
            'name' => 'Cliente Conta B',
            'type' => 'individual',
            'status' => 'active',
        ]);

        // Consulta autenticado na Conta A
        session(['active_account_id' => $this->accountA->id]);
        $response = $this->actingAs($this->adminA)->getJson('/api/v1/people');

        $response->assertStatus(200);
        $names = collect($response->json('data'))->pluck('name')->all();

        $this->assertContains('Cliente Conta A', $names);
        $this->assertNotContains('Cliente Conta B', $names);
    }

    public function test_user_cannot_delete_self(): void
    {
        session(['active_account_id' => $this->accountA->id]);
        $response = $this->actingAs($this->adminA)->deleteJson("/api/v1/users/{$this->adminA->id}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user']);

        $this->assertDatabaseHas('users', ['id' => $this->adminA->id]);
    }

    public function test_cannot_delete_last_super_admin_of_account(): void
    {
        session(['active_account_id' => $this->accountA->id]);

        // Cria um operador comum para tentar excluir o único Super Admin
        $operator = User::create([
            'account_id' => $this->accountA->id,
            'name' => 'Operador Auxiliar',
            'email' => 'operador@redepronta.com',
            'password' => bcrypt('secret'),
            'is_super_admin' => false,
            'status' => 'active',
        ]);

        $response = $this->actingAs($operator)->deleteJson("/api/v1/users/{$this->adminA->id}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user']);

        $this->assertDatabaseHas('users', ['id' => $this->adminA->id]);
    }

    public function test_can_delete_super_admin_if_another_super_admin_exists(): void
    {
        session(['active_account_id' => $this->accountA->id]);

        // Cria um segundo Super Admin na Conta A
        $secondSuperAdmin = User::create([
            'account_id' => $this->accountA->id,
            'name' => 'Segundo Super Admin',
            'email' => 'super2@redepronta.com',
            'password' => bcrypt('secret'),
            'is_super_admin' => true,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminA)->deleteJson("/api/v1/users/{$secondSuperAdmin->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $secondSuperAdmin->id]);
    }

    public function test_cannot_create_user_with_duplicate_email_in_same_account(): void
    {
        session(['active_account_id' => $this->accountA->id]);

        $payload = [
            'name' => 'Tentativa Duplicada',
            'email' => 'admin.a@redepronta.com', // Já existe na Conta A
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'is_super_admin' => false,
        ];

        $response = $this->actingAs($this->adminA)->postJson('/api/v1/users', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_super_admin_has_bypass_on_all_permissions(): void
    {
        $this->assertTrue($this->adminA->isSuperAdmin());
        $this->assertTrue($this->adminA->hasPermission('any_random_permission_slug'));
        $this->assertTrue($this->adminA->hasPermission('supplies.transfers.create'));
        $this->assertTrue($this->adminA->hasPermission('users.delete'));

        $allSlugs = $this->adminA->getAllPermissionsSlugs();
        $this->assertNotEmpty($allSlugs);
        $this->assertContains('users.view', $allSlugs);
        $this->assertContains('supplies.view', $allSlugs);
    }

    public function test_user_inherits_permissions_from_assigned_role(): void
    {
        $role = Role::create([
            'account_id' => $this->accountA->id,
            'name' => 'Almoxarife Especial',
            'slug' => 'special_stock_clerk',
            'description' => 'Acesso apenas ao estoque',
            'status' => 'active',
        ]);

        $perm1 = Permission::where('slug', 'supplies.view')->first();
        $perm2 = Permission::where('slug', 'supplies.transfers.create')->first();
        $role->permissions()->attach([$perm1->id, $perm2->id]);

        $regularUser = User::create([
            'account_id' => $this->accountA->id,
            'name' => 'Operador Regular',
            'email' => 'operador.reg@redepronta.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'is_super_admin' => false,
            'status' => 'active',
        ]);

        $regularUser->load(['role.permissions', 'permissions']);

        $this->assertFalse($regularUser->isSuperAdmin());
        $this->assertTrue($regularUser->hasPermission('supplies.view'));
        $this->assertTrue($regularUser->hasPermission('supplies.transfers.create'));
        $this->assertFalse($regularUser->hasPermission('users.delete'));
    }

    public function test_direct_user_permissions_merge_with_role_permissions(): void
    {
        $role = Role::create([
            'account_id' => $this->accountA->id,
            'name' => 'Papel Básico',
            'slug' => 'basic_role',
            'status' => 'active',
        ]);
        $permRole = Permission::where('slug', 'people.view')->first();
        $role->permissions()->attach([$permRole->id]);

        $user = User::create([
            'account_id' => $this->accountA->id,
            'name' => 'Usuário com Permissão Extra',
            'email' => 'extra@redepronta.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'is_super_admin' => false,
            'status' => 'active',
        ]);

        $permDirect = Permission::where('slug', 'supplies.view')->first();
        $user->permissions()->attach([$permDirect->id]);
        $user->load(['role.permissions', 'permissions']);

        $this->assertTrue($user->hasPermission('people.view'));
        $this->assertTrue($user->hasPermission('supplies.view'));
        $this->assertFalse($user->hasPermission('roles.manage'));
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::create([
            'account_id' => $this->accountA->id,
            'name' => 'Usuário Bloqueado',
            'email' => 'bloqueado@redepronta.com',
            'password' => bcrypt('secret123'),
            'status' => 'inactive',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'bloqueado@redepronta.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_cannot_create_role_with_duplicate_slug(): void
    {
        session(['active_account_id' => $this->accountA->id]);

        Role::create([
            'account_id' => $this->accountA->id,
            'name' => 'Papel Existente',
            'slug' => 'papel_existente',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminA)->postJson('/api/v1/roles', [
            'name' => 'Outro Papel com Mesmo Slug',
            'slug' => 'papel_existente',
            'description' => 'Teste duplicidade',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_cannot_delete_role_assigned_to_active_users(): void
    {
        session(['active_account_id' => $this->accountA->id]);

        $role = Role::create([
            'account_id' => $this->accountA->id,
            'name' => 'Papel em Uso',
            'slug' => 'papel_em_uso',
            'status' => 'active',
        ]);

        User::create([
            'account_id' => $this->accountA->id,
            'name' => 'Operador Vinculado',
            'email' => 'vinculado@redepronta.com',
            'password' => bcrypt('password'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->adminA)->deleteJson("/api/v1/roles/{$role->id}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }
}
