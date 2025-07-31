<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Aukcija extends Model
{
    use HasFactory;
    protected $table = 'aukcija';

    protected $fillable = [
        'pocetnaCena',
        'trenutnaCena',
        'datumPocetka',
        'statusAukcije'
    ];
    public function ponude()
    {
        return $this->hasMany(Ponuda::class, 'aukcijaID');
    }

    public function proizvodi()
    {
        return $this->hasMany(Proizvod::class, 'aukcijaID');
    }
}
