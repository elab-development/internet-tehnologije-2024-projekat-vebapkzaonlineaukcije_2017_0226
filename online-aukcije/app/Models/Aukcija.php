<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aukcija extends Model
{
    public function ponude()
    {
        return $this->hasMany(Ponuda::class, 'aukcijaID');
    }

    public function proizvodi()
    {
        return $this->hasMany(Proizvod::class, 'aukcijaID');
    }
}
