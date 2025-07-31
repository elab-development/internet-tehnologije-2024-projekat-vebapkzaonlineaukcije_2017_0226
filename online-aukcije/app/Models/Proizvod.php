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
        'slikaURL',
        'aukcijaID'
    ];
    public function aukcija()
    {
        return $this->belongsTo(Aukcija::class, 'aukcijaID');
    }
}
