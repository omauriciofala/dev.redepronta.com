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
        $merges = [];

        if ($this->has('document_number')) {
            $merges['document_number'] = preg_replace('/\D/', '', (string) $this->document_number);
        }
        if ($this->has('postal_code')) {
            $merges['postal_code'] = preg_replace('/\D/', '', (string) $this->postal_code);
        }
        if ($this->has('commercial_postal_code')) {
            $merges['commercial_postal_code'] = preg_replace('/\D/', '', (string) $this->commercial_postal_code);
        }

        // Suporte a Dia, Mês e Ano (DD/MM/AAAA)
        if ($this->has('birth_date') && is_string($this->birth_date)) {
            $cleanDate = trim($this->birth_date);
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $cleanDate, $m)) {
                $merges['birth_date'] = "{$m[3]}-{$m[2]}-{$m[1]}";
            }
        }

        if (!empty($merges)) {
            $this->merge($merges);
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

            // Endereço Comercial
            'commercial_same_as_residential' => ['boolean'],
            'commercial_postal_code' => ['nullable', 'string', 'max:10'],
            'commercial_street' => ['nullable', 'string', 'max:150'],
            'commercial_number' => ['nullable', 'string', 'max:30'],
            'commercial_complement' => ['nullable', 'string', 'max:100'],
            'commercial_neighborhood' => ['nullable', 'string', 'max:100'],
            'commercial_city_id' => ['nullable', 'exists:cities,id'],

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
            'city_id.exists' => 'A cidade residencial selecionada é inválida.',
            'commercial_city_id.exists' => 'A cidade comercial selecionada é inválida.',
            'gender_id.exists' => 'O gênero selecionado é inválido.',
            'email.email' => 'O endereço de e-mail informado não é válido.',
            'birth_date.date' => 'A data deve ser válida no formato Dia, Mês e Ano (DD/MM/AAAA).',
        ];
    }
}
