<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class TransferStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_depot_id' => ['required', 'integer', 'exists:depots,id'],
            'destination_depot_id' => ['required', 'integer', 'exists:depots,id', 'different:source_depot_id'],
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'serial_ids' => ['nullable', 'array'],
            'serial_ids.*' => ['integer', 'exists:stock_serials,id'],
            'document_ref' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'source_depot_id.required' => 'O depósito de origem é obrigatório.',
            'destination_depot_id.required' => 'O depósito de destino é obrigatório.',
            'destination_depot_id.different' => 'O depósito de destino deve ser diferente do depósito de origem.',
            'material_id.required' => 'O material é obrigatório.',
            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.min' => 'A quantidade mínima a transferir é 0.01.',
        ];
    }
}
