<?php

namespace App\Http\Requests\Operations;

use Illuminate\Foundation\Http\FormRequest;

class AdvanceDispatchStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:DISPATCHED,ON_ROUTE,ARRIVED_SITE,IN_SERVICE,RFO_SUBMITTED,APPROVED,COMPLETED,CANCELED,FAILED'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'O novo status do acionamento é obrigatório.',
            'status.in' => 'Status do acionamento inválido.',
        ];
    }
}
