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
        'broj_telefona',
        'adresa',
        'stanje_na_racunu',
    ];

    protected $hidden = [
        'lozinka',
    ];

    public function ponude()
    {
        return $this->hasMany(Ponuda::class, 'korisnik_id');
    }
}
