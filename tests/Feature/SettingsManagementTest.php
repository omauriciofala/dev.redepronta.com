<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SettingsManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Account $account;
    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->account = Account::firstOrCreate(
            ['subdomain' => 'matriz'],
            [
                'name' => 'Rede Pronta Matriz Telecom',
                'subdomain' => 'matriz',
                'document' => '12.345.678/0001-90',
                'email' => 'matriz@redepronta.com',
                'phone' => '(11) 4000-0000',
                'status' => 'active',
                'settings' => [
                    'trading_name' => 'Rede Pronta Oficial',
                    'brand_color' => '#FC6714',
                    'slogan' => 'ERP & Field Service',
                ],
            ]
        );

        $adminRole = Role::firstOrCreate(
            ['account_id' => $this->account->id, 'slug' => 'admin'],
            [
                'name' => 'Administrador',
                'description' => 'Acesso administrativo',
                'is_system' => true,
            ]
        );

        $userRole = Role::firstOrCreate(
            ['account_id' => $this->account->id, 'slug' => 'tecnico'],
            [
                'name' => 'Técnico de Campo',
                'description' => 'Operador de campo',
                'is_system' => false,
            ]
        );

        $this->adminUser = User::create([
            'account_id' => $this->account->id,
            'name' => 'Administrador Matriz',
            'email' => 'admin@redepronta.com',
            'password' => Hash::make('senha12345'),
            'role_id' => $adminRole->id,
            'is_super_admin' => true,
        ]);

        $this->regularUser = User::create([
            'account_id' => $this->account->id,
            'name' => 'Técnico Operacional',
            'email' => 'tecnico@redepronta.com',
            'password' => Hash::make('senha12345'),
            'role_id' => $userRole->id,
            'is_super_admin' => false,
        ]);
    }

    public function test_can_get_user_profile(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->getJson('/api/v1/settings/profile');

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Administrador Matriz');
        $response->assertJsonPath('data.email', 'admin@redepronta.com');
        $response->assertJsonPath('data.is_super_admin', true);
        $response->assertJsonPath('data.account.name', 'Rede Pronta Matriz Telecom');
    }

    public function test_can_update_user_profile_and_preferences(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->putJson('/api/v1/settings/profile', [
            'name' => 'Administrador Geral Alterado',
            'email' => 'admin.alterado@redepronta.com',
            'preferences' => [
                'theme' => 'dark',
                'table_density' => 'compact',
                'sound_enabled' => false,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Administrador Geral Alterado');
        $response->assertJsonPath('data.email', 'admin.alterado@redepronta.com');
        $response->assertJsonPath('data.preferences.theme', 'dark');

        $this->adminUser->refresh();
        $this->assertEquals('Administrador Geral Alterado', $this->adminUser->name);
        $this->assertEquals('dark', $this->adminUser->preferences['theme']);
    }

    public function test_can_update_user_password(): void
    {
        $this->actingAs($this->adminUser);

        // Erro: senha atual incorreta
        $failResponse = $this->putJson('/api/v1/settings/profile/password', [
            'current_password' => 'senha_errada',
            'password' => 'nova_senha_123',
            'password_confirmation' => 'nova_senha_123',
        ]);
        $failResponse->assertStatus(422);

        // Sucesso: senha atual correta
        $successResponse = $this->putJson('/api/v1/settings/profile/password', [
            'current_password' => 'senha12345',
            'password' => 'nova_senha_123',
            'password_confirmation' => 'nova_senha_123',
        ]);
        $successResponse->assertStatus(200);

        $this->adminUser->refresh();
        $this->assertTrue(Hash::check('nova_senha_123', $this->adminUser->password));
    }

    public function test_can_upload_and_remove_user_avatar(): void
    {
        $this->actingAs($this->adminUser);

        $file = UploadedFile::fake()->image('meu_avatar.png', 200, 200);

        $uploadResponse = $this->postJson('/api/v1/settings/profile/avatar', [
            'avatar' => $file,
        ]);

        $uploadResponse->assertStatus(200);
        $this->assertNotNull($uploadResponse->json('data.avatar_url'));

        $this->adminUser->refresh();
        $this->assertNotNull($this->adminUser->avatar_url);

        // Remoção
        $removeResponse = $this->deleteJson('/api/v1/settings/profile/avatar');
        $removeResponse->assertStatus(200);

        $this->adminUser->refresh();
        $this->assertNull($this->adminUser->avatar_url);
    }

    public function test_can_get_tenant_settings(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->getJson('/api/v1/settings/tenant');

        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Rede Pronta Matriz Telecom');
        $response->assertJsonPath('data.trading_name', 'Rede Pronta Oficial');
        $response->assertJsonPath('data.brand_color', '#FC6714');
    }

    public function test_admin_can_update_tenant_settings(): void
    {
        $this->actingAs($this->adminUser);

        $response = $this->putJson('/api/v1/settings/tenant', [
            'name' => 'Rede Pronta Matriz Telecom SA',
            'trading_name' => 'Rede Pronta FSM',
            'document' => '12.345.678/0001-90',
            'email' => 'corporativo@redepronta.com',
            'phone' => '(11) 4004-9999',
            'settings' => [
                'trading_name' => 'Rede Pronta FSM',
                'brand_color' => '#FC6714',
                'slogan' => 'Líder em Field Service Inteligente',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.trading_name', 'Rede Pronta FSM');
        $response->assertJsonPath('data.slogan', 'Líder em Field Service Inteligente');

        $this->account->refresh();
        $this->assertEquals('Rede Pronta FSM', $this->account->settings['trading_name']);
    }

    public function test_regular_user_cannot_update_tenant_settings(): void
    {
        $this->actingAs($this->regularUser);

        $response = $this->putJson('/api/v1/settings/tenant', [
            'name' => 'Tentativa de Hack',
        ]);

        $response->assertStatus(403);
    }

    public function test_can_upload_and_remove_tenant_logo_and_favicon(): void
    {
        $this->actingAs($this->adminUser);

        // Upload Logo
        $logoFile = UploadedFile::fake()->image('logo_oficial.png', 400, 100);
        $logoResponse = $this->postJson('/api/v1/settings/tenant/logo', [
            'logo' => $logoFile,
        ]);
        $logoResponse->assertStatus(200);
        $this->assertNotNull($logoResponse->json('data.logo_url'));

        // Upload Favicon
        $faviconFile = UploadedFile::fake()->image('favicon.png', 64, 64);
        $faviconResponse = $this->postJson('/api/v1/settings/tenant/favicon', [
            'favicon' => $faviconFile,
        ]);
        $faviconResponse->assertStatus(200);
        $this->assertNotNull($faviconResponse->json('data.favicon_url'));

        $this->account->refresh();
        $this->assertNotNull($this->account->settings['logo_url']);
        $this->assertNotNull($this->account->settings['favicon_url']);

        // Remoção de Logo e Favicon
        $this->deleteJson('/api/v1/settings/tenant/logo')->assertStatus(200);
        $this->deleteJson('/api/v1/settings/tenant/favicon')->assertStatus(200);

        $this->account->refresh();
        $this->assertNull($this->account->settings['logo_url']);
        $this->assertNull($this->account->settings['favicon_url']);
    }
}
