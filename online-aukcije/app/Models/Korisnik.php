<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Korisnik extends Model
{
    public function ponude()
    {
        return $this->hasMany(Ponuda::class, 'korisnikID');
    }
}
