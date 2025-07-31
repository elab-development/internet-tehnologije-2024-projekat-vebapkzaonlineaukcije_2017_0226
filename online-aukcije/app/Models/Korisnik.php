<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Korisnik extends Model
{
    use HasFactory;
    protected $table = 'korisnik';

    protected $fillable = [
        'ime',
        'prezime',
        'email',
        'lozinka',
        'brojTelefona',
        'adresa',
        'stanjeNaRacunu',
    ];

    protected $hidden = [
        'lozinka',
    ];

    public function ponude()
    {
        return $this->hasMany(Ponuda::class, 'korisnikID');
    }
}
