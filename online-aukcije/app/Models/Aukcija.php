<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Aukcija extends Model
{
    use HasFactory;
    protected $table = 'aukcija';

    protected $fillable = [
        'naziv',
        'pocetna_cena',
        'trenutna_cena',
        'maksimalna_cena',
        'datum_pocetka',
        'vreme_isteka',
        'status_aukcije',

    ];

    protected $casts = [
        'datum_pocetka' => 'datetime',
        'vreme_isteka'=> 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ponude()
    {
        return $this->hasMany(Ponuda::class, 'aukcija_id');
    }

    public function proizvodi()
    {
        return $this->hasMany(Proizvod::class, 'aukcija_id');
    }
}
