<?php

namespace App\Http\Requests\Operations;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'requester_person_id' => ['nullable', 'exists:people,id'],
            'department_id' => ['sometimes', 'required', 'exists:departments,id'],
            'category_id' => ['sometimes', 'required', 'exists:ticket_categories,id'],
            'reason_id' => ['sometimes', 'required', 'exists:ticket_reasons,id'],
            'priority' => ['sometimes', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'status' => ['sometimes', 'in:OPEN,IN_TRIAGE,WAITING_DISPATCH,IN_FIELD,RESOLVED_REMOTE,CLOSED,CANCELED'],
            'address_street' => ['nullable', 'string', 'max:150'],
            'address_number' => ['nullable', 'string', 'max:30'],
            'address_neighborhood' => ['nullable', 'string', 'max:100'],
            'address_postal_code' => ['nullable', 'string', 'max:10'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status do chamado inválido.',
            'priority.in' => 'Prioridade selecionada é inválida.',
        ];
    }
}
