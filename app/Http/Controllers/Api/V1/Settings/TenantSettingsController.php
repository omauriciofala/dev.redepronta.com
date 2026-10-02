<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdateTenantSettingsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantSettingsController extends Controller
{
    /**
     * Retorna os dados da empresa/tenant ativa.
     */
    public function show(Request $request): JsonResponse
    {
        $account = $request->user()->account;

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Conta de negócio não associada ao usuário autenticado.',
            ], 404);
        }

        $settings = $account->settings ?? [];

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $account->id,
                'name' => $account->name,
                'subdomain' => $account->subdomain,
                'document' => $account->document,
                'email' => $account->email,
                'phone' => $account->phone,
                'status' => $account->status,
                'trading_name' => $settings['trading_name'] ?? $account->name,
                'brand_color' => $settings['brand_color'] ?? '#FC6714',
                'slogan' => $settings['slogan'] ?? 'ERP & Field Service',
                'logo_url' => $settings['logo_url'] ?? null,
                'favicon_url' => $settings['favicon_url'] ?? null,
                'settings' => $settings,
            ],
        ]);
    }

    /**
     * Atualiza os dados cadastrais e configurações da empresa/tenant.
     */
    public function update(UpdateTenantSettingsRequest $request): JsonResponse
    {
        $account = $request->user()->account;

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Conta de negócio não encontrada.',
            ], 404);
        }

        $validated = $request->validated();

        $account->name = $validated['name'];
        if (isset($validated['document'])) {
            $account->document = $validated['document'];
        }
        if (isset($validated['email'])) {
            $account->email = $validated['email'];
        }
        if (isset($validated['phone'])) {
            $account->phone = $validated['phone'];
        }

        $currentSettings = $account->settings ?? [];
        $incomingSettings = $validated['settings'] ?? [];

        if (isset($validated['trading_name'])) {
            $incomingSettings['trading_name'] = $validated['trading_name'];
        }

        $account->settings = array_merge($currentSettings, $incomingSettings);
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Configurações do negócio salvas com sucesso.',
            'data' => [
                'id' => $account->id,
                'name' => $account->name,
                'trading_name' => $account->settings['trading_name'] ?? $account->name,
                'brand_color' => $account->settings['brand_color'] ?? '#FC6714',
                'slogan' => $account->settings['slogan'] ?? '',
                'logo_url' => $account->settings['logo_url'] ?? null,
                'favicon_url' => $account->settings['favicon_url'] ?? null,
                'settings' => $account->settings,
            ],
        ]);
    }

    /**
     * Upload do logotipo institucional do Tenant.
     */
    public function uploadLogo(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->is_super_admin && (!$user->role || !in_array($user->role->slug, ['admin', 'administrador']))) {
            return response()->json(['message' => 'Ação restrita a administradores.'], 403);
        }

        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:3072'],
        ], [
            'logo.required' => 'Selecione uma imagem para o logotipo da empresa.',
            'logo.image' => 'O arquivo enviado deve ser uma imagem válida.',
            'logo.mimes' => 'Formatos permitidos para logotipo: PNG, SVG, JPG ou WEBP.',
            'logo.max' => 'O logotipo não pode exceder 3 MB.',
        ]);

        $account = $user->account;
        $settings = $account->settings ?? [];

        // Exclui logo anterior local se houver
        if (!empty($settings['logo_url']) && str_contains($settings['logo_url'], '/storage/tenants/logos/')) {
            $oldPath = str_replace('/storage/', '', $settings['logo_url']);
            Storage::disk('public')->delete($oldPath);
        }

        $file = $request->file('logo');
        $filename = 'logo_tenant_' . $account->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('tenants/logos', $filename, 'public');

        $settings['logo_url'] = Storage::url($path);
        $account->settings = $settings;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Logotipo do negócio atualizado com sucesso.',
            'data' => [
                'logo_url' => $settings['logo_url'],
            ],
        ]);
    }

    /**
     * Remove o logotipo institucional do Tenant.
     */
    public function removeLogo(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->is_super_admin && (!$user->role || !in_array($user->role->slug, ['admin', 'administrador']))) {
            return response()->json(['message' => 'Ação restrita a administradores.'], 403);
        }

        $account = $user->account;
        $settings = $account->settings ?? [];

        if (!empty($settings['logo_url']) && str_contains($settings['logo_url'], '/storage/tenants/logos/')) {
            $oldPath = str_replace('/storage/', '', $settings['logo_url']);
            Storage::disk('public')->delete($oldPath);
        }

        $settings['logo_url'] = null;
        $account->settings = $settings;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Logotipo removido. O sistema utilizará a identidade padrão.',
        ]);
    }

    /**
     * Upload do favicon institucional do Tenant.
     */
    public function uploadFavicon(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->is_super_admin && (!$user->role || !in_array($user->role->slug, ['admin', 'administrador']))) {
            return response()->json(['message' => 'Ação restrita a administradores.'], 403);
        }

        $request->validate([
            'favicon' => ['required', 'file', 'mimes:ico,png,svg', 'max:1024'],
        ], [
            'favicon.required' => 'Selecione um arquivo de favicon.',
            'favicon.mimes' => 'Formatos permitidos para favicon: ICO, PNG ou SVG.',
            'favicon.max' => 'O favicon não pode exceder 1 MB.',
        ]);

        $account = $user->account;
        $settings = $account->settings ?? [];

        // Exclui favicon anterior local se houver
        if (!empty($settings['favicon_url']) && str_contains($settings['favicon_url'], '/storage/tenants/favicons/')) {
            $oldPath = str_replace('/storage/', '', $settings['favicon_url']);
            Storage::disk('public')->delete($oldPath);
        }

        $file = $request->file('favicon');
        $filename = 'favicon_tenant_' . $account->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('tenants/favicons', $filename, 'public');

        $settings['favicon_url'] = Storage::url($path);
        $account->settings = $settings;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Favicon do negócio atualizado com sucesso.',
            'data' => [
                'favicon_url' => $settings['favicon_url'],
            ],
        ]);
    }

    /**
     * Remove o favicon institucional do Tenant.
     */
    public function removeFavicon(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->is_super_admin && (!$user->role || !in_array($user->role->slug, ['admin', 'administrador']))) {
            return response()->json(['message' => 'Ação restrita a administradores.'], 403);
        }

        $account = $user->account;
        $settings = $account->settings ?? [];

        if (!empty($settings['favicon_url']) && str_contains($settings['favicon_url'], '/storage/tenants/favicons/')) {
            $oldPath = str_replace('/storage/', '', $settings['favicon_url']);
            Storage::disk('public')->delete($oldPath);
        }

        $settings['favicon_url'] = null;
        $account->settings = $settings;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Favicon removido.',
        ]);
    }
}
