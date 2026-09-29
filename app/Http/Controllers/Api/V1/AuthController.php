<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Access\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $loginInput = trim($request->input('email'));
        $password = $request->input('password');

        $user = User::with(['role', 'person', 'account'])
            ->where('email', $loginInput)
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['E-mail ou senha incorretos. Verifique suas credenciais.'],
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Este usuário está inativo no sistema. Contate o administrador da sua conta.'],
            ]);
        }

        Auth::login($user, $request->boolean('remember'));
        if ($request->hasSession()) {
            $request->session()->regenerate();
            $request->session()->put('active_account_id', $user->account_id);
        }

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'account_id' => $user->account_id,
                'account_name' => $user->account?->name ?? 'Rede Pronta Matriz',
                'is_super_admin' => (bool) $user->is_super_admin,
                'role' => $user->role ? [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'slug' => $user->role->slug,
                ] : null,
                'permissions' => $user->getAllPermissionsSlugs(),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::logout();
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'message' => 'Sessão encerrada com sucesso.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Se não houver sessão ativa ainda, para uso no ERP retorna o usuário ativo padrão
        if (!$user) {
            $user = User::with(['role', 'person', 'account'])->first();
        } else {
            $user->load(['role', 'person', 'account']);
        }

        if (!$user) {
            return response()->json(['user' => null], 401);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'account_id' => $user->account_id,
                'account_name' => $user->account?->name ?? 'Rede Pronta Matriz',
                'is_super_admin' => (bool) $user->is_super_admin,
                'status' => $user->status,
                'role' => $user->role ? [
                    'id' => $user->role->id,
                    'name' => $user->role->name,
                    'slug' => $user->role->slug,
                ] : null,
                'permissions' => $user->getAllPermissionsSlugs(),
            ],
        ]);
    }
}
