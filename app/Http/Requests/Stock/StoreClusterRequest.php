<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class StoreClusterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'owner_id' => ['required', 'integer', 'exists:material_owners,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'owner_id.required' => 'O vínculo com um Proprietário é obrigatório para a Posição Regional.',
            'owner_id.exists' => 'O proprietário selecionado não foi encontrado.',
            'name.required' => 'O nome da posição regional é obrigatório.',
            'code.required' => 'A sigla/código da posição regional é obrigatória.',
        ];
    }
}
