<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proizvod extends Model
{
    public function aukcija()
    {
        return $this->belongsTo(Aukcija::class, 'aukcijaID');
    }
}
