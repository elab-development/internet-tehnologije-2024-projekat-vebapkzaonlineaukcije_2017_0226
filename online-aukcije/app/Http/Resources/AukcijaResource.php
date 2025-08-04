<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AukcijaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pocetna_cena' => $this->pocetna_cena,
            'trenutna_cena' => $this->trenutna_cena,
            'datum_pocetka' => $this->datum_pocetka->format('Y-m-d H:i:s'),
            'status_aukcije' => $this->status_aukcije,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
