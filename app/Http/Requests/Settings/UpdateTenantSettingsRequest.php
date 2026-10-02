<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTenantSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->is_super_admin || ($user->role && in_array($user->role->slug, ['admin', 'administrador']));
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:150'],
            'trading_name' => ['nullable', 'string', 'max:150'],
            'document' => ['nullable', 'string', 'max:25'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'settings' => ['nullable', 'array'],
            'settings.trading_name' => ['nullable', 'string', 'max:150'],
            'settings.brand_color' => ['nullable', 'string', 'regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'settings.slogan' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'A Razão Social do negócio é obrigatória.',
            'name.min' => 'A Razão Social deve ter no mínimo 3 caracteres.',
            'email.email' => 'Informe um e-mail corporativo válido.',
            'settings.brand_color.regex' => 'A cor da marca deve ser um código hexadecimal válido (ex: #FC6714).',
        ];
    }
}
