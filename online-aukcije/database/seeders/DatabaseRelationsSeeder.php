<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Korisnik;
use App\Models\Aukcija;
use App\Models\Proizvod;
use App\Models\Ponuda;

class DatabaseRelationsSeeder extends Seeder
{
    use WithFaker;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->setUpFaker(); 

        $korisnici = Korisnik::factory()->count(10)->create();
        $this->command->info('Kreirano 10 korisnika.');

        $aukcije = Aukcija::factory()->count(5)->create();
        $this->command->info('Kreirano 5 aukcija.');

        $aukcije->each(function ($aukcija) use ($korisnici) {
            Proizvod::factory()->count($this->faker->numberBetween(1, 3))->create([
                'aukcija_id' => $aukcija->id,
            ]);
            $this->command->info("Aukcija ID: {$aukcija->id} - dodeljeni proizvodi.");

            $ponudaCount = $this->faker->numberBetween(0, 10);
            for ($i = 0; $i < $ponudaCount; $i++) {
                $randomKorisnik = $korisnici->random(); 
                
                $ponuda = Ponuda::factory()->forAukcija($aukcija)->create([
                    'korisnik_id' => $randomKorisnik->id,
                ]);

                $aukcija->trenutna_cena = $ponuda->iznos;
                $aukcija->save();
            }
            $this->command->info("Aukcija ID: {$aukcija->id} - dodeljeno {$ponudaCount} ponuda. Finalna cena: {$aukcija->trenutna_cena}.");
        });

        $this->command->info('Svi podaci uspešno generisani i povezani!');
    }
    
}
