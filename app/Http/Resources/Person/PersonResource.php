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
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'birth_date_formatted' => $this->birth_date?->format('d/m/Y'),
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
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at?->format('d/m/Y H:i'),
        ];
    }
}
