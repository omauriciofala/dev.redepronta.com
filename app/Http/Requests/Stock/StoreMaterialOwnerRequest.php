<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => $this->code ? strtoupper(trim($this->code)) : null,
            'name' => $this->name ? trim($this->name) : null,
            'is_active' => $this->has('is_active') ? filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN) : true,
        ]);
    }

    public function rules(): array
    {
        return [
            'person_id' => ['required', 'integer', 'exists:people,id'],
            'code' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'person_id.required' => 'É obrigatório selecionar uma pessoa com papel de solicitante para vincular ao proprietário.',
            'person_id.exists' => 'A pessoa selecionada não existe na base de dados.',
            'code.required' => 'O código identificador do proprietário é obrigatório.',
            'name.required' => 'O nome ou razão social do proprietário é obrigatório.',
        ];
    }
}
