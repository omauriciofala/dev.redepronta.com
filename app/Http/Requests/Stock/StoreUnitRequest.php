<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUnitRequest extends FormRequest
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
        $unitId = $this->route('unit')?->id ?? $this->route('unit');

        return [
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('units', 'code')->ignore($unitId),
            ],
            'name' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'A sigla/código da unidade de medida é obrigatória (ex: UND, MT, CX).',
            'code.unique' => 'Já existe uma unidade de medida cadastrada com este código.',
            'code.max' => 'A sigla/código não pode ter mais que 10 caracteres.',
            'name.required' => 'O nome por extenso da unidade de medida é obrigatório.',
        ];
    }
}
