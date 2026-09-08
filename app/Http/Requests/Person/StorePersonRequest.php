<?php

namespace App\Http\Requests\Person;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('document_number')) {
            $this->merge([
                'document_number' => preg_replace('/\D/', '', (string) $this->document_number),
            ]);
        }
        if ($this->has('postal_code')) {
            $this->merge([
                'postal_code' => preg_replace('/\D/', '', (string) $this->postal_code),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'person_type' => ['required', 'in:individual,legal'],
            'name' => ['required', 'string', 'max:150'],
            'trade_name' => ['nullable', 'string', 'max:150'],
            'document_number' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('people', 'document_number')
                    ->where(fn ($query) => $query->where('account_id', $this->user()?->account_id ?? 1))
                    ->whereNull('deleted_at'),
            ],
            'rg_ie' => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date'],
            'gender_id' => ['nullable', 'exists:genders,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'street' => ['nullable', 'string', 'max:150'],
            'number' => ['nullable', 'string', 'max:30'],
            'complement' => ['nullable', 'string', 'max:100'],
            'neighborhood' => ['nullable', 'string', 'max:100'],
            'is_employee' => ['boolean'],
            'is_supplier' => ['boolean'],
            'is_client' => ['boolean'],
            'is_requester' => ['boolean'],
            'status' => ['in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome completo ou razão social é obrigatório.',
            'person_type.required' => 'O tipo de pessoa (Física ou Jurídica) é obrigatório.',
            'document_number.unique' => 'Este CPF ou CNPJ já está cadastrado nesta conta.',
            'city_id.exists' => 'A cidade selecionada é inválida.',
            'gender_id.exists' => 'O gênero selecionado é inválido.',
            'email.email' => 'O endereço de e-mail informado não é válido.',
        ];
    }
}
