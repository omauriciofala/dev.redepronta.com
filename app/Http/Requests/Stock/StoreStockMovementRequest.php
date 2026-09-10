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
            'document_ref' => ['nullable', 'string', 'max:100'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'document_date' => ['nullable', 'date'],
            'movement_date' => ['nullable'],
            'receiver_person_id' => ['nullable', 'integer', 'exists:people,id'],
            'driver_person_id' => ['nullable', 'integer', 'exists:people,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'attachments' => ['nullable', 'array'],

            // Suporte a item único (retrocompatibilidade)
            'material_id' => ['required_without:items', 'nullable', 'integer', 'exists:materials,id'],
            'quantity' => ['required_without:items', 'nullable', 'numeric', 'min:0.01'],
            'serial_ids' => ['nullable', 'array'],
            'serial_ids.*' => ['integer', 'exists:stock_serials,id'],
            'serials' => ['nullable', 'array'],

            // Suporte a grid de múltiplos materiais
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.material_id' => ['required_with:items', 'integer', 'exists:materials,id'],
            'items.*.quantity' => ['required_with:items', 'numeric', 'min:0.01'],
            'items.*.serial_ids' => ['nullable', 'array'],
            'items.*.serial_ids.*' => ['integer', 'exists:stock_serials,id'],
            'items.*.serials' => ['nullable', 'array'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
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
            $rules['source_depot_id'] = ['required', 'integer', 'exists:depots,id'];
            $rules['destination_depot_id'] = ['nullable', 'integer', 'exists:depots,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'movement_type.required' => 'O tipo de movimentação é obrigatório.',
            'movement_type.in' => 'Tipo de movimentação inválido. Permite apenas Entrada, Saída, Devolução ou Transferência.',
            'material_id.required_without' => 'O material é obrigatório quando não fornecida a lista de itens.',
            'material_id.exists' => 'O material selecionado não existe.',
            'quantity.required_without' => 'A quantidade é obrigatória quando não fornecida a lista de itens.',
            'quantity.min' => 'A quantidade mínima a movimentar é 0.01.',
            'source_depot_id.required' => 'O depósito de origem é obrigatório.',
            'source_depot_id.exists' => 'O depósito de origem selecionado não existe.',
            'destination_depot_id.required' => 'O depósito de destino é obrigatório.',
            'destination_depot_id.exists' => 'O depósito de destino selecionado não existe.',
            'destination_depot_id.different' => 'O depósito de destino deve ser diferente do depósito de origem.',
            'receiver_person_id.exists' => 'A pessoa selecionada como recebedor não existe.',
            'driver_person_id.exists' => 'A pessoa selecionada como motorista não existe.',
            'items.min' => 'Adicione ao menos um material na grid para efetuar a movimentação.',
            'items.*.material_id.required' => 'O material de cada item é obrigatório.',
            'items.*.material_id.exists' => 'Um dos materiais informados não existe no catálogo.',
            'items.*.quantity.required' => 'A quantidade de cada material na grid é obrigatória.',
            'items.*.quantity.min' => 'A quantidade de cada material deve ser maior que zero.',
        ];
    }
}
