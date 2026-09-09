<?php

namespace App\Http\Resources\Person;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Geo\CityResource;

class PersonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $cityLoaded = $this->relationLoaded('city') && $this->city;
        $commCityLoaded = $this->relationLoaded('commercialCity') && $this->commercialCity;

        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'person_type' => $this->person_type,
            'name' => $this->name,
            'trade_name' => $this->trade_name,
            'document_number' => $this->document_number,
            'rg_ie' => $this->rg_ie,
            'documents' => [
                'rg_number' => $this->rg_ie,
                'rg_issuer' => $this->rg_issuer,
                'rg_issue_date' => $this->rg_issue_date?->format('d/m/Y'),
                'state_registration' => $this->state_registration,
                'municipal_registration' => $this->municipal_registration,
                'cnae' => $this->cnae,
                'suframa_registration' => $this->suframa_registration,
            ],
            'filiation' => [
                'mother_name' => $this->mother_name,
                'father_name' => $this->father_name,
                'birth_or_foundation_date' => $this->birth_or_foundation_date?->format('d/m/Y'),
                'share_capital' => $this->share_capital !== null ? (float) $this->share_capital : null,
                'birth_place' => $this->birth_place,
            ],
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'birth_date_formatted' => $this->birth_date?->format('d/m/Y'),
            'registration_date' => $this->registration_date?->format('Y-m-d'),
            'registration_date_formatted' => $this->registration_date ? $this->registration_date->format('d/m/Y') : $this->created_at?->format('d/m/Y'),
            'group_id' => $this->group_id,
            'group_name' => $this->group?->name ?? $this->group_name ?? 'Geral',
            'group' => $this->group ? [
                'id' => $this->group->id,
                'name' => $this->group->name,
                'color' => $this->group->color,
            ] : null,
            'gender_id' => $this->gender_id,
            'gender_name' => $this->gender?->name,

            // Papéis do Cadastro Canônicos (S/N)
            'personas' => [
                'is_client' => (bool) $this->is_client,
                'is_supplier' => (bool) $this->is_supplier,
                'is_employee' => (bool) $this->is_employee,
                'is_outsourced' => (bool) $this->is_outsourced,
                'is_seller' => (bool) $this->is_seller,
                'is_driver' => (bool) $this->is_driver,
                'is_carrier' => (bool) $this->is_carrier,
                'is_requester' => (bool) $this->is_requester,
            ],
            'roles' => [
                'client' => (bool) $this->is_client,
                'supplier' => (bool) $this->is_supplier,
                'employee' => (bool) $this->is_employee,
                'outsourced' => (bool) $this->is_outsourced,
                'seller' => (bool) $this->is_seller,
                'driver' => (bool) $this->is_driver,
                'carrier' => (bool) $this->is_carrier,
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
                'reference' => $this->reference,
                'latitude' => $this->latitude ? (float) $this->latitude : null,
                'longitude' => $this->longitude ? (float) $this->longitude : null,
                'city_id' => $this->city_id,
                'city_name' => $cityLoaded ? $this->city->name : null,
                'state_code' => $cityLoaded ? $this->city->state?->code : null,
                'ibge_code' => $cityLoaded ? $this->city->ibge_code : null,
                'city' => new CityResource($this->whenLoaded('city')),
            ],
            'residential_address' => [
                'postal_code' => $this->postal_code,
                'street' => $this->street,
                'number' => $this->number,
                'complement' => $this->complement,
                'neighborhood' => $this->neighborhood,
                'reference' => $this->reference,
                'latitude' => $this->latitude ? (float) $this->latitude : null,
                'longitude' => $this->longitude ? (float) $this->longitude : null,
                'city_id' => $this->city_id,
                'city_name' => $cityLoaded ? $this->city->name : null,
                'state_code' => $cityLoaded ? $this->city->state?->code : null,
                'ibge_code' => $cityLoaded ? $this->city->ibge_code : null,
                'city' => new CityResource($this->whenLoaded('city')),
            ],
            'commercial_address' => [
                'same_as_residential' => (bool) ($this->commercial_same_as_residential ?? true),
                'postal_code' => $this->commercial_same_as_residential ? $this->postal_code : $this->commercial_postal_code,
                'street' => $this->commercial_same_as_residential ? $this->street : $this->commercial_street,
                'number' => $this->commercial_same_as_residential ? $this->number : $this->commercial_number,
                'complement' => $this->commercial_same_as_residential ? $this->complement : $this->commercial_complement,
                'neighborhood' => $this->commercial_same_as_residential ? $this->neighborhood : $this->commercial_neighborhood,
                'reference' => $this->commercial_same_as_residential ? $this->reference : $this->commercial_reference,
                'latitude' => $this->commercial_same_as_residential ? ($this->latitude ? (float) $this->latitude : null) : ($this->commercial_latitude ? (float) $this->commercial_latitude : null),
                'longitude' => $this->commercial_same_as_residential ? ($this->longitude ? (float) $this->longitude : null) : ($this->commercial_longitude ? (float) $this->commercial_longitude : null),
                'city_id' => $this->commercial_same_as_residential ? $this->city_id : $this->commercial_city_id,
                'city_name' => $this->commercial_same_as_residential
                    ? ($cityLoaded ? $this->city->name : null)
                    : ($commCityLoaded ? $this->commercialCity->name : null),
                'state_code' => $this->commercial_same_as_residential
                    ? ($cityLoaded ? $this->city->state?->code : null)
                    : ($commCityLoaded ? $this->commercialCity->state?->code : null),
                'ibge_code' => $this->commercial_same_as_residential
                    ? ($cityLoaded ? $this->city->ibge_code : null)
                    : ($commCityLoaded ? $this->commercialCity->ibge_code : null),
                'city' => new CityResource($this->whenLoaded($this->commercial_same_as_residential ? 'city' : 'commercialCity')),
            ],
            'status' => $this->status,
            'group_id' => $this->group_id,
            'group' => $this->group ? [
                'id' => $this->group->id,
                'name' => $this->group->name,
                'color' => $this->group->color,
            ] : null,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),
        ];
    }
}
