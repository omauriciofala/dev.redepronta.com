<?php

namespace App\Http\Requests\Operations;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
            'priority' => ['sometimes', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
            'status' => ['sometimes', 'in:INBOX,TRIAGED,PROMOTED_TICKET,RESOLVED_INTERNAL,CANCELED'],
            'assigned_user_id' => ['nullable', 'exists:users,id'],
            'customer_person_id' => ['nullable', 'exists:people,id'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título da tarefa é obrigatório.',
            'title.max' => 'O título não pode exceder 255 caracteres.',
            'status.in' => 'Status da tarefa inválido.',
            'priority.in' => 'Prioridade selecionada é inválida.',
        ];
    }
}
