<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'owner_id' => $this->owner_id ?: null,
            'responsible_person_id' => $this->responsible_person_id ?: null,
            'city_id' => $this->city_id ?: null,
            'is_active' => $this->has('is_active') ? filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN) : true,
        ]);
    }

    public function rules(): array
    {
        return [
            'cluster_id' => ['required', 'integer', 'exists:depot_clusters,id'],
            'owner_id' => ['nullable', 'integer', 'exists:material_owners,id'],
            'name' => ['required', 'string', 'max:150'],
            'code' => ['required', 'string', 'max:50'],
            'type' => ['required', 'string', 'max:50'],
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
