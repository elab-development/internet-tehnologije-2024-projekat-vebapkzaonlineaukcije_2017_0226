<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Korisnik extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;
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
        'remember_token',
    ];

    protected $casts = [
        'lozinka' => 'hashed',
    ];

    public function getAuthPassword()
    {
        return $this->lozinka;
    }

    public function ponude()
    {
        return $this->hasMany(Ponuda::class, 'korisnik_id');
    }

    public function aukcije()
    {
        return $this->hasMany(Ponuda::class, 'korisnik_id');
    }
}
