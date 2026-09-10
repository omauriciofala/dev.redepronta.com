<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cluster_id' => ['required', 'integer', 'exists:depot_clusters,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'in:CENTRAL,REGIONAL_BASE,LAB_REPAIR'],
            'responsible_person_id' => ['nullable', 'integer', 'exists:people,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'cluster_id.required' => 'A posição regional é obrigatória.',
            'name.required' => 'O nome do depósito é obrigatório.',
            'code.required' => 'O código do depósito é obrigatório.',
            'type.required' => 'O tipo do depósito é obrigatório.',
        ];
    }
}
