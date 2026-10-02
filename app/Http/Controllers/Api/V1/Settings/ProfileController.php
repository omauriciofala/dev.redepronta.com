<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\UpdatePasswordRequest;
use App\Http\Requests\Settings\UpdateProfileRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Retorna os dados completos do perfil do usuário autenticado.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role', 'permissions', 'account']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'is_super_admin' => (bool) $user->is_super_admin,
                'status' => $user->status,
                'role' => $user->role ? [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'slug' => $user->role->slug,
                ] : null,
                'permissions' => $user->is_super_admin
                    ? ['*']
                    : $user->permissions->pluck('slug')->toArray(),
                'account' => $user->account ? [
                    'id' => $user->account->id,
                    'name' => $user->account->name,
                    'subdomain' => $user->account->subdomain,
                    'settings' => $user->account->settings,
                ] : null,
                'preferences' => $user->preferences ?? [
                    'theme' => 'system',
                    'table_density' => 'comfortable',
                    'sound_enabled' => true,
                ],
                'created_at' => $user->created_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Atualiza dados básicos e preferências do usuário autenticado.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validated();
        $user->name = $data['name'];
        $user->email = $data['email'];

        if (isset($data['preferences'])) {
            $currentPreferences = $user->preferences ?? [];
            $user->preferences = array_merge($currentPreferences, $data['preferences']);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Perfil atualizado com sucesso.',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'preferences' => $user->preferences,
            ],
        ]);
    }

    /**
     * Atualiza a senha de acesso do usuário.
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->password = Hash::make($request->validated('password'));
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Senha alterada com sucesso.',
        ]);
    }

    /**
     * Realiza o upload de imagem de avatar do usuário.
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
        ], [
            'avatar.required' => 'Selecione uma imagem para o avatar.',
            'avatar.image' => 'O arquivo enviado deve ser uma imagem válida.',
            'avatar.mimes' => 'Formatos permitidos: JPEG, PNG, JPG, WEBP ou SVG.',
            'avatar.max' => 'A imagem não pode exceder 2 MB.',
        ]);

        $user = $request->user();

        // Se já tiver avatar anterior armazenado localmente, remove
        if ($user->avatar_url && str_contains($user->avatar_url, '/storage/avatars/')) {
            $oldPath = str_replace('/storage/', '', $user->avatar_url);
            Storage::disk('public')->delete($oldPath);
        }

        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');

        $user->avatar_url = Storage::url($path);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Foto de perfil atualizada com sucesso.',
            'data' => [
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }

    /**
     * Remove o avatar do usuário.
     */
    public function removeAvatar(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->avatar_url && str_contains($user->avatar_url, '/storage/avatars/')) {
            $oldPath = str_replace('/storage/', '', $user->avatar_url);
            Storage::disk('public')->delete($oldPath);
        }

        $user->avatar_url = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Foto de perfil removida com sucesso.',
        ]);
    }
}
