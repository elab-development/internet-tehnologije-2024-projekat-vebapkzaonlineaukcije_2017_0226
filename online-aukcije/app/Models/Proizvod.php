<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proizvod extends Model
{
    use HasFactory;
    protected $table = 'proizvod';

    protected $fillable = [
        'naziv',
        'opis',
        'kategorija',
        'stanje',
        'slika_url',
        'aukcija_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function aukcija()
    {
        return $this->belongsTo(Aukcija::class, 'aukcija_id');
    }
}
