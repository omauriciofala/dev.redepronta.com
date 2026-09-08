<?php

namespace App\Http\Resources\Person;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Geo\CityResource;

class PersonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'person_type' => $this->person_type,
            'name' => $this->name,
            'trade_name' => $this->trade_name,
            'document_number' => $this->document_number,
            'rg_ie' => $this->rg_ie,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'gender_id' => $this->gender_id,
            'gender_name' => $this->gender?->name,
            'personas' => [
                'is_employee' => (bool) $this->is_employee,
                'is_supplier' => (bool) $this->is_supplier,
                'is_client' => (bool) $this->is_client,
                'is_requester' => (bool) $this->is_requester,
            ],
            'contact' => [
                'email' => $this->email,
                'phone' => $this->phone,
                'whatsapp' => $this->whatsapp,
            ],
            'address' => [
                'postal_code' => $this->postal_code,
                'street' => $this->street,
                'number' => $this->number,
                'complement' => $this->complement,
                'neighborhood' => $this->neighborhood,
                'city_id' => $this->city_id,
                'city' => new CityResource($this->whenLoaded('city')),
            ],
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
