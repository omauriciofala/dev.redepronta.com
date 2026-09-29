<?php

namespace App\Http\Requests\Access;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $roleId = $this->route('role')?->id ?? $this->route('role');
        $accountId = $this->user()?->account_id ?? 1;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('roles', 'slug')
                    ->where(fn ($q) => $q->where('account_id', $accountId))
                    ->ignore($roleId),
            ],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,slug'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do papel é obrigatório.',
            'name.max' => 'O nome do papel não pode exceder 100 caracteres.',
            'slug.required' => 'O identificador único (slug) do papel é obrigatório.',
            'slug.unique' => 'Já existe um papel com este identificador nesta conta.',
            'description.max' => 'A descrição não pode exceder 255 caracteres.',
        ];
    }
}
