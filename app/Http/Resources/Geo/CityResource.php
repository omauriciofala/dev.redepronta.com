<?php

namespace App\Http\Resources\Geo;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'state_id' => $this->state_id,
            'state_code' => $this->state?->code,
            'state_name' => $this->state?->name,
            'ibge_code' => $this->ibge_code,
            'name' => $this->name,
            'full_name' => "{$this->name} - {$this->state?->code}",
        ];
    }
}
