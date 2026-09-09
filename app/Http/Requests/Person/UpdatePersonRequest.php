<?php

namespace App\Http\Requests\Person;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\CpfCnpjRule;

class UpdatePersonRequest extends FormRequest
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

        if ($this->has('registration_date') && is_string($this->registration_date)) {
            $cleanReg = trim($this->registration_date);
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $cleanReg, $m)) {
                $merges['registration_date'] = "{$m[3]}-{$m[2]}-{$m[1]}";
            }
        }

                if ($this->has('birth_or_foundation_date') && is_string($this->birth_or_foundation_date)) {
            $cleanDate = trim($this->birth_or_foundation_date);
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $cleanDate, $m)) {
                $merges['birth_or_foundation_date'] = "{$m[3]}-{$m[2]}-{$m[1]}";
            }
        }
        if ($this->has('rg_issue_date') && is_string($this->rg_issue_date)) {
            $cleanDate = trim($this->rg_issue_date);
            if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $cleanDate, $m)) {
                $merges['rg_issue_date'] = "{$m[3]}-{$m[2]}-{$m[1]}";
            }
        }
        if ($this->has('share_capital') && is_string($this->share_capital)) {
            $cleanMoney = str_replace(['R$', ' ', '.'], '', $this->share_capital);
            $cleanMoney = str_replace(',', '.', $cleanMoney);
            $merges['share_capital'] = (float) $cleanMoney;
        }
        if (!empty($merges)) {
            $this->merge($merges);
        }
    }

    public function rules(): array
    {
        $personId = $this->route('person')?->id ?? $this->route('person');

        return [
            'person_type' => ['sometimes', 'required', 'in:individual,legal'],
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'trade_name' => ['nullable', 'string', 'max:150'],
            'document_number' => [
                'nullable',
                'string',
                'max:20',
                new CpfCnpjRule($this->person_type),
                Rule::unique('people', 'document_number')
                    ->where(fn ($query) => $query->where('account_id', $this->user()?->account_id ?? 1))
                    ->whereNull('deleted_at')
                    ->ignore($personId),
            ],
            'rg_ie' => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date'],
            'registration_date' => ['nullable', 'date'],
            'group_name' => ['nullable', 'string', 'max:100'],

            // Papéis do Cadastro
            'is_client' => ['boolean'],
            'is_supplier' => ['boolean'],
            'is_employee' => ['boolean'],
            'is_outsourced' => ['boolean'],
            'is_seller' => ['boolean'],
            'is_driver' => ['boolean'],
            'is_carrier' => ['boolean'],
            'is_requester' => ['boolean'],

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
            'reference' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],

            // Endereço Comercial
            'commercial_same_as_residential' => ['boolean'],
            'commercial_postal_code' => ['nullable', 'string', 'max:10'],
            'commercial_street' => ['nullable', 'string', 'max:150'],
            'commercial_number' => ['nullable', 'string', 'max:30'],
            'commercial_complement' => ['nullable', 'string', 'max:100'],
            'commercial_neighborhood' => ['nullable', 'string', 'max:100'],
            'commercial_reference' => ['nullable', 'string'],
            'commercial_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'commercial_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'commercial_city_id' => ['nullable', 'exists:cities,id'],

                        'rg_issuer' => ['nullable', 'string', 'max:30'],
            'rg_issue_date' => ['nullable', 'date'],
            'state_registration' => ['nullable', 'string', 'max:30'],
            'municipal_registration' => ['nullable', 'string', 'max:30'],
            'cnae' => ['nullable', 'string', 'max:150'],
            'suframa_registration' => ['nullable', 'string', 'max:30'],
            'mother_name' => ['nullable', 'string', 'max:150'],
            'father_name' => ['nullable', 'string', 'max:150'],
            'birth_or_foundation_date' => ['nullable', 'date'],
            'share_capital' => ['nullable', 'numeric', 'min:0'],
            'birth_place' => ['nullable', 'string', 'max:100'],
            'status' => ['in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
