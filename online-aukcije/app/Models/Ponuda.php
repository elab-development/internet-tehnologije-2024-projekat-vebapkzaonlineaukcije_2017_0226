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
        'vreme_ponude',
        'aukcija_id',
        'korisnik_id',
        
    ];

    protected $casts = [
        'vreme_ponude' => 'datetime', // 
        'created_at' => 'datetime',  // 
        'updated_at' => 'datetime',  // 
    ];

    public function aukcija()
    {
        return $this->belongsTo(Aukcija::class, 'aukcija_id');
    }

    public function korisnik()
    {
        return $this->belongsTo(Korisnik::class, 'korisnik_id');
    }
}
