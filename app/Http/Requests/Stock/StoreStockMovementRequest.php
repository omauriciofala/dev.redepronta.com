<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $type = strtoupper($this->input('movement_type', 'TRANSFER'));

        $rules = [
            'movement_type' => ['required', 'string', 'in:ENTRY,EXIT,RETURN,TRANSFER'],
            'material_id' => ['required', 'integer', 'exists:materials,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'document_ref' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'serial_ids' => ['nullable', 'array'],
            'serial_ids.*' => ['integer', 'exists:stock_serials,id'],
            'serials' => ['nullable', 'array'],
        ];

        if ($type === 'TRANSFER') {
            $rules['source_depot_id'] = ['required', 'integer', 'exists:depots,id'];
            $rules['destination_depot_id'] = ['required', 'integer', 'exists:depots,id', 'different:source_depot_id'];
        } elseif ($type === 'EXIT') {
            $rules['source_depot_id'] = ['required', 'integer', 'exists:depots,id'];
            $rules['destination_depot_id'] = ['nullable', 'integer', 'exists:depots,id'];
        } elseif ($type === 'ENTRY') {
            $rules['source_depot_id'] = ['nullable', 'integer', 'exists:depots,id'];
            $rules['destination_depot_id'] = ['required', 'integer', 'exists:depots,id'];
        } elseif ($type === 'RETURN') {
            $rules['source_depot_id'] = ['nullable', 'integer', 'exists:depots,id'];
            $rules['destination_depot_id'] = ['required', 'integer', 'exists:depots,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'movement_type.required' => 'O tipo de movimentação é obrigatório.',
            'movement_type.in' => 'Tipo de movimentação inválido. Permite apenas Entrada, Saída, Devolução ou Transferência.',
            'material_id.required' => 'O material é obrigatório.',
            'material_id.exists' => 'O material selecionado não existe.',
            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.min' => 'A quantidade mínima a movimentar é 0.01.',
            'source_depot_id.required' => 'O depósito de origem é obrigatório.',
            'source_depot_id.exists' => 'O depósito de origem selecionado não existe.',
            'destination_depot_id.required' => 'O depósito de destino é obrigatório.',
            'destination_depot_id.exists' => 'O depósito de destino selecionado não existe.',
            'destination_depot_id.different' => 'O depósito de destino deve ser diferente do depósito de origem.',
        ];
    }
}
