<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ponuda extends Model
{
    use HasFactory;
    protected $table = 'ponuda';

    protected $fillable = [
        'iznos',
        'vremePonude',
        'aukcijaID',
        'korisnikID',
        
    ];

    protected $casts = [
        'vremePonude' => 'datetime', // 
        'created_at' => 'datetime',  // 
        'updated_at' => 'datetime',  // 
    ];

    public function aukcija()
    {
        return $this->belongsTo(Aukcija::class, 'aukcijaID');
    }

    public function korisnik()
    {
        return $this->belongsTo(Korisnik::class, 'korisnikID');
    }
}
