<?php

namespace App\Http\Requests\Operations;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'origin_task_id' => ['nullable', 'exists:tasks,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'customer_person_id' => ['required', 'exists:people,id'],
            'requester_person_id' => ['nullable', 'exists:people,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'category_id' => ['required', 'exists:ticket_categories,id'],
            'reason_id' => ['required', 'exists:ticket_reasons,id'],
            'priority' => ['nullable', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
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
            'title.required' => 'O título do chamado é obrigatório.',
            'customer_person_id.required' => 'O cliente é obrigatório.',
            'customer_person_id.exists' => 'O cliente selecionado não existe.',
            'city_id.required' => 'A cidade do atendimento é obrigatória.',
            'city_id.exists' => 'A cidade selecionada é inválida.',
            'department_id.required' => 'O departamento é obrigatório.',
            'department_id.exists' => 'O departamento não existe.',
            'category_id.required' => 'A categoria é obrigatória.',
            'category_id.exists' => 'A categoria não existe.',
            'reason_id.required' => 'O motivo do chamado é obrigatório.',
            'reason_id.exists' => 'O motivo não existe.',
        ];
    }
}
