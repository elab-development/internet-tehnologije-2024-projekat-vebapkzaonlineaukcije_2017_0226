<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ponuda extends Model
{
    public function aukcija()
    {
        return $this->belongsTo(Aukcija::class, 'aukcijaID');
    }

    public function korisnik()
    {
        return $this->belongsTo(Korisnik::class, 'korisnikID');
    }
}
