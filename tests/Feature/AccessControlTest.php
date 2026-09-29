<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Permission;
use App\Models\Person;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->account = Account::create([
            'id' => 1,
            'name' => 'Rede Pronta Matriz',
            'subdomain' => 'matriz',
            'status' => 'active',
        ]);

        $this->adminUser = User::create([
            'account_id' => $this->account->id,
            'name' => 'Administrador Matriz',
            'email' => 'admin@redepronta.com',
            'password' => bcrypt('password'),
            'is_super_admin' => true,
            'status' => 'active',
        ]);

        $this->seed(\Database\Seeders\AccessControlSeeder::class);
    }

    public function test_can_list_users(): void
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/v1/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['stats', 'total'],
            ]);
    }

    public function test_can_create_user_with_role_and_permissions(): void
    {
        $role = Role::where('slug', 'stock_operator')->first();

        $payload = [
            'name' => 'Carlos Operador',
            'email' => 'carlos@redepronta.com',
            'password' => 'segredo123',
            'role_id' => $role?->id,
            'is_super_admin' => false,
            'status' => 'active',
            'permissions' => ['people.view'],
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/v1/users', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Carlos Operador')
            ->assertJsonPath('data.email', 'carlos@redepronta.com');

        $this->assertDatabaseHas('users', [
            'email' => 'carlos@redepronta.com',
            'is_super_admin' => 0,
        ]);
    }

    public function test_super_admin_has_free_access_to_all_permissions(): void
    {
        $superAdmin = User::create([
            'account_id' => $this->account->id,
            'name' => 'Super Chefe',
            'email' => 'super@redepronta.com',
            'password' => bcrypt('123456'),
            'is_super_admin' => true,
            'status' => 'active',
        ]);

        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertTrue($superAdmin->hasPermission('any.arbitrary.permission'));
        $this->assertTrue($superAdmin->hasPermission('people.delete'));
        $this->assertGreaterThanOrEqual(20, count($superAdmin->getAllPermissionsSlugs()));
    }

    public function test_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'account_id' => $this->account->id,
            'name' => 'Operador Login',
            'email' => 'login_test@redepronta.com',
            'password' => bcrypt('senhaForte2026'),
            'status' => 'active',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'login_test@redepronta.com',
            'password' => 'senhaForte2026',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.email', 'login_test@redepronta.com');
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'admin@redepronta.com',
            'password' => 'senha_errada',
        ]);

        $response->assertStatus(422);
    }

    public function test_person_user_role_creates_user_account(): void
    {
        $payload = [
            'person_type' => 'individual',
            'name' => 'João Silva Técnico',
            'email' => 'joao.tecnico@redepronta.com',
            'is_employee' => true,
            'is_user' => true,
            'user_email' => 'joao.tecnico@redepronta.com',
            'user_password' => 'acesso2026',
            'user_is_super_admin' => false,
        ];

        $response = $this->actingAs($this->adminUser)->postJson('/api/v1/people', $payload);

        $response->assertStatus(201);
        $personId = $response->json('data.id');

        $this->assertDatabaseHas('people', [
            'id' => $personId,
            'is_user' => 1,
        ]);

        $this->assertDatabaseHas('users', [
            'person_id' => $personId,
            'email' => 'joao.tecnico@redepronta.com',
        ]);
    }

    public function test_can_update_person_and_sync_user(): void
    {
        $person = Person::create([
            'account_id' => $this->account->id,
            'person_type' => 'individual',
            'name' => 'Maria Analista',
            'email' => 'maria@redepronta.com',
            'is_user' => true,
        ]);

        $user = User::create([
            'account_id' => $this->account->id,
            'person_id' => $person->id,
            'name' => 'Maria Analista',
            'email' => 'maria@redepronta.com',
            'password' => bcrypt('antiga123'),
            'status' => 'active',
        ]);

        $updatePayload = [
            'person_type' => 'individual',
            'name' => 'Maria Analista Atualizada',
            'email' => 'maria.nova@redepronta.com',
            'is_user' => true,
            'user_email' => 'maria.nova@redepronta.com',
            'user_password' => 'novaSenha456',
            'user_is_super_admin' => true,
        ];

        $response = $this->actingAs($this->adminUser)->putJson("/api/v1/people/{$person->id}", $updatePayload);

        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals('maria.nova@redepronta.com', $user->email);
        $this->assertTrue($user->is_super_admin);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('novaSenha456', $user->password));
    }

    public function test_can_create_update_and_delete_role(): void
    {
        // 1. Criar Role
        $createRes = $this->actingAs($this->adminUser)->postJson('/api/v1/roles', [
            'name' => 'Auditor Externo',
            'slug' => 'external_auditor',
            'description' => 'Acesso de leitura para auditoria',
            'permissions' => ['people.view', 'supplies.view'],
        ]);

        $createRes->assertStatus(201)
            ->assertJsonPath('data.name', 'Auditor Externo')
            ->assertJsonPath('data.slug', 'external_auditor');

        $roleId = $createRes->json('data.id');

        // 2. Atualizar Role
        $updateRes = $this->actingAs($this->adminUser)->putJson("/api/v1/roles/{$roleId}", [
            'name' => 'Auditor Sênior',
            'permissions' => ['people.view', 'supplies.view', 'changelog.view'],
        ]);

        $updateRes->assertStatus(200)
            ->assertJsonPath('data.name', 'Auditor Sênior');

        // 3. Excluir Role
        $deleteRes = $this->actingAs($this->adminUser)->deleteJson("/api/v1/roles/{$roleId}");
        $deleteRes->assertStatus(200);

        $this->assertDatabaseMissing('roles', ['id' => $roleId]);
    }

    public function test_can_list_permissions_grouped_by_module(): void
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/v1/permissions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'modules',
                    'all',
                ],
            ]);
    }

    public function test_can_get_authenticated_user_me_and_logout(): void
    {
        $response = $this->actingAs($this->adminUser)->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonPath('user.email', $this->adminUser->email)
            ->assertJsonPath('user.is_super_admin', true);

        $logoutRes = $this->actingAs($this->adminUser)->postJson('/api/v1/auth/logout');
        $logoutRes->assertStatus(200);
    }
}
