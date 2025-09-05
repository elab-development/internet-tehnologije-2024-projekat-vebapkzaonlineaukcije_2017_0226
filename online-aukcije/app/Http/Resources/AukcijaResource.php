<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProizvodResource;
use App\Http\Resources\PonudaResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AukcijaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mojaNajvisaPonudaIznos = null;
        if (Auth::check() && $this->relationLoaded('ponude')) {
            $korisnikId = Auth::id();
            
            $mojaPonuda = $this->ponude
                                 ->where('korisnik_id', $korisnikId)
                                 ->sortByDesc('iznos')
                                 ->first();
            
            if ($mojaPonuda) {
                $mojaNajvisaPonudaIznos = $mojaPonuda->iznos;
            }
        }
        return [
            'id' => $this->id,
            'naziv'=>$this->naziv,
            'pocetna_cena' => $this->pocetna_cena,
            'trenutna_cena' => $this->trenutna_cena,
            'datum_pocetka' => $this->datum_pocetka->format('Y-m-d H:i:s'),
            'vreme_isteka' => $this->vreme_isteka->format('Y-m-d H:i:s'),
            'status_aukcije' => $this->status_aukcije,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'korisnik_id' => $this->korisnik_id,
            'proizvodi' => \App\Http\Resources\ProizvodResource::collection($this->whenLoaded('proizvodi')),
            'ponude' => \App\Http\Resources\PonudaResource::collection($this->whenLoaded('ponude')),
            'moja_najvisa_ponuda_iznos' => 
                $this->when($mojaNajvisaPonudaIznos !== null, $mojaNajvisaPonudaIznos),
        ];
    }
}
