<?php

namespace App\Http\Requests\Operations;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'source' => ['nullable', 'in:MANUAL,API,AI,EMAIL,WHATSAPP'],
            'priority' => ['nullable', 'in:LOW,MEDIUM,HIGH,CRITICAL'],
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
            'source.in' => 'Origem da tarefa inválida.',
            'priority.in' => 'Prioridade selecionada é inválida.',
            'assigned_user_id.exists' => 'O usuário selecionado não existe.',
            'customer_person_id.exists' => 'A pessoa selecionada não existe no cadastro.',
        ];
    }
}
