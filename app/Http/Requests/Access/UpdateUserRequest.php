<?php

namespace App\Http\Requests\Access;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('user');
        $accountId = $this->user()?->account_id ?? 1;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'email' => [
                'sometimes',
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')
                    ->where(fn ($q) => $q->where('account_id', $accountId))
                    ->ignore($userId),
            ],
            'password' => ['nullable', 'string', 'min:6'],
            'person_id' => ['nullable', 'integer', 'exists:people,id'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'is_super_admin' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:active,inactive'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,slug'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do usuário é obrigatório.',
            'name.max' => 'O nome não pode exceder 150 caracteres.',
            'email.required' => 'O e-mail de acesso é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está sendo utilizado por outro usuário nesta conta.',
            'password.min' => 'A nova senha deve conter no mínimo 6 caracteres.',
            'person_id.exists' => 'A pessoa selecionada não existe no cadastro.',
            'role_id.exists' => 'O papel selecionado é inválido.',
            'status.in' => 'O status deve ser ativo ou inativo.',
        ];
    }
}
