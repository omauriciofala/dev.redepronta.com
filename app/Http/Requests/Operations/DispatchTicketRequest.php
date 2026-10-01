<?php

namespace App\Http\Requests\Operations;

use Illuminate\Foundation\Http\FormRequest;

class DispatchTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'worker_person_id' => ['required', 'exists:people,id'],
            'depot_id' => ['required', 'exists:depots,id'],
            'scheduled_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'worker_person_id.required' => 'O técnico responsável pelo acionamento é obrigatório.',
            'worker_person_id.exists' => 'O técnico selecionado não foi encontrado no cadastro.',
            'depot_id.required' => 'A base de apoio ou depósito de suprimentos é obrigatório.',
            'depot_id.exists' => 'A base ou depósito selecionado não existe.',
        ];
    }
}
